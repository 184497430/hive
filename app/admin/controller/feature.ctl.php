<?php
/**
 * Created by PhpStorm.
 * User: zhangy
 * Date: 15/3/20
 * Time: 上午11:44
 */

Core::loadLibrary('svn');

class FeatureController extends BaseController{

    private $status_tpls = array(
        FeatureModel::STATUS_NEW        => '<span class="label label-default">新建</span>',
        FeatureModel::STATUS_TEST       => '<span class="label label-info">送测</span>',
        FeatureModel::STATUS_FAIL       => '<span class="label label-warning">打回</span>',
        FeatureModel::STATUS_PASS       => '<span class="label label-success">通过</span>',
        FeatureModel::STATUS_DONE       => '<span class="label label-primary">部署</span>',
        FeatureModel::STATUS_CLOSE      => '<span class="label label-danger">关闭</span>',
        FeatureModel::STATUS_ROLLBACK   => '<span class="label label-danger">回滚</span>',
    );

    private $status_dict = array(
        FeatureModel::STATUS_NEW        => '新建',
        FeatureModel::STATUS_TEST       => '送测',
        FeatureModel::STATUS_FAIL       => '打回',
        FeatureModel::STATUS_PASS       => '通过',
        FeatureModel::STATUS_DONE       => '部署',
        FeatureModel::STATUS_CLOSE      => '中止',
        FeatureModel::STATUS_ROLLBACK   => '回滚',
    );

    public function __construct() {
        parent::__construct();
    }

    public function newAction(){
        $prj_id = $this->_get('prjid');

        $mod_project = ProjectModel::getInstance();
        $data['prj_info'] = $mod_project->getByPk($prj_id);
        $data['prj_id'] = $prj_id;
        $this->show('feature_new', $data);
    }

    public function donewAction(){
        $prj_id = $this->_post('prj_id');
        $featrue_name = $this->_post('name');
        $desc = $this->_post('desc');

        $mod_feature = FeatureModel::getInstance();
        try{
            $ret = $mod_feature->add($prj_id, $featrue_name, array('desc'=>$desc));
            if($ret){
                $this->showJsonResult(1, "成功" , array('feature_id'=>$ret));
            }else{
                $this->showJsonResult(-1, "失败", array());
            }
        }catch (Exception $e){
            $this->showJsonResult(-1, $e->getMessage(), array());
        }
    }

    public function ajaxFilesAction(){
        $rev = $this->_get('rev');
        $feature_id = $this->_get('id');

        $feature_info = FeatureModel::getInstance()->getByPk($feature_id);

        if($feature_info){
            $prj_info = ProjectModel::getInstance()->getByPk($feature_info['prj_id']);
        }

        $log = array();
        if(isset($prj_info)){
            $svn = CSvn::getInstance($prj_info['svn_url'], $prj_info['svn_username'], $prj_info['svn_pwd']);
            $log = $svn->log_by_rev($rev);
            foreach($log['paths'] as $key=>$each){
                $log['paths'][$key]['type'] = $svn->is_file($each['path'], $rev) ? 'file' : 'dir';
            }
        }
        $this->show('feature_dialog_files', array('log'=>$log));
    }

    public function ajaxGuessAction(){

        $rev = $this->_get('rev');
        $svn = CSvn::getInstance('svn://192.168.56.101', 'admin', 'admin');
        $logs = $svn->guess_log($rev);

        print_r($logs);
    }

    public function workAction(){

        $prj_id = $this->_get('prjid');
        $word = $this->_get('word');
        $page = max(1, intval($this->_get('page')));

        $mod_project = ProjectModel::getInstance();
        $data['prj_info'] = $mod_project->getByPk($prj_id);
        $data['prj_id'] = $prj_id;

        $mod_feature = FeatureModel::getInstance();
        $data['features'] = $mod_feature->listFeatures($word, $page, 20);
        $data['page_tool'] = $this->pageTool($data['features']['page']['no'], $data['features']['page']['count']);

        $data['status_tpls'] = $this->status_tpls;

        $this->show('feature_work', $data);
    }

    public function historyAction(){
        $this->show('feature_history');
    }

    public function detailAction(){
        $feature_id = $this->_get('id');
        $data['feature_id'] = $feature_id;

        $mod_feature = FeatureModel::getInstance();
        $data['feature_info'] = $mod_feature->getByPk($feature_id);

        $mod_project = ProjectModel::getInstance();
        $data['prj_info'] = $mod_project->getByPk($data['feature_info']['prj_id']);

        $data['status_dict'] = $this->status_dict;

        $this->show('feature_detail', $data);
    }

    public function timelineAction(){
        $this->show('feature_timeline');
    }
}