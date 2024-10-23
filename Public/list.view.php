<?php

use CMW\Manager\Env\EnvManager;
use CMW\Utils\Website;

/* @var \CMW\Entity\News\NewsEntity [] $newsList */

Website::setTitle('News');
Website::setDescription('Consultez les dernières actualités');
?>
<?php if (\CMW\Controller\Users\UsersController::isAdminLogged()): ?>
    <div style="background-color: orange; padding: 6px; margin-bottom: 10px">
        <span>Votre thème ne gère pas cette page !</span>
        <br>
        <small>Seuls les administrateurs voient ce message !</small>
    </div>
<?php endif; ?>

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
                <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>news/<?= $news->getSlug() ?>"><?= $news->getTitle() ?></a>
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
                <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>news/<?= $news->getSlug() ?>">Lire
                    la suite</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>