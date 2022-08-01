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
                <p><?= $news->getContent() ?></p>
                <p>- <?= $news->getAuthor()->getUsername() ?></p>
                <small>Lire la news en cliquant <a href="news/<?= $news->getSlug() ?>">ici</a></small>
            </div>
        <hr>
    <?php endforeach; ?>
</main>
