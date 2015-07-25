<?php

class CSvn
{

    private $repos;
    private $username;
    private $pwd;


    /**
     * @param $repos
     * @param $username
     * @param $pwd
     * @return CSvn
     */
    public static function getInstance($repos, $username, $pwd)
    {
        CSvn::check();

        static $svn;

        $key = md5($repos . '|' . $username . '|' . $pwd);

        if (isset($svn[$key])) {
            return $svn[$key];
        }

        $svn[$key] = new CSvn($repos, $username, $pwd);

        return $svn[$key];
    }

    public static function check(){
        if( !function_exists('svn_log') ){
            trigger_error('please install svn extension [http://pecl.php.net/package/svn]', E_USER_ERROR);
        }
    }


    public function __construct($repos, $username, $pwd)
    {
        $this->repos = $repos;
        $this->username = $username;
        $this->pwd = $pwd;
    }

    public function is_dir($path, $revision = SVN_REVISION_HEAD){
        if($path == '/') return true;

        $p = dirname($path);
        $list = $this->ls($p, $revision);

        $file = str_replace('/', '', str_replace($p, '', $path));

        return $list[$file]['type'] == 'dir';
    }

    public function is_file($path, $revision = SVN_REVISION_HEAD){


        if($path == '/') return false;

        $p = dirname($path);
        $list = $this->ls($p, $revision);

        $file = str_replace('/', '', str_replace($p, '', $path));

        return $list[$file]['type'] == 'file';
    }

    public function ls($path, $revision = SVN_REVISION_HEAD){
        static $cache;

        if( isset($cache[$path]) ){
            $ls = $cache[$path];
        }else{
            $this->auth();
            $ls = @svn_ls($this->repos . $path, $revision);
            $cache[$path] = $ls;
        }

        return $ls;
    }

    public function log_by_rev($revision = SVN_REVISION_HEAD){
        $this->auth();
        $log = @svn_log($this->repos, $revision, $revision);
        if(!$log) return array();
        return $log[0];
    }

    public function guess_log($revision){

        if(!is_numeric($revision)) return false;

        $this->auth();
        $head = @svn_log($this->repos, SVN_REVISION_HEAD, SVN_REVISION_HEAD);

        if(!$head) return array();

        $head_revision = $head[0]['rev'];

        $result = array();

        if($revision > $head_revision) return array();

        $result[$revision] = @svn_log($this->repos, $revision, $revision);
        for($i=9; $i>=0;$i--){
            $rev = intval(substr(strval($head_revision), 0, strlen($head_revision) - strlen($revision)-1) . strval($i) . strval($revision));
            if($rev > $head_revision) continue;

            $log = @svn_log($this->repos, $rev, $rev);
            $result[$rev] = $log[0];

        }
        return $result;
    }

    public function log($path = '/')
    {
        $this->auth();
        $result = @svn_log($this->repos . $path);
        return $result;
    }

    private function auth()
    {
        svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_USERNAME, $this->username);
        svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_PASSWORD, $this->pwd);
        svn_auth_set_parameter(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true); // <--- Important for certificate issues!
        svn_auth_set_parameter(SVN_AUTH_PARAM_NON_INTERACTIVE, true);
        svn_auth_set_parameter(SVN_AUTH_PARAM_NO_AUTH_CACHE, true);
    }
}
