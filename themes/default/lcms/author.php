<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('LcmsMHeading')) ?></h2>

<script type='text/javascript' src='<?php echo Flux::config('BaseURI') . FLUX_ADDON_DIR . '/lcms/themes/default/js/functions.js'; ?>'></script>
<form action="<?php echo $this->url('lcms', "edit") ?>" method="post">
<?php echo $lcms->paginator->infoText() ?>
<table class="horizontal-table">
    <tr>
        <th><input type='checkbox' onclick="selectAll(this, 'select')" /></th> 
        <th><?php echo $lcms->paginator->sortableColumn('account_id', Flux::message('LcmsNId')) ?></th>
        <th><?php echo Flux::message('LcmsNName') ?></th>
        <th>[LCMS]<?php echo $lcms->paginator->sortableColumn('access', Flux::message('LcmsNAccess')) ?></th>
        <th><?php echo Flux::message('LcmsNUpdate') ?></th>
        <th><?php echo Flux::message('LcmsNDelete') ?></th>
    </tr>
<?php if (count($author_res) !== 0): ?>
<?php foreach ($author_res as $author): ?>
    <?php $author_dao = new Lcms_DAO($author, null, "author", $session); ?>
    <tr>
        <td style="text-align:center">
            <input type="checkbox" name="select" value="<?php echo $author->account_id ?>" />
        </td>
        <td><?php echo htmlspecialchars($author->account_id) ?></td>
        <td><?php echo htmlspecialchars($author->userid) ?></td>
        <td><?php echo htmlspecialchars($lcms->getHerculesGroupName($author->access)) ?></td>
        <td>
            <button title='<?php echo Flux::message('LcmsNUpdate') ?> <?php echo Flux::message('LcmsTypeAuthor') ?>' name='tsk' value='author;update;<?php echo htmlspecialchars($author->account_id) ?>' style='background:none;border:none;cursor:pointer'>
                <?php echo Flux::message('LcmsNUpdate') ?>
            </button>
        </td>
        <td>
            <?php if ($author_dao->isDeletable()): ?>
            <button title='<?php echo Flux::message('LcmsNDelete') ?> <?php echo Flux::message('LcmsTypeAuthor') ?>' name='tsk' value='author;delete;<?php echo htmlspecialchars($author->account_id) ?>' style='background:none;border:none;cursor:pointer'>
                <?php echo Flux::message('LcmsNDelete') ?>
            </button>
            <?php endif; ?>
        </td>
    </tr>
<?php endforeach; ?>
<?php endif; ?>
    <tr>
        <td colspan="4">
            <?php echo Flux::message('LcmsMesOptions') ?>
        </td>
        <td>
            <button title='<?php echo Flux::message('LcmsNAdd') ?> <?php echo Flux::message('LcmsTypeAuthor') ?>' name='tsk' value='author;add;0' style='background:none;border:none;cursor:pointer'>
                <?php echo Flux::message('LcmsNAdd').' '.Flux::message('LcmsTypeAuthor') ?>
            </button>
        </td>
        <td>
            <button title='<?php echo Flux::message('LcmsNDelete') ?> <?php echo Flux::message('LcmsTypeAuthor') ?>' name='tsk' value="author;dodelete;" onclick="if(!confirm('<?php echo Flux::message('LcmsMesWConfirm') ?>')){return false;}else{this.value += listSelected('select');}" style='background:none;border:none;cursor:pointer'>
                <?php echo Flux::message('LcmsNDelete') ?>
            </button>
        </td>
    </tr>
</table>
<?php echo $lcms->paginator->getHTML() ?>
</form>
