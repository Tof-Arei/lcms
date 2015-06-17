<?php 
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$readonly = ($tsk == "add" || $tsk == "update") ? '' : 'readOnly="readonly"';
$disabled = ($readonly == null) ? '' : 'disabled="disabled"';
?>
<script type="text/javascript">
// Dynamically insert CKEditor script to current page <head> to avoid (multiple) header.php edition during installation
var head = document.getElementsByTagName('head')[0];
var script = document.createElement('script');
script.type = 'text/javascript';
script.src = '<?php echo Flux::config('BaseURI').FLUX_ADDON_DIR."/lcms/lib/ckeditor/ckeditor.js" ?>';
head.appendChild(script);
</script>
<form action="<?php echo $this->url('lcms', "edit") ?>" method="post" class="generic-form">
    <table class="generic-form-table">
        <tr>
            <th><label for="id"><?php echo Flux::message('LcmsNId') ?></label></th>
            <td><input type="text" name="id" id="id" value="<?php echo htmlspecialchars($content_res[0]->id) ?>" readOnly="readonly" /></td>
            <td><p></p></td>
        </tr>
        <tr>
            <th><label for="module_id"><?php echo Flux::message('LcmsTypeModule') ?></label></th>
            <td>
                <select name="module_id" id="module_id">
                    <?php $module_res = $lcms->getAuthorModules($author, $author->access) ?>
                    <?php if (count($module_res) !== 0): ?>
                    <?php foreach ($module_res as $module_field): ?>
                    <option value="<?php echo $module_field->id ?>" <?php echo $disabled ?> <?php echo ($content_res[0]->module_id == $module_field->id) ? "selected" : "" ; ?>><?php echo $module_field->name ?></option>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </td>
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
            <th><label for="status"><?php echo Flux::message('LcmsNStatus') ?></label></th>
            <td>
                <select name="status" id="status">
                    <option value="-1" <?php echo (empty($disabled)) ? (Flux::config('LcmsValidationEnable')) ? ($author->access >= Flux::config('LcmsValidationBypassLevel')) ? : "disabled" : $disabled : $disabled ?> <?php echo ($content_res[0]->status == -1) ? "selected" : "" ?>><?php echo Flux::message('LcmsStatPending') ?></option>
                    <option value="0" <?php echo (empty($disabled)) ? (Flux::config('LcmsValidationEnable')) ? ($author->access >= Flux::config('LcmsValidationBypassLevel')) ? : "disabled" : $disabled : $disabled ?> <?php echo (!Flux::config('LcmsValidationEnable')) ? "selected" : ($content_res[0]->status == 0) ? "selected" : "" ?>><?php echo Flux::message('LcmsStatValid') ?></option>
                </select>
            </td>
            <td><p></p></td>
        </tr>
        <tr>
            <th><label for="access"><?php echo Flux::message('LcmsNAccess') ?></label></th>
            <td>
                <select name="access" id="access">
                    <option value="<?php echo AccountLevel::UNAUTH ?>" <?php echo $disabled ?> <?php echo ($content_res[0]->access == AccountLevel::UNAUTH) ? "selected" : "" ; ?>><?php echo AccountLevel::getGroupName(AccountLevel::UNAUTH) ?></option>
                    <?php foreach (AccountLevel::getArray() as $key => $group): ?>
                    <option value="<?php echo $key ?>" <?php echo ($key <= $group_id) ? $disabled : "disabled" ?> <?php echo ($content_res[0]->access == $key) ? "selected" : "" ; ?>><?php echo $group['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td><p></p></td>
        </tr>
        <tr>
            <th><label for="date"><?php echo Flux::message('LcmsNDate') ?></label></th>
            <td><input type="text" name="date" id="date" value="<?php echo htmlspecialchars($content_res[0]->date) ?>" readOnly="readonly" /></td>
            <td><p></p></td>
        </tr>
        <tr>
            <th><label for="name"><?php echo Flux::message('LcmsNName') ?></label></th>
            <td><input type="text" name="name" id="name" value="<?php echo htmlspecialchars($content_res[0]->name) ?>" <?php echo $readonly ?> /></td>
            <td><p></p></td>
        </tr>
        <tr>
            <th><label for="content"><?php echo Flux::message('LcmsNContent') ?></label></th>
            <td>
                <textarea class="ckeditor" name="content" id="content" <?php echo $disabled ?>>
                    <?php echo htmlspecialchars($content_res[0]->content) ?>
                </textarea>
                <script type="text/javascript">
                    CKEDITOR.config.contentsCss = '<?php echo $this->themePath('css/flux.css') ?>';
                </script>
            </td>
            <td><p></p></td>
        </tr>
        <tr>
            <th></th>
            <td>
                <button title='<?php echo Flux::message('LcmsN' . ucfirst($tsk)) ?> <?php echo Flux::message('LcmsTypePage') ?>' name='tsk' value='page;do<?php echo $tsk ?>;<?php echo htmlspecialchars($content_res[0]->id) ?>' style='background:none;border:none;cursor:pointer'>
                    <?php echo Flux::message('LcmsN' . ucfirst($tsk)) ?>
                </button>
                <button title='<?php echo Flux::message('LcmsNCancel') ?> <?php echo Flux::message('LcmsTypePage') ?>' name='tsk' type="button" onclick="history.go(-1)" style='background:none;border:none;cursor:pointer'>
                    <?php echo Flux::message('LcmsNCancel') ?>
                </button>
            </td>
        </tr>
    </table>
</form>
