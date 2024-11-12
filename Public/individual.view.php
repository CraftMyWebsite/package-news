<?php

use CMW\Manager\Env\EnvManager;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

/* @var \CMW\Entity\News\NewsEntity $news */

/* TITRE ET DESCRIPTION */
Website::setTitle($news->getTitle());
Website::setDescription($news->getDescription());
?>
<?php if (\CMW\Controller\Users\UsersController::isAdminLogged()): ?>
    <div style="background-color: orange; padding: 6px; margin-bottom: 10px">
        <span>Votre thème ne gère pas cette page !</span>
        <br>
        <small>Seuls les administrateurs voient ce message !</small>
    </div>
<?php endif; ?>

<h1 style="text-align: center"><?= $news->getTitle() ?></h1>
<p><?= $news->getDescription() ?></p>

<section>
    <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
        <div
            style="flex: 0 0 20%; text-align: center; border: 1px solid #b4aaaa; border-radius: 5px; height: fit-content">
            <img src="<?= $news->getImageLink() ?>" height="90%" width="90%" alt="...">
            <div>
                <?= $news->getAuthor()->getPseudo() ?>
            </div>
            <div>
                <div><?= $news->getDateCreated() ?></div>
            </div>
            <div>
                <div>
                    <?php if ($news->isLikesStatus()): ?>
                        <span><?= $news->getLikes()->getTotal() ?>
                            <?php if ($news->getLikes()->userCanLike()): ?>
                                <a href="#">Like</a>
                            <?php else: ?>
                                <a href="<?= $news->getLikes()->getSendLike() ?>">Liker</a>
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>news" class="text-blue-600"><
                    Revenir aux news</a>
            </div>
        </div>
        <div style="padding .5rem; flex: 0 0 78%; border: 1px solid #b4aaaa; border-radius: 5px; height: fit-content">
            <div>
                <?= $news->getContent() ?>
            </div>
        </div>
    </div>
</section>

<?php if ($news->isCommentsStatus()): ?>
    <hr>
    <h5 style="text-align: center">Espace commentaire</h5>

    <?php foreach ($news->getComments() as $comment): ?>
        <div style="border: 1px solid #b4aaaa; border-radius: 5px; height: fit-content; margin-top: 20px">
            <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
                <div style="flex: 0 0 10%;">
                    <img style="width: 100%; height: fit-content"
                         src="<?= $comment->getUser()->getUserPicture()->getImage() ?>" alt="...">
                </div>
                <div style="flex: 0 0 78%;">
                    <?= $comment->getUser()->getPseudo() ?> :
                    <?= $comment->getDate() ?>
                    <p><?= $comment->getContent() ?></p>

                    <div>
                    <span class="text-base"><?= $comment->getLikes()->getTotal() ?>
                        <?php if ($comment->userCanLike()): ?>
                            <a href="#">Likes</a>
                        <?php else: ?>
                            <a href="<?= $comment->getSendLike() ?>">Liker</a>
                        <?php endif; ?>
                    </span>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>


    <form method="post" action="<?= $news->sendComments() ?>" style="margin-top: 20px">
        <?php SecurityManager::getInstance()->insertHiddenToken() ?>
        <label for="message">Votre commentaire :</label>
        <textarea minlength="20" name="comments" id="message" style="display: block;width: 100%" placeholder="Bonjour," required></textarea>
        <div class="text-center mt-4">
            <?php if (UsersController::isUserLogged()): ?>
                <button type="submit">Commenter</button>
            <?php else: ?>
                <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>login">Commenter</a>
            <?php endif; ?>
        </div>
    </form>

<?php endif; ?>
