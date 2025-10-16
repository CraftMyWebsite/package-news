<?php

namespace CMW\Controller\News\Public;

use CMW\Controller\Users\UsersController;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\News\NewsCommentsLikesModel;
use CMW\Model\News\NewsCommentsModel;
use CMW\Model\News\NewsLikesModel;
use CMW\Model\News\NewsModel;
use CMW\Model\News\NewsSettingsModel;
use CMW\Model\News\NewsTagsModel;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;
use function is_null;

/**
 * Class: @NewsPublicController
 * @package News
 * @author Teyir
 * @version 0.0.1
 */
class NewsPublicController extends AbstractController
{
    #[Link('/news', Link::GET)]
    #[Link('/blog', Link::GET, weight: 2)]
    #[Link('/actu', Link::GET, weight: 2)]
    #[Link('/articles', Link::GET, weight: 2)]
    #[Link('/actualites', Link::GET, weight: 2)]
    private function publicListNews(): void
    {
        $this->handleSlugPrefix();
        
        $newsList = NewsModel::getInstance()->getNews(true);

        View::createPublicView('News', 'list')
            ->addScriptBefore('Admin/Resources/Vendors/Prismjs/prism.js')
            ->addVariableList(['newsList' => $newsList])
            ->view();
    }

    #[Link('/news/:tagSlug/:articleSlug', Link::GET, ['tagSlug' => '.*?', 'articleSlug' => '.*?'])]
    #[Link('/blog/:tagSlug/:articleSlug', Link::GET, ['tagSlug' => '.*?', 'articleSlug' => '.*?'], weight: 2)]
    #[Link('/actu/:tagSlug/:articleSlug', Link::GET, ['tagSlug' => '.*?', 'articleSlug' => '.*?'], weight: 2)]
    #[Link('/articles/:tagSlug/:articleSlug', Link::GET, ['tagSlug' => '.*?', 'articleSlug' => '.*?'], weight: 2)]
    #[Link('/actualites/:tagSlug/:articleSlug', Link::GET, ['tagSlug' => '.*?', 'articleSlug' => '.*?'], weight: 2)]
    private function publicNewsTagIndividual(string $tagSlug, string $articleSlug): void
    {
        $this->handleSlugPrefix();

        $news = NewsModel::getInstance()->getNewsBySlug($articleSlug);
        $tag = NewsTagsModel::getInstance()->isTagExistByName($tagSlug);

        if (is_null($news) || !$tag) {
            Redirect::errorPage(404);
        }

        if (!$news->isPublished() && !UsersController::isAdminLogged()) {
            Redirect::errorPage(404);
        }

        NewsModel::getInstance()->incrementViews($news->getNewsId());

        View::createPublicView('News', 'individual')
            ->addScriptBefore('Admin/Resources/Vendors/Prismjs/prism.js')
            ->addVariableList(['news' => $news])
            ->view();
    }

    #[Link('/news/:slug', Link::GET, ['slug' => '.*?'])]
    #[Link('/blog/:slug', Link::GET, ['slug' => '.*?'], weight: 2)]
    #[Link('/actu/:slug', Link::GET, ['slug' => '.*?'], weight: 2)]
    #[Link('/articles/:slug', Link::GET, ['slug' => '.*?'], weight: 2)]
    #[Link('/actualites/:slug', Link::GET, ['slug' => '.*?'], weight: 2)]
    private function publicIndividualNews(string $slug): void
    {
        $this->handleSlugPrefix();

        $isTag = NewsTagsModel::getInstance()->isTagExistByName($slug);

        if ($isTag) {
            $this->publicListNewsForTag($slug);
            return;
        }

        $news = NewsModel::getInstance()->getNewsBySlug($slug);

        if (is_null($news) || (!$news->isPublished() && !UsersController::isAdminLogged())) {
            Redirect::errorPage(404);
        }

        NewsModel::getInstance()->incrementViews($news->getNewsId());

        View::createPublicView('News', 'individual')
            ->addScriptBefore('Admin/Resources/Vendors/Prismjs/prism.js')
            ->addVariableList(['news' => $news])
            ->view();
    }

