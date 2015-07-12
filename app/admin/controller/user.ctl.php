<?php
/**
 * Created by PhpStorm.
 * User: 001424
 * Date: 2015/6/27
 * Time: 12:24
 */

class UserController extends BaseController{

    public function listAction(){

        $word = $this->_get('word');
        $page = max(1, intval($this->_get('page')));

        $mod_user = UserModel::getInstance($word, $page, 20);
        $users = $mod_user->listUsers();
        $page_tool = $this->pageTool($users['page']['no'], $users['page']['count']);
        $this->show('user_list', array('users'=>$users, 'page_tool'=>$page_tool));
    }

    public function newAction(){
        $this->show('user_new');
    }

    public function donewAction(){
        $user_name = $this->_post('user_name');
        $password = $this->_post('password');
        $real_name = $this->_post('real_name');

        $mod_user = UserModel::getInstance();
        try{
            $ret = $mod_user->add($user_name, $password, $real_name);
            if($ret){
                $this->showJsonResult(1, "成功" , array('user_id'=>$ret));
            }else{
                $this->showJsonResult(-1, "失败", array());
            }
        }catch (Exception $e){
            $this->showJsonResult(-1, $e->getMessage(), array());
        }
    }
}