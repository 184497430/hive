<?php
/**
 * Created by PhpStorm.
 * User: zhangy
 * Date: 15/3/28
 * Time: 下午3:55
 */

class TimelineController  extends BaseController{

    public function __construct() {
        parent::__construct();
    }

    public function getMyFriendTimelinesAction(){
        $this->checkLogin();
        $auth_info = $this->getAuthorizeInfo();

        $my_info = UserModel::getInstance()->getUserByMobile($auth_info['mobile']);
        if(empty($my_info)){
            $this->showJsonResult(ApiDictHelper::CODE_DB_OPT_FAILE, "faile", null);
            exit;
        }

        $tmp_friends = FriendModel::getInstance()->getFriends($my_info['user_id'], FriendModel::RELATION_FRIEND_AND_FRIEND);

        $friends[$my_info['user_id']] = $my_info;
        foreach($tmp_friends as $key=>$val){
            $friends[ $val['user_id'] ] = $val;
        }

        $friend_user_ids = array_keys($friends);
        $timelines = TimelineModel::getInstance()->getTimelinesByUserIds($friend_user_ids);

        if( is_array($timelines) ){

            foreach($timelines as $key=> $val){
                $timelines[$key]['cover_image'] = staticRes($timelines[$key]['cover_image']);
                $timelines[$key]['author_user_info'] = $friends[ $val['author_user_id'] ];
            }

            $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, "success", $timelines);
        }else{
            $this->showJsonResult(ApiDictHelper::CODE_FAILE, "faile", null);
        }
    }
}