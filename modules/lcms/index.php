<?php if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('LcmsMTitle');

$lcms = new Lcms_Functions($session);
$author = $lcms->getAuthor($session->account->account_id);
$page_res = $lcms->getPagesPaginator($this, null, $author, true);
$module_res = $lcms->getAuthorModules(null, true);

$page_level = Flux::config('LcmsCreatePageMinLevel');
?>
