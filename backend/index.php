<?php

// INFO: 
// phpinfo();
// Permissions: https://www.digitalocean.com/community/tutorials/how-to-create-a-new-user-and-grant-permissions-in-mysql
// Options: https://dev.mysql.com/doc/refman/8.0/en/privileges-provided.html#privileges-provided-summary
// delete user: DROP USER 'username'@'%';
// delete right: REVOKE type_of_permission ON database_name.table_name FROM 'username'@'host'; 
// display permissions: SHOW GRANTS FOR 'username'@'host'; 
// save and reload: FLUSH; 
// WITH GRANT OPTION => grant priviliges to others // ALL PRIVILEGES => all privileges
// DONT USE (table names and attributes): https://dev.mysql.com/doc/refman/8.0/en/keywords.html
// Jump To: REMOVE, TODO, BUG

/* declare(strict_types=1); */

// HEADERS
header("Access-Control-Allow-Origin: *"); // allow CORS
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization"); // Authorization => send token
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // OPTIONS => get available methods
header('Content-Type: application/json'); // return JSON

use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

require_once('./vendor/autoload.php');

/* ---------- */

// JWT - Authentication
// Using getenv() and putenv() is strongly discouraged due to the fact that these functions are not thread safe.
// https://github.com/vlucas/phpdotenv
$dotenv = Dotenv::createImmutable(__DIR__); // (.env content: PRIVATE_KEY="", PUBLIC_KEY="") || __DIR__ ,private_key.pem || public_key.pem
$dotenv->load(); // load .env file
$dotenv->required(['PRIVATE_KEY', 'PUBLIC_KEY'])->notEmpty(); // RS256

/* ---------- */

// INIT (MySQL-Database Cfg)
$host = 'host.docker.internal'; // database IP in docker container (127.0.0.1 (localhost) or other)
$root = 'root';
$pass = 'MEDTSSP';
$schema = 'vidslide';
$port = "3196"; /* 3196 || 3306 */

/* ---------- */

// RESPONSE
$response = array(
    "data" => array(),
    "info" => array(
        "database_connection_details" => array(
            "database_username" => $root
        ),
        "fetch_method" => $_SERVER['REQUEST_METHOD'],
        "fetch_object" => "",
        "fetch_id" => "",
        "fetch_params" => array()
    ),
    "log" => array(),
    "token" => "",
    "response" => "",
    "requested" => "",
    "error" => false
);

// CONNECT
$connection = mysqli_connect($host, $root, $pass, "", $port); // $connection = mysqli_init(); + $connect_options = array(); + mysqli_real_connect(..., $connect_options (SSL and more));

/* ---------- */

