<?php
/**
 * Created by PhpStorm.
 * User: zhangy
 * Date: 15/3/22
 * Time: 上午11:23
 */

class FriendModel  extends Model{

    const TAB_NAME_USERS = "t_users";
    const TAB_NAME_FRIEND_MAKES = 't_friend_makes';
    const TAB_NAME_FRIEND_BLACK_LISTS = 't_friend_black_lists';
    const TAB_NAME_FRIENDS = "t_friends";

    const RELATION_MEYBE_FRIEND = -1;
    const RELATION_STRANGE_AND_STRANGE = 0;
    const RELATION_STRANGE_AND_FRIEND = 1;
    const RELATION_FRIEND_AND_STRANGE = 2;
    const RELATION_FRIEND_AND_FRIEND = 3;

    const RELATION_FRIEND_OR_FRIEND = 99;

    const MAKE_FIREND_MEYBE = 0;
    const MAKE_FIREND_APPLY = 1;
    const MAKE_FIREND_ACCEPT = 2;
    const MAKE_FIREND_REFUSE = 3;
    const MAKE_FIREND_IGNORE = 4;



    private $db;

    public function __construct(){
        $this->db = Mysql::getInstance('dada');
    }

    public function getRelation($user_id1, $user_id2){
        $result = $this->db->getOne(self::TAB_NAME_FRIENDS, 'relation', array('user_id'=> $user_id1, 'friend_user_id'=>$user_id2));
        if(empty($result)) return false;

        return $result['relation'];
    }

    /**
     * 获取好友
     * @param $user_id
     * @param $relation
     *  RELATION_FRIEND_AND_FRIEND 双向好友
     *  RELATION_FRIEND_AND_STRANGE 我对ta友好，ta对我陌生
     *  RELATION_STRANGE_AND_FRIEND 我对ta陌生，ta对我友好
     *  RELATION_FRIEND_OR_FRIEND 我对ta友好，或者ta对我友好
     * @return array|bool
     */
    public function getFriends($user_id, $relation= self::RELATION_FRIEND_AND_FRIEND){
        if( intval($user_id) < 1) return array();

        if( in_array($relation, array(self::RELATION_FRIEND_AND_FRIEND, self::RELATION_FRIEND_AND_STRANGE, self::RELATION_STRANGE_AND_FRIEND))){
            $friends = $this->db->get(self::TAB_NAME_FRIENDS, '*', array('user_id'=>$user_id, 'relation'=>$relation));
        }else if($relation == self::RELATION_FRIEND_OR_FRIEND){
            $where = "user_id = '{$user_id}' AND relation in (" . self::RELATION_FRIEND_AND_FRIEND
                . ", " . self::RELATION_FRIEND_AND_STRANGE . " , " . self::RELATION_STRANGE_AND_FRIEND . ") ";

            $friends = $this->db->get(self::TAB_NAME_FRIENDS, '*', $where);
        }

        $friend_user_id = array();
        foreach($friends as $key => $val){
            $friend_user_id[] = $val['friend_user_id'];
        }

        if( empty($friend_user_id) ){ return array(); }

        $friend_user_id = implode(',', $friend_user_id);

        $where = "user_id in ({$friend_user_id}) ";

        $users = $this->db->get(self::TAB_NAME_USERS, '*', $where, array('nickname'=>'asc'));

        foreach($users as $key => $val){
            $tmp[$val['user_id']] = $val;
        }

        foreach($friends as $key=>$val){
            $friends[$key]['friend_user_info'] = $tmp[$val['friend_user_id']];
        }

        return $friends;
    }

    public function getFriendsUserId($user_id, $relation= self::RELATION_FRIEND_AND_FRIEND){
        if( intval($user_id) < 1) return array();

        if( in_array($relation, array(self::RELATION_FRIEND_AND_FRIEND, self::RELATION_FRIEND_AND_STRANGE, self::RELATION_STRANGE_AND_FRIEND))){
            $friends = $this->db->getField(self::TAB_NAME_FRIENDS, 'friend_user_id', array('user_id'=>$user_id, 'relation'=>$relation));
        }else if($relation == self::RELATION_FRIEND_OR_FRIEND){
            $where = "user_id = '{$user_id}' AND relation in (" . self::RELATION_FRIEND_AND_FRIEND
                . ", " . self::RELATION_FRIEND_AND_STRANGE . " , " . self::RELATION_STRANGE_AND_FRIEND . ") ";

            $friends = $this->db->getField(self::TAB_NAME_FRIENDS, 'friend_user_id', $where);
        }

        return $friends;
    }

