CREATE TABLE IF NOT EXISTS `cmw_news`
(
    `news_id`              INT          NOT NULL AUTO_INCREMENT,
    `news_title`           VARCHAR(255) NOT NULL,
    `news_desc`            VARCHAR(255) NOT NULL,
    `news_comments_status` BOOLEAN      NOT NULL DEFAULT TRUE,
    `news_likes_status`    BOOLEAN      NOT NULL DEFAULT TRUE,
    `news_content`         LONGTEXT     NOT NULL,
    `news_slug`            VARCHAR(255) NOT NULL,
    `news_author`          INT          NULL,
    `news_views`           INT          NOT NULL DEFAULT 0,
    `news_image_name`      VARCHAR(255) NOT NULL,
    `news_date_created`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `news_date_updated`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`news_id`),
    INDEX (`news_author`),
    UNIQUE (`news_title`),
    UNIQUE (`news_slug`),
    CONSTRAINT `cmw_news_ibfk_1` FOREIGN KEY (`news_author`)
        REFERENCES `cmw_users` (`user_id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE = InnoDB
  CHARSET utf8mb4;


CREATE TABLE IF NOT EXISTS `cmw_news_likes`
(
    `news_like_id`      INT(11)   NOT NULL AUTO_INCREMENT,
    `news_like_news_id` INT(11)   NOT NULL,
    `news_like_user_id` INT(11)   NOT NULL,
    `news_like_date`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`news_like_id`),
    KEY `news_like_news_id` (`news_like_news_id`),
    KEY `news_like_user_id` (`news_like_user_id`),
    CONSTRAINT `cmw_news_likes_ibfk_1` FOREIGN KEY (`news_like_user_id`)
        REFERENCES `cmw_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `cmw_news_likes_ibfk_2` FOREIGN KEY (`news_like_news_id`)
        REFERENCES `cmw_news` (`news_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cmw_news_comments`
(
    `news_comments_id`      INT(11)   NOT NULL AUTO_INCREMENT,
    `news_comments_news_id` INT(11)   NOT NULL,
    `news_comments_user_id` INT(11)   NOT NULL,
    `news_comments_content` TEXT      NOT NULL,
    `news_comments_date`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`news_comments_id`),
    KEY `news_comments_news_id` (`news_comments_news_id`),
    KEY `news_comments_user_id` (`news_comments_user_id`),
    CONSTRAINT `cmw_news_comments_ibfk_1` FOREIGN KEY (`news_comments_news_id`)
        REFERENCES `cmw_news` (`news_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `cmw_news_comments_ibfk_2` FOREIGN KEY (`news_comments_user_id`)
        REFERENCES `cmw_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `cmw_news_comments_likes`
(
    `news_comments_likes_id`          INT(11)   NOT NULL AUTO_INCREMENT,
    `news_comments_likes_comments_id` INT(11)   NOT NULL,
    `news_comments_likes_user_id`     INT(11)   NOT NULL,
    `news_comments_likes_date`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`news_comments_likes_id`),
    KEY `news_comments_likes_comments_id` (`news_comments_likes_comments_id`),
    KEY `news_comments_likes_user_id` (`news_comments_likes_user_id`),
    CONSTRAINT `news_cmw_news_comments_likes_ibfk_1` FOREIGN KEY (`news_comments_likes_user_id`)
        REFERENCES `cmw_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `news_cmw_news_comments_likes_ibfk_2` FOREIGN KEY (`news_comments_likes_comments_id`)
        REFERENCES `cmw_news_comments` (`news_comments_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cmw_news_banned_players`
(
    `news_banned_players_id`        INT       NOT NULL AUTO_INCREMENT,
    `news_banned_players_player_id` INT       NOT NULL,
    `news_banned_players_author_id` INT       NULL,
    `news_banned_players_date`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`news_banned_players_id`),
    INDEX (`news_banned_players_author_id`),
    UNIQUE (`news_banned_players_player_id`),
    FOREIGN KEY (`news_banned_players_player_id`)
        REFERENCES `cmw_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`news_banned_players_author_id`)
        REFERENCES `cmw_users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cmw_news_tags`
(
    `news_tags_id`    INT         NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `news_tags_name`  VARCHAR(50) NOT NULL UNIQUE,
    `news_tags_icon`  VARCHAR(50) NULL DEFAULT NULL,
    `news_tags_color` VARCHAR(7)  NULL DEFAULT NULL
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cmw_news_tags_list`
(
    `news_id`      INT NOT NULL,
    `news_tags_id` INT NOT NULL,
    KEY `news_id` (`news_id`),
    KEY `news_tags_id` (`news_tags_id`),
    CONSTRAINT `fk_cmw_news_tags_list_news_id` FOREIGN KEY (`news_id`)
        REFERENCES `cmw_news` (`news_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_cmw_news_tags_list_news_tags_id` FOREIGN KEY (`news_tags_id`)
        REFERENCES `cmw_news_tags` (`news_tags_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;