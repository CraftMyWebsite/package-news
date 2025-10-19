DROP TABLE IF EXISTS `cmw_news_tags_list`;
DROP TABLE IF EXISTS `cmw_news_tags`;
DROP TABLE IF EXISTS `cmw_news_banned_players`;
DROP TABLE IF EXISTS `cmw_news_comments_likes`;
DROP TABLE IF EXISTS `cmw_news_comments`;
DROP TABLE IF EXISTS `cmw_news_likes`;
DROP TABLE IF EXISTS `cmw_news`;


DELETE
FROM cmw_core_options
WHERE option_name IN (
                      'news_cron_token',
                      'news_enable_scheduled_publishing',
                      'news_slug_prefix'
    )