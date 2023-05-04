<?php

namespace CMW\Entity\News;

use CMW\Controller\Core\CoreController;
use CMW\Entity\Users\UserEntity;

class NewsBannedPlayersEntity
{
    private int $id;
    private UserEntity $player;
    private ?UserEntity $author;
    private string $date;

    /**
     * @param int $id
     * @param \CMW\Entity\Users\UserEntity $player
     * @param \CMW\Entity\Users\UserEntity|null $author
     * @param string $date
     */
    public function __construct(int $id, UserEntity $player, ?UserEntity $author, string $date)
    {
        $this->id = $id;
        $this->player = $player;
        $this->author = $author;
        $this->date = $date;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \CMW\Entity\Users\UserEntity
     */
    public function getPlayer(): UserEntity
    {
        return $this->player;
    }

    /**
     * @return \CMW\Entity\Users\UserEntity|null
     */
    public function getAuthor(): ?UserEntity
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return CoreController::formatDate($this->date);
    }

}