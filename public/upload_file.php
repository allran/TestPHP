
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
$max_file_size=5000000; //�ϴ��ļ���С����, ��λBYTE
$destination_folder="upload/";//�ϴ��ļ�·��
$watermark=0; //�Ƿ񸽼�ˮӡ(1Ϊ��ˮӡ,����Ϊ����ˮӡ);
$watertype=1; //ˮӡ����(1Ϊ����,2ΪͼƬ)
$waterposition=1; //ˮӡλ��(1Ϊ���½�,2Ϊ���½�,3Ϊ���Ͻ�,4Ϊ���Ͻ�,5Ϊ����);
$waterstring=��newphp.site.cz��; //ˮӡ�ַ���
$waterimg=��xplore.gif��; //ˮӡͼƬ
$imgpreview=1; //�Ƿ�����Ԥ��ͼ(1Ϊ����,����Ϊ������);
$imgpreviewsize=1/2; //����ͼ����

$response = array();
$response["success"] = 0;
$response["message"] = "�ļ������ڣ�";

if ($_SERVER['REQUEST_METHOD'] == ��POST��) {
	if (!is_uploaded_file($_FILES["tupiana"][tmp_name])) {//�Ƿ�����ļ�
		$response["message"] = "�ļ������ڣ�";
		exit;
	} 
	if($_POST['add']) {	
		$file = $_FILES["tupiana"];
	}
	if($max_file_size < $file["size"]) {
		$response["message"] = "�ļ�̫��";
		exit;
	}
	if(!in_array($file["type"], $uptypes)) {//����ļ�����
		$response["message"] = "ֻ���ϴ�ͼ���ļ���";
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
		$response["message"] = "ͬ���ļ��Ѿ������ˣ�";
		exit;
	}
	if(!move_uploaded_file ($filename, $destination)) {
		$response["message"] = "�ƶ��ļ�����";
		exit;
	}
	$pinfo=pathinfo($destination);
	$fname=$pinfo[basename];
	$response["message"] = "�Ѿ��ɹ��ϴ��ļ���: " + $destination_folder.$fname + "���:".$image_size[0] + "����:".$image_size[1];
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
				$response["message"] = "�����ϴ��������ļ�";
				exit;
		}
		imagecopy($nimage,$simage,0,0,0,0,$image_size[0],$image_size[1]);
		imagefilledrectangle($nimage,1,$image_size[1]-15,80,$image_size[1],$white);
		switch($watertype) {
			case 1: //��ˮӡ�ַ���
				imagestring($nimage,2,3,$image_size[1]-15,$waterstring,$black);
				break;
			case 2: //��ˮӡͼƬ
				$simage1 =imagecreatefromgif(��xplore.gif��);
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
		//����ԭ�ϴ��ļ�
		imagedestroy($nimage);
		imagedestroy($simage);
	}
	if($imgpreview==1) {
		$response["message"] = "<br>ͼƬԤ��:<br>" +
		"<a href=\"".$destination."\" target='_blank'><img src=\"".$destination."\" width=".($image_size[0]*$imgpreviewsize)." height=".($image_size[1]*$imgpreviewsize) +
		" alt=\��ͼƬԤ��:\r�ļ���:".$destination."\r�ϴ�ʱ��:\" border='0'></a>";
	}
}
if($_POST['add']) {
	$res = mysql_query("update tupian set tupiana='".$destination."' where gid = 10");
}
if($res) {
	$response["message"] = "�����ɹ�";
}
$result = mysql_query("select * from tupian");
$array = mysql_fetch_array($result)
?>
<form id=��form2�� name=��form2�� enctype=��multipart/form-data�� method=��post�� action=��upload.php?act=edit&amp;id=<?php echo $array['gid']?>��>
<table width=��100%�� border=��0�� cellspacing=��0�� cellpadding=��0��>
<tr>
<td><table width=��100%�� border=��0�� cellspacing=��0�� cellpadding=��0��>
<tr>
<td width=��120�� align=��right�� class=��cenl2��>�޸�1��</td>
<td width=��866�� class=��cenl2��>
<input name=��tupiana1�� type=��text�� id=��tupiana1�� value=��<?php echo $array['tupiana']?>�� size=��30�� readonly=��true�� />
<input name=��tupiana�� type=��file�� id=��tupiana�� value=��<?php echo $array['tupiana']?>�� size=��30�� />
<input name=��add�� type=��submit�� id=��add�� value=���ϴ��� /></td>
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
if (!empty($_FILES["img"]["name"])) { //��ȡ�ļ����������ƣ����ж�
	$path = "Users/apple/Desktop/SDK/upload/";
	if(!file_exists($path)) {//����Ƿ��и��ļ��У����û�оʹ��������������Ȩ��
		mkdir(��$path��, 0700);
	}
	
	$tp=array('image/jpg', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/gif', 'image/bmp', 'image/x-png');
	if(!in_array($_FILES["img"]["type"],$tp)) {
		echo "��ʽ����.";
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
		$today=date(��YmdHis��); //��ȡʱ�䲢��ֵ������
		$file2 = $path.$today.$type; //ͼƬ������·��
		$img = $today.$type; //ͼƬ����
		$flag=1;
	}
	if($flag)
		$result=move_uploaded_file($_FILES["img"]["tmp_name"],$file2);//�ر�ע�����ﴫ�ݸ�move_uploaded_file�ĵ�һ������Ϊ�ϴ����������ϵ���ʱ�ļ�
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