    public function changeFriendRel($user_id, $friend_user_id, $relation){

        switch($relation){
            case self::RELATION_FRIEND_AND_FRIEND:
                $relation1 = $relation2 = self::RELATION_FRIEND_AND_FRIEND;
                break;
            case self::RELATION_FRIEND_AND_STRANGE:
                $relation1 = self::RELATION_FRIEND_AND_STRANGE;
                $relation2 = self::RELATION_STRANGE_AND_FRIEND;
                break;
            case self::RELATION_STRANGE_AND_FRIEND:
                $relation1 = self::RELATION_STRANGE_AND_FRIEND;
                $relation2 = self::RELATION_FRIEND_AND_STRANGE;
                break;
            default:
                return false;
                break;
        }

        $rel1 = $this->db->getField(self::TAB_NAME_FRIENDS, 'relation', array('user_id' => $user_id, 'friend_user_id'=>$friend_user_id));
        if(empty($rel1)){
            $this->db->insert(self::TAB_NAME_FRIENDS, array(
                'user_id' => $user_id,
                'friend_user_id' => $friend_user_id,
                'intimate_value' => 0,
                'relation'  => $relation1,
                'add_time' => date('Y-m-d H:i:s'),
                'modify_time' => date('Y-m-d H:i:s'),
            ));
        }else{
            $this->db->update(self::TAB_NAME_FRIENDS, array(
                'relation'  => $relation1,
                'modify_time' => date('Y-m-d H:i:s'),
            ), array('user_id'=>$user_id, 'friend_user_id' => $friend_user_id));
        }



        $rel2 = $this->db->getField(self::TAB_NAME_FRIENDS, 'relation', array('user_id' => $friend_user_id, 'friend_user_id'=>$user_id));
        if(empty($rel2)){
            $this->db->insert(self::TAB_NAME_FRIENDS, array(
                'user_id' => $friend_user_id,
                'friend_user_id' => $user_id,
                'intimate_value' => 0,
                'relation'  => $relation2,
                'add_time' => date('Y-m-d H:i:s'),
                'modify_time' => date('Y-m-d H:i:s'),
            ));
        }else{
            $this->db->update(self::TAB_NAME_FRIENDS, array(
                'relation'  => $relation2,
                'modify_time' => date('Y-m-d H:i:s'),
            ), array('user_id'=>$friend_user_id, 'friend_user_id' => $user_id));
        }

        return true;
    }

    /**
     * 取消我好友列表中的人
     * @param $user_id
     * @param $invite_to
     */
    public function removeFriend($user_id, $friend_user_id){
        $relation1 = $this->db->getField(self::TAB_NAME_FRIENDS, 'relation', array('user_id' => $user_id, 'friend_user_id'=>$friend_user_id));
        $relation2 = $this->db->getField(self::TAB_NAME_FRIENDS, 'relation', array('user_id' => $friend_user_id, 'friend_user_id'=>$user_id));

        if( empty($relation1) || empty($relation2) ) return false;

        $relation1 = $relation1[0] | 1;
        $relation2 = $relation2[0] | 2;
        $this->db->update(self::TAB_NAME_FRIENDS, array('relation'=>$relation1), array('user_id' => $user_id, 'friend_user_id'=>$friend_user_id));
        $this->db->update(self::TAB_NAME_FRIENDS, array('relation'=>$relation2), array('user_id' => $friend_user_id, 'friend_user_id'=>$user_id));
    }

    /**
     * 获得交友信息
     * @param $user_id
     */
    public function getMakeFriendMsgs($user_id){

        $params = array(
            "user_id1" => intval($user_id),
            "user_id2" => intval($user_id)
        );
        $sql = "SELECT * FROM " . self::TAB_NAME_FRIEND_MAKES ." WHERE user_id1 = :user_id1 OR user_id2=:user_id2";
        $result = $this->db->query($sql, $params);

        return $result;
    }

