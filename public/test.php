<?php 
//http://www.w3school.com.cn/php/index.asp
function InsertData($name, $age) {
	$con=mysql_connect("localhost","root","");
	if(!$con)
		echo "没有连接成功!". mysql_error();
	else
		echo "连接成功!";
	
	mysql_select_db("Allren", $con); //选择数据库
	$sql="INSERT INTO users (user_name, user_age) VALUES ('$name','$age')";
	$rs = mysql_query($sql, $con); //获取数据集
	if (!$rs)
		die('Error: ' . mysql_error());
	else
		echo "1 record added:" + $name , $age;
	mysql_close($con);
}

function InsertDataWithJSON($name, $age) {
	$response = array();
	if (isset($name) && isset($age)) {
		$con=mysql_connect("localhost","root","");
		mysql_select_db("Allren", $con);
		if(!$con) {
			$response["success"] = 0;
			$response["message"] = 'Error: ' . mysql_error();
		} else {
			$sql="INSERT INTO users (user_name, user_age) VALUES ('$name','$age')";
			$result = mysql_query($sql, $con);

			if (!$result) {
				$response["success"] = 0;
				$response["message"] = "Oops! 插入数据失败.";
			} else {
				$response["success"] = 1;
				$response["message"] = "Product successfully created.";
			}
		}
		mysql_close($con);
	} else {
		// required field is missing
		$response["success"] = 0;
		$response["message"] = "Required field(s) is missing";
	}
	echo json_encode($response);
}

function UpdateData($userid, $name, $age) {
	$response = array();
	if (isset($userid) && isset($name) && isset($age)) {
		$con=mysql_connect("localhost","root","");
		mysql_select_db("Allren", $con);
		if(!$con) {
			$response["success"] = 0;
			$response["message"] = 'Error: ' . mysql_error();
		} else {
			$sql="UPDATE users SET user_name = $name WHERE user_id = $userid";
			$result = mysql_query($sql, $con);

			if (!$result) {
				$response["success"] = 0;
				$response["message"] = "Oops! 更新数据失败.";
			} else {
				$response["success"] = 1;
				$response["message"] = "成功更新数据.";
			}
		}
		mysql_close($con);
	} else {
		// required field is missing
		$response["success"] = 0;
		$response["message"] = "Required field(s) is missing";
	}
	echo json_encode($response);
}

function SearchData(){
	$con=mysql_connect("localhost","root","");
	if (!$con)
		die('Could not connect: ' . mysql_error());
	mysql_select_db("Allren", $con);
	
	$result = mysql_query("SELECT * FROM users");
	$num = mysql_affected_rows();
// 	echo "<table border='1'><tr><th>Name</th><th>Age</th></tr>";
// 	while($row = mysql_fetch_array($result)) {
// 		echo "<tr>";
// 		echo "<td>" . $row['user_name'] . "</td>";
// 		echo "<td>" . $row['user_age'] . "</td>";
// 		echo "</tr>";
// 	}
// 	echo "</table>";

	while($row=mysql_fetch_array($result))
	{
		$arr4[]=$row;
	}
	echo json_encode($arr4);
	mysql_close($con);
}

function DeleteData($userid) {
	$response = array();
	if (isset($userid)) {
		$con=mysql_connect("localhost","root","");
		mysql_select_db("Allren", $con);
		if(!$con) {
			$response["success"] = 0;
			$response["message"] = 'Error: ' . mysql_error();
		} else {
			$sql="DELETE FROM users WHERE user_id = $userid";
			$result = mysql_query($sql, $con);

			if (!$result) {
				$response["success"] = 0;
				$response["message"] = "Oops! 删除数据失败.";
			} else {
				$response["success"] = 1;
				$response["message"] = "成功删除数据.";
			}
		}
		mysql_close($con);
	} else {
		// required field is missing
		$response["success"] = 0;
		$response["message"] = "Required field(s) is missing";
	}
	echo json_encode($response);
}

switch ($_GET[type]) {
	case 1:
		InsertDataWithJSON($_GET[name], $_GET[age]);
		break;
	case 2:
		UpdateData($_GET[userid], $_GET[name], $_GET[age]);
		break;
	case 3:
		SearchData();
		break;
	case 6:
		DeleteData($_GET[userid]);
		break;
	default:
		break;
}


?>