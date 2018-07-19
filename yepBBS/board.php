<?php
session_start();
require('dbconnect.php');
$result=preg_match("/^[0-9]+$/",$_GET['thread_number']);

if(!empty($_POST)){

//	if(isset($_GET['thread_number']) && $result == false){
//		$error['thread_number'] = 'error1';
//	}
	if(isset($_POST['text']) && $_POST['text']==''){
		$error['text'] = 'blank';
	}
	if(isset($_POST['user_name']) && $_POST['user_name']==''){
		$error['user_name'] = 'blank';
	}
}
//var_dump($error);
//
//$relation=$db->prepare('SELECT * FROM comments WHERE room_id=? and thread_number IS NULL ORDER BY created ASC');
//$relation->execute(array($_GET['id']));
//$nullthreads=$relation->fetchAll();



$room_id=$_GET['id'];
//代替案
$num=$db->prepare('select text from comments where room_id = ?');
$num->execute(array($_GET['id']));
$numbers=$num->fetchAll();
$numbers = count($numbers)+1;

//$num=$db->prepare('SELECT count(text) FROM comments');
//$num->execute();
//$numbers=$num->fetchAll();


//if(($_POST['res'] == $check_thread_number['number'])  && (is_null($check_thread_number['thread_number']))){
//	j}
//}else{
//	$error['res'] = 'error2';
//	var_dump($error['res']);
//}


//if(!empty($_POST['res']) && $_POST['res'] > $total_text){
//		$error['res'] = 'error2';
//		echo 'aaa';
//		exit();
//	}
//
//dbへinsert

$get_id=intval($_GET['id']);


//threadnumber照合用のバリデーション
$check=$db->prepare('select * from comments where number = ? room_id = ? ');
$check->execute(array(
	$_POST['res'],
	$_GET['id']
));
$check_thread_number=$check->fetch();


//返信の番号がtextの値よりも小さいか
$text = $db->prepare('select count(text) as count from comments where room_id = ?');
$text->execute(array(
	$_GET['id']));
$total_text = $text->fetch();
var_dump($total_text);

//  thread_numberあり、なしでinsertをわける
if($total_text['count'] <= 100){
	if(empty($error)){
		if(empty($_POST['res'])){
			$bbs =$db->prepare('INSERT INTO comments SET room_id=? ,number=? , text=?, user_name=?,modified=NOW(),created=NOW()');
			$test=$bbs->execute(array(
				$_GET['id'],
				intval($numbers),
				$_POST['text'],
				$_POST['user_name']
			));
		}elseif(!empty($_POST['res']) && is_null($check_thread_number['thread_number'])){
			$bbs =$db->prepare('INSERT INTO comments SET room_id=? ,thread_number=? ,number=? , text=?, user_name=?,modified=NOW(),created=NOW()');
			$test=$bbs->execute(array(
				$_GET['id'],
				intval($_POST['res']),
				intval($numbers),
				$_POST['text'],
				$_POST['user_name']
						));
		}
	  }else{
	  $error['res'] = 'error2';
	  }
}else{
		$error['max'] = 'error3';
}
//<掲示板の内容表示>rooms.idとcomments.room_idを紐づける
//$relation=$db->prepare('SELECT * FROM comments WHERE room_id=? ORDER BY created ASC LIMIT 100');
//$relation->execute(array($get_id));
//$relations=$relation->fetchAll();

//thread_numberがないものとあるものを分けた
	$relation=$db->prepare('SELECT * FROM comments WHERE room_id=? and thread_number IS NULL ORDER BY created ASC');
	$relation->execute(array($get_id));
	$nullthreads=$relation->fetchAll();

	$stanby=$db->prepare('SELECT * FROM comments WHERE room_id=?  ORDER BY created ASC');
	$stanby->execute(array(
		$get_id,
		));
	$threads=$stanby->fetchAll();


//関数
function h($value){
	echo htmlspecialchars($value, ENT_QUOTES, UTF-8);
}

//半角数字以外が入力された時のエラー
//	if(preg_match("/^[0-9]+$/",$_POST['thread_number'])){
//		$_POST['thread_number'] = $_POST['thread_number'];
//	}else{
//		$error['thread_number'] = 'error' ;
//	}

//
//	if(!empty($_POST['thread_number']) && $_POST['thread_number'] !== $threads['number']){
//		$error['thread_number'] = 'error2';
//}

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
<!-- メインコンテンツ	-->
<div class="main">
	<div class="text">
		<?php foreach($nullthreads as $nullthread):?>
			<div class="text_number"><?php h($nullthread['number']);?></div>
			<div class="text_name"><?php h($nullthread['user_name']); ?></div>
			<div class="text_date"><?php h($nullthread['created']);?></div>
			<div class="text_content"><?php h($nullthread['text']); ?><br><br></div>
	</div>

			<div class="thread">
				<?php foreach($threads as $thread):?>
					<?php if($thread['thread_number'] == $nullthread['number']): ?>
						<div class="text_number"><?php h($thread['number']) ;?></div>
						<div class="text_name"><?php h($thread['user_name']); ?></div>
						<div class="text_date"><?php h($thread['created']);?></div>
						<div class="text_content"><?php h($thread['text']); ?><br><br></div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endforeach ;?>
</div>
<!--フォーム入力-->
	<div class="footer">

	<form method="post" action="" >
		<p class="regi">コメント登録</p>
		<span class="roomname">返信</span><input class="room" type="number" name="res"><br>
		<span class="error_msg">
			<?php if(isset($error['res']) && $error['res'] == 'error1'): ?>
				<?php echo '数値を入力してください'.'<br>';?>
			<?php elseif($error['res'] == 'error2'): ?>
				<?php echo '返信先が間違っています'; ?>
			<?php elseif(isset($error['max']) && $error['max'] == 'error3'):?>
				<?php  echo 'コメントが100件になりました。新しいRoomを作成してください'; ?>
			<?php endif; ?>

		</span>
		<span class="roomname">コメント</span><textarea class="room-comment" name="text" raws="30" cols="50" ></textarea><br>
		<span class="error_msg">
			<?php if(isset($error['text']) == 'blank'): ?>
			<?php echo '必ず入力してください'.'<br>'; ?>
			<?php endif; ?>
		</span>

		<span class="roomname">名前</span><input class="room" type="text" name="user_name"><br>
		<span class="error_msg">
				<?php if(isset($error['user_name']) == 'blank'): ?>
			<?php echo '必ず入力してください' ; ?>
			<?php endif; ?>
		</span>
<!--hiddenでnumberとcreatedをpost-->
<!--	<input type="hidden" name="number" value="<?php $board_Detail['number'];?>">
	<input type="hidden" name="created" value="<?php $board_Detail['created']; ?>"> -->
		<input class="btn" type="submit" value="登録" ><br>
	</div>
	</form>

</body>

</html>
