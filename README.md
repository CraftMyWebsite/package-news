# Package NEWS pour [CraftMyWebsite](https://craftmywebsite.fr)

Ajoutez facilement des news sur votre site avec ce package !

## Fonctionnalités

- Création / Édition de news
- Personnalisable à 100%
- Désactivez les commentaires / likes
- Commentez les news, likez les news et les commentaires
- Affichage des news en liste (système de tri + limite)
- Tags (rangez vos articles dans des catégories avec des tags.)
- Prefix pour les url personnalisable

## Exemple affichage des news

Tout d'abord veuillez créer un fichier dans le dossier ```list```de votre thème ```news/list.view.php```

Voici un exemple pour afficher toutes les news (exemple complet)
```php
<?php
$title = "News";
$description = "Affichage de toutes vos news";
/* @var \CMW\Entity\News\NewsEntity[] $newsList */
/* @var \CMW\Model\News\NewsModel $newsModel => $newsModel->getSomeNews(3, 'DESC') */

?>

<main>
    <?php foreach ($newsList as $news): ?>
        <hr>
        <div style="text-align: center">
            <h4><?= $news->getTitle() ?></h4>
            <img src="<?= $news->getImageLink() ?>" height="250" width="250" alt="<?= $news->getImageAlt() ?>">
            <br>
            <p><?= $news->getContent() ?></p>
            <p>- <?= $news->getAuthor()->getPseudo() ?></p>
            <p>Nombre de likes: <?= $news->getLikes()->getTotal() ?></p>

            <?php if ($news->getLikes()->userCanLike()): ?>
                <a href="#">Vous avez déjà liké cet article</a>
            <?php else: ?>
                <a href="<?= $news->getLikes()->getSendLike() ?>">Liker l'article</a>
            <?php endif; ?>

            <br>
            <small>Lire la news en cliquant <a href="news/<?= $news->getSlug() ?>">ici</a></small>

            <h5>-- Commenter --</h5>
            <form method="post" action="<?= $news->sendComments() ?>">
                <textarea name="comments"></textarea>
                <button type="submit">Envoyer</button>
            </form>

            <h6>Liste des commentaires: </h6>
            <?php foreach ($news->getComments() as $comment): ?>

                <p><?= $comment->getContent() ?></p>

                <?php if ($comment->userCanLike()): ?>
                    <a href="#">Vous avez déjà liké ce commentaire</a>
                <?php else: ?>
                    <a href="<?= $comment->getSendLike() ?>">Liker ce commentaire</a>
                <?php endif; ?>
            

                <p>Likes: <?= $comment->getLikes()->getTotal() ?></p>

            <?php endforeach; ?>

        </div>
        <hr>
    <?php endforeach; ?>
</main>
```

Vous pouvez aussi utiliser la fonction 
```php
    $newsModel->getSomeNews(3, 'DESC' | 'ASC')
```
pour afficher un nombre maximum de news trié par ordre croisssant ou décroissant à la place de la variable ``$newsList``


Pour accéder à votre page qui liste toutes les news → ``monsite.fr/news``


## Exemple affichage d'une news

Tout d'abord veuillez créer un fichier dans le dossier ```individual```de votre thème ```news/list.view.php```

Voici un exemple pour afficher une news spécifique
```php
<?php
/* @var \CMW\Entity\News\NewsEntity $news */

$title = "News - " . $news->getTitle();
$description = "Affichage de la news " . $news->getTitle();
?>

<main>

    <div style="text-align: center">
        <h3><?= $news->getTitle() ?></h3>
        <img src="<?= $news->getImageLink() ?>" height="250" width="250">
        <br>
        <p><?= $news->getContent() ?></p>
    </div>

    <a href="/news">Revenir aux news</a>

</main>
```

Pour accéder à votre page qui affiche une news spécifique → ``monsite.fr/news/NOM-DE-LA-NEWS``


> Version: `V1.0`