    public function getMakeInfoLastWeek($user_id1, $user_id2){
        $params = array(
            "user_id1" => intval($user_id1),
            "user_id2" => intval($user_id2)
        );

        $sql = "SELECT * FROM " . self::TAB_NAME_FRIEND_MAKES
            ." WHERE (user_id1 = :user_id1 AND user_id2=:user_id2) OR ((user_id1 = :user_id2 AND user_id2=:user_id1)) ORDER BY modify_time DESC limit 0,1";

        $result = $this->db->query($sql, $params);

        if(!isset($result[0])) return array();
        if($result[0]['modify_time'] < date("Y-m-d 00:00:00", strtotime("-7 day"))) return array();

        return $result[0];
    }

    /**
     * 新增可能是朋友的消息
     * @param $user_id1
     * @param $user_id2
     */
    public function maybeFriend($user_id1, $user_id2, $find_from=''){

        $params = array(
            "user_id1" => intval($user_id1),
            "user_id2" => intval($user_id2)
        );

        $sql = "SELECT * FROM " . self::TAB_NAME_FRIEND_MAKES
            ." WHERE (user_id1 = :user_id1 AND user_id2=:user_id2) OR ((user_id1 = :user_id2 AND user_id2=:user_id1))";

        $result = $this->db->query($sql, $params);

        if(!empty($result)) return false;

        return $this->db->insert(self::TAB_NAME_FRIEND_MAKES, array(
            'propose_user_id' => -1,
            'user_id1'      => intval($user_id1),
            'user_id2'      => intval($user_id2),
            'status'        => self::MAKE_FIREND_MEYBE,
            'find_from'     => $find_from,
            'add_time'      => date('Y-m-d H:i:s'),
            'modify_time'   => date('Y-m-d H:i:s'),
        ));
    }


    /**
     * 申请加好友
     * @param $propose_user_id
     * @param $user_id1
     * @param $user_id2
     */
    public function applyFriend($apply_user_id, $to_user_id, $find_from=""){
        $apply_user_id = intval($apply_user_id);
        $to_user_id = intval($to_user_id);

        $relation = $this->db->getField(self::TAB_NAME_FRIENDS, 'relation', array('user_id' => $apply_user_id, 'friend_user_id'=>$to_user_id));

        if( !empty($relation) ){
            switch($relation[0]){
                case self::RELATION_FRIEND_AND_FRIEND:
                    //已经是好友
                    return true;
                    break;
                case self::RELATION_STRANGE_AND_FRIEND:
                    //直接加为好友
                    $this->db->update(self::TAB_NAME_FRIENDS, array('relation'=>self::RELATION_FRIEND_AND_FRIEND)
                        , array('user_id' => $apply_user_id, 'friend_user_id'=>$to_user_id));
                    $this->db->update(self::TAB_NAME_FRIENDS, array('relation'=>self::RELATION_FRIEND_AND_FRIEND)
                        , array('user_id' => $to_user_id, 'friend_user_id'=>$apply_user_id));
                    return true;
                    break;
            }
        }


        //发起申请

        $params = array(
            "user_id1" => $apply_user_id,
            "user_id2" => $to_user_id
        );

        $sql = "SELECT * FROM " . self::TAB_NAME_FRIEND_MAKES
            ." WHERE (user_id1 = :user_id1 AND user_id2=:user_id2) OR ((user_id1 = :user_id2 AND user_id2=:user_id1))";

        $make_friend_rec = $this->db->query($sql, $params);

        if(empty($make_friend_rec)){
            return $this->db->insert(self::TAB_NAME_FRIEND_MAKES, array(
                'propose_user_id' => $apply_user_id,
                'user_id1'      => $apply_user_id,
                'user_id2'      => $to_user_id,
                'status'        => self::MAKE_FIREND_APPLY,
                'find_from'     => $find_from,
                'add_time'      => date('Y-m-d H:i:s'),
                'modify_time'   => date('Y-m-d H:i:s'),
            ));
        }else{
            return $this->db->update(self::TAB_NAME_FRIEND_MAKES, array(
                'propose_user_id' => $apply_user_id,
                'user_id1'      => $apply_user_id,
                'user_id2'      => $to_user_id,
                'status'        => self::MAKE_FIREND_APPLY,
                'find_from'     => $find_from,
                'modify_time'   => date('Y-m-d H:i:s'),
            ), array('make_id'=>$make_friend_rec['make_id']));
        }
    }

