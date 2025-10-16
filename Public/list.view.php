<?php

use CMW\Entity\News\NewsEntity;
use CMW\Utils\Website;

/* @var NewsEntity [] $newsList */

Website::setTitle('News');
Website::setDescription('Consultez les dernières actualités');
?>
<section style="width: 70%;padding-bottom: 6rem;margin: 1rem auto auto;">

    <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
        <div style="flex: 0 0 32%; border: solid 1px #b4aaaa; border-radius: 5px; padding: 9px;">

            <?php foreach ($newsList as $news): ?>
                <div style="display: flex; justify-content: center">
                    <img style="width: 50%;" src="<?= $news->getImageLink() ?>" alt="..."/>
                </div>
                <div style="display: flex; justify-content: space-between">
                    <span><?= $news->getAuthor()->getPseudo() ?></span>
                    <span><?= $news->getDateCreated() ?></span>
                </div>
                <h3>
                    <a href="<?= $news->getFullUrl() ?>"><?= $news->getTitle() ?></a>
                </h3>
                <p><?= $news->getDescription() ?></p>
                <div style="display: flex; justify-content: space-between">
                    <div class="cursor-pointer">
                        <?php if ($news->isLikesStatus()): ?>
                            <span><?= $news->getLikes()->getTotal() ?>
                                <?php if ($news->getLikes()->userCanLike()): ?>
                                    <a href="#">Likes</a>
                                <?php else: ?>
                                    <a href="<?= $news->getLikes()->getSendLike() ?>">Liker</a>
                                <?php endif; ?>
                                </span>
                        <?php endif; ?>
                    </div>
                    <a href="<?= $news->getFullUrl() ?>">Lire la suite</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>