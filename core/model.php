<?php
/**
 *  model的基础类文件
 * @file      :   model.php
 * @copyright Copyright 2014 Qiyi Inc. All rights reserved.
 * @author    : zhangy <zhangy@qiyi.com>
 * @date      :   14-5-16
 */
abstract class Model
{

    //单例模式
    public static function getInstance() {
        static $_instance = NULL;
        if (is_null($_instance)) {
            $class = get_called_class();
            $_instance = new $class();
        }

        return $_instance;
    }

    public function __construct(){

        $class_name = get_called_class();
        $ref_class = new ReflectionClass($class_name);
        if(!$ref_class->hasConstant("MODEL_NAME")){
            trigger_error("Undefined class constant 'MODEL_NAME' in " . $class_name, E_USER_ERROR);
        }

        $this->table = Table::getInstance($class_name::MODEL_NAME);
    }

    public function getByPk($pk){
        $class_name = get_called_class();
        $ref_class = new ReflectionClass($class_name);
        if(!$ref_class->hasConstant("PK")){
            trigger_error("Undefined class constant 'PK' in " . $class_name, E_USER_ERROR);
        }

        $where = array(
            $class_name::PK => $pk
        );

        return $this->table->getOne('*', $where);
    }

    public function table($name = null){
        if(empty($name)) return $this->table;

        return Table::getInstance($name);
    }

    public function db(){
        return $this->table->getDB();
    }
}

