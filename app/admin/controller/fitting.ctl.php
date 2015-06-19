<?php
/**
 * Created by PhpStorm.
 * User: zhangy
 * Date: 15/3/28
 * Time: 下午5:17
 */

class FittingController extends BaseController{

    public function __construct() {
        parent::__construct();
    }


    public function uploadPhotosAction(){
        $uploads_dir = dirname( dirname(APP_ROOT) ) . "/public/api/static";

        $files = array();
        for($i=0; $i < count($_FILES["photos"]['name']); $i++){
            if($_FILES["photos"]["error"][$i] == UPLOAD_ERR_OK){
                $tmp_name = $_FILES["photos"]["tmp_name"][$i];
                $name = $_FILES["photos"]["name"][$i];
                move_uploaded_file($tmp_name, "$uploads_dir/$name");
                $files[] = staticRes($name);
            }
        }
        echo json_encode($files);
    }

    public function issueAction(){
        $this->checkLogin();

        $fitting_time = $this->getPostParam("fitting_time", "");
        $brand = $this->getPostParam("brand", "");
        $trappings_category = $this->getPostParam("trappings_category", "");
        $author_talk = $this->getPostParam("author_talk", "");
        $location_longitude = $this->getPostParam("location_longitude", -1);
        $location_latitude = $this->getPostParam("location_latitude", -1);

        $tmp = $this->getPostParam("photos", "");
        if(!empty($tmp)){
            $tmp = json_decode($tmp, true);
            if(is_array($tmp)){
                foreach($tmp as $key=>$val){
                    $p= substr($val, strrpos($val, "/") + 1 );
                    if($p != ""){
                        $photos[] = $p;
                    }
                }
            }
        }

        $auth_info = $this->getAuthorizeInfo();
        $my_info = UserModel::getInstance()->getUserByMobile($auth_info['mobile']);
        if(empty($my_info)){
            $this->showJsonResult(ApiDictHelper::CODE_DB_OPT_FAILE, "faile", null);
            exit;
        }

        $issue_date = date('Y-m-d');

        $timeline = TimelineModel::getInstance()->getTimelineByAuthorAndIssueDate($my_info['user_id'], $issue_date);

        if(empty($timeline)){
            $timeline_id = TimelineModel::getInstance()->addTimeline($my_info['user_id'], $issue_date, $photos[0]);
        }else{
            $timeline_id = $timeline['timeline_id'];
        }

        $others = array(
            'fitting_time'          => $fitting_time,
            'brand'                 => $brand,
            'photos'                => implode(',', $photos),
            'first_photo'           => $photos[0],
            'trappings_category'    => $trappings_category,
            'author_talk'           => $author_talk,
            'location_longitude'    => $location_longitude,
            'location_latitude'     => $location_latitude
        );

        $result = FittingModel::getInstance()->addFitting($timeline_id, $my_info['user_id'], date('Y-m-d H:i:s'), $others);
        if($result){
            $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, "sucess", "");
        }else{
            $this->showJsonResult(ApiDictHelper::CODE_DB_OPT_FAILE, "失败", "");
        }
    }

    public function getFittingsInTimelineAction(){
        $this->checkLogin();

        $timeline_id = $this->getPostParam("timeline_id", "");
        $author_user_id = $this->getPostParam("author_user_id", "");

        $auth_info = $this->getAuthorizeInfo();
        $my_info = UserModel::getInstance()->getUserByMobile($auth_info['mobile']);
        if(empty($my_info)){
            $this->showJsonResult(ApiDictHelper::CODE_DB_OPT_FAILE, "faile", null);
            exit;
        }

        if( $my_info['user_id'] != $author_user_id
            && FriendModel::getInstance()->getRelation($my_info['user_id'], $author_user_id) != FriendModel::RELATION_FRIEND_AND_FRIEND){

            $this->showJsonResult(ApiDictHelper::CODE_FAILE, "无权限", null);
            exit;
        }

        $result = FittingModel::getInstance()->getFittingsByTimeLine($timeline_id, $author_user_id);
        if($result){

            foreach($result as $key=>$val){
                $result[$key]['photos'] = explode(",", $result[$key]['photos']);
                foreach( $result[$key]['photos'] as $i=> $p){
                    $result[$key]['photos'][$i] = staticRes($p);
                }
            }

            $this->showJsonResult(ApiDictHelper::CODE_SUCCESS, "sucess", $result);
        }else{
            $this->showJsonResult(ApiDictHelper::CODE_DB_OPT_FAILE, "失败", null);
        }
    }
}