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

    public function newAction(){

        $this->show('prjnew');
    }
}