<?php

namespace CMW\Entity\News;

use CMW\Entity\Users\UserEntity;
use CMW\Model\Users\UsersModel;

class NewsCommentsEntity
{

    private ?int $commentsId;
    private int $newsId;
    private ?UserEntity $user;
    private ?string $content;
    private ?string $date;

    /**
     * @param int|null $commentsId
     * @param int $newsId
     * @param \CMW\Entity\Users\UserEntity|null $user
     * @param string|null $content
     * @param string|null $date
     */
    public function __construct(?int $commentsId, int $newsId, ?UserEntity $user, ?string $content, ?string $date)
    {
        $this->commentsId = $commentsId;
        $this->newsId = $newsId;
        $this->user = $user;
        $this->content = $content;
        $this->date = $date;
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




}
