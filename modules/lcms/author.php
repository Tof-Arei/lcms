<?php if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('LcmsMTitle');

$lcms = new Lcms_Functions($session);
$author = $lcms->getAuthor($session->account->account_id);
$author_res = null;
// Bypass author filter if the current user is an Admin
if ($session->account->group_id == AccountLevel::ADMIN) {
    $author_res = $lcms->getAuthorsPaginator($this);
} else {
    $author_res = $lcms->getAuthorsPaginator($this, $author->access);
}

$access = $session->account->group_id;
if ($author) {
    if ($author->access < AccountLevel::ADMIN) {
        $acces = $author->access;
    }
}
$author_level = Flux::config('LcmsCreateAuthorMinLevel');
?>