<?php
/**
 * Created by PhpStorm.
 * User: 001424
 * Date: 2015/6/27
 * Time: 12:24
 */

class UserController extends BaseController{

    public function listAction(){
        $this->show('user_list');
    }

    public function newAction(){
        $this->show('user_new');
    }
}