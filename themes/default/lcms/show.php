<?php if (!defined('FLUX_ROOT')) exit; ?>
<?php if (!empty($errorMessage)): ?>
    <p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif; ?>
<?php if (!is_null($page)): ?>
    <?php if (!is_null($page_res)): ?>
    <div id="submenu"><?php echo $module->name ?>:
    <?php foreach ($page_res as $submenu): ?>
        <a href="<?php echo Flux::config('BaseURI') ?>?module=lcms&action=show&id=<?php echo $submenu->id ?>" class="sub-menu-item"><?php echo $submenu->name ?></a>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php echo $page->content // Info $page->content is parsed by HTMLPurifier before it's stored into the database ?>
    <h6><?php echo $lcms->getAuthorName($page->account_id) ?> / <?php echo $page->date ?></h6>
<?php endif; ?>
