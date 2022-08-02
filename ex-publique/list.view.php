<?php
$title = "News";
$description = "Affichage de toutes vos news";
/* @var \CMW\Entity\News\NewsEntity[] $newsList */
?>

<main>
    <?php foreach ($newsList as $news): ?>
        <hr>
        <div style="text-align: center">
            <h4><?= $news->getTitle() ?></h4>
            <img src="<?= $news->getImageLink() ?>" height="250" width="250">
            <br>
            <p><?= $news->getContent() ?></p>
            <p>- <?= $news->getAuthor()->getUsername() ?></p>
            <p>Nombre de likes: <?= $news->getLikes()->getTotal() ?></p>
            <?php if ($news->getLikes()->isLike()): ?>
                <a href="#">Vous avez déjà liké cet article</a>
            <?php else: ?>
                <a href="<?= $news->getLikes()->getSendLike() ?>">Liker l'article</a>
            <?php endif; ?>
            <br>
            <small>Lire la news en cliquant <a href="news/<?= $news->getSlug() ?>">ici</a></small>
        </div>
        <hr>
    <?php endforeach; ?>
</main>