    public function publicListNewsForTag(string $tagSlug): void
    {
        $tag = NewsTagsModel::getInstance()->getTagByName($tagSlug);

        if (!$tag) {
            Redirect::redirectToHome();
        }

        $newsList = NewsTagsModel::getInstance()->getNewsForTagById($tag->getId(), true);
        $newsModel = NewsModel::getInstance();

        View::createPublicView('News', 'list')
            ->addScriptBefore('Admin/Resources/Vendors/Prismjs/prism.js')
            ->addVariableList(['newsList' => $newsList, 'newsModel' => $newsModel])
            ->view();
    }

    #[NoReturn]
    #[Link('/like/news/comments/:id', Link::GET, ['id' => '[0-9]+'])]
    #[Link('/like/blog/comments/:id', Link::GET, ['id' => '[0-9]+'], weight: 2)]
    #[Link('/like/actu/comments/:id', Link::GET, ['id' => '[0-9]+'], weight: 2)]
    #[Link('/like/articles/comments/:id', Link::GET, ['id' => '[0-9]+'], weight: 2)]
    #[Link('/like/actualites/comments/:id', Link::GET, ['id' => '[0-9]+'], weight: 2)]
    private function likeCommentsNews(int $commentsId): void
    {
        $this->handleSlugPrefix();

        $user = UsersSessionsController::getInstance()->getCurrentUser();

        if (is_null($user)) {
            Redirect::redirect('login');
        }

        // We check if the player has already like this comments, and we store the like
        if (newsCommentsLikesModel::getInstance()->userCanLike($commentsId, $user?->getId())) {
            newsCommentsLikesModel::getInstance()->storeLike($commentsId, $user?->getId());
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/like/news/:id', Link::GET, ['id' => '[0-9]+'])]
    #[Link('/like/blog/:id', Link::GET, ['id' => '[0-9]+'], weight: 2)]
    #[Link('/like/actu/:id', Link::GET, ['id' => '[0-9]+'], weight: 2)]
    #[Link('/like/articles/:id', Link::GET, ['id' => '[0-9]+'], weight: 2)]
    #[Link('/like/actualites/:id', Link::GET, ['id' => '[0-9]+'], weight: 2)]
    private function likeNews(int $id): void
    {
        $this->handleSlugPrefix();

        $user = UsersSessionsController::getInstance()->getCurrentUser();

        if (is_null($user)) {
            Redirect::redirect('login');
        }

        $news = NewsModel::getInstance()->getNewsById($id);

        if (is_null($news)) {
            Redirect::errorPage(404);
        }

        // First check if the news is likeable
        if (!$news->isLikesStatus()) {
            Redirect::redirect('news');
        }

        if (newsLikesModel::getInstance()->userCanLike($id, $user?->getId())) {
            newsLikesModel::getInstance()->storeLike($id, $user?->getId());
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/news/comments/:id', Link::POST, ['id' => '[0-9]+'])]
    #[Link('/blog/comments/:id', Link::POST, ['id' => '[0-9]+'], weight: 2)]
    #[Link('/actu/comments/:id', Link::POST, ['id' => '[0-9]+'], weight: 2)]
    #[Link('/articles/comments/:id', Link::POST, ['id' => '[0-9]+'], weight: 2)]
    #[Link('/actualites/comments/:id', Link::POST, ['id' => '[0-9]+'], weight: 2)]
    private function commentsNews(int $newsId): void
    {
        $this->handleSlugPrefix();

        $user = UsersSessionsController::getInstance()->getCurrentUser();

        if (is_null($user)) {
            Redirect::redirect('login');
        }

        $content = strip_tags(htmlentities(filter_input(INPUT_POST, 'comments')));

        if (NewsCommentsModel::getInstance()->userCanComment($newsId, $user?->getId())) {
            newsCommentsModel::getInstance()->storeComments($newsId, $user?->getId(), $content);
        }

        Redirect::redirectPreviousRoute();
    }

    /**
     * <p>Handle the slug prefix redirection if needed.</p>
     * @return void
     */
    private function handleSlugPrefix(): void
    {
        $slugPrefix = NewsSettingsModel::getInstance()->getNewsSlugPrefix();
        if (empty($slugPrefix)) {
            return;
        }

        $currentPath = trim($_SERVER['REQUEST_URI'], '/');
        $expectedPrefix = trim($slugPrefix, '/');

        if (str_starts_with($currentPath, $expectedPrefix)) {
            return;
        }

        Redirect::errorPage(404);
    }
}
