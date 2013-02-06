
<?php
abstract class BaseClassWithDB
{
	protected $dbLink;
	public function __construct()
	{
		$this->dbLink = mysql_connect('localhost', 'root', 'UrPassword')or die('fail to connect DB');
		mysql_query("SET NAMES 'utf8'");
		mysql_select_db('wsLookingFun',$this->dbLink)or die('fail to select DB');
	}
	public function __destruct() {
		mysql_close($this->dbLink);
	}
	protected function toJson($key, $code) {
		$code = json_encode(array($key=>$this->urlencodeAry($code)));
		//$code = json_encode($this->urlencodeAry($code));
		return urldecode($code);
	}
 
	protected function urlencodeAry($data) {
		if(is_array($data)) {
			foreach($data as $key=>$val) {
				$data[$key] = $this->urlencodeAry($val);
			}
		return $data;
		} else {
			return urlencode($data);
		}
	}
}
class Catalog extends BaseClassWithDB
{
	static private $instance = NULL;
	public function __construct() {
		parent::__construct();
	}
	//singlton
	static public function getInstance() {
 		if(self::$instance == NULL) {
			self::$instance = new Catalog();
		}
		return self::$instance;
	}
	function __get($property) {
		echo "get property values";
	}
	function __set($property, $value) {
		echo "set property values ";
	}
	//query data �ヨ�
	function getCatalog($timestamp_, $format_='json') {
		$query = "select * from catalogs";
		$catalogs = array();
		$result = mysql_query($query,$this->dbLink)or die('fail to query data!');
		if(mysql_num_rows($result)) {
			while($catalog = mysql_fetch_assoc($result)) {
				$catalogs[] = array('catalog'=>$catalog);
			}
		}
		//output
		//json
		if($format_=='json') {
			header('Content-type: application/json');
			echo $this->toJson('catalogs',$catalogs);
		}
	}
	//insert data �板�
	function setCatalog() {
		$catalog__ = file_get_contents('php://input');
		$obj = json_decode($catalog__);
		$id = $obj->{'id'};
		$desc = $obj->{'description'};
	
		$insert = "insert into catalogs(id,description)values('$id','$desc')";
		if(!mysql_query($insert,$this->dbLink))
			echo 'insert fail';
		else echo 'insert success';
	}
}
 
$timestamp = $_GET['timestamp'];
$format = isset($_GET['format'])?strtolower($_GET['format']):'json';
$action = isset($_GET['action'])?($_GET['action']):NULL;
$catalog_ = Catalog::getInstance();

if(!($action=='insert'))
	$catalogs = $catalog_->getCatalog($timestamp, $format);
else
	$catalogs = $catalog_->setCatalog();
 
?>