<?php
/**
 * Created by PhpStorm.
 * User: zhangy
 * Date: 15/3/20
 * Time: 下午9:20
 */

class UserModel extends Model{

    const TAB_NAME_USER = "user";

    private $db;

    public function __construct(){
        $this->db = SQLite::getInstance();
    }

    public function listUsers($keyword="", $page=1, $pagesize=20){

        if(!empty($keyword)){
            $where = "user_name like '%{$keyword}%' or real_name like '%{$keyword}%'";
        }

        $count = $this->db->getOne(self::TAB_NAME_USER, "COUNT(*) as num", $where);

        $page_count = ceil($count['num'] / max(1, $pagesize) );
        $page = max(1, min($page, $page_count));
        $offset = ($page - 1) * $pagesize;
        $limit = $pagesize;

        $list = $this->db->get(self::TAB_NAME_USER, '*', $where, null, null, $offset, $limit);

        return array(
            'list' => $list,
            'page'  => array(
                'no'        => $page,
                'count'     => $page_count,
                'rec_num'   => $count['num'],
            )
        );
    }

    public function add($user_name, $password, $real_name){
        $row = $this->db->getOne(self::TAB_NAME_USER, '*', array('user_name'=>$user_name));
        if(!empty($row)){
            throw new Exception('用户已存在');
        }

        return $this->db->insert(self::TAB_NAME_USER, array(
            'user_name' => $user_name,
            'password'  => md5($password),
            'real_name' => $real_name,
        ));
    }


    public function checkPwdByMobile($mobile, $pwd){
        $user_info = $this->getUserByMobile($mobile);

        return $user_info['password'] == md5($pwd);
    }

    /**
     * 更新密码
     * @param $mobile
     * @param $pwd
     * @return bool
     */
    public function updatePassword($mobile, $pwd){
        $data = array(
            'mobile' => $mobile,
            'password' => md5($pwd)
        );

        return $this->db->update(self::TAB_NAME_USERS, $data, array('mobile' => $mobile));
    }

    /**
     * 更新用户信息
     * @param $mobile
     * @param $user_info
     * @return bool
     */
    public function updateUserInfo($mobile, $user_info){
        if(empty($user_info) || !is_array($user_info)) return false;
        unset($user_info['password']);
        unset($user_info['mobile']);
        unset($user_info['user_id']);

        return $this->db->update(self::TAB_NAME_USERS, $user_info, array('mobile'=>$mobile));
    }
}