// SETUP
if (!$connection) {
    errorOccurred($connection, $response, __LINE__, "connection error");
} else {
    // create guest user
    $guest_user_username = "guest";
    $guest_user_password = "420GUEST69";
    $guest_user = "CREATE USER IF NOT EXISTS '$guest_user_username'@'%' IDENTIFIED WITH mysql_native_password BY '$guest_user_password'"; // with mysql_native_password to avoid PHP problems
    $guest_user_query = mysqli_query($connection, $guest_user);

    if ($guest_user_query) {
        array_push($response["log"], date('H:i:s') . ": vidslide guest user created/found " . "[$guest_user_query]");
    } else {
        errorOccurred($connection, $response, __LINE__, "couldn't create guest user");
    }

    // get privileges of guest user
    $grantee = "guest'@'%";
    $guest_user_privileges = "SELECT COUNT(*) as privileges FROM INFORMATION_SCHEMA.USER_PRIVILEGES WHERE GRANTEE = \"'guest'@'%'\" AND PRIVILEGE_TYPE = 'SELECT'";
    $guest_user_privileges_query = mysqli_query($connection, $guest_user_privileges);
    $guest_user_privileges_row = mysqli_fetch_assoc($guest_user_privileges_query);
    $guest_user_privilege_count = $guest_user_privileges_row['privileges'];

    if ($guest_user_privilege_count == 0) {
        array_push($response["log"], date('H:i:s') . ": vidslide guest user privileges fetched " . "[$guest_user_privilege_count]");

        mysqli_free_result($guest_user_privileges_query);

        // set guest user privileges => READ ONLY (SELECT)
        $guest_user_grant_privileges = "GRANT SELECT ON *.* TO '$guest_user_username'@'%'";
        $guest_user_grant_privileges_query = mysqli_query($connection, $guest_user_grant_privileges);

        if ($guest_user_grant_privileges_query) {
            array_push($response["log"], date('H:i:s') . ": vidslide guest user privileges set " . "[$guest_user_grant_privileges_query]");
        } else {
            errorOccurred($connection, $response, __LINE__, "couldn't asign guest user privileges");
        }
    } else if ($guest_user_privilege_count == 1) {
        array_push($response["log"], date('H:i:s') . ": vidslide guest user privileges fetched " . "[$guest_user_privilege_count]");
    } else {
        errorOccurred($connection, $response, __LINE__, "couldn't fetch guest user privileges");
    }

    // create database
    $create_schema = "CREATE DATABASE IF NOT EXISTS $schema";
    $schema_query = mysqli_query($connection, $create_schema);

    if ($schema_query) {
        array_push($response["log"], date('H:i:s') . ": vidslide database created/found " . "[$schema_query]");
        mysqli_select_db($connection, $schema);
    } else {
        errorOccurred($connection, $response, __LINE__, "couldn't create database");
    }

    // create tables
    array_push($response["log"], date('H:i:s') . ": looking for tables...");

    $table_01 = "CREATE TABLE IF NOT EXISTS USER (
        USER_ID INT AUTO_INCREMENT PRIMARY KEY,
        USER_USERNAME VARCHAR(25) NOT NULL,
        USER_PASSWORD VARCHAR(25) NOT NULL,
        USER_PROFILEPICTURE VARCHAR(100) DEFAULT NULL,
        USER_PROFILEDESCRIPTION VARCHAR(1000) DEFAULT NULL,
        USER_DATETIMECREATED TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        USER_LASTUPDATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT UNIQUE_USERNAME UNIQUE (USER_USERNAME)
    )";

    $table_02 = "CREATE TABLE IF NOT EXISTS VIDEO (
        VIDEO_ID INT AUTO_INCREMENT PRIMARY KEY,
        VIDEO_TITLE VARCHAR(25) NOT NULL,
        VIDEO_DESCRIPTION VARCHAR(500) DEFAULT NULL,
        VIDEO_LOCATION VARCHAR(250) NOT NULL,
        VIDEO_SIZE VARCHAR(6) NOT NULL,
        VIDEO_VIEWS INT DEFAULT 0,
        VIDEO_SHARES INT DEFAULT 0,
        VIDEO_DATETIMEPOSTED TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        VIDEO_LASTUPDATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        USER_ID INT NOT NULL,
        FOREIGN KEY (USER_ID) REFERENCES USER(USER_ID)
    )";

    $table_03 = "CREATE TABLE IF NOT EXISTS USER_SOCIAL (
        SOCIAL_ID INT AUTO_INCREMENT PRIMARY KEY,
        SOCIAL_PLATFORM VARCHAR(25) NOT NULL,
        SOCIAL_URL VARCHAR(250) NOT NULL,
        USER_ID INT NOT NULL,
        FOREIGN KEY (USER_ID) REFERENCES USER(USER_ID),
        CONSTRAINT UNIQUE_SOCIAL UNIQUE (SOCIAL_PLATFORM, SOCIAL_URL, USER_ID)
    )";

    $table_04 = "CREATE TABLE IF NOT EXISTS USER_FOLLOWING (
        FOLLOWING_ID INT AUTO_INCREMENT PRIMARY KEY,
        FOLLOWING_SUBSCRIBER INT NOT NULL,
        FOLLOWING_SUBSCRIBED INT NOT NULL,
        FOREIGN KEY (FOLLOWING_SUBSCRIBER) REFERENCES USER(USER_ID),
        FOREIGN KEY (FOLLOWING_SUBSCRIBED) REFERENCES USER(USER_ID),
        CONSTRAINT UNIQUE_FOLLOWING UNIQUE (FOLLOWING_SUBSCRIBER, FOLLOWING_SUBSCRIBED)
    )";

    $table_05 = "CREATE TABLE IF NOT EXISTS VIDEO_FEEDBACK (
        VIDEO_FEEDBACK_ID INT AUTO_INCREMENT PRIMARY KEY,
        VIDEO_FEEDBACK_TYPE ENUM('positive', 'negative') NOT NULL,
        VIDEO_ID INT NOT NULL,
        USER_ID INT NOT NULL,
        FOREIGN KEY (VIDEO_ID) REFERENCES VIDEO(VIDEO_ID),
        FOREIGN KEY (USER_ID) REFERENCES USER(USER_ID),
        CONSTRAINT UNIQUE_VIDEO_FEEDBACK UNIQUE (VIDEO_ID, USER_ID)
    )";

    $table_06 = "CREATE TABLE IF NOT EXISTS VIDEO_COMMENT (
        COMMENT_ID INT AUTO_INCREMENT PRIMARY KEY,
        COMMENT_PARENT_ID INT DEFAULT NULL,
        COMMENT_MESSAGE VARCHAR(250) NOT NULL,
        COMMENT_DATETIMEPOSTED TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        COMMENT_LASTUPDATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        VIDEO_ID INT NOT NULL,
        USER_ID INT NOT NULL,
        FOREIGN KEY (VIDEO_ID) REFERENCES VIDEO(VIDEO_ID),
        FOREIGN KEY (USER_ID) REFERENCES USER(USER_ID)
    )";

    $table_07 = "CREATE TABLE IF NOT EXISTS COMMENT_FEEDBACK (
        COMMENT_FEEDBACK_ID INT AUTO_INCREMENT PRIMARY KEY,
        COMMENT_FEEDBACK_TYPE ENUM('positive', 'negative') NOT NULL,
        COMMENT_ID INT NOT NULL,
        USER_ID INT NOT NULL,
        FOREIGN KEY (COMMENT_ID) REFERENCES VIDEO_COMMENT(COMMENT_ID),
        CONSTRAINT UNIQUE_COMMENT_FEEDBACK UNIQUE (COMMENT_ID, USER_ID)
    )";

    $table_08 = "CREATE TABLE IF NOT EXISTS VIDEO_HASHTAG (
        HASHTAG_ID INT AUTO_INCREMENT PRIMARY KEY,
        HASHTAG_NAME VARCHAR(500) NOT NULL,
        VIDEO_ID INT NOT NULL,
        FOREIGN KEY (VIDEO_ID) REFERENCES VIDEO(VIDEO_ID),
        CONSTRAINT UNIQUE_HASHTAG UNIQUE (VIDEO_ID, HASHTAG_NAME)
    )";

    for ($i = 1; $i <= 8; $i++) {
        $table_create_query = mysqli_query($connection, ${"table_" . str_pad($i, 2, "0", STR_PAD_LEFT)});
        if ($table_create_query) {
            array_push($response["log"], date('H:i:s') . ": table " . $i . " created/found");
        } else {
            errorOccurred($connection, $response, __LINE__, "couldn't create table " . $i);
        }
    }

    // create mock data
    $user_count = "SELECT COUNT(*) as amount FROM USER"; // returns 0 => empty || IGNORE => ignores if UNIQUE
    $user_count_query = mysqli_query($connection, $user_count);
    $video_count_fetched = mysqli_fetch_assoc($user_count_query);
    if (intval($video_count_fetched["amount"]) == 0) { // PROBLEM: causes out of sync problem somehow if run again after first execution
        $mock_user =
            "INSERT IGNORE INTO USER (USER_USERNAME, USER_PASSWORD, USER_PROFILEPICTURE, USER_PROFILEDESCRIPTION) VALUES ('maxmustermann', 'passwort123', 'https://picsum.photos/50/50', 'Ich bin Max und ich liebe es, Videos zu machen!');

            INSERT IGNORE INTO VIDEO (VIDEO_TITLE, VIDEO_DESCRIPTION, VIDEO_LOCATION, VIDEO_SIZE, USER_ID) VALUES ('Mein erster Vlog', 'Hier ist mein erster Vlog, den ich jemals gemacht habe!', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', '25MB', 1);
    
            INSERT IGNORE INTO USER_SOCIAL (SOCIAL_PLATFORM, SOCIAL_URL, USER_ID) VALUES ('Twitter', 'https://github.com/jonasfroeller', 1);
        
            INSERT IGNORE INTO USER_FOLLOWING (FOLLOWING_SUBSCRIBER, FOLLOWING_SUBSCRIBED) VALUES (1, 1);
        
            INSERT IGNORE INTO VIDEO_FEEDBACK (VIDEO_FEEDBACK_TYPE, VIDEO_ID, USER_ID) VALUES ('positive', 1, 1);
        
            INSERT IGNORE INTO VIDEO_HASHTAG (HASHTAG_NAME, VIDEO_ID) VALUES ('Vlog', 1);
        
            INSERT IGNORE INTO VIDEO_COMMENT (COMMENT_MESSAGE, VIDEO_ID, USER_ID) VALUES ('Tolles Video!', 1, 1);
        
            INSERT IGNORE INTO COMMENT_FEEDBACK (COMMENT_FEEDBACK_TYPE, COMMENT_ID, USER_ID) VALUES ('negative', 1, 1);";

        mysqli_multi_query($connection, $mock_user);

        array_push($response["log"], date('H:i:s') . ": inserted mock data");
    } else {
        array_push($response["log"], date('H:i:s') . ": found mock data in database");
    }

    // check if server is alive
    if (mysqli_ping($connection)) {
        array_push($response["log"], date('H:i:s') . ": connection ok");
    } else {
        errorOccurred($connection, $response, __LINE__, "database ping failed");
    }

    array_push($response["log"], date('H:i:s') . ": finished database initialisation");
    // export_database($connection);

    mysqli_close($connection);
}

