<?php define('MAIN_INCLUDED', 1); ?>
<?php
$_arguments = array();
if(count($_POST) > 0){
	$_arguments = $_POST;
}else if(count($_GET) > 0){
	$_arguments = $_GET;
}

if(isset($_arguments["do"])&& ($_arguments["do"] != "")){
	if(($_arguments["do"] == "add_classroom")){//MSK-000143-add_classroom
		$page = "model/add_classroom.php";
	}else if(($_arguments["do"] == "add_grade")){//MSK-000143-add_grade
		$page = "model/add_grade.php";
	}																			
}else{
	header("Location: user/login.php");
}
require $page;

?>


