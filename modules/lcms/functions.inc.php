<?php
require_once FLUX_ROOT . '/' . FLUX_ADDON_DIR . '/lcms/lib/htmlpurifier/library/HTMLPurifier.auto.php';

/*
 * Big fat ugly class to handle LCMS specific data and avoid some code repetition
 * Sorry if my Java is showing too much ^^'
 */
class Lcms_Functions {
    public static $VERSION = "1.0b";
    
    public static $PAGE_STATUS_PENDING = -1;
    public static $PAGE_STATUS_VALID = 0;
    private static $PAGE_STATUS = array();
    
    public $paginator;
    
    private $session;
    private $server;
    
    public function __construct($session) {
        $this->session = $session;
        $this->server = $this->session->getAthenaServer();
        self::$PAGE_STATUS[self::$PAGE_STATUS_PENDING] = Flux::message('LcmsStatPending');
        self::$PAGE_STATUS[self::$PAGE_STATUS_VALID] = Flux::message('LcmsStatValid');
    }
    
    public function getAccounts() {
        $account_tbl = 'login';
        $server = $this->server;
        $tableName = "$server->loginDatabase.$account_tbl";

        $cols = "account_id, user_id";
        $bind = array();
        $sql = "SELECT $cols FROM $tableName";
        $sth = $server->connection->getStatement($sql);
        $sth->execute($bind);
        
        return $sth->fetchAll();
    }
    
    public function getHerculesGroupName($group_id) {
        $groups = AccountLevel::getArray();
        return $groups[$group_id]['name'];
    }
    
    public function getAuthorName($account_id) {
        $server = $this->server;
        $tableName = "$server->loginDatabase.login";
        
        $cols = "userid";
        $bind = array(intval($account_id));
        $sql = "SELECT $cols FROM $tableName WHERE account_id = ?";
        $sth = $server->connection->getStatement($sql);
        $sth->execute($bind);
        
        $account_res = $sth->fetch();
        if ($sth->rowCount() === 0) {
            $account_res = null;
        }

        return !is_null($account_res) ? $account_res->userid : Flux::message('LcmsMesEUnknownAuthor');
    }
    
    public function getAuthorsPaginator($template, $author = null, $access = null) {
        return $this->getAuthors($author, $access, $template);
    }
    
    private function getAuthors($author, $access = null, $template = null) {
        $author_tbl = Flux::config('FluxTables.lcms_author');
        $server = $this->server;
        $tableName = "$server->loginDatabase.$author_tbl";
        
        $bind = array();
        $sqlpartial = "";
        if (!is_null($access)) {
            $bind[] = intval($access);
            if (is_null($author)) {
                $sqlpartial = "INNER JOIN $server->loginDatabase.cp_lcms_author ON $server->loginDatabase.cp_lcms_author.account_id = $tableName.account_id";
                $sqlpartial .= " WHERE $server->loginDatabase.cp_lcms_author.access <= ?";
            } else {
                $sqlpartial = "WHERE access <= ?";
            }
        }
        
        if ($author != null) {
            if (is_null($access)) {
                $bind[] = intval($author->account_id);
                if ($sqlpartial != "") {
                    $sqlpartial .= "AND $tableName.account_id = ?";
                } else {
                    $sqlpartial .= "WHERE account_id = ?";
                }
            }
        }
        
        $cols = "$tableName.account_id, $tableName.access";
        $sth = null;
        if (!is_null($template)) {
            $sth = $server->connection->getStatement("SELECT COUNT(account_id) AS total FROM $tableName $sqlpartial");
            $sth->execute($bind);

            Flux::config('ResultsPerPage', Flux::config('LcmsAuthorsPerPage'));
            $this->paginator = $template->getPaginator($sth->fetch()->total);
            $this->paginator->setSortableColumns(array('account_id' => 'asc', 'access'));

            $sql  = $this->paginator->getSQL("SELECT $cols FROM $tableName $sqlpartial");
            $sth  = $server->connection->getStatement($sql);
            $sth->execute($bind);
        } else {
            $sql = "SELECT $cols FROM $tableName $sqlpartial";
            $sth = $server->connection->getStatement($sql);
            $sth->execute($bind);
        }
        
        return $sth->fetchAll();
    }
    
