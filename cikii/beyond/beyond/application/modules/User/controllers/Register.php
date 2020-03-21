<?php
  class RegisterController extends Yaf_Controller_Abstract {
    public function init() { // 如果是ajax请求, 则关闭html输出
      Yaf_loader::import("retwis.php");
      if ($this->getRequest()->isXmlHttpRequest()) {
        Yaf_Dispatcher::getInstance()->disableView();
      }

    }
    public function editAction() {//默认Action

      $this->getView()->assign("content", "Hello World");
    }

    public function thumbnailAction() {//默认Action
      Yaf_loader::import("retwis.php");
      if(!gt("userId")){
        $ret_str = Array('ret' => '用户名或密码不正确');
        echo json_encode($ret_str);
        exit;
      }else{
        $userId = gt("userId");
        $this->getView()->assign("userId", $userId);
      }
    }

    public function addthumbnailAction() {//默认Action
      if(!gt("userId")){
        $ret_str = Array('ret' => '用户名未登陆不能上传头像');
        echo json_encode($ret_str);
        exit;
      }else{
        $userId = gt("userId");
        $file_original_name = gt("file_original_name");
        $org_img = __UPLOAD_BASE_DIR__.'/'.$file_original_name;
        $x = gt("x");
        $y = gt("y");
        $height = gt("height");
        $width = gt("width");
        $timestamp = time();
        $month = substr($timestamp, 0, 6);
        $output_dir = __STORAGE_BASE_DIR__.'/'.'thumbnail/'.$month.'/'.$userId.'/';

        if (!file_exists($output_dir)) {
            @mkdir($output_dir, 0755, true);
        }
        // imagic 截图制作头像, 封装到lib
        $filename = $userId.'_'.$width.'x'.$height.'.png';
        $output_filename = $output_dir.$filename;

        $relate_dir = '/'.'thumbnail/'.$month.'/'.$userId.'/'.$filename;

        $img = new Imagick(realpath($org_img));
        $img -> cropImage($width, $height, $x, $y);
        if(isset($output_filename)) {
            $img -> writeImage($output_filename);
        }

        $r = redisLink();
        $r->hmset("user:$userId",
          "thumbnail", $relate_dir
        );


        if(file_exists($output_filename)) {
            $ret_str= Array(
                'ret' => 'success',
                'message' => 'make thumbnail success',
                );
            header('Content-Type: application/json');
            echo json_encode($ret_str);
        } else {
            $ret_str= Array(
                'ret' => 'faild',
                'message' => 'make thumbnail faild',
                );
            header('Content-Type: application/json');
            echo json_encode($ret_str);
        }
      }
    }

    public function addAction() {
      Yaf_loader::import("retwis.php");
      if (!gt("username") || !gt("password") || !gt("confirmpass") || !gt("nickname")) {
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
      $nickname = gt("nickname");

      $r = redisLink();
      $userid = $r->incr("next_user_id");
      $authsecret = getrand();
      $r->hset("users", $username, $userid);
      $r->hmset("user:$userid",
        "nickname", $nickname,
        "username", $username,
        "password", $password,
        "auth", $authsecret
      );

      $r->hset("auths", $authsecret, $userid);
      $r->zadd("users_by_time", time(), $username);

      setcookie("auth", $authsecret, time()+3600*24*30, "/", ".cikii.com");
      setcookie("username", $username, time()+3600*24*30, "/", ".cikii.com");
      setcookie("nickname", $nickname, time()+3600*24*30, "/", ".cikii.com");
      // $username = $_POST["username"];
      /*
      $db_connect=mysql_connect('127.0.0.1','root','123456') or die("Unable to connect to the MySQL!");
      $select_db = mysql_select_db("yaftest", $db_connect);
      $result=mysql_query("insert into user set `username`=\"$username\"");
      */

      if ($userid != null) {
        setcookie("userId", $userid, time()+3600*24*30, "/", ".cikii.com");
        $ret_str= Array(
            'ret' => 'success',
            'userId' => $userid,
            );
        echo json_encode($ret_str);
      } else {
        $ret_str= Array('ret' => 'failed');
        echo json_encode($ret_str);
      }
      // mysql_close($db_connect);
    }
  }
?>