// login as guest => READ ONLY
$connection = mysqli_connect($host, $guest_user_username, $guest_user_password, $schema, $port);
if (!$connection) {
    errorOccurred($connection, $response, __LINE__, "reconnection as guest user couldn't be astablished");
} else {
    $response["info"]["database_connection_details"]["database_username"] = $guest_user_username;
    array_push($response["log"], date('H:i:s') . ": logged in as read only guest");
}

/* ---------- */

// REQUEST METHODS | GET, POST, PUT, DELETE STATEMENTS

// GET-Options:
// - medium=user [MEDIUM]
//   - id=all [ID]
//   - id=? [ID]
// - medium=video [MEDIUM]
//   - id=all [ID]
//     - user_id=? [ID]
//   - id=random [ID]
//   - id=? [ID]
// - medium=comments [MEDIUM]
//   - id=? (video id) [ID]
if ($_SERVER['REQUEST_METHOD'] === 'GET') { // no private data (password is hashed before writing it into the database)
    $response["info"]["fetch_method"] = $_SERVER['REQUEST_METHOD']; // logging
    if (isset($_GET["medium"])) {
        $medium = $_GET["medium"];
        $response["info"]["fetch_object"] = $medium; // logging
        if ($medium == "user") { // ?medium=user [MEDIUM]
            if (isset($_GET["id"])) {
                $id = mysqli_real_escape_string($connection, $_GET["id"]);

                // logging
                $response["info"]["fetch_id"] = $id;
                $log = $medium . " with id=" . $id . " as " . $response["info"]["database_connection_details"]["database_username"] . " [" . $response["info"]["fetch_method"] . "]";
                array_push($response["log"], date('H:i:s') . ": fetching " . $log);
                $response["response"] = "fetched " . $log;

                if ($id == "all") { // ?medium=video&id=all [ID] // REMOVE: just for testing, not needed in production
                    $table_user = "SELECT * FROM USER";
                    $response = getMedium($connection, $response, $table_user, $id, false);
                } else { // ?medium=video&id=? [ID]
                    $userID = intval($id);
                    if ($userID != 0) { // 0 on failure 1 on non empty arrays :/
                        $response["info"]["fetch_id"] = $userID; // logging
                        $exists = checkIfIdExists($connection, "user", $userID);

                        if ($exists) {
                            $table_user = mysqli_prepare($connection, 'SELECT * FROM USER WHERE USER_ID = ?');
                            $response = getMedium($connection, $response, $table_user, $userID, true);
                        } else {
                            errorOccurred($connection, $response, __LINE__, "user not found");
                        }
                    } else {
                        errorOccurred($connection, $response, __LINE__, "id param invalid");
                    }
                }
            } else {
                errorOccurred($connection, $response, __LINE__, "id param missing");
            }
        } else if ($medium == "video") { // ?medium=video [MEDIUM]
            if (isset($_GET["id"])) {
                $id = mysqli_real_escape_string($connection, $_GET["id"]);
                $response["info"]["fetch_id"] = $id; // logging

                // logging
                $response["info"]["fetch_id"] = $id;
                $log = $medium . " with id=" . $id . " as " . $response["info"]["database_connection_details"]["database_username"] . " [" . $response["info"]["fetch_method"] . "]";
                array_push($response["log"], date('H:i:s') . ": fetching " . $log);
                $response["response"] = "fetched " . $log;

                if ($id == "all") {
                    if (isset($_GET["user_id"])) { // ?medium=video&id=all&user_id=1 [ID]
                        $userID = intval(mysqli_real_escape_string($connection, $_GET["user_id"]));
                        if ($userID != 0) { // 0 on failure 1 on non empty arrays :/
                            array_push($response["info"]["fetch_params"], array("user_id" => $userID)); // logging
                            $exists = checkIfIdExists($connection, "video", $userID);

                            if ($exists) {
                                $table_video = mysqli_prepare($connection, 'SELECT * FROM VIDEO WHERE USER_ID = ? ORDER BY VIDEO_ID DESC');
                                $response = getMedium($connection, $response, $table_video, $userID, true);
                            } else {
                                errorOccurred($connection, $response, __LINE__, "videos of user not found");
                            }
                        } else {
                            errorOccurred($connection, $response, __LINE__, "id param invalid");
                        }
                    } else {
                        errorOccurred($connection, $response, __LINE__, "user_id param missing");
                    }
                } else if ($id == "random") { // ?medium=video&id=random [ID]
                    $table_video = "SELECT * FROM VIDEO ORDER BY RAND() LIMIT 1"; // inefficient
                    $response = getMedium($connection, $response, $table_video, $id, false);
                } else { // ?medium=video&id=? [ID]
                    $videoID = intval($id);
                    if ($videoID != 0) { // 0 on failure 1 on non empty arrays :/
                        $response["info"]["fetch_id"] = $videoID; // logging
                        $exists = checkIfIdExists($connection, "video", $videoID);

                        if ($exists) {
                            $table_video = mysqli_prepare($connection, 'SELECT * FROM VIDEO WHERE VIDEO_ID = ?');
                            $response = getMedium($connection, $response, $table_video, $videoID, true);
                        } else {
                            errorOccurred($connection, $response, __LINE__, "video not found");
                        }
                    } else {
                        errorOccurred($connection, $response, __LINE__, "id param invalid");
                    }
                }
            } else {
                errorOccurred($connection, $response, __LINE__, "id param missing");
            }
        } else if ($medium == "comments") { // ?medium=comments [MEDIUM]
            if (isset($_GET["id"])) { // ?medium=comments&id=? [ID]
                $videoID = intval(mysqli_real_escape_string($connection, $_GET["id"]));
                if ($videoID != 0) { // 0 on failure 1 on non empty arrays :/
                    $response["info"]["fetch_id"] = $videoID;
                    $exists_1 = checkIfIdExists($connection, "video", $videoID);
                    $exists_2 = checkIfIdExists($connection, "comment", $videoID);

                    if ($exists_1 && $exists_2) {
                        $table_comment = mysqli_prepare($connection, 'SELECT * FROM VIDEO_COMMENT WHERE VIDEO_ID = ?');
                        $response = getMedium($connection, $response, $table_comment, $videoID, true);
                    } else {
                        errorOccurred($connection, $response, __LINE__, "comments not found");
                    }
                } else {
                    errorOccurred($connection, $response, __LINE__, "id param invalid");
                }
            }
        }
    } else {
        errorOccurred($connection, $response, __LINE__, "medium param missing");
    }
}

