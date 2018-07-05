<?php
session_start();
require('dbconnect.php');

//エラー時のメッセージ
//$error_msg='必ず入力してください';

//エラーの確認
if(!empty($_POST)){
	if(isset($_POST['room']) && $_POST['room']==''){
		$error['room'] = 'blank';
	}
	if(isset($_POST['name']) && $_POST['name']==''){
		$error['name'] = 'blank';
	}

	if(empty($error)){
		$_SESSION['index'] = $_POST;
//		header('Location:room.php ');
//		exit();
	}
}	


//dbへデータを挿入
if(!empty($_POST)){
	$member=$db->prepare('INSERT INTO rooms SET title=?, user_name=?, modified=getlastmod(), created=NOW()');
	$member->execute(array(
		$_SESSION['index']['room'],
		$_SESSION['index']['name'],
		));
	unset($_SESSION['index']);

	exit();
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
		<span class="roomname">ルーム名</span><input class="room" type="text" name="room"><br>
	<span class="error_msg"><?php if(isset($error['room']) == 'blank'): ?>
		<?php echo '必ず入力してください'.'<br>';?>
		<?php endif; ?>
	</span>
		<span class="list-name">名前</span><input class="name"type="text" name="name">
	
		<input class="btn" type="submit" value="登録" ><br>
	<span class="error_msg">
		<?php if(isset($error['name']) == 'blank'): ?>
		<?php echo '必ず入力してください'.'<br>'; ?>
		<?php endif; ?>
	</span>
	</div>	
	</form>

</body>

</html>
