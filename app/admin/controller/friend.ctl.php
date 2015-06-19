<?php
/**
 * Created by PhpStorm.
 * User: zhangy
 * Date: 15/3/22
 * Time: 下午5:28
 */

class FriendController  extends BaseController{

    public function __construct() {
        parent::__construct();
    }


    public function checkApplableAction(){
        $this->checkLogin();

        $auth_info = $this->getAuthorizeInfo();
        $friend_uid = $this->getPostParam('friend_uid');

        $my_info = UserModel::getInstance()->getUserByMobile($auth_info['mobile']);

        if(empty($my_info)){
            $this->showJsonResult(ApiDictHelper::CODE_DB_OPT_FAILE, "fail", false);
            exit;
        }

        if($my_info['user_id'] == $friend_uid){
            $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, "不能自己加自己", false);
            exit;
        }

        $relation = FriendModel::getInstance()->getRelation($my_info['user_id'], $friend_uid);

        if($relation == FriendModel::RELATION_FRIEND_AND_FRIEND){
            $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, "已是好友", false);
            exit;
        }

        $make_info = FriendModel::getInstance()->getMakeInfoLastWeek($my_info['user_id'], $friend_uid);

        if( !empty($my_info) && $make_info['propose_user_id'] = $my_info['user_id']){
            if( in_array($make_info['status'], array(FriendModel::MAKE_FIREND_APPLY, FriendModel::MAKE_FIREND_IGNORE, FriendModel::MAKE_FIREND_REFUSE)) ){
                $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, "1周内不能重复", false);
                exit;
            }
        }

        $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, "success", true);
    }

    public function acceptFriendAction(){
        $this->checkLogin();
        $auth_info = $this->getAuthorizeInfo();
        $make_id = $this->getPostParam('make_id');

        $my_info = UserModel::getInstance()->getUserByMobile($auth_info['mobile']);
        if(empty($my_info)){
            $this->showJsonResult(ApiDictHelper::CODE_DB_OPT_FAILE, "fail", "");
            exit;
        }

        $result = FriendModel::getInstance()->acceptFriend($make_id, $my_info['user_id']);

        if($result == true){
            $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, "success", "");
        }else{
            $this->showJsonResult(ApiDictHelper::CODE_FAILE, "faile", "");
        }
    }

    public function applyFriendAction(){
        $this->checkLogin();
        $auth_info = $this->getAuthorizeInfo();
        $friend_uid = $this->getPostParam('friend_uid');

        $my_info = UserModel::getInstance()->getUserByMobile($auth_info['mobile']);
        if(empty($my_info)){
            $this->showJsonResult(ApiDictHelper::CODE_DB_OPT_FAILE, "fail", "");
            exit;
        }

        $friend_info = UserModel::getInstance()->getUserById($friend_uid);
        if(empty($friend_info)){
            $this->showJsonResult(ApiDictHelper::CODE_DB_OPT_FAILE, "fail", "");
            exit;
        }

        $result = FriendModel::getInstance()->applyFriend($my_info['user_id'], $friend_uid);

        if($result == true){
            $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, "success", "");
        }else{
            $this->showJsonResult(ApiDictHelper::CODE_FAILE, "faile", "");
        }
    }

    public function getMyFriendsAction(){
        $this->checkLogin();
        $auth_info = $this->getAuthorizeInfo();
        $relation = $this->getPostParam('relation');

        if(! in_array($relation, array(
            FriendModel::RELATION_FRIEND_AND_STRANGE,
            FriendModel::RELATION_FRIEND_AND_FRIEND,
            FriendModel::RELATION_STRANGE_AND_FRIEND))){

            $this->showJsonResult(ApiDictHelper::CODE_PARAMETER_INVALID, "无效的参数", null);
            exit;
        }

        $my_info = UserModel::getInstance()->getUserByMobile($auth_info['mobile']);
        if(empty($my_info)){
            $this->showJsonResult(ApiDictHelper::CODE_DB_OPT_FAILE, "faile", null);
            exit;
        }

        $friends = FriendModel::getInstance()->getFriends($my_info['user_id'], $relation);

        if( is_array($friends) ){

            foreach($friends as $key=> $val){
                $friends[$key]['friend_user_info']['thumb_url'] = staticRes($friends[$key]['friend_user_info']['thumb']);
            }

            $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, "success", $friends);
        }else{
            $this->showJsonResult(ApiDictHelper::CODE_FAILE, "faile", null);
        }
    }

    public function getMyMakeFriendMsgsAction(){
        $this->checkLogin();
        $auth_info = $this->getAuthorizeInfo();

        $my_info = UserModel::getInstance()->getUserByMobile($auth_info['mobile']);
        if(empty($my_info)){
            $this->showJsonResult(ApiDictHelper::CODE_DB_OPT_FAILE, "faile", null);
            exit;
        }

        $make_msgs = FriendModel::getInstance()->getMakeFriendMsgs($my_info['user_id']);

        foreach($make_msgs as $key => $each){
            $ta_user_id = $each['user_id1'] != $my_info['user_id'] ? $each['user_id1'] : $each['user_id2'];
            $make_msgs[$key]['ta_user_info'] = UserModel::getInstance()->getUserById($ta_user_id);
            $make_msgs[$key]['ta_user_info']['thumb_url'] = staticRes($make_msgs[$key]['ta_user_info']['thumb']);
        }

        if( is_array($make_msgs) ){
            $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, "success", $make_msgs);
        }else{
            $this->showJsonResult(ApiDictHelper::CODE_FAILE, "faile", null);
        }
    }
}