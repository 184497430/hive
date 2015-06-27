<?php
/**
 * Created by PhpStorm.
 * User: zhangy
 * Date: 15/3/20
 * Time: 上午11:44
 */

class IndexController extends BaseController{

    public function __construct() {
        parent::__construct();
    }

    public function indexAction(){

        $this->show('main');
    }

    public function loginAction(){
        $this->show('login');
    }

}