// POST-Options:
// TODO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response["info"]["fetch_method"] = $_SERVER['REQUEST_METHOD'];
    if (isset($_POST["action"])) {
        $action = $_POST["action"];
        if ($action == "auth") {
            // authentication
            // https://github.com/firebase/php-jwt
            $privateKey = $_ENV['PRIVATE_KEY'];
            $issuedAt   = new DateTimeImmutable();
            $expire     = $issuedAt->modify('+60 seconds')->getTimestamp();
            $serverName = "vidslide.com";

            $payload = [
                'iat'  => $issuedAt->getTimestamp(),         // Issued at: time when the token was generated
                'iss'  => $serverName,                       // Issuer
                'nbf'  => $issuedAt->getTimestamp(),         // Not before
                'exp'  => $expire,                           // Expire
            ];

            if (isset($_POST["username"]) && isset($_POST["password"])) {
                $username = mysqli_real_escape_string($connection, $_POST["username"]);
                $password = mysqli_real_escape_string($connection, $_POST["password"]);
                $response["info"]["database_connection_details"]["database_username"] = $username;

                // TODO: USER EXISTS => false: create user
            }

            $response["token"] = sendJWT($payload, $privateKey);
        } else if ($action == "signup") {
        } else if ($action == "signin") {
        } else if ($action == "signout") {
        } else if ($action == "video") {
            // authentication
            // https://github.com/firebase/php-jwt

            if (!preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
                $response["token"] = 'Token not found in request';
            }

            $jwt = $matches[1];
            $publicKey = $_ENV['PUBLIC_KEY'];

            $response["token"] = getJWT($connection, $response, $jwt, $publicKey);
        } else if ($action == "comment") {
        } else if ($action == "like") {
        } else if ($action == "dislike") {
        }
    }
}

