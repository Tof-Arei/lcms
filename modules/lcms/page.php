<?php if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('LcmsMTitle');

$lcms = new Lcms_Functions($session);
$author = $lcms->getAuthor($session->account->account_id);
$page_res = null;
$author_res = null;
// Bypass author filter if the current user is an Admin
if ($session->account->group_id == AccountLevel::ADMIN) {
    $page_res = $lcms->getPagesPaginator($this);
    $module_res = $lcms->getAuthorModules(null);
} else {
    $page_res = $lcms->getPagesPaginator($this, null, $author, $author->access);
    $module_res = $lcms->getAuthorModules($author, $author->access);
}

$page_level = Flux::config('LcmsCreatePageMinLevel');
?>
