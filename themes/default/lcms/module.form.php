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
            <td><input type="text" name="id" id="id" value="<?php echo htmlspecialchars($content_res->id) ?>" readOnly="readonly" /></td>
            <td><p></p></td>
        </tr>
        <tr>
            <th><label for="account_id"><?php echo Flux::message('LcmsTypeAuthor') ?></label></th>
            <td>
                <select name="account_id" id="account_id">
                    <?php $author_res = $lcms->getAuthorAuthors($author) ?>
                    <?php if (count($author_res) !== 0): ?>
                    <?php foreach ($author_res as $author_field): ?>
                    <option value="<?php echo $author_field->account_id ?>" <?php echo ($content_res->account_id != $author_field->account_id) ? (is_null($author)) ? '' : 'readOnly="readonly"' : "selected" ?>><?php echo $lcms->getAuthorName($author_field->account_id) ?></option>
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
                    <option value="<?php echo AccountLevel::UNAUTH ?>" <?php echo $disabled ?> <?php echo ($content_res->access == AccountLevel::UNAUTH) ? "selected" : "" ; ?>><?php echo AccountLevel::getGroupName(AccountLevel::UNAUTH) ?></option>
                    <?php foreach (AccountLevel::getArray() as $key => $group): ?>
                    <option value="<?php echo $key ?>" <?php echo $disabled ?> <?php echo ($content_res->access == $key) ? "selected" : "" ; ?>><?php echo $group['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td><p></p></td>
        </tr>
        <tr>
            <th><label for="name"><?php echo Flux::message('LcmsNName') ?></label></th>
            <td><input type="text" name="name" id="name" value="<?php echo htmlspecialchars($content_res->name) ?>" <?php echo $readonly ?> /></td>
            <td><p></p></td>
        </tr>
        <tr>
            <th></th>
            <td>
                <button title='<?php echo Flux::message('LcmsN' . ucfirst($tsk)) ?> <?php echo Flux::message('LcmsTypeModule') ?>' name='tsk' value='module;do<?php echo $tsk ?>;<?php echo htmlspecialchars($content_res->id) ?>' style='background:none;border:none;cursor:pointer'>
                    <?php echo Flux::message('LcmsN' . ucfirst($tsk)) ?>
                </button>
            </td>
            <td><p></p></td>
        </tr>
    </table>
</form>