// PUT-Options:
// TODO
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $response["info"]["fetch_method"] = $_SERVER['REQUEST_METHOD'];
    if (isset($_POST["medium"])) {
        $medium = $_POST["medium"];
        if ($medium == "profile_username") {
        } else if ($medium == "profile_password") {
        } else if ($medium == "profile_description") {
        } else if ($medium == "profile_socials") {
        } else if ($medium == "profile_picture") {
        } else if ($medium == "video_post_title") {
        } else if ($medium == "video_post_description") {
        } else if ($medium == "video_post_hashtags") {
        } else if ($medium == "comment_post_text") {
        }
    }
}

// DELETE-Options:
// TODO
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $response["info"]["fetch_method"] = $_SERVER['REQUEST_METHOD'];
    if (isset($_POST["medium"])) {
        $medium = $_POST["medium"];
        if ($medium == "all") {
        } else if ($medium == "account") {
        } else if ($medium == "video") {
        } else if ($medium == "comment") {
        }
    }
}

/* ---------- */

// FUNCTIONS
// GENERAL FUNCTIONS
// on error
function errorOccurred($connection = null, $response, $line, $message = null)
{
    $response["error"] = ($message ?? "") . " (error occurred at line " . $line . " [" . mysqli_connect_errno() . ";" .  mysqli_connect_error() . "])";
    // Disconnect
    mysqli_close($connection); // mysqli_kill() => close fast | $thread_id = mysqli_thread_id($con); mysqli_kill($con, $thread_id);
    // Log Response
    echo json_encode($response);
    exit(); // die()
}

