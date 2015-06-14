<?php
$lcms = new Lcms_Functions($session);
$page_id = (int) $params->get('id');
$page = $lcms->getPage($page_id);
$page_res = null;

if ($page) {
    if ($page->access <= $session->account->group_id) {
        $module = $lcms->getModule($page->module_id);
        $page_res = $lcms->getModulePages($module, true);
        $title = sprintf(Flux::message('LcmsShowTitle'), $page->name);
    } else {
        $errorMessage = Flux::message('LcmsMesEAccess');
    }
} else {
    $errorMessage = Flux::message('LcmsMesE404');
}
?>