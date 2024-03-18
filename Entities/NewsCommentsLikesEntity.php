<?php

namespace CMW\Entity\News;

use CMW\Entity\Users\UserEntity;
use CMW\Controller\Core\CoreController;
use CMW\Manager\Env\EnvManager;

class NewsCommentsLikesEntity
{
    private ?int $likesId;
    private ?int $commentsId;
    private ?UserEntity $user;
    private ?string $date;

    //UTILS
    private int $total;
    private string $sendLike;
    private bool $isLike;

    /**
     * @param int|null $likesId
     * @param int|null $commentsId
     * @param \CMW\Entity\Users\UserEntity|null $user
     * @param string|null $date
     * @param int $total
     */
    public function __construct(?int $likesId, ?int $commentsId, ?UserEntity $user, ?string $date, int $total)
    {
        $this->likesId = $likesId;
        $this->commentsId = $commentsId;
        $this->user = $user;
        $this->date = $date;
        $this->total = $total;
    }

    /**
     * @return int|null
     */
    public function getLikesId(): ?int
    {
        return $this->likesId;
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
    public function getDate(): ?string
    {
        return CoreController::formatDate($this->date);
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
        return EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . "like/news/comments/" . $this->commentsId;
    }


}