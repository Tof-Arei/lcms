<?php if (!defined('FLUX_ROOT')) exit; ?>
<?php if (!empty($errorMessage)): ?>
    <p class="red"><?php echo nl2br($errorMessage) ?></p>
    <?php exit; ?>
<?php endif; ?>
<?php if (!empty($resultMessage)): ?>
    <p class="green"><?php echo nl2br($resultMessage) ?></p>
    <?php printf(Flux::message('LogoutInfo2'), $metaRefresh['location']) ?>
    <?php exit; ?>
<?php endif; ?>
    
<h2><?php echo ucfirst($type) . " [$tsk] :" ?></h2>
<?php include($form); ?>