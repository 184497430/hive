<?php
/**
 * Created by PhpStorm.
 * User: zhangy
 * Date: 15/3/20
 * Time: 上午11:44
 */

class UserController extends BaseController{

    public function __construct() {
        parent::__construct();
    }

    public function checkExistAction(){
        $mobile = trim( $this->getPostParam('mobile', '') );

        $user_info = UserModel::getInstance()->getUserByMobile($mobile);

        if(!empty($user_info)) {
            $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, "", "exist");
        }else{
            $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, "", "no find");
        }
    }

    public function loginAction(){
        $mobile = trim( $this->getPostParam('mobile', '') );
        $password = $this->getPostParam('pwd', '');
        $unique_no = $this->getPostParam('unique_no', '');

        if( UserModel::getInstance()->checkPwdByMobile($mobile, $password) ){
            $authorizeInfo = UserModel::getInstance()->getNewAuthorize($mobile,$unique_no);
            $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, "sucess", $authorizeInfo['authcookie']);
        }else{
            $this->showJsonResult(ApiDictHelper::CODE_FAILE, "用户名或密码错误", "");
        }
    }

    public function sendVerifyCodeAction(){
        $mobile = trim( $this->getPostParam('mobile', '') );
        $password = $this->getPostParam('pwd', '');

        $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, "sucess", "");
    }

    public function regAction(){

        $mobile = $this->getPostParam('mobile', '');
        $password = $this->getPostParam('pwd', '');
        $verify_code = $this->getPostParam('verify_code', '');

        //验证参数
        if( empty($mobile) ){
            $this->showJsonResult(ApiDictHelper::CODE_PARAMETER_INVALID, "手机号不能为空", "");
            exit;
        }

        if( empty( $password ) ){
            $this->showJsonResult(ApiDictHelper::CODE_PARAMETER_INVALID, "密码不能为空", "");
            exit;
        }

        $user_info = UserModel::getInstance()->getUserByMobile($mobile);
        if(!empty($user_info)){
            $this->showJsonResult(ApiDictHelper::CODE_FAILE, "手机号已被注册了！");
            exit;
        }

        $result = UserModel::getInstance()->addUser($mobile, $password);

        if($result){
            $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, "sucess", "");
        }else{
            $this->showJsonResult(ApiDictHelper::CODE_DB_OPT_FAILE, "失败", "");
        }

    }

    public function getAuthorizeTimeAction(){
        $authcookie = $this->getPostParam('auth_cookie', '');

        $authorize_info = UserModel::getInstance()->getAuthorizeInfo($authcookie);
        if($authorize_info){
            $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, "success", $authorize_info["modify_time"]);
        }else{
            $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, "success", "");
        }
    }

    public function getMyInfoAction(){
        $this->checkLogin();
        $auth_info = $this->getAuthorizeInfo();
        $my_info = UserModel::getInstance()->getUserByMobile($auth_info['mobile']);

        if( is_array( $my_info ) ){
            $my_info['thumb_url'] = staticRes($my_info['thumb']);
            $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, "success", $my_info);
            exit;
        }else{
            $this->showJsonResult(ApiDictHelper::CODE_DB_OPT_FAILE, "fail", null);
            exit;
        }
    }

    public function getUserInfoAction(){
        $this->checkLogin();

        $mobile = $this->getPostParam('mobile', '');

        $user_info = UserModel::getInstance()->getUserByMobile($mobile);

        if( is_array( $user_info ) ){
            if( !empty($user_info) ){
                $user_info['thumb_url'] = staticRes($user_info['thumb']);
                $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, "success", $user_info);
            }else{
                $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, "success", null);
            }
            exit;
        }else{
            $this->showJsonResult(ApiDictHelper::CODE_DB_OPT_FAILE, "fail", null);
            exit;
        }
    }

    public function uploadThumbAction(){
        $uploads_dir = dirname( dirname(APP_ROOT) ) . "/public/api/static";
        if( $_FILES["thumb"]["error"] == UPLOAD_ERR_OK ){
            $tmp_name = $_FILES["thumb"]["tmp_name"];
            $name = $_FILES["thumb"]["name"];
            move_uploaded_file($tmp_name, "$uploads_dir/$name");
            echo $user_info['thumb_url'] = staticRes($name);
        }
    }

    public function updateMyInfoAction(){
        $this->checkLogin();

        $thumb_url = parse_url($this->getPostParam('thumb_url', ''));

        $thumb = substr($thumb_url["path"], strrpos($thumb_url["path"], "/") + 1 );
        if(thumb != ""){
            $_POST['thumb'] = $thumb;
        }

        $update_fields = array('nickname', 'thumb', 'birthday', 'sex', 'city', 'style');
        $user_info = array_intersect_key( $_POST, array_flip($update_fields) );

        $auth_info = $this->getAuthorizeInfo();

        if(UserModel::getInstance()->updateUserInfo($auth_info['mobile'], $user_info)){
            $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, 'success', "");
            exit;
        }else{
            $this->showJsonResult(ApiDictHelper::CODE_DB_OPT_FAILE, 'fail', "");
            exit;
        }

    }

    public function checkPasswordAction(){
        $this->checkLogin();

        $pwd = $this->getPostParam('pwd', '');
        $auth_info = $this->getAuthorizeInfo();

        if( UserModel::getInstance()->checkPwdByMobile($auth_info['mobile'], $pwd) ){
            $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, "success", true);
            exit;
        }else{
            $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, "success", false);
            exit;
        }
    }

    public function updatePasswordAction(){
        $this->checkLogin();

        $pwd = $this->getPostParam('pwd', '');
        $auth_info = $this->getAuthorizeInfo();

        if( UserModel::getInstance()->updatePassword($auth_info['mobile'], $pwd) ){
            $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, "success", "");
            exit;
        }else{
            $this->showJsonResult(ApiDictHelper::CODE_DB_OPT_FAILE, "失败", "");
            exit;
        }
    }
}