<?php
// This template provides a standard structure for admin pages
?>
<div class="admin-page">
    <div class="admin-page-header">
        <div class="header-left">
            <h1><?php echo Page::getTitle(); ?></h1>
            <?php if (isset($pageDescription)): ?>
                <p class="text-muted"><?php echo $pageDescription; ?></p>
            <?php endif; ?>
        </div>
        <div class="header-actions">
            <?php if (isset($headerActions)): ?>
                <?php echo $headerActions; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="admin-page-content">
        <?php if (isset($filterSection)): ?>
            <div class="filter-section">
                <?php echo $filterSection; ?>
            </div>
        <?php endif; ?>

        <div class="content-cards">
            <?php if (isset($contentCards)): ?>
                <?php echo $contentCards; ?>
            <?php endif; ?>
        </div>

        <div class="main-content">
            <?php echo $mainContent; ?>
        </div>
    </div>
</div>

<?php if (isset($modals)): ?>
    <?php echo $modals; ?>
<?php endif; ?> 