<?php
/* @var \CMW\Entity\News\NewsEntity $news */

$title = "News - " . $news->getTitle();
$description = "Affichage de la news " . $news->getTitle();
?>


<main>

        <div style="text-align: center">
            <h3><?= $news->getTitle() ?></h3>
            <p><?= $news->getContent() ?></p>
        </div>

    <a href="/news">Revenir aux news</a>

</main>
