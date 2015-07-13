<?php
/**
 * Created by PhpStorm.
 * User: zhangy
 * Date: 15/3/20
 * Time: 下午9:20
 */

class ProjectModel extends Model{

    const MODEL_NAME = "project";
    const PK = "prj_id";

    public function listProjects($keyword="", $page=1, $pagesize=20){

        if(!empty($keyword)){
            $where = "prj_name like '%{$keyword}%'";
        }

        return $this->table()->page($page, $pagesize, '*', $where);

    }

    public function add($prj_name, $params){
        $row = $this->table()->getOne('*', array('prj_name'=>$prj_name));
        if(!empty($row)){
            throw new Exception('用户已存在');
        }

        $params['prj_name'] = $prj_name;

        return $this->table()->insert($params);
    }

    public function modify($prj_id, $prj_name, $params){
        $params['prj_name'] = $prj_name;
        $where = array(
            'prj_id'    => $prj_id
        );
        return $this->table()->update($params, $where);
    }

    public function getPrjsByUser($user_id){
        $sql = "SELECT * FROM project";
        $rows = $this->table()->query($sql);

        return $rows;
    }
}