// VALIDATION FUNCTIONS
// sends token to client
function sendJWT($payload, $privateKey)
{
    $jwt = JWT::encode($payload, $privateKey, 'RS256');
    return $jwt;
}

// gets and validates token from client
function getJWT($connection, $response, $jwt, $publicKey)
{
    try {
        $token = JWT::decode($jwt, new Key($publicKey, 'RS256'));
    } catch (InvalidArgumentException $e) {
        errorOccurred($connection, $response, __LINE__, "provided key/key-array is empty or malformed.");
    } catch (DomainException $e) {
        errorOccurred($connection, $response, __LINE__, "provided algorithm is unsupported OR provided key is invalid OR unknown error thrown in openSSL or libsodium OR libsodium is required but not available.");
    } catch (SignatureInvalidException $e) {
        errorOccurred($connection, $response, __LINE__, "provided JWT signature verification failed.");
    } catch (BeforeValidException $e) {
        errorOccurred($connection, $response, __LINE__, "provided JWT is trying to be used before 'nbf' claim OR provided JWT is trying to be used before 'iat' claim.");
    } catch (ExpiredException $e) {
        errorOccurred($connection, $response, __LINE__, "provided JWT is trying to be used after 'exp' claim.");
    } catch (UnexpectedValueException $e) {
        errorOccurred($connection, $response, __LINE__, "provided JWT is malformed OR provided JWT is missing an algorithm / using an unsupported algorithm OR provided JWT algorithm does not match provided key OR provided key ID in key/key-array is empty or invalid.");
    }

    return json_decode(json_encode($token), true); // (array) casts to assoc array
}

