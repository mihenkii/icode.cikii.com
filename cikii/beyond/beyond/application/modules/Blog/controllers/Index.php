<?php
class IndexController extends Yaf_Controller_Abstract {
    public function indexAction() { //默认Action
        // $this->getView()->assign("content", "Hello World");
        Yaf_loader::import("retwis.php");
        Yaf_loader::import("mongodb_helper.php");
        $category = "beyond";
        $category = $_SERVER['HTTP_HOST'];
        $post_with_thumbnail = array();
        $options = array('category'=>$category);
        $mongo = new Mongo_db("127.0.0.1", "27017", "abc", "post");
        $allPostList = $mongo -> find("","post", $options);
        if($allPostList) {
            foreach($allPostList as $postinfo) {
                $postinfo->post_thumbnail = $this->extractPostImg($postinfo);
                $autherId = $postinfo->userId;
                settype($autherId, "integer");
                $postinfo->auther_nick_name = $this->get_username_by_id($autherId);
                $post_with_thumbnail[] = $postinfo;
            }
        }
        $allPostList = json_encode($post_with_thumbnail);
        $this->getView()->assign("allPostList", $allPostList);

        if(isLoggedIn()) {
            $this->getView()->assign("logged", "true");
            $user = Yaf_Registry::get('user'); 
            $username = $user['nickname'];
            $userName = $user['username'];
            $nickName = $user['nickname'];
            $arr = explode('@', $nickName);
            if(strlen($arr[0]) < 11)
                $this->getView()->assign("showname", $arr[0]);
            else {
                $this->getView()->assign("showname", substr("$arr[0]", 0, 11));
            }


            // 部分环境mb_strlen函数没有开启， 需要去php.ini开启
            if(mb_strlen($arr[0]) < 11)
                $this->getView()->assign("showname", $arr[0]);
            else
                $this->getView()->assign("showname", substr("$arr[0]", 0, 11));

            /*
               $username = $_COOKIE['username'];
               $arr = explode('@', $username);
               if(strlen($arr[0]) < 11)
               $this->getView()->assign("username", $arr[0]);
               else
               $this->getView()->assign("username", substr("$arr[0]", 0, 11));
             */
        } else {
            $this->getView()->assign("logged", "false");
        }
    }

    //  展示beyond category所有post
    public function allPostAction() {//默认Action
        Yaf_Dispatcher::getInstance()->disableView();
        Yaf_loader::import("mongodb_helper.php");

        $category = "beyond";
        $options = array('category'=>$category);
        $mongo = new Mongo_db("127.0.0.1", "27017", "abc", "post");
        $result = $mongo -> find("","post", $options);
        $post_with_thumbnail = array();
        if($result) {
            foreach($result as $postinfo) {
                $postinfo->post_thumbnail = $this->extractPostImg($postinfo);
                $postinfo->digest = $this->extractPostImg($postinfo);
                $autherId = $postinfo->userId;
                settype($autherId, "integer");
                $postinfo->auther_nick_name = $this->get_username_by_id($autherId);
                $post_with_thumbnail[] = $postinfo;
            }
        }
        $this->getView()->assign("post_with_thumbnail", $post_with_thumbnail);
        print_r($post_with_thumbnail);
    }

    // 获取文章中图片地址
    public function extractPostImg($post) {
        $postContent = $post->postContent; 
        $pattern = "#/ueditor/php/upload/image/[^\"'>]+?\.(gif|jpg|jpeg|bmp|png)#i";
        if(preg_match($pattern, $postContent, $matches)) {
            $post_thumbnail = $matches[0];
        } else {
            $post_thumbnail = "/ueditor/php/upload/image/20160903/1472895696720314.jpg";
        }
        return $post_thumbnail;
    }  /* extractPostImg end */

    public function get_username_by_id($userId) {
        Yaf_loader::import("retwis.php");
        $r = redisLink();
        $auther_nick_name = $r->hget("user:$userId","nickname");
        return $auther_nick_name;
    }

}
?>
