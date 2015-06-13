<?php
$title = Flux::message('LcmsShowTitle'); //Must be dynamic

$lcms = new Lcms_Functions($session);
$page_id = (int) $params->get('id');
$page = $lcms->getPage($page_id, null, true);

if (!is_null($page)) {
    $module = $lcms->getModule($page->module_id);
    $page_res = $lcms->getModulePages($module, true);
} else {
    $errorMessage = Flux::message('LcmsMesEAccess');
}
?>