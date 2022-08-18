<?php

namespace CMW\Entity\News;

use CMW\Entity\Users\UserEntity;
use CMW\Model\News\NewsLikesModel;
use CMW\Model\Users\UsersModel;

class NewsLikesEntity
{

    private ?int $likeId;
    private ?UserEntity $user;
    private ?string $date;

    //Utils
    private int $newsId;
    private int $total;
    private string $sendLike;
    private bool $isLike;


    /**
     * @param int|null $likeId
     * @param \CMW\Entity\Users\UserEntity|null $user
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
     * @return \CMW\Entity\Users\UserEntity
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
        return $this->date;
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
        return getenv("PATH_SUBFOLDER") . "news/like/" . $this->newsId;
    }

    /**
     * @return bool
     */
    public function userCanLike(): bool
    {
        return !(new NewsLikesModel())->userCanLike($this->newsId, (new UsersModel())->getCurrentUser()->getId());
    }

}