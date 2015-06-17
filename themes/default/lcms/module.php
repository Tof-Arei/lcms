<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('LcmsMHeading')) ?></h2>

<?php if ($author == null): ?>
    <?php if ($session->account->group_id == 99): ?>
        <h4><?php echo Flux::message('LcmsMesWAdmin') ?></h4>
        <h4><?php echo Flux::message('LcmsMesWAdmin2') ?></h4>
    <?php else: ?>
        <h4><?php echo Flux::message('LcmsMesWAuthor') ?></h4>
    <?php endif; ?>
    <?php exit; ?>
<?php endif; ?>

<script type='text/javascript' src='<?php echo Flux::config('BaseURI') . FLUX_ADDON_DIR . '/lcms/themes/default/js/functions.js'; ?>'></script>
<form action="<?php echo $this->url('lcms', "edit") ?>" method="post">
<?php echo $lcms->paginator->infoText() ?>
<table class="horizontal-table">
    <tr>
        <th><input type='checkbox' onclick="selectAll(this, 'select')" /></th> 
        <th><?php echo $lcms->paginator->sortableColumn('id', Flux::message('LcmsNId')) ?></th>
        <th><?php echo Flux::message('LcmsTypeAuthor') ?></th>
        <th><?php echo $lcms->paginator->sortableColumn('access', Flux::message('LcmsNAccess')) ?></th>
        <th><?php echo $lcms->paginator->sortableColumn('name', Flux::message('LcmsNName')) ?></th>
        <th><?php echo Flux::message('LcmsNUpdate') ?></th>
        <th><?php echo Flux::message('LcmsNDelete') ?></th>
    </tr>
<?php if (count($module_res) !== 0): ?>
<?php foreach ($module_res as $module): ?>
    <?php $module_dao = new Lcms_DAO($module, null, "module", $session); ?>
    <tr>
        <td style="text-align:center">
            <input type="checkbox" name="select" value="<?php echo $module->id ?>" />
        </td>
        <td><?php echo htmlspecialchars($module->id) ?></td>
        <td><?php echo htmlspecialchars($module->userid) ?></td>
        <td><?php echo $lcms->getHerculesGroupName($module->access) ?></td>
        <td><?php echo htmlspecialchars($module->name) ?></td>
        <td>
            <button title='<?php echo Flux::message('LcmsNUpdate') ?> <?php echo Flux::message('LcmsTypeModule') ?>' name='tsk' value='module;update;<?php echo htmlspecialchars($module->id) ?>' style='background:none;border:none;cursor:pointer'>
                <?php echo Flux::message('LcmsNUpdate') ?>
            </button>
        </td>
        <td>
            <?php if ($module_dao->isDeletable()): ?>
            <button title='<?php echo Flux::message('LcmsNDelete') ?> <?php echo Flux::message('LcmsTypeModule') ?>' name='tsk' value='module;delete;<?php echo htmlspecialchars($module->id) ?>' style='background:none;border:none;cursor:pointer'>
                <?php echo Flux::message('LcmsNDelete') ?>
            </button>
            <?php endif; ?>
        </td>
    </tr>
<?php endforeach; ?>
<?php endif ?>
    <tr>
        <td colspan="5">
            <?php echo Flux::message('LcmsMesOptions') ?>
        </td>
        <td>
            <button title='<?php echo Flux::message('LcmsNAdd') ?> <?php echo Flux::message('LcmsTypeModule') ?>' name='tsk' value='module;add;0' style='background:none;border:none;cursor:pointer'>
                <?php echo Flux::message('LcmsNAdd').' '.Flux::message('LcmsTypeModule') ?>
            </button>
        </td>
        <td>
            <button title='<?php echo Flux::message('LcmsNDelete') ?> <?php echo Flux::message('LcmsTypeModule') ?>' name='tsk' value="module;dodelete;" onclick="if(!confirm('<?php echo Flux::message('LcmsMesWConfirm') ?>')){return false;}else{this.value += listSelected('select');}" style='background:none;border:none;cursor:pointer'>
                <?php echo Flux::message('LcmsNDelete') ?>
            </button>
        </td>
    </tr>
</table>
<?php echo $lcms->paginator->getHTML() ?>
</form>