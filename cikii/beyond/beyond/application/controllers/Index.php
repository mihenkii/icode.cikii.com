<?php
  class IndexController extends Yaf_Controller_Abstract {
    public function init() { // 如果是ajax请求, 则关闭html输出
        if ($this->getRequest()->isXmlHttpRequest()) {
            Yaf_Dispatcher::getInstance()->disableView();
        }
    }


    public function indexAction() { //默认Action
      // $this->getView()->assign("content", "Hello World");
      Yaf_loader::import("retwis.php");
      if(isLoggedIn()) {
        $this->getView()->assign("logged", "true");

        // 为什么cookie里面的nickname失效了呢
        // $username = $_COOKIE['nickname'];
        $user = Yaf_Registry::get('user');
        $username = $user['nickname'];
        $arr = explode('@', $username);
        // 部分环境mb_strlen函数没有开启， 需要去php.ini开启
        if(mb_strlen($arr[0]) < 11)
          $this->getView()->assign("username", $arr[0]);
        else
          $this->getView()->assign("username", substr("$arr[0]", 0, 11));
      } else {
        $this->getView()->assign("logged", "false");
      }
    }
// 乐队介绍

    public function introAction() { //默认Action
      // $this->getView()->assign("content", "Hello World");
      Yaf_loader::import("retwis.php");
      if(isLoggedIn()) {
        $this->getView()->assign("logged", "true");

        // 为什么cookie里面的nickname失效了呢
        // $username = $_COOKIE['nickname'];
        $user = Yaf_Registry::get('user');
        $username = $user['nickname'];
        $arr = explode('@', $username);
        if(strlen($arr[0]) < 11)
          $this->getView()->assign("username", $arr[0]);
        else
          $this->getView()->assign("username", substr("$arr[0]", 0, 11));
      } else {
        $this->getView()->assign("logged", "false");
      }
    }

    // huangjiaju
    public function huangjiajuAction() { //默认Action
      // $this->getView()->assign("content", "Hello World");
      Yaf_loader::import("retwis.php");
      if(isLoggedIn()) {
        $this->getView()->assign("logged", "true");

        // 为什么cookie里面的nickname失效了呢
        // $username = $_COOKIE['nickname'];
        $user = Yaf_Registry::get('user');
        $username = $user['nickname'];
        $arr = explode('@', $username);
        if(strlen($arr[0]) < 11)
          $this->getView()->assign("username", $arr[0]);
        else
          $this->getView()->assign("username", substr("$arr[0]", 0, 11));
      } else {
        $this->getView()->assign("logged", "false");
      }
    }

    // huangjiaqiang
    public function huangjiaqiangAction() { //默认Action
      // $this->getView()->assign("content", "Hello World");
      Yaf_loader::import("retwis.php");
      if(isLoggedIn()) {
        $this->getView()->assign("logged", "true");

        // 为什么cookie里面的nickname失效了呢
        // $username = $_COOKIE['nickname'];
        $user = Yaf_Registry::get('user');
        $username = $user['nickname'];
        $arr = explode('@', $username);
        if(strlen($arr[0]) < 11)
          $this->getView()->assign("username", $arr[0]);
        else
          $this->getView()->assign("username", substr("$arr[0]", 0, 11));
      } else {
        $this->getView()->assign("logged", "false");
      }
    }

    // 叶世荣
    public function yeshirongAction() { //默认Action
      // $this->getView()->assign("content", "Hello World");
      Yaf_loader::import("retwis.php");
      if(isLoggedIn()) {
        $this->getView()->assign("logged", "true");

        // 为什么cookie里面的nickname失效了呢
        // $username = $_COOKIE['nickname'];
        $user = Yaf_Registry::get('user');
        $username = $user['nickname'];
        $arr = explode('@', $username);
        if(strlen($arr[0]) < 11)
          $this->getView()->assign("username", $arr[0]);
        else
          $this->getView()->assign("username", substr("$arr[0]", 0, 11));
      } else {
        $this->getView()->assign("logged", "false");
      }
    }

    // 黄贯中
    public function huangguanzhongAction() { //默认Action
      // $this->getView()->assign("content", "Hello World");
      Yaf_loader::import("retwis.php");
      if(isLoggedIn()) {
        $this->getView()->assign("logged", "true");

        // 为什么cookie里面的nickname失效了呢
        // $username = $_COOKIE['nickname'];
        $user = Yaf_Registry::get('user');
        $username = $user['nickname'];
        $arr = explode('@', $username);
        if(strlen($arr[0]) < 11)
          $this->getView()->assign("username", $arr[0]);
        else
          $this->getView()->assign("username", substr("$arr[0]", 0, 11));
      } else {
        $this->getView()->assign("logged", "false");
      }
    }

    // 热门blog
    public function hotBlogAction() { //默认Action
        Yaf_loader::import("mongodb_helper.php");
        $mongo = new Mongo_db("127.0.0.1", "27017", "abc", "post");
        $req = array();
        $options = array(
            'limit'=>10,
            'sort'=>array('ctime'=>-1)
            );
        $result = $mongo -> find($req, "post", $options);

        if($result != null) {
            $resp = array(
                "ret" => "success",
                "data" => $result
            );
        } else {
            $resp = array(
                'result' => "failed"
            );
        }
        // print_r($resp);
        echo json_encode($resp);
    }

  } // class end
?>
