<?php if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('LcmsMTitle');

$lcms = new Lcms_Functions($session);
$author = $lcms->getAuthor($session->account->account_id);
$module_res = null;
$author_res = null;
// Bypass author filter if the current user is an Admin
if ($session->account->group_id == AccountLevel::ADMIN) {
    $module_res = $lcms->getModulesPaginator($this);
} else {
    $module_res = $lcms->getModulesPaginator($this, null, $author->access);
}

$module_level = Flux::config('LcmsCreateModuleMinLevel');
?>
