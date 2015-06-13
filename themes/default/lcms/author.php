<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('LcmsMCHeading')) ?></h2>

    <h3><?php echo Flux::message('LcmsMCHeading3') ?></h3>
    <form action="<?php echo $this->url('lcms', "edit") ?>" method="post">
    <?php echo $lcms->paginator->infoText() ?>
    <table class="horizontal-table">
        <tr>
            <th><?php echo Flux::message('LcmsNSelect') ?></th>
            <th><?php echo $lcms->paginator->sortableColumn('account_id', Flux::message('LcmsNId')) ?></th>
            <th><?php echo Flux::message('LcmsNName') ?></th>
            <th>[LCMS]<?php echo $lcms->paginator->sortableColumn('access', Flux::message('LcmsNAccess')) ?></th>
            <th><?php echo Flux::message('LcmsNUpdate') ?></th>
            <th><?php echo Flux::message('LcmsNDelete') ?></th>
        </tr>
    <?php if (count($author_res) !== 0): ?>
    <?php foreach ($author_res as $author): ?>
        <tr>
            <td>Sel.</td>
            <td><?php echo htmlspecialchars($author->account_id) ?></td>
            <td><?php echo htmlspecialchars($lcms->getAuthorName($author->account_id)) ?></td>
            <td><?php echo htmlspecialchars($lcms->getHerculesGroupName($author->access)) ?></td>
            <td>
                <button title='<?php echo Flux::message('LcmsNUpdate') ?> <?php echo Flux::message('LcmsTypeAuthor') ?>' name='tsk' value='author;update;<?php echo htmlspecialchars($author->account_id) ?>' style='background:none;border:none;cursor:pointer'>
                    <?php echo Flux::message('LcmsNUpdate') ?>
                </button>
            </td>
            <td>
                <button title='<?php echo Flux::message('LcmsNDelete') ?> <?php echo Flux::message('LcmsTypeAuthor') ?>' name='tsk' value='author;delete;<?php echo htmlspecialchars($author->account_id) ?>' style='background:none;border:none;cursor:pointer'>
                    <?php echo Flux::message('LcmsNDelete') ?>
                </button>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php endif; ?>
    <?php if ($author_level <= $access): ?>    
        <tr>
            <td colspan="5">
                <?php echo Flux::message('LcmsMesOptions') ?>
            </td>
            <td>
                <button title='<?php echo Flux::message('LcmsNAdd') ?> <?php echo Flux::message('LcmsTypeAuthor') ?>' name='tsk' value='author;add;-1' style='background:none;border:none;cursor:pointer'>
                    <?php echo Flux::message('LcmsNAdd').' '.Flux::message('LcmsTypeAuthor') ?>
                </button>
            </td>
        </tr>
        <?php endif; ?>
    </table>
    <?php echo $lcms->paginator->getHTML() ?>
    </form>
