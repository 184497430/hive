<?php
/**
 * Created by PhpStorm.
 * User: zhangy
 * Date: 15/3/20
 * Time: 下午9:20
 */

class FeatureModel extends Model{

    const STATUS_NEW    = 1;
    const STATUS_TEST   = 2;
    const STATUS_FAIL   = 3;
    const STATUS_PASS   = 4;
    const STATUS_CLOSE  = 5;
    const STATUS_DONE   = 6;
    const STATUS_ROLLBACK = 7;

    const MODEL_NAME = "feature";
    const PK = "feature_id";

    public function listFeatures($keyword="", $page=1, $pagesize=20){

        if(!empty($keyword)){
            $where = "feature_name like '%{$keyword}%'";
        }

        return $this->table()->page($page, $pagesize, '*', $where);

    }

    public function add($prj_id, $feature_name, $params){

        $params['prj_id'] = $prj_id;
        $params['feature_name'] = $feature_name;
        $params['ctime'] = date('Y-m-d H:i:s');
        $params['status'] = self::STATUS_NEW;
        return $this->table()->insert($params);
    }

    public function modify($feature_id, $params){
        unset($params['prj_name']);
        unset($params['prj_id']);
        $where = array(
            'feature_id'    => $feature_id
        );
        return $this->table()->update($params, $where);
    }
}