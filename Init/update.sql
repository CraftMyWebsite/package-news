-------- Version: 1.6.0 (DONE) --------

-- Add scheduled date column
ALTER TABLE `cmw_news`
    ADD COLUMN `news_date_scheduled` TIMESTAMP NULL DEFAULT NULL AFTER `news_status`;

-- Slug prefix
INSERT INTO `cmw_core_options` (`option_name`, `option_value`)
VALUES ('news_slug_prefix', 'news')
ON DUPLICATE KEY UPDATE `option_value` = 'news';

----------------------------------------------------------------------------------------