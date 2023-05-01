/* TABLES */

-- SHOW TABLES LIKE 'table_name'; // CHECK IF EXISTS
-- SELECT COUNT(*) FROM table_name; // CHECK IF EMPTY

CREATE TABLE IF NOT EXISTS VS_USER (
    VS_USER_ID INT AUTO_INCREMENT PRIMARY KEY,
    USER_USERNAME VARCHAR(25) NOT NULL,
    USER_PASSWORD VARCHAR(250) NOT NULL,
    USER_PROFILEPICTURE VARCHAR(100) DEFAULT NULL,
    USER_PROFILEDESCRIPTION VARCHAR(1000) DEFAULT NULL,
    USER_DATETIMECREATED TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    USER_LASTUPDATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT UNIQUE_USERNAME UNIQUE (USER_USERNAME)
);

CREATE TABLE IF NOT EXISTS VS_VIDEO (
    VS_VIDEO_ID INT AUTO_INCREMENT PRIMARY KEY,
    VIDEO_TITLE VARCHAR(25) NOT NULL,
    VIDEO_DESCRIPTION VARCHAR(500) DEFAULT NULL,
    VIDEO_LOCATION VARCHAR(250) NOT NULL,
    VIDEO_SIZE VARCHAR(6) NOT NULL,
    VIDEO_VIEWS INT DEFAULT 0,
    VIDEO_SHARES INT DEFAULT 0,
    VIDEO_DATETIMEPOSTED TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    VIDEO_LASTUPDATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    VS_USER_ID INT NOT NULL,
    FOREIGN KEY (VS_USER_ID) REFERENCES VS_USER(VS_USER_ID)
);

CREATE TABLE IF NOT EXISTS VS_USER_SOCIAL (
    SOCIAL_ID INT AUTO_INCREMENT PRIMARY KEY,
    SOCIAL_PLATFORM VARCHAR(25) NOT NULL,
    SOCIAL_URL VARCHAR(250) NOT NULL,
    VS_USER_ID INT NOT NULL,
    FOREIGN KEY (VS_USER_ID) REFERENCES VS_USER(VS_USER_ID),
    CONSTRAINT UNIQUE_SOCIAL UNIQUE (SOCIAL_PLATFORM, SOCIAL_URL, VS_USER_ID)
);

CREATE TABLE IF NOT EXISTS VS_USER_FOLLOWING (
    FOLLOWING_ID INT AUTO_INCREMENT PRIMARY KEY,
    FOLLOWING_SUBSCRIBER INT NOT NULL,
    FOLLOWING_SUBSCRIBED INT NOT NULL,
    FOREIGN KEY (FOLLOWING_SUBSCRIBER) REFERENCES VS_USER(VS_USER_ID),
    FOREIGN KEY (FOLLOWING_SUBSCRIBED) REFERENCES VS_USER(VS_USER_ID),
    CONSTRAINT UNIQUE_FOLLOWING UNIQUE (FOLLOWING_SUBSCRIBER, FOLLOWING_SUBSCRIBED)
);

CREATE TABLE IF NOT EXISTS VS_VIDEO_FEEDBACK ( /* cannot like own? */
    VIDEO_FEEDBACK_ID INT AUTO_INCREMENT PRIMARY KEY,
    VIDEO_FEEDBACK_TYPE ENUM('positive', 'negative') NOT NULL,
    VS_VIDEO_ID INT NOT NULL,
    VS_USER_ID INT NOT NULL,
    FOREIGN KEY (VS_VIDEO_ID) REFERENCES VS_VIDEO(VS_VIDEO_ID),
    FOREIGN KEY (VS_USER_ID) REFERENCES VS_USER(VS_USER_ID),
    CONSTRAINT UNIQUE_VIDEO_FEEDBACK UNIQUE (VS_VIDEO_ID, VS_USER_ID)
);

CREATE TABLE IF NOT EXISTS VS_COMMENT_FEEDBACK (
    COMMENT_FEEDBACK_ID INT AUTO_INCREMENT PRIMARY KEY,
    COMMENT_FEEDBACK_TYPE ENUM('positive', 'negative') NOT NULL,
    COMMENT_ID INT NOT NULL,
    VS_USER_ID INT NOT NULL,
    FOREIGN KEY (COMMENT_ID) REFERENCES VS_VIDEO_COMMENT(COMMENT_ID),
    FOREIGN KEY (VS_USER_ID) REFERENCES VS_USER(VS_USER_ID),
    CONSTRAINT UNIQUE_COMMENT_FEEDBACK UNIQUE (COMMENT_ID, VS_USER_ID)
);

CREATE TABLE IF NOT EXISTS VS_VIDEO_HASHTAG (
    HASHTAG_ID INT AUTO_INCREMENT PRIMARY KEY,
    HASHTAG_NAME VARCHAR(500) NOT NULL,
    VS_VIDEO_ID INT NOT NULL,
    FOREIGN KEY (VS_VIDEO_ID) REFERENCES VS_VIDEO(VS_VIDEO_ID),
    CONSTRAINT UNIQUE_HASHTAG UNIQUE (VS_VIDEO_ID, HASHTAG_NAME)
);

CREATE TABLE IF NOT EXISTS VS_VIDEO_COMMENT (
    COMMENT_ID INT AUTO_INCREMENT PRIMARY KEY,
    COMMENT_PARENT_ID INT DEFAULT NULL,
    COMMENT_MESSAGE VARCHAR(250) NOT NULL,
    COMMENT_DATETIMEPOSTED TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    COMMENT_LASTUPDATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    VS_VIDEO_ID INT NOT NULL,
    VS_USER_ID INT NOT NULL,
    FOREIGN KEY (VS_VIDEO_ID) REFERENCES VS_VIDEO(VS_VIDEO_ID),
    FOREIGN KEY (VS_USER_ID) REFERENCES VS_USER(VS_USER_ID)
);

/* INSERTS */

-- IMAGES: https://picsum.photos/50/50
-- VIDEOS: https://www.youtube.com/watch?v=dQw4w9WgXcQ
-- SOCIALS: https://github.com/jonasfroeller