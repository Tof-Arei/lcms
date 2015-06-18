<?php if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('LcmsMTitle');

// Get the instanciating parameters
$param = explode(';', (String) $params->get('tsk'));
$type = (String) $param[0];
$tsk = (String) $param[1];
$content_ids = explode(":", $param[2]);

$lcms = new Lcms_Functions($session);
// Get author and Hercules/FluxCP group_id
$author = $lcms->getAuthor($session->account->account_id);
$group_id = $session->account->group_id;

// Check action parameters and author existance (in case somebody "hacks" his way to the form pages)
$errorMessage = "";
if ($param[2] == '') {
    $errorMessage = sprintf(Flux::message('LcmsMesEEmpty'), ucfirst($type));
}
if (is_null($author)) {
    $errorMessage .= Flux::message('LcmsMesEAccess');
}

// If no author/param error(s) :
if (empty($errorMessage)) {
    // Grab content(s) from the database according to the parameters and author access
    $content_res = array();
    foreach ($content_ids as $content_id) {
        $content = call_user_func_array(array($lcms, 'get'.ucfirst($type)), array($content_id, $author->access));
        if ($content) {
            $content_res[] = $content;
        }

    }

    // If adding a new element, instantiate an empty DAO with default values since there are no data to retrieve
    if (count($content_res) == 0) {
        $content_res[] = new Lcms_DAO(null, null, $type, $session);
    }

    // do<action> block when actually doing something to the data
    $results = array();
    switch ($tsk) {
        case "doadd" :
            $dao = new Lcms_DAO(null, $_POST, $type, $session);
            $results[] = call_user_func_array(array($lcms, 'add'.ucfirst($type)), array($dao));
            break;
        case "doupdate" :
            $dao = new Lcms_DAO(null, $_POST, $type, $session);
            $results[] = call_user_func_array(array($lcms, 'update'.ucfirst($type)), array($dao));
            break;
        case "dodelete" :
            foreach ($content_res as $content) {
                $dao = new Lcms_DAO($content, null, $type, $session);
                $results[] = call_user_func_array(array($lcms, 'delete'.ucfirst($type)), array($dao));
            }
            break;
        case "dovalidate" :
            foreach ($content_res as $content) {
                if ($content->status == Lcms_Functions::$PAGE_STATUS_PENDING) {
                    $page_dao = new Lcms_DAO($content, null, $type, $session);
                    $page_dao->status = Lcms_Functions::$PAGE_STATUS_VALID;
                    $results[] = $lcms->updatePage($page_dao);
                }
            }
            break;
    }
    
    // Parse results and print result/error messages to the user
    $resultMessage = "";
    if (count($results) != 0) {
        foreach ($results as $result) {
            if ($result['result'] == 'success') {
                //$metaRefresh = array('seconds' => 2, 'location' => $this->basePath . "?module=lcms");
                $resultMessage .= sprintf(Flux::message('LcmsMes' . ucfirst(substr($tsk, 2)) . ucfirst($type)), $result['name']) . "\r\n";
            } else if ($result['result'] == 'failed') {
                $errorMessage .= sprintf(Flux::message('LcmsMesE' . ucfirst(substr($tsk, 2)) . ucfirst($type)), $result['name']) . "\r\n";
                foreach ($result['messages'] as $message) {
                    $errorMessage .= "\r\n" . $message;
                }
            }
        }
    }

    $form = Lcms_Functions::getPagePath("$type.form.php");
    if ($form === null) {
        $errorMessage = Flux::message('LcmsMesE404');
    }
}
?>