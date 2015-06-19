<?php


class Mysql extends PDO{

    public $last_sql;

    public static function getInstance($db){
        static $instances = array();

        if( array_key_exists($db, $instances) ){
            return $instances[ $db ];
        }

        $db_configs = Core::getConfig('db');

        if( !isset($db_configs[$db]) || empty($db_configs[$db])){
            trigger_error("缺少［{$db}］数据库配置", E_USER_ERROR);
        }

        $config = $db_configs[$db];

        if (!isset($config['dbcoding'])) {
            $config['dbcoding'] = "UTF8";
        }

        $mysql = new Mysql($config, $db);
        $instances[$db] = $mysql;
        return $mysql;
    }


    function __construct($config, $db_name = '') {

        try {
            $dsn = 'mysql:host=' . $config['dbhost'] . ';dbname=' . $config['dbname'];

            if ($config['dbport']) {
                $dsn .= ';port=' . $config['dbport'];
            }

            $driver_options = array(
                PDO::ATTR_CASE               => PDO::CASE_LOWER,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . $config['dbcoding']
            );

            parent::__construct($dsn, $config['dbuser'], $config['dbpswd'], $driver_options);

            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            //数据库链接发生错误

            trigger_error("数据库链接失败[dsn={$dsn}]", E_USER_ERROR);
        }
    }



    function execute($sql, $params = array(), $driver_options = array() ){
        $this->last_sql = $sql;
        $stmt = $this->prepare($sql, $driver_options);
        foreach ($params as $k => &$val) {
            if (is_int($val)) {
                $stmt->bindParam(":{$k}", $val, PDO::PARAM_INT);
            } else {
                $stmt->bindParam(":{$k}", $val, PDO::PARAM_STR);
            }
        }

        $result = $stmt->execute();

        if( !$result ) {

        }

        return $result;

    }

