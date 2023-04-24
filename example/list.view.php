<!------------------------------------
    ----- Required namespace-----
-------------------------------------->
<?php 
    use CMW\Controller\Users\UsersController;
    use CMW\Manager\Security\SecurityManager;
    $newsList = $newsModel->getSomeNews("NUMBER_OF_NEWS_YOU_WANT_TO_SHOW_MAX", 'DESC');
?>


<!------------------------------------
    ----- list all news -----
-------------------------------------->
<?php foreach ($newsList as $news): ?>
    <img src="<?= $news->getImageLink() ?>" alt="..."/>
    <?= $news->getAuthor()->getPseudo() ?>
    <?= $news->getDateCreated() ?>
    <a href="<?= Utils::getEnv()->getValue("PATH_SUBFOLDER") ?>news/<?= $news->getSlug() ?>"></a>
    <?= $news->getTitle() ?>
    <?= $news->getDescription() ?>
    <a href="<?= Utils::getEnv()->getValue("PATH_SUBFOLDER") ?>news/<?= $news->getSlug() ?>"></a>
    <?= $news->getLikes()->getTotal() ?>    

    <!--YOU CAN CHECK LIKE THIS FOR LIKES -->                        
    <?php if ($news->getLikes()->userCanLike()): ?>
        <?php if(UsersController::isUserLogged()) {echo "You already love!";} else {echo "Log in to like!";} ?>
        <?php else: ?> 
            <a href="<?= $news->getLikes()->getSendLike() ?>">You will like</a>
        <?php endif; ?>
    <?php endif; ?>
<?php endforeach; ?>