// DATABASE FUNCTIONS
function checkIfIdExists($connection, $type, $id)
{
    if ($type == "user") {
        $id_exists = mysqli_prepare($connection, "SELECT COUNT(*) as count FROM USER WHERE USER_ID = ?");
    } else if ($type == "video") {
        $id_exists = mysqli_prepare($connection, "SELECT COUNT(*) as count FROM VIDEO WHERE VIDEO_ID = ?");
    } else if ($type == "comment") {
        $id_exists = mysqli_prepare($connection, "SELECT COUNT(*) as count FROM VIDEO_COMMENT WHERE VIDEO_ID = ?");
    } else { // fallback
        $id_exists = mysqli_prepare($connection, "SELECT COUNT(*) as count FROM USER WHERE USER_ID = ?");
    }

    mysqli_stmt_bind_param($id_exists, 'i', $id);
    mysqli_stmt_execute($id_exists);
    $query = mysqli_stmt_get_result($id_exists);
    $result = mysqli_fetch_all($query, MYSQLI_ASSOC);

    return $result[0]["count"] !== 0;
}

// gets data from database
function getMedium($connection, $response, $table, $id, $prepare = false)
{
    if ($prepare) {
        // bind values
        mysqli_stmt_bind_param($table, 'i', $id);
        // execute query
        mysqli_stmt_execute($table);
        // fetch result
        $query = mysqli_stmt_get_result($table);
        $rows = mysqli_fetch_all($query, MYSQLI_ASSOC);
        $res = json_encode($rows);
    } else {
        // execute query
        $query = mysqli_query($connection, $table); // SELECT, SHOW, DESCRIBE or EXPLAIN returns mysqli_result object // mysqli_real_query() => doesn't wait for response // mysqli_reap_async_query() => async
        // fetch all
        $rows = mysqli_fetch_all($query, MYSQLI_ASSOC);
        $res = json_encode($rows);
    }

    // save as response data
    $response["requested"] = $response["requested"] . "READ table [$prepare];"; // res;res;res
    array_push($response["data"], $res);
    // free result set
    mysqli_free_result($query);
    return $response;
}

// exports data from database
function export_database($connection)
{
    // read tables
    $tables = array();
    $result = mysqli_query($connection, 'SHOW TABLES');
    while ($row = mysqli_fetch_row($result)) {
        $tables[] = $row[0];
    }

    // create dump file
    $backup_name = "backup_" . time() . ".sql";
    $backup_path = "./database-backups/" . $backup_name;
    $handle = fopen($backup_path, 'w');

    // export structure
    $i = 0;
    foreach ($tables as $table) {
        $result = mysqli_query($connection, 'SHOW CREATE TABLE ' . $table);
        $row = mysqli_fetch_row($result);

        if ($i != 0) {
            fwrite($handle, "\n\n" . $row[1] . ";\n\n");
        } else {
            fwrite($handle, $row[1] . ";\n\n");
        }

        $i++;

        // export
        $result = mysqli_query($connection, 'SELECT * FROM ' . $table);
        $num_fields = mysqli_num_fields($result);

        while ($row = mysqli_fetch_row($result)) {
            fwrite($handle, 'INSERT INTO ' . $table . ' VALUES(');

            for ($i = 0; $i < $num_fields; $i++) {
                if (isset($row[$i])) {
                    $value = $row[$i];
                } else {
                    $value = 'NULL';
                }

                if ($i == 0) {
                    fwrite($handle, '');
                } else {
                    fwrite($handle, ',');
                }

                fwrite($handle, "'" . mysqli_real_escape_string($connection, $value) . "'");
            }

            fwrite($handle, ");\n");
        }
    }

    fclose($handle);
}

/* ---------- */

// CLEAN UP
// Log Response
echo json_encode($response);

// Disconnect
mysqli_close($connection);

/* ---------- */

// SCRIPT END
exit();