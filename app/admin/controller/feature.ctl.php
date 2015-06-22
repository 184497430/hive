<?php
/**
 * Created by PhpStorm.
 * User: zhangy
 * Date: 15/3/20
 * Time: 上午11:44
 */

class FeatureController extends BaseController{

    public function __construct() {
        parent::__construct();
    }

    public function newAction(){

        $this->show('feature_new');
    }

    public function workAction(){
        $this->show('feature_work');
    }

    public function historyAction(){
        $this->show('feature_history');
    }
}