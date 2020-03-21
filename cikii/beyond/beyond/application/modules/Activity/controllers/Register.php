<?php
  class RegisterController extends Yaf_Controller_Abstract {
    public function init() { // 如果是ajax请求, 则关闭html输出
      if ($this->getRequest()->isXmlHttpRequest()) {
        Yaf_Dispatcher::getInstance()->disableView();
      }

    }
    public function editAction() {//默认Action

      $this->getView()->assign("content", "Hello World");
    }

    public function addAction() {
      Yaf_loader::import("retwis.php");
      if (!gt("username") || !gt("password") || !gt("confirmpass")) {
        $ret_str = Array('ret' => '用户名或密码不正确');
        echo json_encode($ret_str);
        exit;
      }

      if(gt("password") != gt("confirmpass")) {
        $ret_str = Array('ret' => '两次密码不一致');
        echo json_encode($ret_str);
        exit;
      }

      $username = gt("username");
      $password = gt("password");

      $r = redisLink();
      $userid = $r->incr("next_user_id");
      $authsecret = getrand();
      $r->hset("users", $username, $userid);
      $r->hmset("user:$userid",
        "username", $username,
        "password", $password,
        "auth", $authsecret
      );

      $r->hset("auths", $authsecret, $userid);
      $r->zadd("users_by_time", time(), $username);

      setcookie("auth", $authsecret, time()+3600*24*30, "/", "cikii.com");
      setcookie("username", $username, time()+3600*24*30, "/", "cikii.com");
      // $username = $_POST["username"];
      /*
      $db_connect=mysql_connect('127.0.0.1','root','123456') or die("Unable to connect to the MySQL!");
      $select_db = mysql_select_db("yaftest", $db_connect);
      $result=mysql_query("insert into user set `username`=\"$username\"");
      */

      if ($userid != null) {
        $ret_str= Array('ret' => 'success');
        echo json_encode($ret_str);
      } else {
        $ret_str= Array('ret' => 'failed');
        echo json_encode($ret_str);
      }
      // mysql_close($db_connect);
    }
  }
?>
