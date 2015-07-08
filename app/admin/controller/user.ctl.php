<?php
/**
 * Created by PhpStorm.
 * User: 001424
 * Date: 2015/6/27
 * Time: 12:24
 */

class UserController extends BaseController{

    public function listAction(){

        $mod_user = UserModel::getInstance();
        $users = $mod_user->listUsers();
        $this->show('user_list', array('users'=>$users));
    }

    public function newAction(){
        $this->show('user_new');
    }

    public function donewAction(){
        $user_name = $_POST['user_name'];
        $password = $_POST['password'];
        $real_name = $_POST['real_name'];

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