<?php if (!defined('FLUX_ROOT')) exit; ?>
<?php if (!empty($errorMessage)): ?>
    <p class="red"><?php echo nl2br($errorMessage) ?></p>
<?php endif; ?>
<?php if (!empty($resultMessage)): ?>
    <p class="green"><?php echo nl2br($resultMessage) ?></p>
    <?php //printf(Flux::message('LogoutInfo2'), $metaRefresh['location']) ?>
<?php endif; ?>
<?php if (!empty($resultMessage) || !empty($errorMessage)): ?>
    <a href="<?php echo $this->url('lcms', 'index') ?>"><?php echo Flux::message('LcmsMesBackToIndex') ?></a>
    <?php exit; ?>
<?php endif; ?>
    
<h2><?php echo ucfirst($type) . " [$tsk] :" ?></h2>
<?php include($form); ?>