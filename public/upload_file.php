
// <?php
// if ($_FILES["file"]["error"] > 0) {
//   echo "Error: " . $_FILES["file"]["error"] . "<br />";
// } else {
//   echo "Upload: " . $_FILES["file"]["name"] . "<br />";
//   echo "Type: " . $_FILES["file"]["type"] . "<br />";
//   echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
//   echo "Stored in: " . $_FILES["file"]["tmp_name"];
// }
// ?>
 

<?php
$sql = mysql_connect("localhost","root","");
$db = mysql_select_db("Allren",$sql);
$result = mysql_query("SELECT tupiana FROM tupian");
//include('../include/dbconn.php');
$uptypes=array('image/jpg', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/gif', 'image/bmp', 'image/x-png');
$max_file_size=5000000; //上传文件大小限制, 单位BYTE
$destination_folder="upload/";//上传文件路径
$watermark=0; //是否附加水印(1为加水印,其他为不加水印);
$watertype=1; //水印类型(1为文字,2为图片)
$waterposition=1; //水印位置(1为左下角,2为右下角,3为左上角,4为右上角,5为居中);
$waterstring=”newphp.site.cz”; //水印字符串
$waterimg=”xplore.gif”; //水印图片
$imgpreview=1; //是否生成预览图(1为生成,其他为不生成);
$imgpreviewsize=1/2; //缩略图比例

$response = array();
$response["success"] = 0;
$response["message"] = "文件不存在！";

if ($_SERVER['REQUEST_METHOD'] == ‘POST’) {
	if (!is_uploaded_file($_FILES["tupiana"][tmp_name])) {//是否存在文件
		$response["message"] = "文件不存在！";
		exit;
	} 
	if($_POST['add']) {	
		$file = $_FILES["tupiana"];
	}
	if($max_file_size < $file["size"]) {
		$response["message"] = "文件太大！";
		exit;
	}
	if(!in_array($file["type"], $uptypes)) {//检查文件类型
		$response["message"] = "只能上传图像文件！";
		exit;
	}
	if(!file_exists($destination_folder)) 
		mkdir($destination_folder);
	$filename=$file["tmp_name"];
	$image_size = getimagesize($filename);
	$pinfo=pathinfo($file["name"]);
	$ftype=$pinfo[extension];
	$destination = $destination_folder.time().".".$ftype;
	if (file_exists($destination) && $overwrite != true) {
		$response["message"] = "同名文件已经存在了！";
		exit;
	}
	if(!move_uploaded_file ($filename, $destination)) {
		$response["message"] = "移动文件出错！";
		exit;
	}
	$pinfo=pathinfo($destination);
	$fname=$pinfo[basename];
	$response["message"] = "已经成功上传文件名: " + $destination_folder.$fname + "宽度:".$image_size[0] + "长度:".$image_size[1];
	if($watermark==1) {
		$iinfo=getimagesize($destination,$iinfo);
		$nimage=imagecreatetruecolor($image_size[0],$image_size[1]);
		$white=imagecolorallocate($nimage,255,255,255);
		$black=imagecolorallocate($nimage,0,0,0);
		$red=imagecolorallocate($nimage,255,0,0);
		imagefill($nimage,0,0,$white);
		switch ($iinfo[2]) {
			case 1:
				$simage =imagecreatefromgif($destination);
				break;
			case 2:
				$simage =imagecreatefromjpeg($destination);
				break;
			case 3:
				$simage =imagecreatefrompng($destination);
				break;
			case 6:
				$simage =imagecreatefromwbmp($destination);
				break;
			default:
				$response["message"] = "不能上传此类型文件";
				exit;
		}
		imagecopy($nimage,$simage,0,0,0,0,$image_size[0],$image_size[1]);
		imagefilledrectangle($nimage,1,$image_size[1]-15,80,$image_size[1],$white);
		switch($watertype) {
			case 1: //加水印字符串
				imagestring($nimage,2,3,$image_size[1]-15,$waterstring,$black);
				break;
			case 2: //加水印图片
				$simage1 =imagecreatefromgif(“xplore.gif”);
				imagecopy($nimage,$simage1,0,0,0,0,85,15);
				imagedestroy($simage1);
				break;
		}
		switch ($iinfo[2])
		{
			case 1:
				//imagegif($nimage, $destination);
				imagejpeg($nimage, $destination);
				break;
			case 2:
				imagejpeg($nimage, $destination);
				break;
			case 3:
				imagepng($nimage, $destination);
				break;
			case 6:
				imagewbmp($nimage, $destination);
				//imagejpeg($nimage, $destination);
				break;
		}
		//覆盖原上传文件
		imagedestroy($nimage);
		imagedestroy($simage);
	}
	if($imgpreview==1) {
		$response["message"] = "<br>图片预览:<br>" +
		"<a href=\"".$destination."\" target='_blank'><img src=\"".$destination."\" width=".($image_size[0]*$imgpreviewsize)." height=".($image_size[1]*$imgpreviewsize) +
		" alt=\”图片预览:\r文件名:".$destination."\r上传时间:\" border='0'></a>";
	}
}
if($_POST['add']) {
	$res = mysql_query("update tupian set tupiana='".$destination."' where gid = 10");
}
if($res) {
	$response["message"] = "操作成功";
}
$result = mysql_query("select * from tupian");
$array = mysql_fetch_array($result)
?>
<form id=”form2″ name=”form2″ enctype=”multipart/form-data” method=”post” action=”upload.php?act=edit&amp;id=<?php echo $array['gid']?>”>
<table width=”100%” border=”0″ cellspacing=”0″ cellpadding=”0″>
<tr>
<td><table width=”100%” border=”0″ cellspacing=”0″ cellpadding=”0″>
<tr>
<td width=”120″ align=”right” class=”cenl2″>修改1：</td>
<td width=”866″ class=”cenl2″>
<input name=”tupiana1″ type=”text” id=”tupiana1″ value=”<?php echo $array['tupiana']?>” size=”30″ readonly=”true” />
<input name=”tupiana” type=”file” id=”tupiana” value=”<?php echo $array['tupiana']?>” size=”30″ />
<input name=”add” type=”submit” id=”add” value=”上传” /></td>
</tr>
</table></td>
</tr>
</table>
</form>


<?php
if ((($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/pjpeg")) && ($_FILES["file"]["size"] < 20000)) {
  if ($_FILES["file"]["error"] > 0) {
    echo "Error: " . $_FILES["file"]["error"] . "<br />";
  } else {
    echo "Upload: " . $_FILES["file"]["name"] . "<br />";
    echo "Type: " . $_FILES["file"]["type"] . "<br />";
    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
    echo "Stored in: " . $_FILES["file"]["tmp_name"];
    }
  } else {
  echo "Invalid file";
}
?>

<?php 
if (!empty($_FILES["img"]["name"])) { //提取文件域内容名称，并判断
	$path = "Users/apple/Desktop/SDK/upload/";
	if(!file_exists($path)) {//检查是否有该文件夹，如果没有就创建，并给予最高权限
		mkdir(“$path”, 0700);
	}
	
	$tp=array('image/jpg', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/gif', 'image/bmp', 'image/x-png');
	if(!in_array($_FILES["img"]["type"],$tp)) {
		echo "格式不对.";
		exit;
	}

	$filetype = $_FILES['img']['type']; 
	if($filetype == 'image/jpeg') {
		$type = '.jpg';
	} else if ($filetype == 'image/jpg') {
		$type = '.jpg';
	} else if ($filetype == 'image/pjpeg') {
		$type = '.jpg';
	} else if($filetype == 'image/gif'){
		$type = '.gif'';
	}
	if($_FILES["img"]["name"]) {
		$today=date(“YmdHis”); //获取时间并赋值给变量
		$file2 = $path.$today.$type; //图片的完整路径
		$img = $today.$type; //图片名称
		$flag=1;
	}
	if($flag)
		$result=move_uploaded_file($_FILES["img"]["tmp_name"],$file2);//特别注意这里传递给move_uploaded_file的第一个参数为上传到服务器上的临时文件
}
?>

<?php
if ((($_FILES["file"]["type"] == "image/gif") || 
($_FILES["file"]["type"] == "image/jpeg") ||
 ($_FILES["file"]["type"] == "image/png") || 
($_FILES["file"]["type"] == "image/pjpeg"))
 && ($_FILES["file"]["size"] < 20000)) {
  if ($_FILES["file"]["error"] > 0) {
    echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
  } else {
    echo "Upload: " . $_FILES["file"]["name"] . "<br />";
    echo "Type: " . $_FILES["file"]["type"] . "<br />";
    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
    echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";


    	
//     if (file_exists($path . $_FILES["file"]["name"])) {
//       echo $_FILES["file"]["name"] . " already exists. ";
//     } else {
//       move_uploaded_file($_FILES["file"]["tmp_name"],
//       $path . $_FILES["file"]["name"]);
//       echo "Stored in: " . "upload/" . $_FILES["file"]["name"];
//     }
  }
} else {
  echo "Invalid file";
}
?>