    function query($sql, $params = array(), $driver_options = array()){
        $this->last_sql = $sql;
        $stmt = $this->prepare($sql, $driver_options);
        foreach ($params as $k => &$val) {
            if (is_int($val)) {
                $stmt->bindParam(":{$k}", $val, PDO::PARAM_INT);
            } else {
                $stmt->bindParam(":{$k}", $val, PDO::PARAM_STR);
            }
        }

        $result = $stmt->execute();

        if( !$result ) {
            return false;
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 这个函数简化了写入数据库的insert操作。它直接往数据库中插入一条数据，操作成功返回true，否则返回false。例如：
     * [code]
     * $data = array('name' => $name, 'email' => $email, 'url' => $url);
     * $ret = $this->insert('table_name', $data);
     * [/code]
     * 第一个参数是表名，第二个是被插入数据的联合数组，上面的例子将执行如下语句：
     * [code]INSERT INTO table_name (name, email, url) VALUES ('Rick', 'rick@example.com', 'example.com')[/code]
     * 注解：被插入的数据会被自动转换和过滤。
     */
    function insert($tab_name,$arr_data){
        if($sql = $this->insertStr($tab_name,$arr_data)){
            if($this->execute($sql, $arr_data)){
                return $this->lastInsertId();
            }
        }
        return false;
    }


    /**
     * 这个函数简化了写入数据库的insert函数。它返回一个标准的SQL insert字符串。例如：
     * [code]
     * $data = array('name' => $name, 'email' => $email, 'url' => $url);
     * $str = $this->insert_string('table_name', $data);
     * [/code]
     * 第一个参数是表名，第二个是被插入数据的联合数组，上面的例子可以写成：
     * INSERT INTO table_name (name, email, url) VALUES ('Rick', 'rick@example.com', 'example.com')
     * 注解：被插入的数据会被自动转换和过滤，生成安全的查询语句。
     */
    function insertStr($tab_name,$arr_data){
        if(empty($tab_name) || !is_string($tab_name)){
            $this->err_msg .= "insertStr函数的第一个参数数据类型错误，应为字符串！\r\n";
            return false;
        }
        if(!is_array($arr_data) || count($arr_data)==0){
            $this->err_msg .= "insertStr函数的第二个参数数据类型错误，应为联合数组！\r\n";
            return false;
        }

        $sql = "INSERT INTO `{$tab_name}` ";

        $field_str = "";
        $val_str = "";
        foreach($arr_data as $field=>$val){
            $field_str .= empty($field_str) ? "`{$field}`" : ",`{$field}`";
            $val_str .= empty($val_str) ? ":$field" : ", :$field";
        }

        $sql .= "(" . $field_str . ") VALUES (" . $val_str .")";

        return $sql;
    }

    /**
     * 这个函数简化了写入数据库的update操作。它直接往数据库中更新数据，操作成功返回true，否则返回false。例如：
     * [code]
     * $data = array('name' => $name, 'email' => $email, 'url' => $url);
     * $where = "author_id = 1 AND status = 'active'";
     * $str = $this->db->update_string('table_name', $data, $where);
     * [/code]
     * 第一个参数是表名，第二个是被插入数据的联合数组，上面的例子将执行如下语句：
     * [code]UPDATE table_name SET name = 'Rick', email = 'rick@example.com', url = 'example.com' WHERE author_id = 1 AND status = 'active'[/code]
     * 注解：被插入的数据会被自动转换和过滤。
     */
    function update($tab_name,$arr_data,$where=null){
        if($sql = $this->updateStr($tab_name,$arr_data,$where)){
            return $this->execute($sql, $arr_data);
        }
        return false;
    }


    /**
     * 这个函数简化了写入数据库的update函数。它返回一个标准的SQL update字符串。例如：
     * [code]
     * $data = array('name' => $name, 'email' => $email, 'url' => $url);
     * $where = "author_id = 1 AND status = 'active'";
     * $str = $this->db->update_string('table_name', $data, $where);
     * [/code]
     * 第一个参数是表名，第二个是被插入数据的联合数组，上面的例子可以写成：
     * [code]UPDATE table_name SET name = 'Rick', email = 'rick@example.com', url = 'example.com' WHERE author_id = 1 AND status = 'active'[/code]
     * 注解：被插入的数据会被自动转换和过滤，生成安全的查询语句。
     */
    function updateStr($tab_name, $arr_data, $where=null){
        if(empty($tab_name) || !is_string($tab_name)){
            $this->err_msg .= "updateStr函数的第一个参数数据类型错误，应为字符串！\r\n";
            return false;
        }
        if(!is_array($arr_data) || count($arr_data)==0){
            $this->err_msg .= "updateStr函数的第二个参数数据类型错误，应为联合数组！\r\n";
            return false;
        }

        if(!is_null($where) && !empty($where) && !is_array($where) && !is_string($where)){
            $this->err_msg .= "updateStr函数的第三个参数数据类型错误，应为联合数组或为null或为字符串！\r\n";
            return false;
        }

        $sql = "UPDATE `{$tab_name}` SET ";

        $set_str = "";
        foreach($arr_data as $field=>$val){
            $set_str .= empty($set_str) ? "`{$field}`=:{$field}" : ",`{$field}`=:{$field}";
        }

        $where_str = "";
        if(is_array($where) && count($where)>0){
            foreach($where as $field=>$val){
                $where_str .= empty($where_str) ? "`".$field."`=".$this->escape($val) : " AND `".$field."`=".$this->escape($val);
            }
            $where_str = " WHERE " . $where_str;
        }else if(is_string($where)){
            $where_str = "WHERE " . $where;
        }

        $sql .= $set_str . $where_str;

        return $sql;
    }


    /**
     * 这个函数简化了写入数据库的delete操作。它直接往数据库中删除数据，操作成功返回true，否则返回false。例如：
     * [code]
     * $where = "author_id = 1 AND status = 'active'";
     * $str = $this->db->update_string('table_name', $where);
     * [/code]
     * 第一个参数是表名，第二个是条件的联合数组或者字符串，上面的例子将执行如下语句：
     * [code]DELETE FROM table_name WHERE author_id = 1 AND status = 'active'[/code]
     * 注解：被插入的数据会被自动转换和过滤。
     */
    function del($tab_name,$where=null){
        if($sql = $this->delStr($tab_name,$where)){
            return $this->execute($sql, $where);
        }
        return false;
    }


    /**
     * 这个函数简化了写入数据库的delete函数。它返回一个标准的SQL delete字符串。例如：
     * [code]
     * $where = "author_id = 1 AND status = 'active'";
     * $str = $this->db->update_string('table_name', $where);
     * [/code]
     * 第一个参数是表名，第二个是条件的联合数组或者字符串，上面的例子可以写成：
     * [code]DELETE FROM table_name WHERE author_id = 1 AND status = 'active'[/code]
     * 注解：被插入的数据会被自动转换和过滤，生成安全的查询语句。
     */
    function delStr($tab_name,$where=null){
        if(empty($tab_name) || !is_string($tab_name)){
            $this->err_msg .= "updateStr函数的第一个参数数据类型错误，应为字符串！\r\n";
            return false;
        }

        if(!is_null($where) && !empty($where) && !is_array($where) && !is_string($where)){
            $this->err_msg .= "updateStr函数的第三个参数数据类型错误，应为联合数组或为null或为字符串！\r\n";
            return false;
        }

        $sql = "DELETE FROM `{$tab_name}` ";

        $where_str = "";
        if(is_array($where) && count($where)>0){
            foreach($where as $field=>$val){
                $where_str .= empty($where_str) ? "`{$field}`=:{$field}" : " AND `{$field}`=:{$field}";
            }
            $where_str = " WHERE " . $where_str;
        }else if(is_string($where)){
            $where_str = "WHERE " . $where;
        }

        $sql .= $where_str;

        return $sql;
    }

    function get($tab_name,$field='*',$where=null, $order=null, $having=null, $offest=0 ,$limit=null){
        if( is_string($where) ){
            $where_sql = $where;
            $where = array();
        }elseif( is_array( $where ) ){
            foreach($where as $key=>$val){
                $where_sql .= empty($where_sql)
                    ? "`{$key}` = :{$key}"
                    : " AND `{$key}` = :{$key}";
            }
        }

        if( is_string($order) ){
            $order_sql = $order;
        }elseif( is_array( $order ) ){
            foreach($order as $key=>$val){
                $order_sql .= empty($order_sql)
                    ? "`{$key}` $val "
                    : " , `{$key}` $val" ;
            }
        }

        $sql = "SELECT {$field} FROM {$tab_name}"
            . ( empty($where_sql) ? "" : " WHERE {$where_sql}")
            . ( empty($order_sql) ? "" : " ORDER BY {$order_sql}");

        return $this->query($sql, $where);
    }

    /**
     * @param $tab_name
     * @param $one_field
     * @param null $where
     * @param null $order
     * @param null $having
     * @param int $offest
     * @param null $limit
     */
    function getField($tab_name, $one_field, $where=null, $order=null, $having=null, $offest=0 ,$limit=null){
        if( empty($one_field) || !is_string($one_field) || strpos($one_field, ',') !== false ){
            trigger_error("one_filed参数需一个字段名", E_USER_ERROR);
        }
        $data = $this->get($tab_name, $one_field, $where, $order, $having, $offest, $limit);
        if( empty($data) ) return array();

        $result = array();
        foreach($data as $key=>$val){
            $result[] = $val;
        }
        return $result;
    }

    function getOne($tab_name,$field='*',$where=null, $order=null){

        if( is_string($where) ){
            $where_sql = $where;
        }elseif( is_array( $where ) ){
            foreach($where as $k=>$val){
                $where_sql .= empty($where_sql)
                    ? "`{$k}` = :{$k}"
                    : " AND `{$k}` = :{$k}";
            }
        }

        if( is_string($order) ){
            $order_sql = $order;
        }elseif( is_array( $order ) ){
            foreach($order as $k=>$val){
                $order_sql .= empty($order_sql)
                    ? "`{$k}` = " . $this->escape($val)
                    : " , `{$k}` = " . $this->escape($val);
            }
        }

        $sql = "SELECT {$field} FROM {$tab_name}"
            . ( empty($where_sql) ? "" : " WHERE {$where_sql}")
            . ( empty($order_sql) ? "" : " ORDER BY {$order_sql}")
            . " limit 0,1";

        $result = $this->query($sql, $where);

        return is_array($result) ? $result[0] : $result;
    }

    /**
     * 转义mysql字符，这函数不转义 % 和 _ 字符
     * 这个函数将会确定数据类型，以便仅对字符串类型数据进行转义。
     * 它将会在数据的周围自动增加单引号，所以你不能这样做：
     * [code]$sql = "INSERT INTO table (title) VALUES('.$this->escape($title).')";[/code]
     */
    function escape($val){
        if(strval(intval($val)) == strval($val) || strval(floatval($val)) == strval($val)){
            //数字类型处理
            return $val;
        }

        $str = "'" . $val . "'";
        return $str;
    }
}