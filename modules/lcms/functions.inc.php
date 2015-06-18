<?php
// HTMLPurifier import
require_once FLUX_ROOT . '/' . FLUX_ADDON_DIR . '/lcms/lib/htmlpurifier/library/HTMLPurifier.auto.php';
// Note: CKEditor is imported dynamically by lcms/themes/default/lcms/page.form.php to avoid edition of (multiple) FluxCP/theme/<theme>/header.php during installation

/*
 * Big fat ugly class, aka personal clusterfuck to handle LCMS specific data and avoid some code repetition
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
    
    /*
     * get Hercules accounts list
     * @param access
     * @return array
     */
    public function getAccounts($access = null) {
        $account_tbl = Flux::config('FluxTables.login');
        $db = $this->server->loginDatabase;

        $cols = "$db.$account_tbl.account_id, $db.$account_tbl.group_id, $db.$account_tbl.userid";
        $bind = array();
        $sqlpartial = "";
        if (!is_null($access)) {
            $bind[] = $access;
            $sqlpartial = "WHERE $db.$account_tbl.group_id <= ?";
        }
        $sql = "SELECT $cols FROM $db.$account_tbl $sqlpartial";
        $sth = $this->server->connection->getStatement($sql);
        $sth->execute($bind);
        
        return $sth->fetchAll();
    }
    
    /*
     * get Hercules account
     * @param account_id
     * @return Hercules account
     */
    public function getAccount($account_id, $access = null) {
        $account_tbl = Flux::config('FluxTables.login');
        $db = $this->server->loginDatabase;

        $cols = "$db.$account_tbl.account_id, $db.$account_tbl.group_id, $db.$account_tbl.userid";
        $bind = array($account_id);
        $sqlpartial = "WHERE $db.$account_tbl.account_id = ?";
        if (!is_null($access)){
            $bind[] = $access;
            $sqlpartial .= " AND $db.$account_tbl.access <= ?";
        }
        $sql = "SELECT $cols FROM $db.$account_tbl $sqlpartial";
        $sth = $this->server->connection->getStatement($sql);
        $sth->execute($bind);

        return $sth->fetch();
    }
    
    /*
     * get Hercules group name
     * @param Hercules group_id
     * @return Hercules group name
     */
    public function getHerculesGroupName($group_id) {
        $groups = AccountLevel::getArray();
        if ($group_id >= 0) {
            return $groups[$group_id]['name'];
        } else {
            return "N/A";
        }
    }
    
    /*
     * get LCMS authors paginator
     * @param FluxCP template
     * @param access
     * @return LCMS authors paginator
     */
    public function getAuthorsPaginator($template, $access = null) {
        return $this->getAuthors($access, $template);
    }
    
    /*
     * get LCMS authors
     * @param access
     * @param FluxCP template
     * @return LCMS authors
     */
    private function getAuthors($access = null, $template = null) {
        $author_tbl = Flux::config('FluxTables.lcms_author');
        $login_tbl = Flux::config('FluxTables.login');
        $db = $this->server->loginDatabase;
        
        $cols = "$db.$author_tbl.account_id, $db.$author_tbl.access";
        $cols .= ", $db.$login_tbl.userid";
        $login_join = "LEFT JOIN $db.$login_tbl ON $db.$login_tbl.account_id = $db.$author_tbl.account_id";
        $bind = array();
        $sqlpartial = "";
        if (!is_null($access)) {
            $bind[] = intval($access);
            $sqlpartial = "WHERE $db.$author_tbl.access <= ?";
        }
        
        $sth = null;
        if (!is_null($template)) {
            $sth = $this->server->connection->getStatement("SELECT COUNT($db.$author_tbl.account_id) AS total FROM $db.$author_tbl $sqlpartial");
            $sth->execute($bind);

            Flux::config('ResultsPerPage', Flux::config('LcmsAuthorsPerPage'));
            $this->paginator = $template->getPaginator($sth->fetch()->total);
            $this->paginator->setSortableColumns(array("$author_tbl.account_id" => "asc", "$author_tbl.access"));

            $sql  = $this->paginator->getSQL("SELECT $cols FROM $db.$author_tbl $login_join $sqlpartial");
            $sth  = $this->server->connection->getStatement($sql);
            $sth->execute($bind);
        } else {
            $sql = "SELECT $cols FROM $db.$author_tbl $login_join $sqlpartial";
            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($bind);
        }
        
        return $sth->fetchAll();
    }
    
    /*
     * get LCMS author
     * @param account_id
     * @param access
     * @return LCMS author
     */
    public function getAuthor($account_id, $access = null) {
        $author_tbl = Flux::config('FluxTables.lcms_author');
        $login_tbl = Flux::config('FluxTables.login');
        $db = $this->server->loginDatabase;

        $cols = "$db.$author_tbl.account_id, $db.$author_tbl.access";
        $cols .= ", $db.$login_tbl.userid";
        $login_join = "LEFT JOIN $db.$login_tbl ON $db.$login_tbl.account_id = $db.$author_tbl.account_id";
        $bind = array($account_id);
        $sqlpartial = "WHERE $db.$author_tbl.account_id = ?";
        if (!is_null($access)){
            $bind[] = $access;
            $sqlpartial .= " AND $db.$author_tbl.access <= ?";
        }
        $sql = "SELECT $cols FROM $db.$author_tbl $login_join $sqlpartial";
        $sth = $this->server->connection->getStatement($sql);
        $sth->execute($bind);

        return $sth->fetch();
    }
    
    /*
     * add LCMS author
     * @param LCMS author DAO
     * @return LCMS DAO validation
     */
    public function addAuthor($author_dao) {
        $validation = $author_dao->validate();
        if ($validation['result'] == 'success') {
            $author_tbl = Flux::config('FluxTables.lcms_author');
            $db = $this->server->loginDatabase;

            $cols = "$db.$author_tbl.account_id, $db.$author_tbl.access";
            $bind = array($author_dao->account_id, $author_dao->access);
            $sql = "INSERT INTO $db.$author_tbl ($cols) VALUES(?, ?)";

            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($bind);
        }
        return $validation;
    }
    
    /*
     * update LCMS author
     * @param LCMS author DAO
     * @return LCMS DAO validation
     */
    public function updateAuthor($author_dao) {
        $validation = $author_dao->validate();
        if ($validation['result'] == 'success') {
            $author_tbl = Flux::config('FluxTables.lcms_author');
            $db = $this->server->loginDatabase;

            $cols = "$db.$author_tbl.access = ?";
            $bind = array($author_dao->access, $author_dao->account_id);
            $sql = "UPDATE $db.$author_tbl SET $cols WHERE account_id = ?";

            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($bind);
        }
        return $validation;
    }
    
    /*
     * delete LCMS author
     * @param LCMS author DAO
     * @return LCMS DAO validation
     */
    public function deleteAuthor($author_dao) {
        $validation = $author_dao->validate();
        if ($validation['result'] == 'success') {
            if ($author_dao->isDeletable()) {
                $author_tbl = Flux::config('FluxTables.lcms_author');
                $db = $this->server->loginDatabase;

                $bind = array($author_dao->account_id);
                $sql = "DELETE FROM $db.$author_tbl WHERE $db.$author_tbl.account_id = ?";

                $sth = $this->server->connection->getStatement($sql);
                $sth->execute($bind);
            } else {
                $validation['result'] = 'failed';
            }
        }
        return $validation;
    }
    
    /*
     * get LCMS modules paginator
     * @param FluxCP template
     * @param LCMS author
     * @param access
     * @return LCMS modules paginator
     */
    public function getModulesPaginator($template, $author = null, $access = null) {
        return $this->getModules($author, $access, $template);
    }
    
    /*
     * get LCMS modules
     * @param LCMS author
     * @param access
     * @param FluxCP template
     * @return LCMS modules
     */
    private function getModules($author = null, $access = null, $template = null) {
        $module_tbl = Flux::config('FluxTables.lcms_module');
        $author_tbl = Flux::config('FluxTables.lcms_author');
        $login_tbl =  Flux::config('FluxTables.login');
        $db = $this->server->loginDatabase;

        $cols = "$db.$module_tbl.id, $db.$module_tbl.account_id, $db.$module_tbl.access, $db.$module_tbl.name";
        $cols .= ", $login_tbl.userid";
        $login_join = "LEFT JOIN $db.$login_tbl ON $db.$login_tbl.account_id = $db.$module_tbl.account_id";
        $bind = array();
        $sqlpartial = "";
        if (!is_null($access)) {
            $bind[] = intval($access);
            if (is_null($author)) {
                $sqlpartial = "INNER JOIN $db.$author_tbl ON $db.$author_tbl.account_id = $db.$module_tbl.account_id";
                $sqlpartial .= " WHERE $db.$module_tbl.access <= ?";
            } else {
                $sqlpartial = "WHERE $db.$module_tbl.access <= ?";
            }
        }
        
        if ($author != null) {
            if (is_null($access)) {
                $bind[] = intval($author->account_id);
                if ($sqlpartial != "") {
                    $sqlpartial .= "AND $db.$module_tbl.account_id = ?";
                } else {
                    $sqlpartial .= "WHERE $db.$module_tbl.account_id = ?";
                }
            }
        }

        $sth = null;
        if (!is_null($template)) {
            $sth = $this->server->connection->getStatement("SELECT COUNT($db.$module_tbl.id) AS total FROM $db.$module_tbl $sqlpartial");
            $sth->execute($bind);

            Flux::config('ResultsPerPage', Flux::config('LcmsModulesPerPage'));
            $this->paginator = $template->getPaginator($sth->fetch()->total);
            $this->paginator->setSortableColumns(array("$module_tbl.id" => "asc", "$module_tbl.access", "name"));

            $sql  = $this->paginator->getSQL("SELECT $cols FROM $db.$module_tbl $login_join $sqlpartial");
            $sth  = $this->server->connection->getStatement($sql);
            $sth->execute($bind);
        } else {
            $sql = "SELECT $cols FROM $db.$module_tbl $login_join $sqlpartial";
            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($bind);
        }

        return $sth->fetchAll();
    }
    
    /*
     * get LCMS module
     * @param module_id
     * @param access
     * @return LCMS module
     */
    public function getModule($module_id, $access = null) {
        $module_tbl = Flux::config('FluxTables.lcms_module');
        $login_tbl = Flux::config('FluxTables.login');
        $db = $this->server->loginDatabase;

        $cols = "$db.$module_tbl.id, $db.$module_tbl.account_id, $db.$module_tbl.access, $db.$module_tbl.name";
        $cols .= ", $db.$login_tbl.userid";
        $login_join = "LEFT JOIN $db.$login_tbl ON $db.$login_tbl.account_id = $db.$module_tbl.account_id";
        $bind = array($module_id);
        $sqlpartial = "WHERE $db.$module_tbl.id = ?";
        if (!is_null($access)){
            $bind[] = $access;
            $sqlpartial .= " AND $db.$module_tbl.access <= ?";
        }
        $sql = "SELECT $cols FROM $db.$module_tbl $login_join $sqlpartial";
        $sth = $this->server->connection->getStatement($sql);
        $sth->execute($bind);

        return $sth->fetch();
    }
    
    /*
     * add LCMS module
     * @param LCMS module DAO
     * @return LCMS DAO validation
     */
    public function addModule($module_dao) {
        $validation = $module_dao->validate();
        if ($validation['result'] == 'success') {
            $module_tbl = Flux::config('FluxTables.lcms_module');
            $db = $this->server->loginDatabase;

            $cols = "$db.$module_tbl.account_id, $db.$module_tbl.access, name";
            $bind = array($module_dao->account_id, $module_dao->access, $module_dao->name);
            $sql = "INSERT INTO $db.$module_tbl ($cols) VALUES(?, ?, ?)";

            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($bind);
        }
        return $validation;
    }
    
    /*
     * update LCMS module
     * @param LCMS module DAO
     * @return LCMS DAO validation
     */
    public function updateModule($module_dao) {
        $validation = $module_dao->validate();
        if ($validation['result'] == 'success') {
            $module_tbl = Flux::config('FluxTables.lcms_module');
            $db = $this->server->loginDatabase;

            $cols = "$db.$module_tbl.account_id = ?, $db.$module_tbl.access = ?, $db.$module_tbl.name = ?";
            $bind = array($module_dao->account_id, $module_dao->access, $module_dao->name, $module_dao->id);
            $sql = "UPDATE $db.$module_tbl SET $cols WHERE $db.$module_tbl.id = ?";

            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($bind);
        }
        return $validation;
    }
    
    /*
     * delete LCMS module
     * @param LCMS module DAO
     * @return LCMS DAO validation
     */
    public function deleteModule($module_dao) {
        $validation = $module_dao->validate();
        if ($validation['result'] == 'success') {
            if ($module_dao->isDeletable()) {
                $module_tbl = Flux::config('FluxTables.lcms_module');
                $db = $this->server->loginDatabase;

                $bind = array($module_dao->id);
                $sql = "DELETE FROM $db.$module_tbl WHERE $db.$module_tbl.id = ?";

                $sth = $this->server->connection->getStatement($sql);
                $sth->execute($bind);
            } else {
                $validation['result'] = 'failed';
            }
        }
        return $validation;
    }
    
    /*
     * get LCMS author authors
     * @param access
     * @return LCMS author authors
     */
    public function getAuthorAuthors($access = null) {
        return $this->getAuthors($access);
    }
    
    /*
     * get LCMS author modules
     * @param LCMS author
     * @param access
     * @return LCMS author modules
     */
    public function getAuthorModules($author, $access = null) {
        return $this->getModules($author, $access);
    }
    
    /*
     * get LCMS author pages
     * @param LCMS author
     * @param access
     * @return LCMS author pages
     */
    public function getAuthorPages($author, $access = null) {
        return $this->getPages(null, $author, $access);
    }
    
    /*
     * get LCMS module pages
     * @param LCMS modules
     * @param access
     * @return LCMS module pages
     */
    public function getModulePages($module, $access = null) {
        return $this->getPages($module, null, $access);
    }
    
    /*
     * get LCMS pages paginator
     * @param FluxCP template
     * @param LCMS module
     * @param LCMS author
     * @param access
     * @return LCMS pages paginator
     */
    public function getPagesPaginator($template, $module = null, $author = null, $access = null) {
        return $this->getPages($module, $author, $access, $template);
    }
    
    /*
     * get LCMS pages
     * @param LCMS module
     * @param LCMS author
     * @param access
     * @param FluxCP template
     * @return LCMS pages
     */
    private function getPages($module = null, $author = null, $access = null, $template = null) {
        $page_tbl = Flux::config('FluxTables.lcms_page');
        $module_tbl = Flux::config('FluxTables.lcms_module');
        $author_tbl = Flux::config('FluxTables.lcms_author');
        $login_tbl = Flux::config('FluxTables.login');
        $db = $this->server->loginDatabase;

        $cols = "$db.$page_tbl.id, $db.$page_tbl.module_id, $db.$page_tbl.account_id, $db.$page_tbl.status, $db.$page_tbl.access, $db.$page_tbl.date, $db.$page_tbl.name, $db.$page_tbl.content";
        $cols .= ", $db.$module_tbl.name AS module_name";
        $cols .= ", $db.$login_tbl.userid";
        $module_join = "LEFT JOIN $db.$module_tbl ON $db.$module_tbl.id = $db.$page_tbl.module_id";
        $login_join = "LEFT JOIN $db.$login_tbl ON $db.$login_tbl.account_id = $db.$page_tbl.account_id";
        $bind = array();
        $sqlpartial = "";
        if (!is_null($access)) {
            $bind[] = intval($access);
            if (!is_null($author)) {
                $sqlpartial = "INNER JOIN $db.$author_tbl ON $db.$author_tbl.account_id = $db.$page_tbl.account_id";
                $sqlpartial .= " WHERE $db.$author_tbl.access <= ?";
            } else {
                $sqlpartial = "WHERE $db.$page_tbl.access <= ?";
            }
        }
        
        if ($module != null) {
            $bind[] = intval($module->id);
            if ($sqlpartial != "") {
                $sqlpartial .= "AND $db.$page_tbl.module_id = ?";
            } else {
                $sqlpartial .= "WHERE $db.$page_tbl.module_id = ?";
            }
        } else if ($author != null) {
            if (is_null($access)) {
                $bind[] = intval($author->account_id);
                if ($sqlpartial != "") {
                    $sqlpartial .= "AND $db.$page_tbl.account_id = ?";
                } else {
                    $sqlpartial .= "WHERE $db.$page_tbl.account_id = ?";
                }
            }
        }

        $sth = null;
        if (!is_null($template)) {
            $sth = $this->server->connection->getStatement("SELECT COUNT($db.$page_tbl.id) AS total FROM $db.$page_tbl $sqlpartial");
            $sth->execute($bind);

            Flux::config('ResultsPerPage', Flux::config('LcmsPagesPerPage'));
            $this->paginator = $template->getPaginator($sth->fetch()->total);
            $this->paginator->setSortableColumns(array("$page_tbl.id" => "asc", "$page_tbl.access", "$page_tbl.date", "$page_tbl.name", "$page_tbl.status"));

            $sql  = $this->paginator->getSQL("SELECT $cols FROM $db.$page_tbl $module_join $login_join $sqlpartial");
            $sth  = $this->server->connection->getStatement($sql);
            $sth->execute($bind);
        } else {
            $sql = "SELECT $cols FROM $db.$page_tbl $module_join $login_join $sqlpartial";
            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($bind);
        }

        return $sth->fetchAll();
    }
    
    /*
     * get LCMS page
     * @param page_id
     * @param access
     * @return LCMS page
     */
    public function getPage($page_id, $access = null) {
        $page_tbl = Flux::config('FluxTables.lcms_page');
        $module_tbl = Flux::config('FluxTables.lcms_module');
        $login_tbl = Flux::config('FluxTables.login');
        $db = $this->server->loginDatabase;

        $cols = "$db.$page_tbl.id, $db.$page_tbl.module_id, $db.$page_tbl.account_id, $db.$page_tbl.status, $db.$page_tbl.access, $db.$page_tbl.date, $db.$page_tbl.name, $db.$page_tbl.content";
        $cols .= ", $db.$module_tbl.name AS module_name";
        $cols .= ", $db.$login_tbl.userid";
        $module_join = "LEFT JOIN $db.$module_tbl ON $db.$module_tbl.id = $db.$page_tbl.module_id";
        $login_join = "LEFT JOIN $db.$login_tbl ON $db.$login_tbl.account_id = $db.$page_tbl.account_id";
        $bind = array($page_id);
        $sqlpartial = "WHERE $db.$page_tbl.id = ?";
        if (!is_null($access)){
            $bind[] = $access;
            $sqlpartial .= " AND $db.$page_tbl.access <= ?";
        }
        $sql = "SELECT $cols FROM $db.$page_tbl $module_join $login_join $sqlpartial";
        $sth = $this->server->connection->getStatement($sql);
        $sth->execute($bind);

        return $sth->fetch();
    }
    
    /*
     * add LCMS page
     * @param LCMS page DAO
     * @return LCMS DAO validation
     */
    public function addPage($page_dao) {
        $validation = $page_dao->validate();
        if ($validation['result'] == 'success') {
            $page_tbl = Flux::config('FluxTables.lcms_page');
            $db = $this->server->loginDatabase;

            $cols = "$db.$page_tbl.module_id, $db.$page_tbl.account_id, $db.$page_tbl.status, $db.$page_tbl.access, $db.$page_tbl.date, $db.$page_tbl.name, $db.$page_tbl.content";
            $bind = array($page_dao->module_id, $page_dao->account_id, $page_dao->status, $page_dao->access, $page_dao->date, $page_dao->name, $this->cleanHTML($page_dao->content));
            $sql = "INSERT INTO $db.$page_tbl ($cols) VALUES(?, ?, ?, ?, ?, ?, ?)";

            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($bind);
        }
        return $validation;
    }
    
    /*
     * update LCMS page
     * @param LCMS page DAO
     * @return LCMS DAO validation
     */
    public function updatePage($page_dao) {
        $validation = $page_dao->validate();
        if ($validation['result'] == 'success') {
            $page_tbl = Flux::config('FluxTables.lcms_page');
            $db = $this->server->loginDatabase;

            $cols = "$db.$page_tbl.module_id = ?, $db.$page_tbl.account_id = ?, $db.$page_tbl.status = ?, $db.$page_tbl.access = ?, $db.$page_tbl.date = ?, $db.$page_tbl.name = ?, $db.$page_tbl.content = ?";
            $bind = array($page_dao->module_id, $page_dao->account_id, $page_dao->status, $page_dao->access, $page_dao->date, $page_dao->name, $this->cleanHTML($page_dao->content), $page_dao->id);
            $sql = "UPDATE $db.$page_tbl SET $cols WHERE $db.$page_tbl.id = ?";

            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($bind);
        }
        return $validation;
    }
    
    /*
     * delete LCMS page
     * @param LCMS page DAO
     * @return LCMS DAO validation
     */
    public function deletePage($page_dao) {
        $validation = $page_dao->validate();
        if ($validation['result'] == 'success') {
            if ($page_dao->isDeletable()) {
                $page_tbl = Flux::config('FluxTables.lcms_page');
                $db = $this->server->loginDatabase;

                $bind = array($page_dao->id);
                $sql = "DELETE FROM $db.$page_tbl WHERE $db.$page_tbl.id = ?";

                $sth = $this->server->connection->getStatement($sql);
                $sth->execute($bind);
            } else {
                $validation['result'] = 'failed';
            }
        }
        return $validation;
    }
    
    /*
     * hook LCMS page
     * @param page_id
     * @return page content or access error
     * @todo find a proper way to handle this for 1.1
     */
    public function getPageHTML($page_id) {
        $page = $this->getPage($page_id);
        if ($page->access <= $this->session->account->account_id) {
            return $page->content;
        } else {
            return '<p class="red">'.Flux::message('LcmsMesEAccess').'</p>';
        }
    }
    
    /*
     * clean HTML
     * @param dirty_html
     * @return clean HTML
     */
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
    
    /*
     * get LCMS status name
     * @param status_id
     * @return status name
     */
    public static function getStatusName($status_id) {
        return self::$PAGE_STATUS[$status_id];
    }
    
    /*
     * Function to retrieve LCMS menu according to the user permissions 
     * and dynamically inject them into Flux_Template::getMenuItems().
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
                        $display = true;
                        if (Flux::config('LcmsValidationEnable')) {
                            if ($page->status != Lcms_Functions::$PAGE_STATUS_VALID) {
                                $display = false;
                            }
                        }
                        if ($display) {
                            $allowedItems[$module->name][] = array(
                                    'name'   => $page->name,
                                    'exturl' => null,
                                    'module' => 'lcms',
                                    'action' => 'show',
                                    'url'    => $template->url('lcms', 'show', array('id' => $page->id))
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
 * DAO-like class to handle data and validation
 */
