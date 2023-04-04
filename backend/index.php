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
        "fetch_method" => $_SERVER['REQUEST_METHOD']
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
    errorOccurred($connection, $response, __LINE__);
} else {
    // create guest user
    $guest_user_username = "guest";
    $guest_user_password = "420GUEST69";
    $guest_user = "CREATE USER IF NOT EXISTS '$guest_user_username'@'%' IDENTIFIED WITH mysql_native_password BY '$guest_user_password'"; // with mysql_native_password to avoid PHP problems
    $guest_user_query = mysqli_query($connection, $guest_user);

    if ($guest_user_query) {
        array_push($response["log"], date('H:i:s') . ": vidslide guest user created/found " . "[$guest_user_query]");
    } else {
        errorOccurred($connection, $response, __LINE__);
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
            errorOccurred($connection, $response, __LINE__);
        }
    } else if ($guest_user_privilege_count == 1) {
        array_push($response["log"], date('H:i:s') . ": vidslide guest user privileges fetched " . "[$guest_user_privilege_count]");
    } else {
        errorOccurred($connection, $response, __LINE__);
    }

    // create database
    $create_schema = "CREATE DATABASE IF NOT EXISTS $schema";
    $schema_query = mysqli_query($connection, $create_schema);

    if ($schema_query) {
        array_push($response["log"], date('H:i:s') . ": vidslide database created/found " . "[$schema_query]");
        mysqli_select_db($connection, $schema);
    } else {
        errorOccurred($connection, $response, __LINE__);
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
            errorOccurred($connection, $response, __LINE__);
        }
    }

    // create mock data
    $user_count = "SELECT COUNT(*) as amount FROM USER"; // returns 0 => empty || IGNORE => ignores if UNIQUE
    $user_count_query = mysqli_query($connection, $user_count);
    $video_count_fetched = mysqli_fetch_assoc($user_count_query);
    if (intval($video_count_fetched["amount"]) == 0) { // PROBLEM: causes out of sync problem somehow if run again after first execution
        $mock_user =
            "INSERT IGNORE INTO USER (USER_USERNAME, USER_PASSWORD, USER_PROFILEPICTURE, USER_PROFILEDESCRIPTION) VALUES ('maxmustermann', 'passwort123', 'https://example.com/profilepic1.jpg', 'Ich bin Max und ich liebe es, Videos zu machen!');

            INSERT IGNORE INTO VIDEO (VIDEO_TITLE, VIDEO_DESCRIPTION, USER_ID) VALUES ('Mein erster Vlog', 'Hier ist mein erster Vlog, den ich jemals gemacht habe!', 1);
    
            INSERT IGNORE INTO USER_SOCIAL (SOCIAL_PLATFORM, SOCIAL_URL, USER_ID) VALUES ('Twitter', 'https://twitter.com/maxmustermann', 1);
        
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
        errorOccurred($connection, $response, __LINE__);
    }

    array_push($response["log"], date('H:i:s') . ": finished database initialisation");
    mysqli_close($connection);
}

// login as guest => READ ONLY
$connection = mysqli_connect($host, $guest_user_username, $guest_user_password, $schema, $port);
if (!$connection) {
    errorOccurred($connection, $response, __LINE__);
} else {
    $response["info"]["database_connection_details"]["database_username"] = $guest_user_username;
    array_push($response["log"], date('H:i:s') . ": logged in as read only guest");
}

/* ---------- */

// REQUEST METHODS | GET, POST, PUT, DELETE STATEMENTS
if ($_SERVER['REQUEST_METHOD'] === 'GET') { // no private data (password is hashed)
    $response["info"]["fetch_method"] = $_SERVER['REQUEST_METHOD'];
    if (isset($_GET["medium"])) {
        $medium = $_GET["medium"];
        if ($medium == "user") {
            $id = mysqli_real_escape_string($connection, $_GET["id"]);
            if ($id == "all") {
                $table_user = "SELECT * FROM USER";
                $response = getUser($connection, $response, $table_user, $id, false);
            } else {
                $userID = intval($id); // check if exists => SELECT COUNT(*) as count FROM USER WHERE UID = $userID;
                $userID_exists = mysqli_prepare($connection, "SELECT COUNT(*) as count FROM USER WHERE USER_ID = ?");
                mysqli_stmt_bind_param($userID_exists, 'i', $userID);
                mysqli_stmt_execute($userID_exists);
                $user_query = mysqli_stmt_get_result($userID_exists);
                $user_rows = mysqli_fetch_all($user_query, MYSQLI_ASSOC);

                if ($user_rows[0]["count"] !== 0) {
                    $table_user = mysqli_prepare($connection, 'SELECT * FROM USER WHERE USER_ID = ?');
                    $response = getUser($connection, $response, $table_user, $userID, true);
                } else {
                    errorOccurred($connection, $response, __LINE__, "user not found");
                }
            }
        } else if ($medium == "video") {
            $id = mysqli_real_escape_string($connection, $_GET["id"]);
            if ($id == "all") {
                // TODO: get all videos from specfied user
                $table_video = "SELECT * FROM VIDEO";
                $response = getUser($connection, $response, $table_video, $id, false);
            } else if ($id == "random") {
                $table_video = "SELECT * FROM VIDEO ORDER BY RAND() LIMIT 1"; // inefficient
                $response = getVideo($connection, $response, $table_video, $id, false);
            } else {
                $videoID = intval($id);
                if ($videoID !== 0) {
                    $table_video = mysqli_prepare($connection, 'SELECT * FROM VIDEO WHERE VIDEO_ID = ?');
                    $response = getVideo($connection, $response, $table_video, $videoID, true);
                } else {
                    errorOccurred($connection, $response, __LINE__, "video not found");
                }
            }
        } else if ($medium == "comments") {
            $videoID = intval(mysqli_real_escape_string($connection, $_GET["id"]));
            $table_comment = "SELECT * FROM VIDEO_COMMENT WHERE VIDEO_ID = ?";
            $response = getComments($response, $table_comment, $videoID);
        }
    }
}

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

            $response["token"] = getJWT($jwt, $publicKey);
        } else if ($action == "comment") {
        } else if ($action == "like") {
        } else if ($action == "dislike") {
        }
    }
}

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
// sends token to client
function sendJWT($payload, $privateKey)
{
    $jwt = JWT::encode($payload, $privateKey, 'RS256');
    return $jwt;
}

