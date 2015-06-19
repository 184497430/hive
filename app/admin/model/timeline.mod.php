<?php
/**
 * Created by PhpStorm.
 * User: zhangy
 * Date: 15/3/22
 * Time: 上午11:23
 */

class TimelineModel  extends Model{

    const TAB_NAME_TIMELINES = 't_timelines';

    private $db;

    public function __construct(){
        $this->db = Mysql::getInstance('dada');
    }


    public function getTimelineByAuthorAndIssueDate($author_user_id, $issue_date){
        return $this->db->getOne(self::TAB_NAME_TIMELINES, '*', array(
            'author_user_id'    => $author_user_id,
            'issue_date'        => $issue_date,
        ));
    }

    public function getTimelinesByUserIds($arr_user_id){

        if(!is_array($arr_user_id)) return false;

        foreach($arr_user_id as $key=>$val){
            $arr_user_id[$key] = intval($val);
        }
        $arr_user_id = array_unique(array_filter($arr_user_id));
        $str_user_id = implode(',', $arr_user_id);
        if(empty($str_user_id)) return array();

        $where = "author_user_id in ({$str_user_id}) ";

        return $this->db->get(self::TAB_NAME_TIMELINES, '*', $where, array('issue_date'=>'desc'));
    }

    public function addTimeline($author_user_id, $issue_date, $cover_image){
        $data = array(
            'author_user_id'    => $author_user_id,
            'issue_date'        => $issue_date,
            'cover_image'       => $cover_image,
            'fitting_count'     => 0,
            'photo_count'   => 0,
            'reply_count'      => 0,
            'add_time'          => date('Y-m-d H:i:s'),
            'modify_time'          => date('Y-m-d H:i:s'),
        );
        return $this->db->insert( self::TAB_NAME_TIMELINES, $data );
    }

    public function incFittingNum(){

    }

    public function incPhotoNumber(){

    }

    public function incReplyNumber(){

    }
}