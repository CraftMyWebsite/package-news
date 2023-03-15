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
    private string $contentNt;
    private string $slug;
    private ?UserEntity $author;
    private int $views;
    private string $imageName;
    private string $imageLink;
    private string $dateCreated;
    private ?NewsLikesEntity $likes;
    /** @var \CMW\Entity\News\NewsCommentsEntity|\CMW\Entity\News\NewsCommentsEntity[] $comments */
    private ?array $comments;

    /**
     * @param int $newsId
     * @param string $title
     * @param string $description
     * @param bool $commentsStatus
     * @param bool $likesStatus
     * @param string $content
     * @param string $contentNt
     * @param string $slug
     * @param ?\CMW\Entity\Users\UserEntity $author
     * @param int $views
     * @param string $imageName
     * @param string $dateCreated
     * @param ?\CMW\Entity\News\NewsLikesEntity $likes
     * @param \CMW\Entity\News\NewsCommentsEntity[]|null $comments
     */
    public function __construct(int $newsId, string $title, string $description, bool $commentsStatus, bool $likesStatus, string $content, string $contentNt, string $slug, ?UserEntity $author, int $views, string $imageName, string $dateCreated, ?NewsLikesEntity $likes, ?array $comments)
    {
        $this->newsId = $newsId;
        $this->title = $title;
        $this->description = $description;
        $this->commentsStatus = $commentsStatus;
        $this->likesStatus = $likesStatus;
        $this->content = $content;
        $this->contentNt = $contentNt;
        $this->slug = $slug;
        $this->author = $author;
        $this->views = $views;
        $this->imageName = $imageName;
        $this->dateCreated = $dateCreated;
        $this->likes = $likes;
        $this->comments = $comments;
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
    public function getContentNotTranslate(): string
    {
        return $this->contentNt;
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
     * @return int
     */
    public function getViews(): int
    {
        return $this->views;
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
     * @return \CMW\Entity\News\NewsCommentsEntity[]|null
     */
    public function getComments(): ?array
    {
        return $this->comments;
    }

    /**
     * @return string
     */
    public function getImageLink(): string
    {
        return getenv("PATH_SUBFOLDER") . "public/uploads/news/" . $this->imageName;
    }

    public function sendComments(): string
    {
        return getenv("PATH_SUBFOLDER") . "news/comments/" . $this->newsId;
    }

}