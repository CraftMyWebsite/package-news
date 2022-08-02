<?php

namespace CMW\Entity\News;

use CMW\Entity\Users\UserEntity;

class NewsEntity
{
    private int $newsId;
    private string $title;
    private string $description;
    private bool $commentsStatus;
    private bool $likesStatus;
    private string $content;
    private string $slug;
    private ?UserEntity $author;
    private string $imageName;
    private string $imageLink;
    private string $dateCreated;
    private ?NewsLikesEntity $likes;

    /**
     * @param int $newsId
     * @param string $title
     * @param string $description
     * @param bool $commentsStatus
     * @param bool $likesStatus
     * @param string $content
     * @param string $slug
     * @param ?\CMW\Entity\Users\UserEntity $author
     * @param string $imageName
     * @param string $dateCreated
     * @param ?\CMW\Entity\News\NewsLikesEntity $likes
     */
    public function __construct(int $newsId, string $title, string $description, bool $commentsStatus, bool $likesStatus, string $content, string $slug, ?UserEntity $author, string $imageName, string $dateCreated, ?NewsLikesEntity $likes)
    {
        $this->newsId = $newsId;
        $this->title = $title;
        $this->description = $description;
        $this->commentsStatus = $commentsStatus;
        $this->likesStatus = $likesStatus;
        $this->content = $content;
        $this->slug = $slug;
        $this->author = $author;
        $this->imageName = $imageName;
        $this->dateCreated = $dateCreated;
        $this->likes = $likes;
    }

    /**
     * @return int
     */
    public function getNewsId(): int
    {
        return $this->newsId;
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
     * @return bool
     */
    public function isCommentsStatus(): bool
    {
        return $this->commentsStatus;
    }

    /**
     * @return bool
     */
    public function isLikesStatus(): bool
    {
        return $this->likesStatus;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return ?\CMW\Entity\Users\UserEntity
     */
    public function getAuthor(): ?UserEntity
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getImageName(): string
    {
        return $this->imageName;
    }

    /**
     * @return string
     */
    public function getDateCreated(): string
    {
        return $this->dateCreated;
    }

    /**
     * @return ?\CMW\Entity\News\NewsLikesEntity
     */
    public function getLikes(): ?NewsLikesEntity
    {
        return $this->likes;
    }

    /**
     * @return string
     */
    public function getImageLink(): string
    {
        return "/public/uploads/news/" . $this->imageName;
    }

}