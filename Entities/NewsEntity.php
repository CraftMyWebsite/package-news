<?php

namespace CMW\Entity\News;

use CMW\Entity\Users\UserEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Package\AbstractEntity;
use CMW\Manager\Package\EntityType;
use CMW\Utils\Date;
use function htmlspecialchars;

class NewsEntity extends AbstractEntity
{
    private int $newsId;
    private string $title;
    private string $description;
    private bool $commentsStatus;
    private bool $likesStatus;
    private bool $status;
    private ?string $dateScheduled;
    private string $content;
    private string $contentNt;
    private string $slug;
    private ?UserEntity $author;
    private int $views;
    private string $imageName;
    private string $dateCreated;
    private string $dateUpdated;
    private ?NewsLikesEntity $likes;
    /** @var NewsCommentsEntity|NewsCommentsEntity[] $comments */
    private ?array $comments;
    /** @var NewsTagsEntity[] $tags */
    private array $tags;

    /**
     * @param int $newsId
     * @param string $title
     * @param string $description
     * @param bool $commentsStatus
     * @param bool $likesStatus
     * @param bool $status
     * @param string|null $dateScheduled
     * @param string $content
     * @param string $contentNt
     * @param string $slug
     * @param ?UserEntity $author
     * @param int $views
     * @param string $imageName
     * @param string $dateCreated
     * @param string $dateUpdated
     * @param ?NewsLikesEntity $likes
     * @param NewsCommentsEntity[]|null $comments
     * @param NewsTagsEntity[] $tags
     */
    public function __construct(
        int                                             $newsId,
        string                                          $title,
        string                                          $description,
        bool                                            $commentsStatus,
        bool                                            $likesStatus,
        bool                                            $status,
        ?string                                         $dateScheduled,
        string                                          $content,
        string                                          $contentNt,
        string                                          $slug,
        ?UserEntity                                     $author,
        int                                             $views,
        string                                          $imageName,
        string                                          $dateCreated,
        string                                          $dateUpdated,
        ?NewsLikesEntity                                $likes,
        #[EntityType(NewsCommentsEntity::class)] ?array $comments,
        #[EntityType(NewsTagsEntity::class)] array      $tags,
    )
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
        $this->status = $status;
        $this->dateScheduled = $dateScheduled;
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
     * <p>Return if the news is published (news_status)</p>
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->status;
    }

    /**
     * @return ?string
     */
    public function getDateScheduled(): ?string
    {
        return $this->dateScheduled;
    }

    /**
     * @return ?string
     */
    public function getDateScheduledFormatted(): ?string
    {
        if ($this->dateScheduled === null) {
            return null;
        }

        return Date::formatDate($this->dateScheduled);
    }

    /**
     * @return bool
     */
    public function isScheduled(): bool
    {
        return $this->dateScheduled !== null;
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
        return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'news/' . $this->slug;
    }

    /**
     * @return ?UserEntity
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
        return Date::formatDate($this->dateCreated);
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
        return Date::formatDate($this->dateUpdated);
    }

    /**
     * @return ?NewsLikesEntity
     */
    public function getLikes(): ?NewsLikesEntity
    {
        return $this->likes;
    }

    /**
     * @return NewsCommentsEntity[]|null
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
        return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Public/Uploads/News/' . $this->imageName;
    }

    /**
     * @return string
     */
    public function getFullImageLink(): string
    {
        return EnvManager::getInstance()->getValue('PATH_URL') . $this->getImageLink();
    }

    public function sendComments(): string
    {
        return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'news/comments/' . $this->newsId;
    }

    /**
     * @param int $tagId
     * @return bool
     */
    public function hasTag(int $tagId): bool
    {
        foreach ($this->tags as $tag) {
            if ($tag->getId() === $tagId) {
                return true;
            }
        }

        return false;
    }
}
