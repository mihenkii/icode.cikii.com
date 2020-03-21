<?php
  class Bootstrap extends Yaf_Bootstrap_Abstract{
  
    public function _initConfig() {
      $config = Yaf_Application::app()->getConfig();
      Yaf_Registry::set("config", $config);
 
      $theme = $config ->theme;
      $theme = empty($theme) ? 'default' : $theme;

      $domain = $config ->domain;
      $staticdomain = $config ->staticdomain;
      $context = $config ->context;
      $upload_base_dir = $config -> upload_base_dir;
      $upload_tmp_dir = $config -> upload_tmp_dir;
      $storage_base_dir = $config -> storage_base_dir;

      define('__THEME__', $staticdomain.$context.'themes/'.$theme);
      define('THEME_PATH', ROOT_PATH.'/themes/'.$theme);
      define('__STATIC_FILE__', __THEME__.'/public');
      define('__CROSS_DOMAIN_STATIC_FILE__', THEME_PATH.'/public');
      define('__UPLOAD_BASE_DIR__', $upload_base_dir);
      define('__UPLOAD_TMP_DIR__', $upload_tmp_dir);
      define('__STORAGE_BASE_DIR__', $storage_base_dir);

      // if(!(file_exists(ROOT_PATH.'/themes/'.$theme.'/public'))) {
      //   define('__STATIC_FILE__', SITE_URL.'/themes/default/public');
      // } else {
      //   define('__STATIC_FILE__', __THEME__.'/public');
      // }
      ini_set('session.cookie_path', '/');
      ini_set('session.cookie_domain', '.cikii.com');
      ini_set('session.cookie_lifetime', '1800');
    }
  
    public function _initDefaultName(Yaf_Dispatcher $dispatcher) {
      $dispatcher->setDefaultModule("Index")->setDefaultController("Index")->setDefaultAction("index");
    }

    public function _initRouter(Yaf_Dispatcher $dispatcher) {
      Yaf_Dispatcher::getInstance()->getRouter()->addRoute("simple", new Yaf_Route_Simple("m", "c", "a"));
      $router = Yaf_Dispatcher::getInstance()->getRouter();
    }

    public function _initUser(Yaf_Dispatcher $dispatcher) {
      Yaf_loader::import("retwis.php"); 
      if(isLoggedIn()) { //通过isLoggedIn方法把用户信息保存全局
        $user = Yaf_Registry::get("user");
      }
    }

  }
?>
