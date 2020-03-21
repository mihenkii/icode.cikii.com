<?php
  class IndexController extends Yaf_Controller_Abstract {
    public function init() { // 如果是ajax请求, 则关闭html输出
      if ($this->getRequest()->isXmlHttpRequest()) {
        Yaf_Dispatcher::getInstance()->disableView();
      }
    }

    public function loginAction(){
      Yaf_loader::import("retwis.php");
      if (!gt("username") && !gt("password")) {
        $ret_str = Array('ret' => '用户名或密码不正确');
        echo json_encode($ret_str);
        exit(1);;
      }

      $username = gt("username");
      $password = gt("password");
      $r = redisLink();
      $userid = $r->hget("users", $username);
      if(!$userid){
        $ret_str= Array('ret' => 'failed');
        echo json_encode($ret_str);
        // $ret_str = Array('ret' => '用户名或密码错误');
        // echo json_encode($ret_str);
        exit(1);
      }
      
      $realpassword = $r->hget("user:$userid", "password");
      if($realpassword != $password) {
        $ret_str= Array('ret' => 'failed');
        echo json_encode($ret_str);
        // $ret_str = Array('ret' => '用户名或密码错误');
        // echo json_encode($ret_str);
        exit(1);
      }

      $authsecret = $r->hget("user:$userid","auth");
      setcookie("auth", $authsecret, time()+3600*24*30, "/", ".cikii.com");
      setcookie("username", $username, time()+3600*24*30, "/", ".cikii.com");

      if($authsecret != null) {
        $ret_str= Array('ret' => 'success');
        echo json_encode($ret_str);
      }else{
        $ret_str= Array('ret' => 'failed');
        echo json_encode($ret_str);
      }
    }

    public function logoutAction() {
      Yaf_loader::import("retwis.php");
      // if(!isLoggedIn()) {
      //   header("Location: /");
      //   exit;
      // }

      $r = redisLink();
      $newauthsecret = getrand();
      $User = Yaf_Registry::get("user");
      $userid = $User['id'];
      $oldauthsecret = $r->hget("user:$userid","auth");

      $r->hset("user:$userid","auth",$newauthsecret);
      $r->hset("auths",$newauthsecret,$userid);
      $r->hdel("auths",$oldauthsecret);
      header("Location: /");
      // Yaf_Registry::del("user");
    }

    public function cleanAction() {
      if(!isLoggedIn()) {
        header("Location: index.php");
        exit;
      }

      Yaf_loader::import("retwis.php");
      $r = redisLink();
      $newauthsecret = getrand();
      $userid = $User['id'];
      $oldauthsecret = $r->hget("user:$userid","auth");

      $r->hset("user:$userid","auth",$newauthsecret);
      $r->hset("auths",$newauthsecret,$userid);
      $r->hdel("auths",$oldauthsecret);

      print_r($oldauthsecret);
      print_r($userid);
      print_r("clean cookie");

      unset($_COOKIE['auth']);
      unset($_COOKIE['username']);

      setcookie("auth", '', time()-3600, '.cikii.com', '/');
      setcookie("username", '', time()-3600, '.cikii.com', '/');

    }

    // 未登陆跳转首页
    public function indexAction(){
      Yaf_loader::import("mongodb_helper.php");
      Yaf_loader::import("retwis.php");
      $post_collection = new Mongo_db("127.0.0.1", "27017", "abc", "post");
      $comment_collection = new Mongo_db("127.0.0.1", "27017", "abc", "comment");
      // $activity_collection = new Mongo_db("127.0.0.1", "27017", "abc", "comment");
      $messages_collection = new Mongo_db("127.0.0.1", "27017", "abc", "messages");
        
      // 登录的用户信息
      if(isLoggedIn()) {
        $this->getView()->assign("logged", "true");
        $user = Yaf_Registry::get("user");
        $userId = $user['id'];
        $userName = $user['username'];
        $nickName = $user['nickname'];
        $arr = explode('@', $nickName);
        if(strlen($arr[0]) < 11)
            $this->getView()->assign("showname", $arr[0]);
        else {
            $this->getView()->assign("showname", substr("$arr[0]", 0, 11));
        }
       } else {
        $this->getView()->assign("logged", "false");
       }

      $r = redisLink();
      $userId = $this->getRequest()->getParam("u", 0);
      settype($userId, "integer");
      $myId = Yaf_Registry::get("user");
      settype($myId, "integer");

      $score = 0;

      // 判断是否已经是好友
      $r = redisLink();
      $is_friends = $r->sismember("followers:$userId", $myId);
      $follower_array = $r->smembers("followers:$userId");
      $following_array = $r->smembers("followering:$userId");
      $follower_num = count($follower_array,COUNT_NORMAL);
      $following_num = count($following_array,COUNT_NORMAL);
      
      // 如果没有传入userId, 跳转主页
      if(0 == $userId) {
        $userId = 0;
      } else {
        $total_activity = array();
        $total_messages = array();
        $options = array('userId'=>"$userId");
        $messages_options = array('to_user_id' => $myId);
        $reply_options = array('from_user_id' => $myId);
        $post_result = $post_collection->find('','post', $options);
        $post_num = sizeof($post_result);
        foreach($post_result as $postItem) {
            $userid = $postItem->userId;
            settype($userid, "integer");
            $ctime = $postItem>ctime;
            $cdate = date('Y-m-d H:i', $ctime);
            $thumbnail = $r->hget("user:$userid","thumbnail");
            $nickname = $r->hget("user:$userid","nickname");
            $postItem->thumbnail = $thumbnail;
            $postItem->cdate = $cdate;
            $postItem->nickname = $nickname;
            $postItem->digest = $this->get_post_digest($postItem->postId);
            $total_activity[] = $postItem;
        }

        $comment_result = $comment_collection->find('','comment', $options);
        $comment_num = sizeof($comment_result);
        foreach($comment_result as $commentItem) {
            $userid = $commentItem->userId;
            settype($userid, "integer");
            $ctime = $commentItem->ctime;
            $cdate = date('Y-m-d H:i', $ctime);
            $thumbnail = $r->hget("user:$userid","thumbnail");
            $nickname = $r->hget("user:$userid","nickname");
            $commentItem->thumbnail = $thumbnail;
            $commentItem->cdate = $cdate;
            $commentItem->nickname = $nickname;
            $commentItem->post_title = $this->get_post_title($commentItem->postId);
            $total_activity[] = $commentItem;
        }
        // $activity_result = $mongo->find('','activity', $options);

        $messages_result = $messages_collection -> find('','messages', $messages_options);
        $reply_result = $messages_collection -> find('','messages', $reply_options);
        $message_num = sizeof($messages_result);
        foreach($messages_result as $messageItem) {
            $from_user_id = $messageItem->from_user_id;
            settype($from_user_id, "integer");
            $ctime = $messageItem->ctime;
            $cdate = date('Y-m-d H:i', $ctime);
            $thumbnail = $r->hget("user:$from_user_id","thumbnail");
            $nickname = $r->hget("user:$from_user_id","nickname");
            $messageItem->thumbnail = $thumbnail;
            $messageItem->cdate = $cdate;
            $messageItem->nickname = $nickname;
            $total_messages[] = $messageItem;
        }
        foreach($reply_result as $messageItem) {
            $from_user_id = $messageItem->from_user_id;
            settype($from_user_id, "integer");
            $ctime = $messageItem->ctime;
            $cdate = date('Y-m-d H:i', $ctime);
            $thumbnail = $r->hget("user:$from_user_id","thumbnail");
            $nickname = $r->hget("user:$from_user_id","nickname");
            $messageItem->thumbnail = $thumbnail;
            $messageItem->cdate = $cdate;
            $messageItem->nickname = $nickname;
            $total_messages[] = $messageItem;
        }

        // $array_total = $post_result + $comment_result;
        // 按照日期排序
        usort($total_activity, function($a, $b){
            return strcmp($b->ctime, $a->ctime);
        });

        // 按照日期倒排序messages
        arsort($total_messages);

        //  测试函数
        foreach ($total_activity as $arr) {
            // print_r($arr->commentId."<br>");
            continue;
        }

        $total_activity = json_encode($total_activity);
        $score = $post_num * 10 + $comment_num *2 + $follower_num;
        $this->getView()->assign("total_activity", $total_activity);
        $this->getView()->assign("total_messages", $total_messages);
        $this->getView()->assign("myId", $myId);
        $this->getView()->assign("userId", $userId);
        $this->getView()->assign("is_friends", $is_friends);
        $this->getView()->assign("follower_num", $follower_num);
        $this->getView()->assign("following_num", $following_num);
        $this->getView()->assign("score", $score);

      }
    } // index

/*
*/
    public function get_post_digest($postId) {
        Yaf_loader::import("mongodb_helper.php");
        $mongo = new Mongo_db("127.0.0.1", "27017", "abc", "post");
        settype($postId, "integer");
        $postIdArray = array('postId' => $postId);
        $options = array();
        $result = $mongo -> findOne($postIdArray, $options);
        if(mb_strlen($result->postContent) < 80) {
            return $result->postContent;
        } else {
            return mb_substr($result->postContent, 0, 80);
        }
    }

    public function get_post_title($postId) {
        Yaf_loader::import("mongodb_helper.php");
        $mongo = new Mongo_db("127.0.0.1", "27017", "abc", "post");
        settype($postId, "integer");
        $postIdArray = array('postId' => $postId);
        $options = array();
        $result = $mongo -> findOne($postIdArray, $options);
        return $result->title;
    }

    public function followAction() {// 添加关注
        Yaf_loader::import("retwis.php");
        $userId = gt("u");
        settype($userId, "integer");
        $myId = Yaf_Registry::get("user");
        settype($myId, "integer");

        // print_r("userId is ".$userId."\n");
        // print_r("myId is ".$myId);

        if ($myId==null || $userId==null) {
            $ret_str = Array('ret' => 'userId or myId is null');
            echo json_encode($ret_str);
            exit;
        }


        $r = redisLink();
        $r->sadd("followers:$userId", $myId);
        $r->sadd("following:$myId", $userId);
        $follower_array = $r->smembers("followers:$userId");
        // print_r($follower_array);
        if( is_array($follower_array) && count($follower_array,COUNT_NORMAL) > 0) {
            $ret_str= Array('ret' => 'success', 'follower_num' => count($follower_array,COUNT_NORMAL));
            echo json_encode($ret_str);
        } else {
            $ret_str= Array('ret' => 'failed', "follower_num" => 0);
            echo json_encode($ret_str);
        }
    } // follow end
  }
?>