    public function getAuthor($account_id) {
        $author_tbl = Flux::config('FluxTables.lcms_author');
        $server = $this->server;
        $tableName = "$server->loginDatabase.$author_tbl";

        $cols = "$tableName.account_id, $tableName.access";
        $bind = array($account_id);
        $sqlpartial = "WHERE account_id = ?";
        $sql = "SELECT $cols FROM $tableName $sqlpartial";
        $sth = $this->server->connection->getStatement($sql);
        $sth->execute($bind);

        return $sth->fetch();
    }
    
    public function addAuthor($author_dao) {
        $validation = $author_dao->validate();
        if ($validation['result'] == 'success') {
            $author_tbl = Flux::config('FluxTables.lcms_author');
            $server = $this->server;
            $tableName = "$server->loginDatabase.$author_tbl";

            $cols = "account_id, access";
            $bind = array($author_dao->account_id, $author_dao->access);
            $sql = "INSERT INTO $tableName ($cols) VALUES(?, ?)";

            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($bind);
        }
        return $validation;
    }
    
    public function updateAuthor($author_dao) {
        $validation = $author_dao->validate();
        if ($validation['result'] == 'success') {
            $author_tbl = Flux::config('FluxTables.lcms_author');
            $server = $this->server;
            $tableName = "$server->loginDatabase.$author_tbl";

            $cols = "access = ?";
            $bind = array($author_dao->access, $author_dao->account_id);
            $sql = "UPDATE $tableName SET $cols WHERE account_id = ?";

            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($bind);
        }
        return $validation;
    }
    
    public function deleteAuthor($author_dao) {
        $validation = $author_dao->validate();
        if ($validation['result'] == 'success') {
            if ($author_dao->isDeletable()) {
                $author_tbl = Flux::config('FluxTables.lcms_author');
                $server = $this->server;
                $tableName = "$server->loginDatabase.$author_tbl";

                $bind = array($author_dao->account_id);
                $sql = "DELETE FROM $tableName WHERE account_id = ?";

                $sth = $this->server->connection->getStatement($sql);
                $sth->execute($bind);
            } else {
                $validation['result'] = 'failed';
            }
        }
        return $validation;
    }
    
    public function getModuleName($module_id) {
        $module_tbl = Flux::config('FluxTables.lcms_module');
        $server = $this->server;
        $tableName = "$server->loginDatabase.$module_tbl";
        
        $cols = "name";
        $bind = array(intval($module_id));
        $sql = "SELECT $cols FROM $tableName WHERE id = ?";
        $sth = $server->connection->getStatement($sql);
        $sth->execute($bind);
        
        $module_res = $sth->fetch();
        if ($sth->rowCount() === 0) {
            $module_res = null;
        }

        return !is_null($module_res) ? $module_res->name : Flux::message('LcmsMesEUnknownModule');
    }
    
    public function getModulesPaginator($template, $author = null, $access = null) {
        return $this->getModules($author, $access, $template);
    }
    
