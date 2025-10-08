ALTER TABLE `cmw_news`
    ADD COLUMN `news_date_scheduled` TIMESTAMP NULL DEFAULT NULL AFTER `news_status`;