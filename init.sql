CREATE TABLE IF NOT EXISTS `cmw_news`
(
    `news_id`           INT          NOT NULL AUTO_INCREMENT,
    `news_title`        VARCHAR(255) NOT NULL,
    `news_desc`         VARCHAR(255) NOT NULL,
    `news_comments_status`     BOOLEAN      NOT NULL DEFAULT TRUE,
    `news_likes_status`        BOOLEAN      NOT NULL DEFAULT TRUE,
    `news_content`      LONGTEXT     NOT NULL,
    `news_slug`         VARCHAR(255) NOT NULL,
    `news_author`    INT          NULL,
    `news_image_name`   VARCHAR(255) NOT NULL,
    `news_date_created` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`news_id`),
    INDEX (`news_author`),
    UNIQUE (`news_title`),
    UNIQUE (`news_slug`)
) ENGINE = InnoDB
  CHARSET utf8mb4;

ALTER TABLE `cmw_news`
    ADD CONSTRAINT `cmw_news_ibfk_1` FOREIGN KEY (`news_author`) REFERENCES `cmw_users` (`user_id`) ON DELETE SET NULL ON UPDATE SET NULL;
COMMIT;