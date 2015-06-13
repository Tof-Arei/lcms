<?php if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('LcmsMTitle');

$param = explode(';', (String) $params->get('tsk'));
$type = (String) $param[0];
$tsk = (String) $param[1];
$content_id = (int) $param[2];

$lcms = new Lcms_Functions($session);
// Bypass author access filter if the current user is an Hercules/FluxCP Admin
// (Allows the Hercules/FluxCP admin to *actually* manage LCMS admins' stuff)
//$author = ($session->account->group_id != AccountLevel::ADMIN) ? $lcms->getAuthor($session->account->account_id) : null;
$author = $lcms->getAuthor($session->account->account_id);

switch ($type) {
    case "module" :
        $content_res = $lcms->getModule($content_id);
        break;
    case "page" :
        $content_res = $lcms->getPage($content_id);
        break;
    case "author" :
        $content_res = $lcms->getAuthor($content_id);
        break;
}
if (!$content_res) {
    $content_res = new Lcms_DAO(null, null, $type, $session);
}

$result = null;
switch ($tsk) {
    case "doadd" :
        $dao = new Lcms_DAO(null, $_POST, $type, $session);
        $result = call_user_func_array(array($lcms, 'add'.ucfirst($type)), array($dao));
        break;
    case "doupdate" :
        $dao = new Lcms_DAO(null, $_POST, $type, $session);
        $result = call_user_func_array(array($lcms, 'update'.ucfirst($type)), array($dao));
        break;
    case "dodelete" :
        $dao = new Lcms_DAO($content_res, null, $type, $session);
        $result = call_user_func_array(array($lcms, 'delete'.ucfirst($type)), array($dao));
        break;
    case "dovalidate" :
        
        break;
}

$resultMessage = null;
if (!is_null($result)) {
    if ($result['result'] == 'success') {
        $metaRefresh = array('seconds' => 2, 'location' => $this->basePath . "?module=lcms");
        $resultMessage = Flux::message('LcmsMes' . ucfirst(substr($tsk, 2)) . ucfirst($type));
    } else if ($result['result'] == 'failed') {
        $errorMessage = Flux::message('LcmsMesE' . ucfirst(substr($tsk, 2)) . ucfirst($type));
        foreach ($result['messages'] as $message) {
            $errorMessage .= "\r" . $message;
        }
    }
}

$form = Lcms_Functions::getPagePath("$type.form.php");
if ($form === null) {
    $errorMessage = Flux::message('LcmsMesE404');
}
?>