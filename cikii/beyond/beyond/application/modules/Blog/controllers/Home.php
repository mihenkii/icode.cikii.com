<?php
class HomeController extends Yaf_Controller_Abstract {

    public function init() { // 如果是ajax请求, 则关闭html输出
        if ($this->getRequest()->isXmlHttpRequest()) {
            Yaf_Dispatcher::getInstance()->disableView();
        }
    }

    public function indexAction() {//默认Action
        Yaf_loader::import("mongodb_helper.php");
        $mongo = new Mongo_db("127.0.0.1", "27017", "abc", "post");
        $user = Yaf_Registry::get("user");
        $userId = $user['id'];
        $userName = $user['username'];
        $this->getView()->assign("content", "Hello World");
    }

    public function listAction() {//默认Action
        Yaf_loader::import("mongodb_helper.php");
        Yaf_loader::import("retwis.php");
        $mongo = new Mongo_db("127.0.0.1", "27017", "abc", "post");
        $r = redisLink();
		/*
		$userId = null;
		$userName = null;
		$nickName = null;
		*/
        $postId = $this->getRequest()->getParam("postId", 0);
        settype($postId, "integer");
		
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
		/**/
        // 顶和踩统计
		
		$up_array = $r->smembers("up:$postId");
		$down_array = $r->smembers("down:$postId");

        //  如果没有传入postId，展示自己的所有post
        if ( 0 == $postId ) {
            $postId = 0; 
            // print_r($postId);
        } else {
            $postIdArray = array('postId' => $postId);
            $options = array();

            $result = $mongo -> findOne($postIdArray, $options);
            // 文章作者
            $autherId = $result->userId;
            settype($autherId, "integer");
            $nickName = $r->hget("user:$autherId","nickname");
            $result = json_encode($result);
        }

        if( is_array($up_array) && count($up_array,COUNT_NORMAL) > 0) {
            $this->getView()->assign("upNum", count($up_array,COUNT_NORMAL));
        }
        if( is_array($down_array) && count($down_array,COUNT_NORMAL) > 0) {
            $this->getView()->assign("downNum", count($down_array,COUNT_NORMAL));
        }
        $this->getView()->assign("post", $result);
        $this->getView()->assign("username", $userName);
        $this->getView()->assign("nickname", $nickName);
        $this->getView()->assign("userId", $userId);
		
    }


    //  展示个人所有post
    public function mypostAction() {//默认Action
        Yaf_loader::import("mongodb_helper.php");
        $user = Yaf_Registry::get("user");
        $userId = $user['id'];
        settype($userId, "integer");

        //  如果没有传入postId，展示自己的所有post
        if ( 0 == $userId ) {
            $ret_str= Array('ret' => 'failed', 'msg' => 'user id is null, 内部错误');
            echo json_encode($ret_str);
        } else {

            $postIdArray = array('postId' => $postId);
            $options = array();

            $result = $mongo -> findOne($postIdArray, $options);
            $result = json_encode($result);
        }
        $this->getView()->assign("post", $result);
    }

    public function newAction() {//默认Action
        Yaf_loader::import("retwis.php");
		
		if(isLoggedIn()) {			
			$this->getView()->assign("logged", "true");
			// 为什么cookie里面的nickname失效了呢
            // $username = $_COOKIE['nickname'];
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

        $this->getView()->assign("content", "Hello World");
    } // new action

    public function addPostAction() {//添加文章
        Yaf_loader::import("retwis.php");
        Yaf_loader::import("mongodb_helper.php");
        if (!gt("title") || !gt("postContent")) {
            $ret_str = Array('ret' => '标题或内容为空');
            echo json_encode($ret_str);
            exit;
        }

        $postType = gt("postType");
        $is_public = gt("options");
        $category = gt("category");

        $mongo = new Mongo_db("127.0.0.1", "27017", "abc", "post");
        $user = Yaf_Registry::get("user");
        $userId = $user['id'];
        $ctime = time();

        if($userId == null || $userId == "") {
            $ret_str= Array('ret' => 'failed', 'msg' => 'userId is null');
            echo json_encode($ret_str);
            exit;
        }

        $r = redisLink();
        $postId = $r->incr("global:nextPostId");
        $title = htmlspecialchars(gt("title"), ENT_QUOTES);
        $postContent = htmlspecialchars(gt("postContent"), ENT_QUOTES);
        $post = Array('postId' => $postId, 'userId' => $userId, 'title' => $title, 'postContent' => $postContent, 'postType' => $postType, 'isPublic' => $is_public, 'ctime' => $ctime, 'category' => $category);
        $result = $mongo -> insert($post);

        if($result){
            $ret_str= Array('ret' => 'success', 'postId' => $postId);
            echo json_encode($ret_str);
        } else {
            $ret_str= Array('ret' => 'failed');
            echo json_encode($ret_str);
        }
    } // add post

    public function upAction() {//添加文章
        Yaf_loader::import("retwis.php");

        $postId = $this->getRequest()->getParam("postId", 0);
        $userId = $this->getRequest()->getParam("userId", 0);
        settype($postId, "integer");
        settype($userId, "integer");

        if ($postId==null || $userId==null) {
            $ret_str = Array('ret' => 'postId or userId is null');
            echo json_encode($ret_str);
            exit;
        }

        $user = Yaf_Registry::get("user");
        $userId = $user['id'];
        $ctime = time();

        if($userId == null || $userId == "") {
            $ret_str= Array('ret' => 'failed', 'msg' => 'userId is null');
            echo json_encode($ret_str);
            exit;
        }

        $r = redisLink();
        $r->sadd("up:$postId", $userId);
        $up_array = $r->smembers("up:$postId");
        print_r($total_up);

        if( is_array($up_array) && count($up_array,COUNT_NORMAL) > 0) {
            $ret_str= Array('ret' => 'success', 'upNum' => count($up_array,COUNT_NORMAL));
            echo json_encode($ret_str);
        } else {
            $ret_str= Array('ret' => 'failed', "upNum" => 0);
            echo json_encode($ret_str);
        }
    } // zan up 

    public function downAction() {//添加文章
        Yaf_loader::import("retwis.php");

        $postId = $this->getRequest()->getParam("postId", 0);
        $userId = $this->getRequest()->getParam("userId", 0);
        settype($postId, "integer");
        settype($userId, "integer");

        if ($postId==null || $userId==null) {
            $ret_str = Array('ret' => 'postId or userId is null');
            echo json_encode($ret_str);
            exit;
        }

        $user = Yaf_Registry::get("user");
        $userId = $user['id'];
        $ctime = time();

        if($userId == null || $userId == "") {
            $ret_str= Array('ret' => 'failed', 'msg' => 'userId is null');
            echo json_encode($ret_str);
            exit;
        }

        $r = redisLink();
        $r->sadd("down:$postId", $userId);
        $up_array = $r->smembers("down:$postId");
        print_r($total_up);

        if( is_array($up_array) && count($up_array,COUNT_NORMAL) > 0) {
            $ret_str= Array('ret' => 'success', 'downNum' => count($up_array,COUNT_NORMAL));
            echo json_encode($ret_str);
        } else {
            $ret_str= Array('ret' => 'failed', "downNum" => 0);
            echo json_encode($ret_str);
        }
    } // zan down
    
    // 生成postId
    public function genPostIdAction() {
        Yaf_Dispatcher::getInstance()->disableView();
        Yaf_loader::import("retwis.php");
        $r = redisLink();
        $postId = $r->incr("global:nextPostId");
        echo $postId;
    }

} // HomeController
?>