    private function getModules($author = null, $access = null, $template = null) {
        $module_tbl = Flux::config('FluxTables.lcms_module');
        $server = $this->server;
        $tableName = "$server->loginDatabase.$module_tbl";

        $bind = array();
        $sqlpartial = "";
        if (!is_null($access)) {
            $bind[] = intval($access);
            if (is_null($author)) {
                $sqlpartial = "INNER JOIN $server->loginDatabase.cp_lcms_author ON $server->loginDatabase.cp_lcms_author.account_id = $tableName.account_id";
                $sqlpartial .= " WHERE $server->loginDatabase.cp_lcms_author.access <= ?";
            } else {
                $sqlpartial = "WHERE access <= ?";
            }
        }
        
        if ($author != null) {
            if (is_null($access)) {
                $bind[] = intval($author->account_id);
                if ($sqlpartial != "") {
                    $sqlpartial .= "AND $tableName.account_id = ?";
                } else {
                    $sqlpartial .= "WHERE account_id = ?";
                }
            }
        }
        
        $cols = "$tableName.id, $tableName.account_id, $tableName.access, $tableName.name";
        $sth = null;
        if (!is_null($template)) {
            $sth = $server->connection->getStatement("SELECT COUNT(id) AS total FROM $tableName $sqlpartial");
            $sth->execute($bind);

            Flux::config('ResultsPerPage', Flux::config('LcmsModulesPerPage'));
            $this->paginator = $template->getPaginator($sth->fetch()->total);
            $this->paginator->setSortableColumns(array('id' => 'asc', 'access', 'name'));

            $sql  = $this->paginator->getSQL("SELECT $cols FROM $tableName $sqlpartial");
            $sth  = $server->connection->getStatement($sql);
            $sth->execute($bind);
        } else {
            $sql = "SELECT $cols FROM $tableName $sqlpartial";
            $sth = $server->connection->getStatement($sql);
            $sth->execute($bind);
        }

        return $sth->fetchAll();
    }
    
    
    
    public function getModule($module_id) {
        $module_tbl = Flux::config('FluxTables.lcms_module');
        $server = $this->server;
        $tableName = "$server->loginDatabase.$module_tbl";

        $cols = "$tableName.id, $tableName.account_id, $tableName.access, $tableName.name";
        $bind = array($module_id);
        $sqlpartial = "WHERE id = ?";
        $sql = "SELECT $cols FROM $tableName $sqlpartial";
        $sth = $this->server->connection->getStatement($sql);
        $sth->execute($bind);

        return $sth->fetch();
    }
    
    public function addModule($module_dao) {
        $validation = $module_dao->validate();
        if ($validation['result'] == 'success') {
            $module_tbl = Flux::config('FluxTables.lcms_module');
            $server = $this->server;
            $tableName = "$server->loginDatabase.$module_tbl";

            $cols = "account_id, access, name";
            $bind = array($module_dao->account_id, $module_dao->access, $module_dao->name);
            $sql = "INSERT INTO $tableName ($cols) VALUES(?, ?, ?)";

            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($bind);
        }
        return $validation;
    }
    
    public function updateModule($module_dao) {
        $validation = $module_dao->validate();
        if ($validation['result'] == 'success') {
            $module_tbl = Flux::config('FluxTables.lcms_module');
            $server = $this->server;
            $tableName = "$server->loginDatabase.$module_tbl";

            $cols = "account_id = ?, access = ?, name = ?";
            $bind = array($module_dao->account_id, $module_dao->access, $module_dao->name, $module_dao->id);
            $sql = "UPDATE $tableName SET $cols WHERE id = ?";

            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($bind);
        }
        return $validation;
    }
    
    public function deleteModule($module_dao) {
        $validation = $module_dao->validate();
        if ($validation['result'] == 'success') {
            if ($module_dao->isDeletable()) {
                $module_tbl = Flux::config('FluxTables.lcms_module');
                $server = $this->server;
                $tableName = "$server->loginDatabase.$module_tbl";

                $bind = array($module_dao->id);
                $sql = "DELETE FROM $tableName WHERE id = ?";

                $sth = $this->server->connection->getStatement($sql);
                $sth->execute($bind);
            } else {
                $validation['result'] = 'failed';
            }
        }
        return $validation;
    }
    
    public function getAuthorAuthors($author, $access = null) {
        return $this->getAuthors($author, $access);
    }
    
