<?php 
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$readonly = ($tsk == "add" || $tsk == "update") ? '' : 'readOnly="readonly"';
$disabled = ($readonly == null) ? '' : 'disabled="disabled"';
?>
<form action="<?php echo $this->url('lcms', "edit") ?>" method="post" class="generic-form">
    <table class="generic-form-table">
        <tr>
            <th><label for="id"><?php echo Flux::message('LcmsNId') ?></label></th>
            <td><input type="text" name="id" id="id" value="<?php echo htmlspecialchars($content_res[0]->id) ?>" readOnly="readonly" /></td>
            <td><p></p></td>
        </tr>
        <tr>
            <th><label for="account_id"><?php echo Flux::message('LcmsTypeAuthor') ?></label></th>
            <td>
                <select name="account_id" id="account_id">
                    <?php $author_res = $lcms->getAuthorAuthors($author->access) ?>
                    <?php if (count($author_res) !== 0): ?>
                    <?php foreach ($author_res as $author_field): ?>
                    <option value="<?php echo $author_field->account_id ?>" <?php echo ($content_res[0]->account_id == $author_field->account_id) ? 'selected' : '' ?>><?php echo htmlspecialchars($author_field->userid) ?></option>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </td>
            <td><p></p></td>
        </tr>
        <tr>
            <th><label for="access"><?php echo Flux::message('LcmsNAccess') ?></label></th>
            <td>
                <select name="access" id="access">
                    <?php foreach (AccountLevel::getArray() as $key => $group): ?>
                    <option value="<?php echo $key ?>" <?php echo ($key <= $group_id) ? $disabled : "disabled" ?> <?php echo ($content_res[0]->access == $key) ? "selected" : "" ; ?>><?php echo $group['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td><p></p></td>
        </tr>
        <tr>
            <th><label for="name"><?php echo Flux::message('LcmsNName') ?></label></th>
            <td><input type="text" name="name" id="name" value="<?php echo htmlspecialchars($content_res[0]->name) ?>" <?php echo $readonly ?> /></td>
            <td><p></p></td>
        </tr>
        <tr>
            <th></th>
            <td>
                <button title='<?php echo Flux::message('LcmsN' . ucfirst($tsk)) ?> <?php echo Flux::message('LcmsTypeModule') ?>' name='tsk' value='module;do<?php echo $tsk ?>;<?php echo htmlspecialchars($content_res[0]->id) ?>' style='background:none;border:none;cursor:pointer'>
                    <?php echo Flux::message('LcmsN' . ucfirst($tsk)) ?>
                </button>
                <button title='<?php echo Flux::message('LcmsNCancel') ?> <?php echo Flux::message('LcmsTypePage') ?>' name='tsk' type="button" onclick="history.go(-1)" style='background:none;border:none;cursor:pointer'>
                    <?php echo Flux::message('LcmsNCancel') ?>
                </button>
            </td>
        </tr>
    </table>
</form>
