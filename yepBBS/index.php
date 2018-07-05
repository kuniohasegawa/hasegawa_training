<?php
session_start();
require('dbconnect.php');

if(!empty($_POST)){
	if(isset($_POST['title']) && $_POST['title']==''){
		$error['title'] = 'blank';
	}
	if(isset($_POST['user_name']) && $_POST['user_name']==''){
		$error['user_name'] = 'blank';
	}
}	


//var_dump($SESSION);
//echo var_dump($SESSION['index']['room'];
//dbへデータを挿入
if(!empty($_POST)){
	$member=$db->prepare('INSERT INTO rooms SET title=?, user_name=?, modified=NOW(), created=NOW()');
	$member->execute(array(
	 	$_POST['title'],
		$_POST['user_name']
		));
var_dump($_POST);	
}

date_default_timezone_set('Asia/Tokyo');
?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	
	<link rel="stylesheet" href="style.css">
	<title>yepBBS</title>
</head>

<body>
	<div class="header">
		<h1><span>yep</span>BBS</h1>
	</div>
	<div class="time">
		<?php echo date("y/m/d"); ?><br>
		<?php echo date("h/i"); ?>
	
	</div>
	
	
	<div class="footer">

	<form method="post" action="" >		
		<p class="regi">ルーム登録</p>
		<span class="roomname">ルーム名</span><input class="room" type="text" name="title"><br>
	<span class="error_msg"><?php if(isset($error['title']) == 'blank'): ?>
		<?php echo '必ず入力してください'.'<br>';?>
		<?php endif; ?>
	</span>
		<span class="list-name">名前</span><input class="name"type="text" name="user_name">
	
		<input class="btn" type="submit" value="登録" ><br>
	<span class="error_msg">
		<?php if(isset($error['user_name']) == 'blank'): ?>
		<?php echo '必ず入力してください'.'<br>'; ?>
		<?php endif; ?>
	</span>
	</div>	
	</form>

</body>

</html>
