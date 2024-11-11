<?php

namespace CMW\Entity\News;

use CMW\Entity\Users\UserEntity;
use CMW\Manager\Package\AbstractEntity;
use CMW\Utils\Date;

class NewsBannedPlayersEntity extends AbstractEntity
{
    private int $id;
    private UserEntity $player;
    private ?UserEntity $author;
    private string $date;

    /**
     * @param int $id
     * @param UserEntity $player
     * @param UserEntity|null $author
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
     * @return UserEntity
     */
    public function getPlayer(): UserEntity
    {
        return $this->player;
    }

    /**
     * @return UserEntity|null
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
        return Date::formatDate($this->date);
    }
}
