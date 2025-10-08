<?php

namespace CMW\Controller\News\Public;

use CMW\Controller\Users\UsersSessionsController;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\News\NewsCommentsLikesModel;
use CMW\Model\News\NewsCommentsModel;
use CMW\Model\News\NewsLikesModel;
use CMW\Model\News\NewsModel;
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
    private function publicListNews(): void
    {
        $newsList = NewsModel::getInstance()->getNews(true);

        View::createPublicView('News', 'list')
            ->addScriptBefore('Admin/Resources/Vendors/Prismjs/prism.js')
            ->addVariableList(['newsList' => $newsList])
            ->view();
    }

    #[Link('/news/:tagSlug/:articleSlug', Link::GET, ['tagSlug' => '.*?', 'articleSlug' => '.*?'])]
    private function publicNewsTagIndividual(string $tagSlug, string $articleSlug): void
    {
        $news = NewsModel::getInstance()->getNewsBySlug($articleSlug);
        $tag = NewsTagsModel::getInstance()->isTagExistByName($tagSlug);

        if (is_null($news) || !$tag) {
            Redirect::errorPage(404);
        }

        if (!$news->isPublished()) {
            Redirect::errorPage(404);
        }

        NewsModel::getInstance()->incrementViews($news->getNewsId());

        View::createPublicView('News', 'individual')
            ->addScriptBefore('Admin/Resources/Vendors/Prismjs/prism.js')
            ->addVariableList(['news' => $news])
            ->view();
    }

    #[Link('/news/:slug', Link::GET, ['slug' => '.*?'])]
    private function publicIndividualNews(string $slug): void
    {
        $isTag = NewsTagsModel::getInstance()->isTagExistByName($slug);

        if ($isTag) {
            $this->publicListNewsForTag($slug);
            return;
        }

        $news = NewsModel::getInstance()->getNewsBySlug($slug);

        if (is_null($news) || !$news->isPublished()) {
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

        $newsList = NewsTagsModel::getInstance()->getNewsForTagById($tag->getId());
        $newsModel = NewsModel::getInstance();

        View::createPublicView('News', 'list')
            ->addScriptBefore('Admin/Resources/Vendors/Prismjs/prism.js')
            ->addVariableList(['newsList' => $newsList, 'newsModel' => $newsModel])
            ->view();
    }

    #[NoReturn]
    #[Link('/like/news/comments/:id', Link::GET, ['id' => '[0-9]+'])]
    private function likeCommentsNews(int $commentsId): void
    {
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
    private function likeNews(int $id): void
    {
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
    private function commentsNews(int $newsId): void
    {
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
}
