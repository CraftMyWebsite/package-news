<?php

namespace CMW\Entity\News;

use CMW\Manager\Package\AbstractEntity;

/**
 * Class: @FrontApiNewsEntity
 * @package News
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/entities
 */
class FrontApiNewsEntity extends AbstractEntity
{
    private int $id;
    private string $title;
    private string $description;
    private string $authorPseudo;
    private string $authorImageLink;
    /**
     * @var NewsTagsEntity[]
     */
    private array $tags;
    private string $dateCreated;
    private string $articleLink;
    private string $imageLink;

    /**
     * @param int $id
     * @param string $title
     * @param string $description
     * @param string $authorPseudo
     * @param string $authorImageLink
     * @param NewsTagsEntity[] $tags
     * @param string $dateCreated
     * @param string $articleLink
     * @param string $imageLink
     */
    public function __construct(int $id, string $title, string $description, string $authorPseudo, string $authorImageLink, array $tags, string $dateCreated, string $articleLink, string $imageLink)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->authorPseudo = $authorPseudo;
        $this->authorImageLink = $authorImageLink;
        $this->tags = $tags;
        $this->dateCreated = $dateCreated;
        $this->articleLink = $articleLink;
        $this->imageLink = $imageLink;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getAuthorPseudo(): string
    {
        return $this->authorPseudo;
    }

    /**
     * @return string
     */
    public function getAuthorImageLink(): string
    {
        return $this->authorImageLink;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @return string
     */
    public function getDateCreated(): string
    {
        return $this->dateCreated;
    }

    /**
     * @return string
     */
    public function getArticleLink(): string
    {
        return $this->articleLink;
    }

    /**
     * @return string
     */
    public function getImageLink(): string
    {
        return $this->imageLink;
    }
}
