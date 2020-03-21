<?php
  class MessageController extends Yaf_Controller_Abstract {
    public function init() { // 如果是ajax请求, 则关闭html输出
      if ($this->getRequest()->isXmlHttpRequest()) {
        Yaf_Dispatcher::getInstance()->disableView();
      }
    }
    /*
    * add user message
    */
    public function messagesAction(){
        Yaf_loader::import("mongodb_helper.php");
        $mongo = new Mongo_db("127.0.0.1", "27017", "abc", "messages");

        $to_user_id = $_POST['to_user_id'];
        $from_user_id = $_POST['from_user_id'];
        $message_text = $_POST['message_text'];
        $message_text = htmlspecialchars($message_text, ENT_QUOTES);
        $ctime = time();
        settype($to_user_id, "integer");
        settype($from_user_id, "integer");

        $message = array('to_user_id' => $to_user_id, 'from_user_id' => $from_user_id, 'message_text' => $message_text, 'ctime' => $ctime, 'reply_id' => $reply_id);
        $result = $mongo -> insert($message);
        if($result) {
            $ret_str = array(
                'ret' => 'success',
            );
        } else {
            $ret_str = array(
                'ret' => 'failed',
            );
        }
        echo json_encode($ret_str);
    }

    /*
    * reply user message
    */
    public function replyAction(){
        Yaf_loader::import("mongodb_helper.php");
        $mongo = new Mongo_db("127.0.0.1", "27017", "abc", "messages");

        $to_user_id = $_POST['to_user_id'];
        $from_user_id = $_POST['from_user_id'];
        $message_text = $_POST['message_text'];
        $message_text = htmlspecialchars($message_text, ENT_QUOTES);
        $ctime = time();
        settype($to_user_id, "integer");
        settype($from_user_id, "integer");

        $message = array('to_user_id' => $to_user_id, 'from_user_id' => $from_user_id, 'message_text' => $message_text, 'ctime' => $ctime);
        $result = $mongo -> insert($message);
        if($result) {
            $ret_str = array(
                'ret' => 'success',
            );
        } else {
            $ret_str = array(
                'ret' => 'failed',
            );
        }
        echo json_encode($ret_str);
    }
  }
?>
