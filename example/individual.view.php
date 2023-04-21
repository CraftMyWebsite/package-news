<!------------------------------------
    ----- Required namespace-----
-------------------------------------->
<?php 
use CMW\Manager\Security\SecurityManager;
?>

<!------------------------------------
          ----- SHOW NEWS -----
-------------------------------------->
<?= $news->getTitle() ?>
<?= $news->getDescription() ?>
<img src="<?= $news->getImageLink() ?>" alt="...">
<?= $news->getAuthor()->getPseudo() ?>
<?= $news->getDateCreated() ?>