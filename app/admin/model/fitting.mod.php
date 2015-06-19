<?php
/**
 * Created by PhpStorm.
 * User: zhangy
 * Date: 15/3/22
 * Time: 上午11:23
 */

class FittingModel  extends Model{

    const TAB_NAME_FITTINGS = 't_fittings';

    private $db;

    public function __construct(){
        $this->db = Mysql::getInstance('dada');
    }

    public function addFitting($timeline_id, $author_user_id, $fitting_time, $others = array()){

        unset($others['fitting_id']);

        $others['timeline_id'] = $timeline_id;
        $others['author_user_id'] = $author_user_id;
        $others['fitting_time'] = $fitting_time;
        $others['liked_count'] = 0;
        $others['reply_count'] = 0;
        $tmp = array_unique(array_filter(explode(',', $others['photos'])));
        $others['photos'] = implode(',', $tmp);
        $others['photo_count'] = count($tmp);
        $others['add_time'] = date('Y-m-d H:i:s');
        $others['modify_time'] = date('Y-m-d H:i:s');

        return $this->db->insert( self::TAB_NAME_FITTINGS, $others );

    }

    public function getFittingsByTimeLine($timeline_id, $author_user_id){
        return $this->db->get(self::TAB_NAME_FITTINGS, '*',
            array('timeline_id'=>$timeline_id, 'author_user_id'=>$author_user_id),
            array('add_time'=>'desc'));
    }
}