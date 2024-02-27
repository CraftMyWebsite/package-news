<?php

namespace CMW\Entity\News;

use CMW\Entity\Users\UserEntity;
use CMW\Controller\Core\CoreController;
use CMW\Manager\Env\EnvManager;

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
    private string $dateUpdated;
    private ?NewsLikesEntity $likes;
    /** @var \CMW\Entity\News\NewsCommentsEntity|\CMW\Entity\News\NewsCommentsEntity[] $comments */
    private ?array $comments;
    /** @var \CMW\Entity\News\NewsTagsEntity[] $tags */
    private array $tags;

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
     * @param string $dateUpdated
     * @param ?\CMW\Entity\News\NewsLikesEntity $likes
     * @param \CMW\Entity\News\NewsCommentsEntity[]|null $comments
     * @param \CMW\Entity\News\NewsTagsEntity[] $tags
     */
    public function __construct(int $newsId, string $title, string $description, bool $commentsStatus, bool $likesStatus, string $content, string $contentNt, string $slug, ?UserEntity $author, int $views, string $imageName, string $dateCreated, string $dateUpdated, ?NewsLikesEntity $likes, ?array $comments, array $tags)
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
        $this->dateUpdated = $dateUpdated;
        $this->likes = $likes;
        $this->comments = $comments;
        $this->tags = $tags;
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
        return htmlspecialchars($this->contentNt);
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getFullUrl(): string
    {
        return EnvManager::getInstance()->getValue('PATH_SUBFOLDE') . $this->slug;
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
     * @return string
     */
    public function getDateCreatedFormatted(): string
    {
        return CoreController::formatDate($this->dateCreated);
    }

    /**
     * @return string
     */
    public function getDateUpdated(): string
    {
        return $this->dateUpdated;
    }

    /**
     * @return string
     */
    public function getDateUpdatedFormatted(): string
    {
        return CoreController::formatDate($this->dateUpdated);
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
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @return string
     */
    public function getImageLink(): string
    {
        return EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . "Public/Uploads/News/" . $this->imageName;
    }

    /**
     * @return string
     */
    public function getFullImageLink(): string
    {
        return EnvManager::getInstance()->getValue("PATH_URL") . $this->getImageLink();
    }

    public function sendComments(): string
    {
        return EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . "news/comments/" . $this->newsId;
    }

    /**
     * @param int $tagId
     * @return bool
     */
    public function hasTag(int $tagId): bool
    {
        foreach ($this->tags as $tag) {
            if ($tag->getId() === $tagId){
                return true;
            }
        }

        return false;
    }

}