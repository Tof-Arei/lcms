<?php
$lcms = new Lcms_Functions($session);
$page_id = (int) $params->get('id');
$page = $lcms->getPage($page_id);
$page_res = null;

$errorMessage = null;
if ($page) {
    $display = true;
    if (Flux::config('LcmsValidationEnable')) {
        if ($page->status != Lcms_Functions::$PAGE_STATUS_VALID) {
            $display = false;
        }
    }
    if ($display) {
        $group_id = (!is_null($session->account->group_id)) ? $session->account->group_id : -1;
        if ($page->access <= $group_id) {
            $module = $lcms->getModule($page->module_id);
            $page_res = $lcms->getModulePages($module, true);
            $title = sprintf(Flux::message('LcmsShowTitle'), $page->name);
        } else {
            $errorMessage = Flux::message('LcmsMesEAccess');
        }
    } else {
        $errorMessage = Flux::message('LcmsMesE404');
    }
} else {
    $errorMessage = Flux::message('LcmsMesE404');
}
if (!is_null($errorMessage)) {
    $metaRefresh = array('seconds' => 2, 'location' => $this->basePath);
}
?>