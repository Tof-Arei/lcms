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

    <form action="<?php echo $this->url('lcms', "edit") ?>" method="post">
    <?php echo $lcms->paginator->infoText() ?>
    <table class="horizontal-table">
        <tr>
            <th><?php echo Flux::message('LcmsNSelect') ?></th>
            <th><?php echo $lcms->paginator->sortableColumn('id', Flux::message('LcmsNId')) ?></th>
            <th><?php echo Flux::message('LcmsTypeAuthor') ?></th>
            <th><?php echo $lcms->paginator->sortableColumn('access', Flux::message('LcmsNAccess')) ?></th>
            <th><?php echo $lcms->paginator->sortableColumn('name', Flux::message('LcmsNName')) ?></th>
            <th><?php echo Flux::message('LcmsNUpdate') ?></th>
            <th><?php echo Flux::message('LcmsNDelete') ?></th>
        </tr>
    <?php if (count($module_res) !== 0): ?>
    <?php foreach ($module_res as $module): ?>
        <tr>
            <td>Sel.</td>
            <td><?php echo htmlspecialchars($module->id) ?></td>
            <td><?php echo htmlspecialchars($lcms->getAuthorName($module->account_id)) ?></td>
            <td><?php echo $lcms->getHerculesGroupName($module->access) ?></td>
            <td><?php echo htmlspecialchars($module->name) ?></td>
            <td>
                <button title='<?php echo Flux::message('LcmsNUpdate') ?> <?php echo Flux::message('LcmsTypeModule') ?>' name='tsk' value='module;update;<?php echo htmlspecialchars($module->id) ?>' style='background:none;border:none;cursor:pointer'>
                    <?php echo Flux::message('LcmsNUpdate') ?>
                </button>
            </td>
            <td>
                <button title='<?php echo Flux::message('LcmsNDelete') ?> <?php echo Flux::message('LcmsTypeModule') ?>' name='tsk' value='module;delete;<?php echo htmlspecialchars($module->id) ?>' style='background:none;border:none;cursor:pointer'>
                    <?php echo Flux::message('LcmsNDelete') ?>
                </button>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php endif ?>
    <?php if ($module_level <= $author->access): ?>    
        <tr>
            <td colspan="6">
                <?php echo Flux::message('LcmsMesOptions') ?>
            </td>
            
            <td>
                <button title='<?php echo Flux::message('LcmsNAdd') ?> <?php echo Flux::message('LcmsTypeModule') ?>' name='tsk' value='module;add;-1' style='background:none;border:none;cursor:pointer'>
                    <?php echo Flux::message('LcmsNAdd').' '.Flux::message('LcmsTypeModule') ?>
                </button>
            </td>
        </tr>
        <?php endif; ?>
    </table>
    <?php echo $lcms->paginator->getHTML() ?>
    </form>