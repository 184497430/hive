<?php
/**
 * Created by PhpStorm.
 * User: zhangy
 * Date: 15/3/20
 * Time: 上午11:44
 */

class ProjectController extends BaseController{

    public function __construct() {
        parent::__construct();
    }

    public function listAction(){
        $page = max(1, intval($this->_get('page')));

        $mod_project = ProjectModel::getInstance();
        $projects = $mod_project->listProjects('', $page, 20);
        $page_tool = $this->pageTool($projects['page']['no'], $projects['page']['count']);
        $this->show('project_list', array('projects'=>$projects, 'page_tool'=>$page_tool));
    }

    public function newAction(){

        $this->show('project_new');
    }

    public function modifyAction(){
        $prj_id = $this->_get('id');
        $mod_project = ProjectModel::getInstance();
        $prj_info = $mod_project->getByPk($prj_id);
        $this->show('project_modify', array('prj_info'=>$prj_info));
    }



    public function dosaveAction(){
        $prj_id = $this->_post('id');
        $prj_name = $this->_post('prj_name');
        $params = array(
            'url'                   => $this->_post('url'),
            'desc'                  => $this->_post('desc'),
            'svn_url'               => $this->_post('svn_url'),
            'svn_username'          => $this->_post('svn_username'),
            'svn_pwd'               => $this->_post('svn_pwd'),
            'test_server_ip'        => $this->_post('test_ip'),
            'test_server_path'      => $this->_post('test_path'),
            'product_server_ip'     => $this->_post('product_ip'),
            'product_server_path'   => $this->_post('product_path'),
        );


        $mod_project = ProjectModel::getInstance();
        try{

            if(!empty($prj_id)){
                $ret = $mod_project->modify($prj_id, $prj_name, $params);
            }else{
                $ret = $mod_project->add($prj_name, $params);
            }

            if($ret){
                $this->showJsonResult(1, "成功" , array('project_id'=>$ret));
            }else{
                $this->showJsonResult(-1, "失败", array());
            }
        }catch (Exception $e){
            $this->showJsonResult(-1, $e->getMessage(), array());
        }
    }
}