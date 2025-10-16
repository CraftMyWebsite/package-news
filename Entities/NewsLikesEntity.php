<?php

namespace CMW\Entity\News;

use CMW\Controller\Users\UsersSessionsController;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Package\AbstractEntity;
use CMW\Model\News\NewsLikesModel;
use CMW\Model\News\NewsSettingsModel;
use CMW\Utils\Date;

class NewsLikesEntity extends AbstractEntity
{
    private ?int $likeId;
    private ?UserEntity $user;
    private ?string $date;

    // Utils
    private int $newsId;
    private int $total;

    /**
     * @param int|null $likeId
     * @param UserEntity|null $user
     * @param string|null $date
     * @param int $total
     * @param int $newsId
     */
    public function __construct(?int $likeId, ?UserEntity $user, ?string $date, int $total, int $newsId)
    {
        $this->likeId = $likeId;
        $this->user = $user;
        $this->date = $date;
        $this->total = $total;
        $this->newsId = $newsId;
    }

    /**
     * @return int|null
     */
    public function getLikeId(): ?int
    {
        return $this->likeId;
    }

    /**
     * @return UserEntity
     */
    public function getUser(): UserEntity
    {
        return $this->user;
    }

    /**
     * @return string|null
     */
    public function getDate(): ?string
    {
        return Date::formatDate($this->date);
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @return string
     */
    public function getSendLike(): string
    {
        $slugPrefix = NewsSettingsModel::getNewsSlugPrefix();
        return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'like/' . $slugPrefix . '/' . $this->newsId;
    }

    /**
     * @return bool
     */
    public function userCanLike(): bool
    {
        return !NewsLikesModel::getInstance()->userCanLike(
            $this->newsId, UsersSessionsController::getInstance()->getCurrentUser()?->getId(),
        );
    }
}
