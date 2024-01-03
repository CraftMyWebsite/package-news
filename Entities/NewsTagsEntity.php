<?php

namespace CMW\Entity\News;

class NewsTagsEntity
{
    private int $id;
    private string $name;
    private ?string $icon;
    private ?string $color;

    /**
     * @param int $id
     * @param String $name
     * @param String|null $icon
     * @param String|null $color
     */
    public function __construct(int $id, string $name, ?string $icon, ?string $color)
    {
        $this->id = $id;
        $this->name = $name;
        $this->icon = $icon;
        $this->color = $color;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return String
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return String|null
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @return String|null
     */
    public function getColor(): ?string
    {
        return $this->color;
    }
}