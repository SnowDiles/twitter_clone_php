CREATE TABLE Themes (
    theme_id INT PRIMARY KEY AUTO_INCREMENT,
    theme_name VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE Media (
    media_id INT PRIMARY KEY AUTO_INCREMENT,
    media_url VARCHAR(255) UNIQUE NOT NULL,
    short_url VARCHAR(8) UNIQUE NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    password_hash CHAR(40) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    bio TEXT,
    profile_picture_id INT,
    banner_id INT,
    theme_id INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_picture_id) REFERENCES Media(media_id),
    FOREIGN KEY (banner_id) REFERENCES Media(media_id),
    FOREIGN KEY (theme_id) REFERENCES Themes(theme_id)
);

CREATE TABLE Posts (
    post_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    content VARCHAR(140) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reply_to INT,
    repost_of INT,
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (reply_to) REFERENCES Posts(post_id),
    FOREIGN KEY (repost_of) REFERENCES Posts(post_id)
);

CREATE TABLE Hashtags (
    hashtag_id INT PRIMARY KEY AUTO_INCREMENT,
    tag VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE PostHashtag (
    post_id INT NOT NULL,
    hashtag_id INT NOT NULL,
    PRIMARY KEY (post_id, hashtag_id),
    FOREIGN KEY (post_id) REFERENCES Posts(post_id),
    FOREIGN KEY (hashtag_id) REFERENCES Hashtags(hashtag_id)
);

CREATE TABLE Mentions (
    mention_id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (post_id) REFERENCES Posts(post_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE Follows (
    follower_id INT NOT NULL,
    following_id INT NOT NULL,
    followed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (follower_id, following_id),
    FOREIGN KEY (follower_id) REFERENCES Users(user_id),
    FOREIGN KEY (following_id) REFERENCES Users(user_id)
);

CREATE TABLE Messages (
    message_id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    content TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES Users(user_id),
    FOREIGN KEY (receiver_id) REFERENCES Users(user_id)
);

CREATE TABLE PostMedia (
    post_id INT NOT NULL,
    media_id INT NOT NULL,
    FOREIGN KEY (post_id) REFERENCES Posts(post_id),
    FOREIGN KEY (media_id) REFERENCES Media(media_id)
);

CREATE TABLE MessageMedia (
    message_id INT NOT NULL,
    media_id INT NOT NULL,
    FOREIGN KEY (message_id) REFERENCES Messages(message_id),
    FOREIGN KEY (media_id) REFERENCES Media(media_id)
);
