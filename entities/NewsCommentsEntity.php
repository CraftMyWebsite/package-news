<?php

namespace CMW\Entity\News;

use CMW\Entity\Users\UserEntity;
use CMW\Model\News\NewsCommentsLikesModel;
use CMW\Model\Users\UsersModel;

class NewsCommentsEntity
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
     * @param \CMW\Entity\Users\UserEntity|null $user
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
     * @return \CMW\Entity\Users\UserEntity|null
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
        return $this->date;
    }


    /**
     * @return ?\CMW\Entity\News\NewsCommentsLikesEntity
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
        return getenv("PATH_SUBFOLDER") . "news/like/comment/" . $this->commentsId;
    }


    /**
     * @return bool
     */
    public function userCanLike(): bool
    {
        return !(new NewsCommentsLikesModel())->userCanLike($this->commentsId, (new UsersModel())->getCurrentUser()->getId());
    }

}
