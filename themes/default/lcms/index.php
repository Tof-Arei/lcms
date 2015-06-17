<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('LcmsMHeading')) ?></h2>

<?php if ($author == null): ?>
    <?php if ($session->account->group_id == 99): ?>
        <h4><?php echo Flux::message('LcmsMesWAdmin') ?></h4>
        <h4><?php echo Flux::message('LcmsMesWAdmin2') ?></h4>
    <?php exit; ?>
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
        <th><?php echo Flux::message('LcmsTypeModule') ?></th>
        <th><?php echo Flux::message('LcmsTypeAuthor') ?></th>
        <th><?php echo $lcms->paginator->sortableColumn('access', Flux::message('LcmsNAccess')) ?></th>
        <th><?php echo Flux::message('LcmsNName') ?></th>
        <th><?php echo $lcms->paginator->sortableColumn('date', Flux::message('LcmsNDate')) ?></th>
        <th><?php echo $lcms->paginator->sortableColumn('status', Flux::message('LcmsNStatus')) ?></th>
        <th><?php echo Flux::message('LcmsNUpdate') ?></th>
        <th><?php echo Flux::message('LcmsNDelete') ?></th>
    </tr>
    <?php if (count($page_res) !== 0): ?>
    <?php foreach ($page_res as $page): ?>
    <tr>
        <td style="text-align:center">
            <input type="checkbox" name="select" value="<?php echo $page->id ?>" />
        </td>
        <td><?php echo htmlspecialchars($page->id) ?></td>
        <td><?php echo htmlspecialchars($page->module_name) ?></td>
        <td><?php echo htmlspecialchars($page->userid) ?></td>
        <td><?php echo htmlspecialchars($lcms->getHerculesGroupName($page->access)) ?></td>
        <td><?php echo htmlspecialchars($page->name) ?></td>
        <td><?php echo htmlspecialchars($page->date) ?></td>
        <td><?php echo htmlspecialchars(Lcms_Functions::getStatusName($page->status)) ?></td>
        <td>
            <button title='<?php echo Flux::message('LcmsNUpdate') ?> <?php echo Flux::message('LcmsTypePage') ?>' name='tsk' value='page;update;<?php echo htmlspecialchars($page->id) ?>' style='background:none;border:none;cursor:pointer'>
                <?php echo Flux::message('LcmsNUpdate') ?>
            </button>
        </td>
        <td>
            <button title='<?php echo Flux::message('LcmsNDelete') ?> <?php echo Flux::message('LcmsTypePage') ?>' name='tsk' value='page;delete;<?php echo htmlspecialchars($page->id) ?>' style='background:none;border:none;cursor:pointer'>
                <?php echo Flux::message('LcmsNDelete') ?>
            </button>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php endif ?>
    <?php if ($module_res != null): ?>
    <tr>
        <td colspan="8">
            <?php echo Flux::message('LcmsMesOptions') ?>
        </td>
        <td>
            <button title='<?php echo Flux::message('LcmsNAdd') ?> <?php echo Flux::message('LcmsTypePage') ?>' name='tsk' value='page;add;0' style='background:none;border:none;cursor:pointer'>
                <?php echo Flux::message('LcmsNAdd').' '.Flux::message('LcmsTypePage') ?>
            </button>
        </td>
        <td>
            <button title='<?php echo Flux::message('LcmsNDelete') ?> <?php echo Flux::message('LcmsTypePage') ?>' name='tsk' value="page;dodelete;" onclick="if(!confirm('<?php echo Flux::message('LcmsMesWConfirm') ?>')){return false;}else{this.value += listSelected('select');}" style='background:none;border:none;cursor:pointer'>
                <?php echo Flux::message('LcmsNDelete') ?>
            </button>
        </td>
    </tr>
    <?php endif; ?>
</table>
<?php echo $lcms->paginator->getHTML() ?>
</form>
<p style="text-align:right;">
    Light CMS version: <?php echo Lcms_Functions::$VERSION ?>
</p>