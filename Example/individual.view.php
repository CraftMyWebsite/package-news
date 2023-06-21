<!------------------------------------
    ----- Required namespace-----
-------------------------------------->
<?php 
use CMW\Manager\Security\SecurityManager;
use CMW\Controller\Users\UsersController;
?>

<!------------------------------------
          ----- SHOW NEWS -----
-------------------------------------->
<?= $news->getTitle() ?>
<?= $news->getDescription() ?>
<img src="<?= $news->getImageLink() ?>" alt="...">
<?= $news->getAuthor()->getPseudo() ?>
<?= $news->getDateCreated() ?>

    <!--YOU CAN CHECK LIKE THIS FOR LIKES -->                        
    <?php if ($news->getLikes()->userCanLike()): ?>
        <?php if(UsersController::isUserLogged()) {echo "You already love!";} else {echo "Log in to like!";} ?>
        <?php else: ?> 
            <a href="<?= $news->getLikes()->getSendLike() ?>">You will like</a>
        <?php endif; ?>

<?= $news->getContent() ?>

<!------------------------------------
        ----- COMMENT -----
-------------------------------------->
<?php foreach ($news->getComments() as $comment): ?>
    <img src="<?= $comment->getUser()->getUserPicture()->getImageLink() ?>" alt="...">
    <?= $comment->getUser()->getPseudo() ?>
    <?= $comment->getDate() ?>
    <?= $comment->getContent() ?>

    <!--YOU CAN CHECK LIKE THIS FOR LIKES -->
    <?php if ($comment->userCanLike()): ?>
        <?php if(UsersController::isUserLogged()) {echo "You already love!";} else {echo "Log in to like!";} ?>
        <?php else: ?> 
            <a href="<?= $comment->getSendLike() ?>">You will like</a>
        <?php endif; ?>

<?php endforeach; ?>   

<!------------------------------------
        ----- SEND COMMENT -----
-------------------------------------->
<form method="post" action="<?= $news->sendComments() ?>">
    <?php (new SecurityManager())->insertHiddenToken() ?>
    <textarea name="comments" required></textarea>

    <!--YOU CAN CHECK IF USER IS LOGGED BEFORE USE COMMENT BUTTON -->
    <?php if(UsersController::isUserLogged()): ?>
        <button type="submit">Comment</button>
    <?php else: ?> 
        <a href="<?= Utils::getEnv()->getValue("PATH_SUBFOLDER") ?>login" >Login</a>
    <?php endif; ?>
    
</form>