    public function getAuthorModules($author, $access = null) {
        return $this->getModules($author, $access);
    }
    
    public function getAuthorPages($author, $access = null) {
        return $this->getPages(null, $author, $access);
    }
    
    public function getModulePages($module, $access = null) {
        return $this->getPages($module, null, $access);
    }
    
    public function getPagesPaginator($template, $module = null, $author = null, $access = null) {
        return $this->getPages($module, $author, $access, $template);
    }
    
    private function getPages($module = null, $author = null, $access = null, $template = null) {
        $page_tbl = Flux::config('FluxTables.lcms_page');
        $server = $this->server;
        $tableName = "$server->loginDatabase.$page_tbl";

        $bind = array();
        $sqlpartial = "";
        if (!is_null($access)) {
            $bind[] = intval($access);
            if (!is_null($author)) {
                $sqlpartial = "INNER JOIN $server->loginDatabase.cp_lcms_author ON $server->loginDatabase.cp_lcms_author.account_id = $tableName.account_id";
                $sqlpartial .= " WHERE $server->loginDatabase.cp_lcms_author.access <= ?";
            } else {
                $sqlpartial = "WHERE access <= ?";
            }
        }
        
        if ($module != null) {
            $bind[] = intval($module->id);
            if ($sqlpartial != "") {
                $sqlpartial .= "AND $tableName.module_id = ?";
            } else {
                $sqlpartial .= "WHERE module_id = ?";
            }
        } else if ($author != null) {
            if (is_null($access)) {
                $bind[] = intval($author->account_id);
                if ($sqlpartial != "") {
                    $sqlpartial .= "AND $tableName.account_id = ?";
                } else {
                    $sqlpartial .= "WHERE account_id = ?";
                }
            }
        }
        
        $cols = "$tableName.id, $tableName.module_id, $tableName.account_id, $tableName.status, $tableName.access, $tableName.date, $tableName.name, $tableName.content";
        $sth = null;
        if (!is_null($template)) {
            $sth = $server->connection->getStatement("SELECT COUNT(id) AS total FROM $tableName $sqlpartial");
            $sth->execute($bind);

            Flux::config('ResultsPerPage', Flux::config('LcmsPagesPerPage'));
            $this->paginator = $template->getPaginator($sth->fetch()->total);
            $this->paginator->setSortableColumns(array('id' => 'asc', 'access', 'date', 'name', 'status'));

            $sql  = $this->paginator->getSQL("SELECT $cols FROM $tableName $sqlpartial");
            $sth  = $server->connection->getStatement($sql);
            $sth->execute($bind);
        } else {
            $sql = "SELECT $cols FROM $tableName $sqlpartial";
            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($bind);
        }

        return $sth->fetchAll();
    }
    
    public function getPage($page_id) {
        $page_tbl = Flux::config('FluxTables.lcms_page');
        $server = $this->server;
        $tableName = "$server->loginDatabase.$page_tbl";

        $cols = "$tableName.id, $tableName.module_id, $tableName.account_id, $tableName.status, $tableName.access, $tableName.date, $tableName.name, $tableName.content";
        $bind = array($page_id);
        $sqlpartial = "WHERE id = ?";
        $sql = "SELECT $cols FROM $tableName $sqlpartial";
        $sth = $this->server->connection->getStatement($sql);
        $sth->execute($bind);

        return $sth->fetch();
    }
    
    public function addPage($page_dao) {
        $validation = $page_dao->validate();
        if ($validation['result'] == 'success') {
            $page_tbl = Flux::config('FluxTables.lcms_page');
            $server = $this->server;
            $tableName = "$server->loginDatabase.$page_tbl";

            $cols = "module_id, account_id, status, access, date, name, content";
            $bind = array($page_dao->module_id, $page_dao->account_id, $page_dao->status, $page_dao->access, $page_dao->date, $page_dao->name, $this->cleanHTML($page_dao->content));
            $sql = "INSERT INTO $tableName ($cols) VALUES(?, ?, ?, ?, ?, ?, ?)";

            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($bind);
        }
        return $validation;
    }
    
