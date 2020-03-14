<?php 
session_start();
header('Access-Control-Allow-Origin: http://beyond.cikii.com');  
$session_captcha_code = $_SESSION['captcha_code'];

// header('Access-Control-Allow-Origin: *');  
//设置: 你可以在这里修改验证码图片的参数

/* 将验证码图片在HTML页面上显示出来 */ 

if (!function_exists('http_response_code'))
{
  function http_response_code($newcode = NULL)
  {
    static $code = 200;
    if($newcode !== NULL)
    {
      header('X-PHP-Response-Code: '.$newcode, true, $newcode);
      header('Access-Control-Allow-Origin: http://beyond.cikii.com');  
      header('Access-Control-Allow-Origin: *');  
      if(!headers_sent())
        $code = $newcode;
    }       
    return $code;
  }
}

if(isset($_GET["captcha_code"])) {
  $captcha_code = $_GET["captcha_code"];
  if($captcha_code == $session_captcha_code) {
    http_response_code(200);
  } else {
    http_response_code(400);
  }
} else {
  http_response_code(400);
}

?>
