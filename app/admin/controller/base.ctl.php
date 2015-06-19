<?php
/**
 *
 * 
 *
 * 
 * @file:   base.ctl.php
 * @date:   2015/2/26
 */
class BaseController extends Controller
{

    private $auth_info;

	public function __construct() {

	}

    public function getAuthorizeInfo(){

        if( empty($auth_info) ){
            $auth_info = UserModel::getInstance()->getAuthorizeInfo( $this->getAuthCookie() );
        }

        return $auth_info;
    }

    public function checkLogin(){

        $auth_info = $this->getAuthCookie();

        if(empty($auth_info)){
            $this->showJsonResult(ApiDictHelper::CODE_AUTH_NOT_ENOUGH, "权限不足", "");
            exit;
        }
    }

    public function getAuthCookie(){
        return $_REQUEST['authcookie'];
    }
}
