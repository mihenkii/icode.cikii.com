<?php
  class CommentController extends Yaf_Controller_Abstract {

    public function init() { // 如果是ajax请求, 则关闭html输出
      if ($this->getRequest()->isXmlHttpRequest()) {
        Yaf_Dispatcher::getInstance()->disableView();
      }

    }

    public function indexAction() {//默认Action
      Yaf_loader::import("retwis.php");
      Yaf_loader::import("mongodb_helper.php");
      $mongo = new Mongo_db("127.0.0.1", "27017", "abc", "post");
      $user = Yaf_Registry::get("user");
      $userId = $user['id'];
      $userName = $user['username'];
      $postId = gt("postId");
      $req = array("postId" => $postId);
      $comment_list = $mongo->find($req);
      if($comment_list != null) {
        $resp = array(
            "result" => true,
            "data" => $comment_list, 
        );
      } else {
        $resp = array(
            "result" => false,
        );
      }
      echo json_encode($resp);
    }

    public function listAction() {//默认Action
      Yaf_loader::import("retwis.php");
      Yaf_loader::import("mongodb_helper.php");
      $mongo = new Mongo_db("127.0.0.1", "27017", "abc", "comment");
      $user = Yaf_Registry::get("user");
      $userId = $user['id'];
      $userName = $user['username'];
      $postId = $this->getRequest()->getParam("postId", 0);
      settype($postId, "integer");

      if($postId == null) {
        $resp = array(
            "result" => false,
        );
      }

      $req = array("postId" => "$postId");
      // $comment_list = $mongo->find($req, "comment", NULL);
      $comment_list = $mongo->find("", "comment", $req);

      if($comment_list != null) {
      // 将用户头像路径填充到返回list中
        $r = redisLink();
        foreach($comment_list as $comment) {
          $userid = $comment->userId;
          $ctime = $comment->ctime;
          $cdate = date('Y-m-d H:i:s', $ctime);
          $thumbnail = $r->hget("user:$userid","thumbnail");
          $nickname = $r->hget("user:$userid","nickname");
          $comment->thumbnail = $thumbnail;
          $comment->cdate = $cdate;
          $comment->nickname = $nickname;
          // print_r($comment);
        }
        $resp = array(
            "result" => true,
            "data" => $comment_list, 
        );
      } else {
        $resp = array(
            "result" => false,
        );
      }
      echo json_encode($resp);
    }
// 增加评论
    public function addCommentAction() {//添加评论
      Yaf_loader::import("retwis.php");
      Yaf_loader::import("mongodb_helper.php");
      $mongo = new Mongo_db("127.0.0.1", "27017", "abc", "comment");

      $commentContent = gt("commentContent");
      $userId = gt("userId");
      $postId = gt("postId");
      $ctime = time();

      if($userId == null || $userId == "") {
        $ret_str= Array('ret' => 'failed', 'msg' => 'userId is null');
        echo json_encode($ret_str);
        exit;
      }

      $req = array("postId" => "$postId");
      $comment_list = $mongo->find("", "comment", $req);
      $commentId = count($comment_list, 0) + 1;

      $r = redisLink();
      $comment_global_id = $r->incr("global:next_comment_id");
      $commentContent = htmlspecialchars(gt("commentContent"), ENT_QUOTES);
      $comment = Array('comment_global_id' => $comment_global_id, 'commentId' => $commentId,'postId' => $postId, 'userId' => $userId, 'content' => $commentContent, 'replyId' => $replyId, 'ctime' => $ctime);
      $result = $mongo -> insert($comment);

      /* 通过ajax方法调用，返回dataType设置是json
         但是本方法上面用了print_r输出了部分调试信息，导致返回值一直都不是json
         所以数据一直返回到error方法里，找了好久的错误....
      */
      if($result){

        $cdate = date('Y-m-d H:i:s', $ctime);
        $thumbnail = $r->hget("user:$userid","thumbnail");
        $nickname = $r->hget("user:$userid","nickname");
        $backValue = array(
            'ret' => 'success', 
            'commentId' => "$commentId",
            'commentContent' => $commentContent,
            'cdate' => $cdate,
            'nickname' => $nickname,
            'thumbnail' => $thumbnail,
            );
        echo json_encode($backValue);
      } else {
        $ret_str= array(
            'ret' => 'failed',
            );
        echo json_encode($ret_str);
      }
    } // add commit
// 回复评论
    public function replyCommentAction() {//添加评论
      Yaf_loader::import("retwis.php");
      Yaf_loader::import("mongodb_helper.php");
      $mongo = new Mongo_db("127.0.0.1", "27017", "abc", "comment");

      $commentContent = gt("commentContent");
      $userId = gt("userId");
      $postId = gt("postId");
      $replyId = gt("recipient-name");
      $ctime = time();

      if($userId == null || $userId == "") {
        $ret_str= Array('ret' => 'failed', 'msg' => 'userId is null');
        echo json_encode($ret_str);
        exit;
      }

      $req = array("postId" => "$postId");
      $comment_list = $mongo->find("", "comment", $req);
      $commentId = count($comment_list, 0) + 1;

      $r = redisLink();

      $comment_global_id = $r->incr("global:next_comment_id");
      $commentContent = htmlspecialchars(gt("commentContent"), ENT_QUOTES);
      $comment = Array('comment_global_id' => $comment_global_id,'commentId' => $commentId,'postId' => $postId, 'userId' => $userId, 'content' => $commentContent, 'replyId' => $replyId, 'ctime' => $ctime);
      $result = $mongo -> insert($comment);

      /* 通过ajax方法调用，返回dataType设置是json
         但是本方法上面用了print_r输出了部分调试信息，导致返回值一直都不是json
         所以数据一直返回到error方法里，找了好久的错误....
      */
      if($result){

        $cdate = date('Y-m-d H:i:s', $ctime);
        $thumbnail = $r->hget("user:$userId","thumbnail");
        $nickname = $r->hget("user:$userId","nickname");
        $backValue = array(
            'ret' => 'success', 
            'commentId' => "$commentId",
            'commentContent' => $commentContent,
            'cdate' => $cdate,
            'nickname' => $nickname,
            'thumbnail' => $thumbnail,
            );
        echo json_encode($backValue);
      } else {
        $ret_str= array(
            'ret' => 'failed',
            );
        echo json_encode($ret_str);
      }
    } // add commit


  } // HomeController
?>