    public function updatePage($page_dao) {
        $validation = $page_dao->validate();
        if ($validation['result'] == 'success') {
            $page_tbl = Flux::config('FluxTables.lcms_page');
            $server = $this->server;
            $tableName = "$server->loginDatabase.$page_tbl";

            $cols = "module_id = ?, account_id = ?, status = ?, access = ?, date = ?, name = ?, content = ?";
            $bind = array($page_dao->module_id, $page_dao->account_id, $page_dao->status, $page_dao->access, $page_dao->date, $page_dao->name, $this->cleanHTML($page_dao->content), $page_dao->id);
            $sql = "UPDATE $tableName SET $cols WHERE id = ?";

            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($bind);
        }
        return $validation;
    }
    
    public function deletePage($page_dao) {
        $validation = $page_dao->validate();
        if ($validation['result'] == 'success') {
            if ($page_dao->isDeletable()) {
                $page_tbl = Flux::config('FluxTables.lcms_page');
                $server = $this->server;
                $tableName = "$server->loginDatabase.$page_tbl";

                $bind = array($page_dao->id);
                $sql = "DELETE FROM $tableName WHERE id = ?";

                $sth = $this->server->connection->getStatement($sql);
                $sth->execute($bind);
            } else {
                $validation['result'] = 'failed';
            }
        }
        return $validation;
    }
    
    private function cleanHTML($dirty_html) {
        $config = HTMLPurifier_Config::createDefault();
        if (!Flux::config('LcmsEnableHTMLPurifierCache')) {
            $config->set('Cache.DefinitionImpl', null);
        }
        $purifier = new HTMLPurifier($config);
        return $purifier->purify($dirty_html);
    }
    
    /*
     * Function to retrieve the form absolute url before including it
     * This function also acts as an XSS exploit protection
     */
    public static function getPagePath($page) {
        $ret = null;
        
        $page_path = FLUX_ROOT.'/'.FLUX_ADDON_DIR.'/lcms/themes/default/lcms/'.$page;
        if(preg_match('/^[a-zA-Z0-9_\.]+$/', $page) && file_exists($page_path)) {
            $ret = $page_path;
        }
        
        return $ret;
    }
    
    public static function getStatusName($status_id) {
        return self::$PAGE_STATUS[$status_id];
    }
    
    /*
     * Function to retrieve LCMS menu according to the Hercules/FluxCP permissions 
     * and dynamically inject them into FluxCP getMenuItems().
     * @param array ($allowedItems, $template)
     * @return array ($allowedItems + LCMS allowed menu items)
     */
    public function getLcmsMenuItems($allowedItems, $template) { 
        $module_res = $this->getModules();

        if (count($module_res) !== 0) {
            foreach ($module_res as $module) {
                $group_id = (!is_null($this->session->account->group_id)) ? $this->session->account->group_id : AccountLevel::UNAUTH;
                $page_res = $this->getModulePages($module, $group_id);

                if (count($page_res) !== 0) {
                    foreach ($page_res as $page) {
                        if (empty($allowedItems[$module->name])) {
                                $allowedItems[$module->name] = array();
                        }
                        if (Flux::config('LcmsValidationEnable') && $page->status != Lcms_Functions::$PAGE_STATUS_PENDING) {
                            $allowedItems[$module->name][] = array(
                                    'name'   => $page->name,
                                    'exturl' => null,
                                    'module' => 'lcms',
                                    'action' => 'show',
                                    'url'    => $template->url('lcms', "show&id=$page->id")
                            );
                        }
                    }
                }
            }
        }

        return $allowedItems;
    }
}

/*
 * DAO-like class to handle inserts/updates
 */
class Lcms_DAO {
    private $object;
    private $post;
    private $type;
    private $session;
    private $lcms;
    private $lcms_config;
    
