<?php

use CMW\Manager\Lang\LangManager;

$title = LangManager::translate("news.dashboard.title");
$description = LangManager::translate("news.dashboard.desc");
?>

<?php $scripts = '
<script>
    $(function () {
        $("#users_table").DataTable({
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            language: {
                processing:     "' . LangManager::translate("core.datatables.list.processing") . '",
                search:         "' . LangManager::translate("core.datatables.list.search") . '",
                lengthMenu:    "' . LangManager::translate("core.datatables.list.lenghtmenu") . '",
                info:           "' . LangManager::translate("core.datatables.list.info") . '",
                infoEmpty:      "' . LangManager::translate("core.datatables.list.info_empty") . '",
                infoFiltered:   "' . LangManager::translate("core.datatables.list.info_filtered") . '",
                infoPostFix:    "' . LangManager::translate("core.datatables.list.info_postfix") . '",
                loadingRecords: "' . LangManager::translate("core.datatables.list.loadingrecords") . '",
                zeroRecords:    "' . LangManager::translate("core.datatables.list.zerorecords") . '",
                emptyTable:     "' . LangManager::translate("core.datatables.list.emptytable") . '",
                paginate: {
                    first:      "' . LangManager::translate("core.datatables.list.first") . '",
                    previous:   "' . LangManager::translate("core.datatables.list.previous") . '",
                    next:       "' . LangManager::translate("core.datatables.list.next") . '",
                    last:       "' . LangManager::translate("core.datatables.list.last") . '"
                },
                aria: {
                    sortAscending:  "' . LangManager::translate("core.datatables.list.sort.ascending") . '",
                    sortDescending: "' . LangManager::translate("core.datatables.list.sort.descending") . '"
                }
            },
        });
    });
</script>'; ?>

<div class="content">

    <div class="container-fluid">
        <div class="row">

            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h3 class="card-title"><?= LangManager::translate("news.list.list") ?></h3>
                    </div>

                    <div class="card-body">

                        <table id="faq_table" class="table table-bordered table-striped">

                            <thead>
                            <tr>
                                <th><?= LangManager::translate("news.list.table.title") ?></th>
                                <th><?= LangManager::translate("news.list.table.description") ?></th>
                                <th><?= LangManager::translate("news.list.table.author") ?></th>
                                <th><?= LangManager::translate("news.list.table.creation_date") ?></th>
                                <th><?= LangManager::translate("news.list.table.edit") ?></th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php /** @var \CMW\Entity\News\NewsEntity[] $newsList */
                            foreach ($newsList as $news) : ?>
                                <tr>
                                    <td><?= $news->getTitle() ?></td>
                                    <td><?= $news->getDescription() ?></td>
                                    <td><?= $news->getAuthor()->getUsername() ?></td>
                                    <td><?= $news->getDateCreated() ?></td>
                                    <td class="text-center">

                                        <a href="../news/edit/<?= $news->getNewsId() ?>" class="btn btn-warning"><i
                                                    class="fas fa-edit"></i></a>

                                        <a href="../news/delete/<?= $news->getNewsId() ?>" class="btn btn-danger"><i
                                                    class="fas fa-trash"></i></a>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>

                            <tfoot>
                            <tr>
                                <th><?= LangManager::translate("news.list.table.title") ?></th>
                                <th><?= LangManager::translate("news.list.table.description") ?></th>
                                <th><?= LangManager::translate("news.list.table.author") ?></th>
                                <th><?= LangManager::translate("news.list.table.creation_date") ?></th>
                                <th><?= LangManager::translate("news.list.table.edit") ?></th>
                            </tr>
                            </tfoot>

                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>