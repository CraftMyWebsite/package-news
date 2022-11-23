<!--




















CETTE PAGE DOIT ÊTRE SUPPRIMÉ

























-->
<?php

use CMW\Manager\Lang\LangManager;
use CMW\Utils\SecurityService;

$title = LangManager::translate("news.dashboard.title");
$description = LangManager::translate("news.dashboard.desc");
?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-newspaper"></i> <span class="m-lg-auto"><?= LangManager::translate("news.dashboard.title") ?></span></h3>
</div>


<section>
    <div class="card">
        <div class="card-header">
            <h4><?= LangManager::translate("news.list.list") ?></h4>
        </div>
        <div class="card-body">
            <table class="table" id="table1">
                <thead>
                <tr>
                    <th class="text-center"><?= LangManager::translate("news.list.table.title") ?></th>
                    <th class="text-center"><?= LangManager::translate("news.list.table.description") ?></th>
                    <th class="text-center"><?= LangManager::translate("news.list.table.author") ?></th>
                    <th class="text-center"><?= LangManager::translate("news.list.table.creation_date") ?></th>
                    <th class="text-center"><?= LangManager::translate("core.btn.edit") ?></th>
                </tr>
                </thead>
                <tbody class="text-center">
                    <?php /** @var \CMW\Entity\News\NewsEntity[] $newsList */ foreach ($newsList as $news) : ?>
                    <tr>
                        <td><?= $news->getTitle() ?></td>
                        <td><?= $news->getDescription() ?></td>
                        <td><?= $news->getAuthor()->getUsername() ?></td>
                        <td><?= $news->getDateCreated() ?></td>
                        <td>
                            <a href="../news/edit/<?= $news->getNewsId() ?>">
                                <i class="text-primary fa-solid fa-gears"></i>
                            </a>
                            <a href="../news/delete/<?= $news->getNewsId() ?>">
                                <i class="ms-2 text-danger fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>