    public function __construct($object, $post, $type, $session) {
        $this->object = $object;
        $this->post = $post;
        $this->type = $type;
        
        $this->lcms_config = $lcms_config = Flux::config("LcmsValues.$type");
        $this->session = $session;
        $this->lcms = new Lcms_Functions($this->session);
        
        $this->populate();
    }
    
    private function populate() {
        if (!is_null($this->object)) {
            foreach ($this->lcms_config->toArray() as $key => $value) {
                $field_type = $this->lcms_config->get($key, false);
                if ($field_type != null) {
                    $prop = $this->object->__get($key);
                    if (!is_null($prop)) {
                        $this->$key = $prop;
                        settype($this->$key, $field_type[0]);
                    } else {
                        $this->$key = null;
                    }
                }
            }
        } else if (!is_null($this->post)) {
            foreach ($this->lcms_config->toArray() as $key => $value) {
                $field_type = $this->lcms_config->get($key, false);
                if ($field_type != null) {
                    if (array_key_exists($key, $this->post)) {
                        $this->$key = $this->post[$key];
                        settype($this->$key, $field_type[0]);
                    } else {
                        $this->$key = null;
                    }
                }
            }
        } else {
            foreach ($this->lcms_config->toArray() as $key => $field_type) {
                if (count($field_type) > 3) {
                    switch ($field_type[3]) {
                        case "now()" :
                            $this->$key = date('Y-m-d H:i:s');
                            break;
                        case "author()" :
                            $this->$key = $this->session->account->account_id;
                            break;
                        default:
                            $this->$key = $field_type[3];
                    }
                } else {
                    $this->$key = null;
                }
            }
        }
    }
    
    public function validate() {
        $ret = array(
            'result'   => 'success',
            'messages' => array()
        );
        
        foreach($this as $key => $value) {
            $field_type = $this->lcms_config->get($key, false);
            if ($field_type != null) {
                if (!$this->validateField($field_type, $value)) {
                    $ret['result'] = 'failed';
                    $not_null = (count($field_type) > 2) ? $field_type[2] : "";
                    $strlen_val = ($field_type[0] == 'string') ? 'null' : strlen($value);
                    $ret['messages'][] = sprintf(Flux::message('LcmsMesEType'), $key, "$field_type[0]($field_type[1]) $not_null", gettype($value)."(".$strlen_val.")");
                }
            }
        }
        return $ret;
    }
    
    private function validateField($field_type, $value) {
        $ret = true;
        switch ($field_type[0]) {
            case "int" :
                if (!is_int($value)) {
                    $ret = false;
                }
                break;
            case "string" :
                if (!is_string($value)) {
                    $ret = false;
                }
                break;
        }
        if (!is_null($field_type[1])) {
            if ($field_type[1] == 'DateTime') {
                if (!$this->isDateValid($value)) {
                    $ret = false;
                }
            } else {
                if (strlen($value) > (int) $field_type[1]) {
                    $ret = false;
                }
            }
        }
        if (!is_null($field_type[2])) {
            if (is_null($value)) {
                $ret = false;
            } else if ($field_type[0] == 'string' && empty($value)) {
                $ret = false;
            }
        }
        return $ret;
    }
    
    private function isDateValid($date) {
        $df = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        return $df && $df->format('Y-m-d H:i:s') == $date;
    }
    
    public function isDeletable() {
        $ret = true;
        
        $module_res = null;
        $page_res = null;
        switch ($this->type) {
            case "module" :
                $page_res = $this->lcms->getModulePages($this);
                break;
            case "author" :
                $page_res = $this->lcms->getAuthorPages($this);
                $module_res = $this->lcms->getAuthorModules($this);
                break;
        }
        
        if (count($page_res) > 0 || count($module_res) > 0) {
            $ret = false;
        }
        return $ret;
    }
}
?>

