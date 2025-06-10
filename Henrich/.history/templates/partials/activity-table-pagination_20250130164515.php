<?php
$totalRecords = Page::get('totalRecords');
$limit = Page::get('limit');
$page = Page::get('currentPage');
$offset = Page::get('offset');
$totalPages = ceil($totalRecords / $limit);
?>

<div class="pagination-wrapper">
    <div class="dataTables_info">
        Showing <?= $offset + 1 ?> to <?= min($offset + $limit, $totalRecords) ?> of <?= $totalRecords ?> entries
    </div>
    <ul class="pagination">
        <li><a href="?page=<?= max(1, $page - 1) ?>&limit=<?= $limit ?>" class="prev">&laquo;</a></li>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li><a href="?page=<?= $i ?>&limit=<?= $limit ?>" 
                  class="page <?= $page == $i ? 'active' : '' ?>"><?= $i ?></a></li>
        <?php endfor; ?>
        <li><a href="?page=<?= min($totalPages, $page + 1) ?>&limit=<?= $limit ?>" 
              class="next">&raquo;</a></li>
    </ul>
</div>
