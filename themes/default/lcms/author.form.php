<?php 
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$readonly = ($tsk == "add" || $tsk == "update") ? '' : 'readOnly="readonly"';
$disabled = ($readonly == null) ? '' : 'disabled="disabled"';

$account_res = $lcms->getAccounts($group_id);
?>
<form action="<?php echo $this->url('lcms', "edit") ?>" method="post" class="generic-form">
    <table class="generic-form-table">
        <tr>
            <th><label for="account_id"><?php echo Flux::message('LcmsTypeAccount') ?></label></th>
            <td>
                <select name="account_id" id="account_id" <?php echo $readonly ?>>
                    <?php if (count($account_res) !== 0): ?>
                    <?php foreach ($account_res as $account): ?>
                    <option value="<?php echo $account->account_id ?>" <?php echo $disabled ?> <?php echo ($content_res[0]->account_id == $account->account_id) ? "selected" : "" ?>><?php echo $account->userid ?></option>
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
            <th></th>
            <td>
                <button title='<?php echo Flux::message('LcmsN' . ucfirst($tsk)) ?> <?php echo Flux::message('LcmsTypeAuthor') ?>' name='tsk' value='author;do<?php echo $tsk ?>;<?php echo htmlspecialchars($content_res[0]->account_id) ?>' style='background:none;border:none;cursor:pointer'>
                    <?php echo Flux::message('LcmsN' . ucfirst($tsk)) ?>
                </button>
                <button title='<?php echo Flux::message('LcmsNCancel') ?> <?php echo Flux::message('LcmsTypePage') ?>' name='tsk' type="button" onclick="history.go(-1)" style='background:none;border:none;cursor:pointer'>
                    <?php echo Flux::message('LcmsNCancel') ?>
                </button>
            </td>
        </tr>
    </table>
</form>
