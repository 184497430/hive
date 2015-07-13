<?php
/**
 *
 * 
 *
 * 
 * @file:   base.ctl.php
 * @date:   2015/2/26
 */
class BaseController extends Controller
{

	public function __construct() {
        $mod_project = ProjectModel::getInstance();
        $projects = $mod_project->getPrjsByUser(1);
        $this->assign('my_projects', $projects);
	}


    public function _post($key, $val=null){
        return isset($_POST[$key]) ? $_POST[$key] : $val;
    }

    public function _get($key, $val=null){
        return isset($_GET[$key]) ? $_GET[$key] : $val;
    }

    public function _request($key, $val=null){
        return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $val;
    }

    public function pageTool($page_no, $page_count){
        parse_str($_SERVER['QUERY_STRING'], $query);

        $prior_group = array();
        if($page_no - 3 > 1 ){
            $query['page'] = $page_no - 3;
            $prior_group = array(
                'no'    => $page_no - 3,
                'url'   => $_SERVER['SCRIPT_NAME'] . "?" . http_build_query($query),
            );
        }


        $prior = array();
        if($page_no - 1 > 0){
            $query['page'] = $page_no - 1;
            $prior = array(
                'no'    => $page_no - 1,
                'url'   => $_SERVER['SCRIPT_NAME'] . "?" . http_build_query($query),
            );
        }

        $query['page'] = $page_no;
        $current = array(
            'no'    => $page_no,
            'url'   => $_SERVER['SCRIPT_NAME'] . "?" . http_build_query($query),
        );


        $next = array();
        if($page_no + 1 < $page_count){
            $query['page'] = $page_no + 1;
            $prior = array(
                'no'    => $page_no + 1,
                'url'   => $_SERVER['SCRIPT_NAME'] . "?" . http_build_query($query),
            );
        }

        $next_group = array();
        if($page_no + 3 < $page_count){
            $query['page'] = $page_no + 3;
            $prior = array(
                'no'    => $page_no + 3,
                'url'   => $_SERVER['SCRIPT_NAME'] . "?" . http_build_query($query),
            );
        }

        $data = array(
            'prior_group'   => $prior_group,
            'prior'         => $prior,
            'current'       => $current,
            'next'          => $next,
            'next_group'    => $next_group,
        );

        return $this->getView('page_tool', $data);
    }

}
