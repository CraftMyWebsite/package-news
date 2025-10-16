<?php

namespace CMW\Entity\News;

use CMW\Controller\Users\UsersSessionsController;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Package\AbstractEntity;
use CMW\Model\News\NewsCommentsLikesModel;
use CMW\Model\News\NewsCommentsModel;
use CMW\Model\News\NewsSettingsModel;
use CMW\Utils\Date;

class NewsCommentsEntity extends AbstractEntity
{
    private ?int $commentsId;
    private int $newsId;
    private ?UserEntity $user;
    private ?string $content;
    private ?string $date;
    private ?NewsCommentsLikesEntity $likes;

    /**
     * @param int|null $commentsId
     * @param int $newsId
     * @param UserEntity|null $user
     * @param string|null $content
     * @param string|null $date
     * @param NewsCommentsLikesEntity|null $likes
     */
    public function __construct(?int $commentsId, int $newsId, ?UserEntity $user, ?string $content, ?string $date, ?NewsCommentsLikesEntity $likes)
    {
        $this->commentsId = $commentsId;
        $this->newsId = $newsId;
        $this->user = $user;
        $this->content = $content;
        $this->date = $date;
        $this->likes = $likes;
    }

    /**
     * @return int|null
     */
    public function getCommentsId(): ?int
    {
        return $this->commentsId;
    }

    /**
     * @return int
     */
    public function getNewsId(): int
    {
        return $this->newsId;
    }

    /**
     * @return UserEntity|null
     */
    public function getUser(): ?UserEntity
    {
        return $this->user;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @return string|null
     */
    public function getDate(): ?string
    {
        return Date::formatDate($this->date);
    }

    /**
     * @return ?NewsCommentsLikesEntity
     */
    public function getLikes(): ?NewsCommentsLikesEntity
    {
        return $this->likes;
    }

    /**
     * @return string
     */
    public function getSendLike(): string
    {
        $slugPrefix = NewsSettingsModel::getNewsSlugPrefix();
        return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'like/' . $slugPrefix . '/comments/' . $this->commentsId;
    }

    /**
     * @return bool
     */
    public function userCanLike(): bool
    {
        return !NewsCommentsLikesModel::getInstance()->userCanLike(
            $this->commentsId,
            UsersSessionsController::getInstance()->getCurrentUser()?->getId(),
        );
    }

    /**
     * @return bool
     */
    public function userCanComment(): bool
    {
        return NewsCommentsModel::getInstance()->userCanComment(
            $this->newsId,
            UsersSessionsController::getInstance()->getCurrentUser()?->getId()
        );
    }
}
