<?php

/**
 * Created by PhpStorm.
 * User: yangyulong
 * Date: 2015/5/26
 * Time: 13:45
 */
class Mongo_db
{
  private static $instanceof = NULL;
  public $mongo;
  private $host = '127.0.0.1';
  private $port = '27017';

  private $db;
  public $dbname = 'abc';
  private $table = NULL;
  private $dbmanager;

  /**
   * 初始化类，得到mongo的实例对象
   */
  public function __construct($host = NULL, $port = NULL, $dbname = NULL, $table = NULL)
  {

    require 'vendor/autoload.php';
    if (NULL === $dbname) {
      $this->throwError('集合不能为空！');
    }

    //判断是否传递了host和port
    if (NULL !== $host) {
      $this->host = $host;
    }

    if (NULL !== $port) {
      $this->port = $port;
    }

    $this->table = $table;

    $manager = new MongoDB\Driver\Manager("mongodb://$host:27017");
    $collection = new MongoDB\Collection($manager, "$dbname.$table");

    $this->dbname = $collection;
    $this->db = $this->dbname;
    $this->dbmanager = $manager;
  }


  /**
   * 单例模式
   * @return Mongo|null
   */
  //public static function getInstance($host=null, $port=null, $dbname=null, $table=null){
  //
  //    if(!(self::$instanceof instanceof self)){
  //        self::$instanceof = new self($host, $port, $dbname, $table);
  //    }
  //
  //    return self::$instanceof;
  //}

  /**
   * 插入一条数据
   * @param array $doc
   */
  public function insert($doc = array())
  {
    if (empty($doc)) {
      $this->throwError('插入的数据不能为空！');
    }
    //保存数据信息
    try {
      if (!($result=$this->db->insertOne($doc))) {
        throw new MongoException('插入数据失败');
      } else {
        return $result->getInsertedId();
      }
    } catch (MongoException $e) {
      $this->throwError($e->getMessage());
    }
  }

  /**
   * 插入多条数据信息
   * @param array $doc
   */
  public function insertMulti($doc = array())
  {
    if (empty($doc)) {
      $this->throwError('插入的数据不能为空！');
    }
    //插入数据信息
    foreach ($doc as $key => $val) {
      //判断$val是不是数组
      if (is_array($val)) {
        $this->insert($val);
      }
    }
  }

  /**
   * 查找一条记录
   * @return array|null
   */
  public function findOne($where = NULL, $options = NULL)
  {
    // var_dump($where);
    // $where = array("postId" => $where);
    // $where = array("postId" => 2);
    // var_dump($where);
    // exit;
    $query = new MongoDB\Driver\Query($where, $options);
    if (NULL === $where) {
      try {
        if ($result = $this->dbmanager->executeQuery($this->db, $query)) {
          return $result;
        } else {
          throw new MongoException('查找数据失败');
        }
      } catch (MongoException $e) {
        $this->throwError($e->getMessage());
      }
    } else {
      try {
        if ($cursor = $this->dbmanager->executeQuery($this->db, $query)) {
          $post = array();
          $post = $cursor->toArray();
          // var_dump($post[0]);
          // return a obj
          return $post[0];
          // return $cursor;
        } else {
          throw new MongoException('查找数据失败');
        }
      } catch (MongoException $e) {
        $this->throwError($e->getMessage());
      }
    }

  }

  /**
   * todo 带条件的随后做
   * 查找所有的文档
   * @return MongoCursor
   */
  public function find($where = NULL, $tableName = NULL, $options)
  {
  /*
    $category = 'beyond';
    $options = array('category'=>$category);
    $where = array("postId" => "2");
    $where = array("category" => $category);
  */
    $where = $options;

    // $db = new Mongo_db('127.0.0.1', "27017", "abc", $tableName);
    $query = new MongoDB\Driver\Query($where, $options);

    if (empty($where)) {
      try {
        if ($result = $this->dbmanager->executeQuery($this->db, $query)) {
            return $result;
        } else {
          throw new MongoException('查找数据失败');
        }
      } catch (MongoException $e) {
        $this->throwError($e->getMessage());
      }
    } else {
      try {
        if ($cursor = $this->dbmanager->executeQuery($this->db, $query)) {
        // if ($cursor = $this->dbmanager->executeQuery($db, $query)) {
            $result = $cursor->toArray();
            return $result;
        } else {
          throw new MongoException('查找数据失败');
        }
      } catch (MongoException $e) {
        print_r("exception occur");
        $this->throwError($e->getMessage());
      }
    }

    $arr = array();
    foreach ($result as $id => $val) {
      $arr[] = $val;
    }

    print_r("arr".$arr);
    return $arr;
  }

  /**
   * 获取记录条数
   * @return int
   */
  public function getCount()
  {
    try {
      if ($count = $this->db->count()) {
        return $count;
      } else {
        throw new MongoException('查找总数失败');
      }
    } catch (MongoException $e) {
      $this->throwError($e->getMessage());
    }
  }

  /**
   * 获取所有的数据库
   * @return array
   */
  public function getDbs()
  {
    return $this->mongo->listDBs();
  }

  /**
   * 删除数据库
   * @param null $dbname
   * @return mixed
   */
  public function dropDb($dbname = NULL)
  {
    if (NULL !== $dbname) {
      $retult = $this->mongo->dropDB($dbname);
      if ($retult['ok']) {
        return TRUE;
      } else {
        return FALSE;
      }
    }
    $this->throwError('请输入要删除的数据库名称');
  }

  /**
   * 强制关闭数据库的链接
   */
  public function closeDb()
  {
    $this->mongo->close(TRUE);
  }

  /**
   * 输出错误信息
   * @param $errorInfo 错误内容
   */
  public function throwError($errorInfo='')
  {
    echo "<h3>出错了：$errorInfo</h3>";
    die();
  }

}
