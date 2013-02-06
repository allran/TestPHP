//BaseClassWithDB.php 抽象型别，主要是与数据库建立联机的基底类别和一些Method
<?php
  abstract class BaseClassWithDB
  {
      protected $dbLink;
      public function __construct()
      {
          $this->dbLink = mysql_connect('localhost', 'root', '')or die('fail to connect DB');
        mysql_query("SET NAMES 'utf8'");
          mysql_select_db('touchide_xuan',$this->dbLink)or die('fail to select DB');
      }
      
      public function __destruct()
      {
          mysql_close($this->dbLink);
      }
    
      protected function toJson($code)
    {
        $code = json_encode($code);
        return $code;
    }
          
      protected function toJsonWithKey($key, $code)
    {
        $code = json_encode(array($key=>$this->urlencodeAry($code)));
        return urldecode($code);
    }
 
    protected function urlencodeAry($data)
    {
        if(is_array($data))
        {
            foreach($data as $key=>$val)
            {
                $data[$key] = $this->urlencodeAry($val);
            }
            return $data;
        }
        else
        {
            return urlencode($data);
        }
    }
    /* 
        用途：取得日期的年月
        参数：无
        回传型别：201206
    */
     public static function getDateYM()
     {
        $date = date("Ym");
        return $date; 
     }
    /* 
        用途：取得日期
        参数：无
        回传型别：20120601
    */
     public static function getDate()
     {
        $date = date("Ymd");
        return $date; 
     }
    /* 
        用途：取得日期
        参数：无
        回传型别：2012/06/01
    */
     public static function getDateWithSlash()
     {
        $date = date("Y/m/d");
        return $date; 
     }
    /* 
        用途：取得日期
        参数：无 www.it165.net
        回传型别：2012/06/01
    */
     public static function getDateTime()
     {
        $date = date("YmdHis");
        return $date; 
     }
     public static function getDateTimeWithSlash()
     {
        $date = date("Y/m/d H:i:s");
        return $date; 
     }
     
     public static function base64ToImage($base64String=NULL, $filePath=NULL)
     {
        $img = imagecreatefromstring(base64_decode($base64String));
        if($img != false)
        {
            imagepng($img, $filePath);
        }
     }
     
     public static function createFolder($folderPath=NULL)
     {
         if(!file_exists($folderPath))
         {
             if(!file_exists(dirname($folderPath)))
             {
                 mkdir($folderPath, 0700);
             }             
         }
     }
  }
 ?>
//wsLine.php 主要用来   接受/产生JSON，继承自BaseClassWithDB
<?php
  include("BaseClassWithDB.php");
  class Message extends BaseClassWithDB
  {
      static private $instance = NULL;
    private $dirId = NULL;
    private $fullDirPath = NULL;
    
      public function __construct($dirId = NULL)
      {
          parent::__construct();
        $this->dirId = $dirId;
        $this->fullDirPath = 'Line_Pics/'.$dirId.'/';
        //建立数据夹
        //parent::createFolder($this->fullDirPath);
      }
      //singleton
      static public function getInstance($dirId = NULL)
      {        
          if(self::$instance == NULL)
          {
              self::$instance = new Message($dirId);
          }
          return self::$instance;
      }
      
      function __get($property)
      {
          echo "get property values";
      }
      
      function __set($property, $value)
      {
          echo "set property values ";
      }
      //query data
      function getMessages($format_='json', $term = NULL)
      {
        //query 
          $query = "select * from Line_MSG where 1 = 1 ";
        if($term!=NULL)
          $query = $query + $term;
          
          
          $result = mysql_query($query,$this->dbLink)or die('fail to query data!');
        $messages = array();
          if(mysql_num_rows($result))
          {
              while($message = mysql_fetch_assoc($result))
              {   
                  $messages[] = $message;
              }
            //var_dump($messages);
          }
          //output
          //json
        if($format_=='json')
        {
            header('Content-type: application/json');
            echo $this->toJson($messages);
        }
      }
 
      //insert data
      function insertMessage()
      {
        //
        $message__ = file_get_contents('php://input');
        //MainArray = [{"id":"a0001", "name":"王小明"},{"id":"a0002", "name":"李小华"}]
        $MainArray = json_decode($message__, true);
        if (is_array($MainArray))
        {
            foreach ($MainArray as $KVArray){
                $dir = NULL;
                $insFields = NULL;
                $insValues = NULL;
                foreach ($KVArray as $key => $value){
                    if (strcasecmp($key,'msg_date')==0){
                        $value = parent::getDateTimeWithSlash();
                        //$imageName = $value;
                    }
                    if (strcasecmp($key,'id')==0){
                        $dir = $value;
                    }                                            
                    if (strcasecmp($key,'misc')==0){
                        $imageName = parent::getDateTime();
                        parent::base64ToImage($value, $this->fullDirPath . $imageName . '.png');
                    }    
 
                    $insFields .= sprintf("%s,", $key);
                    $insValues .= "'" . $value ."',"; 
                }
                $digi = strlen($insFields)-1;
                $insFields = substr($insFields, 0, $digi);
                
                $digi = strlen($insValues)-1;
                $insValues = substr($insValues, 0, $digi);
                
                $insert = "insert into Line_MSG(". $insFields .")" .
                        "values(". $insValues .")";
                if(!mysql_query($insert, $this->dbLink))
                {
                    echo 'insert fail '. $insert ;
                } else echo 'insert success ';
                sleep(1);            
            }
        }
      }
      //delete data
      function deleteMessage()
      {
          $message__ = file_get_contents('php://input');
        $obj = json_decode($message__);
        $id = $obj->{'id'};
        $msg_date = $obj->{'msg_date'};
                
        $delete = "delete from Line_MSG where id='". $id ."' and msg_date='". $msg_date ."'";
        if(!mysql_query($delete, $this->dbLink))
            echo 'delete fail';
        else echo 'delete success';
      }
  }
 
  $format = isset($_GET['format'])?strtolower($_GET['format']):'json';
  $action = isset($_GET['action'])?($_GET['action']):NULL;
  $dirId = isset($_GET['dirId'])?($_GET['dirId']):NULL;
  $message_ = Message::getInstance($dirId);
  
  if($dirId!=NULL)
  {
      $folderPath = 'Line_Pics/'.$dirId.'/';
    if(!file_exists($folderPath))
    {
        if(!file_exists($folderPath))
        {
            mkdir($folderPath,0700);
        }             
    }
  }
  switch($action)
  {
      case 'insert':
        $message_->insertMessage(); 
        break;
       case 'delete':
        $message_->deleteMessage(); 
        break;               
      default:
        $message_->getMessages($format);
        break;
  }
?>