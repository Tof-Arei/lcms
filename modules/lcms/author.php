<?php if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('LcmsMTitle');

$lcms = new Lcms_Functions($session);
$author = $lcms->getAuthor($session->account->account_id);
$author_res = null;
// Bypass access filter if the current user is an Admin on Hercules/FluxCP
if ($session->account->group_id == AccountLevel::ADMIN) {
    $author_res = $lcms->getAuthorsPaginator($this);
} else {
    $author_res = $lcms->getAuthorsPaginator($this, $author->access);
}
?>