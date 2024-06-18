<?php define('MAIN_INCLUDED', 1); ?>
<?php
$_arguments = array();
if(count($_POST) > 0){
	$_arguments = $_POST;
}else if(count($_GET) > 0){
	$_arguments = $_GET;
}

if(isset($_arguments["do"])&& ($_arguments["do"] != "")){
																		
}else{
	header("Location: view/index1.php");
}
require $page;

?>


