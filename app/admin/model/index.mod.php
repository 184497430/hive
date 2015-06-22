<?php
/**
 * Created by PhpStorm.
 * User: zhangy
 * Date: 15/3/20
 * Time: 下午9:20
 */

class UserModel extends Model{

    const TAB_NAME_USERS = "t_users";
    const TAB_USER_AUTH = "t_user_authorizes";

    private $db;

    public function __construct(){
        $this->db = Mysql::getInstance('dada');
    }

    public function checkPwdByMobile($mobile, $pwd){
        $user_info = $this->getUserByMobile($mobile);

        return $user_info['password'] == md5($pwd);
    }

    public function getAuthorizeInfo($authcookie){
        return $this->db->getOne(self::TAB_USER_AUTH, '*', array('authcookie'=>$authcookie));
    }

    public function getNewAuthorize($mobile, $unique_no){
        $authInfo = $this->db->getOne(self::TAB_USER_AUTH, '*', array('mobile'=>$mobile));
        if($authInfo){
            $authInfo['authcookie'] = sha1($mobile . $unique_no . md5( rand(100000, 999999) ) . time());
            $authInfo['modify_time'] = date('Y-m-d H:i:s');
            $this->db->update(self::TAB_USER_AUTH, $authInfo, array('mobile'=>$mobile));
        }else{
            $authInfo['authcookie'] = sha1($mobile . $unique_no . md5( rand(100000, 999999) ) . time());
            $authInfo['mobile'] = $mobile;
            $authInfo['add_time'] = date('Y-m-d H:i:s');
            $authInfo['modify_time'] = date('Y-m-d H:i:s');
            $this->db->insert(self::TAB_USER_AUTH, $authInfo);
        }

        return $authInfo;
    }

    /**
     * 获取用户信息
     * @param $mobile
     * @return array|bool
     */
    public function getUserByMobile($mobile){
        return $this->db->getOne( self::TAB_NAME_USERS, '*', array('mobile'=>$mobile));
    }

    public function getUserById($user_id){
        return $this->db->getOne( self::TAB_NAME_USERS, '*', array('user_id'=>$user_id));
    }

    /**
     * 新增用户
     * @param $mobile
     * @param $pwd
     * @param array $others
     * @return bool
     */
    public function addUser($mobile, $pwd, $others = array()){
        $others['mobile'] = $mobile;
        $others['password'] = md5( $pwd );
        return $this->db->insert( self::TAB_NAME_USERS, $others );
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