// gets and validates token from client
function getJWT($jwt, $publicKey) // TODO: ERROR MESSAGES
{
    try {
        $token = JWT::decode($jwt, new Key($publicKey, 'RS256'));
    } catch (InvalidArgumentException $e) {
        // provided key/key-array is empty or malformed.
    } catch (DomainException $e) {
        // provided algorithm is unsupported OR
        // provided key is invalid OR
        // unknown error thrown in openSSL or libsodium OR
        // libsodium is required but not available.
    } catch (SignatureInvalidException $e) {
        // provided JWT signature verification failed.
    } catch (BeforeValidException $e) {
        // provided JWT is trying to be used before "nbf" claim OR
        // provided JWT is trying to be used before "iat" claim.
    } catch (ExpiredException $e) {
        // provided JWT is trying to be used after "exp" claim.
    } catch (UnexpectedValueException $e) {
        // provided JWT is malformed OR
        // provided JWT is missing an algorithm / using an unsupported algorithm OR
        // provided JWT algorithm does not match provided key OR
        // provided key ID in key/key-array is empty or invalid.
    }

    return json_decode(json_encode($token), true); // (array) casts to assoc array
}

function errorOccurred($connection, $response, $line, $message = null)
{
    $response["error"] = ($message ?? "") . " (error occurred at line " . $line . " [" . mysqli_connect_errno() . ";" .  mysqli_connect_error() . "])";
    // Disconnect
    mysqli_close($connection); // mysqli_kill() => close fast | $thread_id = mysqli_thread_id($con); mysqli_kill($con, $thread_id);
    // Log Response
    echo json_encode($response);
    exit(); // die()
}

function getUser($connection, $response, $table_user, $userID, $prepare)
{
    if ($prepare) {
        // bind values
        mysqli_stmt_bind_param($table_user, 'i', $userID);
        // execute query
        mysqli_stmt_execute($table_user);
        // fetch result
        $user_query = mysqli_stmt_get_result($table_user);
        $user_rows = mysqli_fetch_all($user_query, MYSQLI_ASSOC);
        $user_response = json_encode($user_rows);
    } else {
        // execute query
        $user_query = mysqli_query($connection, $table_user); // SELECT, SHOW, DESCRIBE or EXPLAIN returns mysqli_result object // mysqli_real_query() => doesn't wait for response // mysqli_reap_async_query() => async
        // fetch all
        $user_rows = mysqli_fetch_all($user_query, MYSQLI_ASSOC);
        $user_response = json_encode($user_rows);
    }

    // save as response data
    $response["requested"] = $response["requested"] . "READ user table [$prepare];"; // res;res;res
    array_push($response["data"], $user_response);
    // free result set
    mysqli_free_result($user_query);
    return $response;
}

function getVideo($connection, $response, $table_video, $videoID, $prepare)
{
    if ($prepare) {
        // bind values
        mysqli_stmt_bind_param($table_video, 'i', $videoID);
        // execute query
        mysqli_stmt_execute($table_video);
        // fetch result
        $video_query = mysqli_stmt_get_result($table_video);
        $video_rows = mysqli_fetch_all($video_query, MYSQLI_ASSOC);
        $video_response = json_encode($video_rows);
    } else {
        // excecute query
        $video_query = mysqli_query($connection, $table_video); // SELECT, SHOW, DESCRIBE or EXPLAIN returns mysqli_result object
        // fetch all
        $video_rows = mysqli_fetch_all($video_query, MYSQLI_ASSOC);
        $video_response = json_encode($video_rows);
    }

    // save as response data
    $response["requested"] = $response["requested"] . "READ video table [$prepare];"; // res;res;res
    array_push($response["data"], $video_response);
    // free result set
    mysqli_free_result($video_query);
    return $response;
}

function getComments($response, $table_comment, $videoID)
{
    // bind values
    mysqli_stmt_bind_param($table_comment, 'i', $videoID);
    // execute query
    mysqli_stmt_execute($table_comment);
    // fetch result
    $comment_query = mysqli_stmt_get_result($table_comment);
    $comment_rows = mysqli_fetch_all($comment_query, MYSQLI_ASSOC);
    $comment_response = json_encode($comment_rows);

    // save as response data
    $response["requested"] = $response["requested"] . "READ comment table;"; // res;res;res
    array_push($response["data"], $comment_response);
    // free result set
    mysqli_free_result($comment_query);
    return $response;
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