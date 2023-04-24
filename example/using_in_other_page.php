<!------------------------------------
    ----- Required namespace-----
-------------------------------------->
<?php 
use CMW\Controller\Users\UsersController;

/*!!! COMPATIBILITY : You have to check if the package is installed before use !!!*/
/*Check installed package*/
use CMW\Controller\Core\PackageController;
/*NEWS BASIC NEED*/
use CMW\Model\News\NewsModel;
if (PackageController::isInstalled("news")) {
    $newsList = new newsModel;
    $newsList = $newsList->getSomeNews( ThemeModel::fetchConfigValue('news_number_display'));
}
?>

<!--------------------------------------------------------
    ----- list all news & check if news is installed -----
--------------------------------------------------------->

<?php if (PackageController::isInstalled("news")): ?>
    <?php foreach ($newsList as $news): ?>
        <img src="<?= $news->getImageLink() ?>" alt="..."/>
        <?= $news->getAuthor()->getPseudo() ?>
        <?= $news->getDateCreated() ?>
        <a href="<?= Utils::getEnv()->getValue("PATH_SUBFOLDER") ?>news/<?= $news->getSlug() ?>"><?= $news->getTitle() ?></a>

        <!--YOU CAN CHECK LIKE THIS FOR LIKES -->                        
        <?php if ($news->getLikes()->userCanLike()): ?>
            <?php if(UsersController::isUserLogged()) {echo "You already love!";} else {echo "Log in to like!";} ?>
            <?php else: ?> 
                <a href="<?= $news->getLikes()->getSendLike() ?>">You will like</a>
            <?php endif; ?>
        <?php endif; ?>

    <?php endforeach; ?>
<?php endif; ?>