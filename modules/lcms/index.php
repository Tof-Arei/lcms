<?php if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('LcmsMTitle');

$lcms = new Lcms_Functions($session);
$author = $lcms->getAuthor($session->account->account_id);
$page_res = $lcms->getPagesPaginator($this, null, $author, null);
$module_res = $lcms->getAuthorModules(null, $author->access);

$page_level = Flux::config('LcmsCreatePageMinLevel');
?>