class Lcms_DAO {
    public static $TYPE_PAGE = "page";
    public static $TYPE_MODULE = "module";
    public static $TYPE_AUTHOR = "author";
    
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
    
    /*
     * populate the DAO according to the source input
     */
    private function populate() {
        if (!is_null($this->object)) {
            $this->populateWObject();
        } else if (!is_null($this->post)) {
            $this->populateWPOST();
            $this->getAccountRef();
        } else {
            $this->populateWNew();
        }
    }
    
    /*
     * Populate the DAO from an existing Flux_DataObject
     */
    private function populateWObject() {
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
    }
    
    /*
     * Populate the DAO from a POST request
     */
    private function populateWPOST() {
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
    }
    
    /*
     * Populate a new empty DAO with default values
     */
    private function populateWNew() {
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
    
    /*
     * Populate external references of current data-object
     * Hackity hack until 1.1 function.inc.php rewrite
     */
    private function getAccountRef() {
        $ref_object = $this->lcms->getAccount($this->account_id);
        $this->userid = $ref_object->userid;
    }
    
    /*
     * validate the DAO according to the data-model in addon.php
     */
    public function validate() {
        $ret = array(
            'result'   => 'success',
            'name'     => ($this->type == Lcms_DAO::$TYPE_AUTHOR) ? $this->userid : $this->name,
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
    
    /*
     * validate a DAO field according to the data-model in addon.php
     */
    private function validateField($field_type, $value) {
        $ret = true;
        switch ($field_type[0]) {
            case "int" :
                if (!is_int($value)) {
                    $ret = false;
                }
                break;
            case "string" :
                if (!is_null($value)) {
                    if (!is_string($value)) {
                        $ret = false;
                    }
                }
                break;
        }
        if (!is_null($field_type[1])) {
            if (!is_null($value)) {
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
        }
        if (!empty($field_type[2])) {
            if ($field_type[2] == 'not null') {
                if (is_null($value)) {
                    $ret = false;
                } else if ($field_type[0] == 'string' && empty($value)) {
                    $ret = false;
                }
            }
        }
        return $ret;
    }
    
    /*
     * Validate date format
     */
    private function isDateValid($date) {
        $df = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        return $df && $df->format('Y-m-d H:i:s') == $date;
    }
    
    /*
     * Is this element deletable?
     */
    public function isDeletable() {
        $ret = true;
        
        $module_res = null;
        $page_res = null;
        switch ($this->type) {
            case Lcms_DAO::$TYPE_MODULE :
                $page_res = $this->lcms->getModulePages($this);
                break;
            case Lcms_DAO::$TYPE_AUTHOR :
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