    /**
     * 接受
     * @param $make_id
     * @param $opt_user_id
     */
    public function acceptFriend($make_id, $opt_user_id){
        $make_friend_rec = $this->db->getOne(self::TAB_NAME_FRIEND_MAKES, '*', array('make_id'=>$make_id));

        if( empty($make_friend_rec) || $make_friend_rec['user_id2'] != $opt_user_id || $make_friend_rec['status'] != self::MAKE_FIREND_APPLY){
            return false;
        }

        $make_friend_rec['status'] = self::MAKE_FIREND_ACCEPT;
        $make_friend_rec['modify_time'] = date('Y-m-d H:i:s');

        $ok = $this->db->update(self::TAB_NAME_FRIEND_MAKES, $make_friend_rec, array('make_id'=>$make_friend_rec['make_id']));
        if(!$ok) return false;

        $this->db->update(self::TAB_NAME_FRIENDS, array('relation'=>self::RELATION_FRIEND_AND_FRIEND)
            , array('user_id' => $make_friend_rec['user_id1'], 'friend_user_id'=>$make_friend_rec['user_id2']));
        $this->db->update(self::TAB_NAME_FRIENDS, array('relation'=>self::RELATION_FRIEND_AND_FRIEND)
            , array('user_id' => $make_friend_rec['user_id2'], 'friend_user_id'=>$make_friend_rec['user_id1']));

        $this->changeFriendRel($make_friend_rec['user_id1'], $make_friend_rec['user_id2'], self::RELATION_FRIEND_AND_FRIEND);

        return true;
    }

    /**
     * 拒绝
     * @param $make_id
     * @param $opt_user_id
     */
    public function refuseFriend($make_id, $opt_user_id){
        $make_friend_rec = $this->db->getOne(self::TAB_NAME_FRIEND_MAKES, '*', array('make_id'=>$make_id));

        if( empty($make_friend_rec) || $make_friend_rec['user_id2'] != $opt_user_id || $make_friend_rec['status'] != self::MAKE_FIREND_APPLY){
            return false;
        }

        $make_friend_rec['status'] = self::MAKE_FIREND_REFUSE;
        $make_friend_rec['modify_time'] = date('Y-m-d H:i:s');

        return $this->db->update(self::TAB_NAME_FRIEND_MAKES, $make_friend_rec, array('make_id'=>$make_friend_rec['make_id']));
    }

    /**
     * 忽略
     * @param $make_id
     * @param $opt_user_id
     */
    public function ignoreFriend($make_id, $opt_user_id){
        $make_friend_rec = $this->db->getOne(self::TAB_NAME_FRIEND_MAKES, '*', array('make_id'=>$make_id));

        if( empty($make_friend_rec) || $make_friend_rec['user_id2'] != $opt_user_id || $make_friend_rec['status'] != self::MAKE_FIREND_APPLY){
            return false;
        }

        $make_friend_rec['status'] = self::MAKE_FIREND_IGNORE;
        $make_friend_rec['modify_time'] = date('Y-m-d H:i:s');

        return $this->db->update(self::TAB_NAME_FRIEND_MAKES, $make_friend_rec, array('make_id'=>$make_friend_rec['make_id']));
    }

    /**
     * 拉到黑名单
     * @param $user_id
     * @param $black_user_id
     */
    public function addFriendToBlackList($user_id, $friend_user_id){
        if( removeFriend($user_id, $friend_user_id) == false ) return false;

        $black_list = $this->getBlackList($user_id);
        if( in_array($friend_user_id, $black_list) ) return true;

        $black_list[] = $friend_user_id;
        $black_list_str = implode(',', $black_list);
        return $this->db->update(self::TAB_NAME_FRIEND_BLACK_LISTS, array('black_list'=>$black_list_str)
            , array('user_id'=>$user_id) );
    }

    /**
     * @param $user_id
     * @param $friend_user_id
     * @return bool
     */
    public function inBlackList($user_id, $friend_user_id){
        $black_list = $this->getBlackList($user_id);
        return in_array($friend_user_id, $black_list);
    }

    /**
     * @param $user_id
     * @return array
     */
    public function getBlackList($user_id){
        $black_list_rec = $this->db->getOne(self::TAB_NAME_FRIEND_BLACK_LISTS, 'black_list', array('user_id'=>$user_id) );
        $black_list = array_unique( array_filter( explode(',', $black_list_rec['black_list']) ) );
        return $black_list;
    }
}