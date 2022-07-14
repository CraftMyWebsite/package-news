<?php
$title = NEWS_DASHBOARD_TITLE;
$description = NEWS_DASHBOARD_DESC;
?>

<?php $scripts = '
<script>
    $(function () {
        $("#faq_table").DataTable({
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            language: {
                processing:     "' . CORE_DATATABLES_LIST_PROCESSING . '",
                search:         "' . CORE_DATATABLES_LIST_SEARCH . '",
                lengthMenu:     "' . CORE_DATATABLES_LIST_LENGTHMENU . '",
                info:           "' . CORE_DATATABLES_LIST_INFO . '",
                infoEmpty:      "' . CORE_DATATABLES_LIST_INFOEMPTY . '",
                infoFiltered:   "' . CORE_DATATABLES_LIST_INFOFILTERED . '",
                infoPostFix:    "' . CORE_DATATABLES_LIST_INFOPOSTFIX . '",
                loadingRecords: "' . CORE_DATATABLES_LIST_LOADINGRECORDS . '",
                zeroRecords:    "' . CORE_DATATABLES_LIST_ZERORECORDS . '",
                emptyTable:     "' . CORE_DATATABLES_LIST_EMPTYTABLE . '",
                paginate: {
                    first:      "' . CORE_DATATABLES_LIST_FIRST . '",
                    previous:   "' . CORE_DATATABLES_LIST_PREVIOUS . '",
                    next:       "' . CORE_DATATABLES_LIST_NEXT . '",
                    last:       "' . CORE_DATATABLES_LIST_LAST . '"
                },
                aria: {
                    sortAscending:  "' . CORE_DATATABLES_LIST_SORTASCENDING . '",
                    sortDescending: "' . CORE_DATATABLES_LIST_SORTDESCENDING . '"
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
                        <h3 class="card-title"><?= NEWS_LIST ?></h3>
                    </div>

                    <div class="card-body">

                        <table id="faq_table" class="table table-bordered table-striped">

                            <thead>
                            <tr>
                                <th><?= NEWS_LIST_TABLE_TITLE ?></th>
                                <th><?= NEWS_LIST_TABLE_DESCRIPTION ?></th>
                                <th><?= NEWS_LIST_TABLE_AUTHOR ?></th>
                                <th><?= NEWS_LIST_TABLE_CREATION_DATE ?></th>
                                <th><?= NEWS_LIST_TABLE_EDIT ?></th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php /** @var \CMW\Entity\News\NewsEntity[] $newsList */
                            foreach ($newsList as $news) : ?>
                                <tr>
                                    <td><?= $news->getTitle() ?></td>
                                    <td><?= $news->getDescription() ?></td>
                                    <td><?= $news->getAuthor()->getUsername() ?></td>
                                    <td><?= $news->getDateCreated()?></td>
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
                                <th><?= NEWS_LIST_TABLE_TITLE ?></th>
                                <th><?= NEWS_LIST_TABLE_DESCRIPTION ?></th>
                                <th><?= NEWS_LIST_TABLE_AUTHOR ?></th>
                                <th><?= NEWS_LIST_TABLE_CREATION_DATE ?></th>
                                <th><?= NEWS_LIST_TABLE_EDIT ?></th>
                            </tr>
                            </tfoot>

                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>