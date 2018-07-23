<?php
session_start();
require('dbconnect.php');
$result=preg_match("/^[0-9]+$/",$_POST['res']);

if(!empty($_POST)){

	if(!empty($_POST['res']) && $result == 0){
		$error['thread_number'] = 'error1';
	}
	if(isset($_POST['text']) && $_POST['text']==''){
		$error['text'] = 'blank';
	}
	if(isset($_POST['user_name']) && $_POST['user_name']==''){
		$error['user_name'] = 'blank';
	}
}

$room_id=$_GET['id'];
//代替案
$num=$db->prepare('select text from comments where room_id = ?');
$num->execute(array($_GET['id']));
$numbers=$num->fetchAll();
$numbers = count($numbers)+1;

$get_id=intval($_GET['id']);

//threadnumber照合用のバリデーション
$check=$db->prepare('select * from comments where number = ? and room_id = ? ');
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

//返信先の値がnumberよりも大きくなってしまった場合にerror2を
if($total_text['count'] < $_POST['res']){
	$error['res'] = 'error2';
}

//  thread_numberあり、なしでinsertをわける
if($total_text['count'] <= 100){
	if(empty($error)){
		if(empty($_POST['res']) && ($total_text['count'] > $_POST['res'])){
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
		 }else{
	  $error['res'] = 'error2';
		 }}
}else{
		$error['max'] = 'error3';
}


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
		<section class="heads">
			<div class="text_number"><?php h($nullthread['number']);?></div>
			<div class="text_name"><?php h($nullthread['user_name']); ?></div>
		</section>
			<div class="text_date"><?php h(date('Y/m/d/h/i',strtotime($nullthread['created'])));?></div>
			<div class="text_content"><?php h($nullthread['text']); ?><br><br></div>
	</div>

			<div class="thread_text">
				<?php foreach($threads as $thread):?>
					<?php if($thread['thread_number'] == $nullthread['number']): ?>
					<section class="threads">
						<div class="res_number"><?php h($thread['number']) ;?></div>
						<div class="res_name"><?php h($thread['user_name']); ?></div>
						<div class="res_date"><?php h(date('Y/m/d/h/i',strtotime($thread['created'])));?></div>
					</section>
						<div class="res_content"><?php h($thread['text']); ?><br><br></div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endforeach ;?>
</div>
<!--フォーム入力-->
	<div class="footer">

		<form method="post" action="" >
			<ul>
				<li class="title_register"><p class="regi">コメント登録</p></li>
				<li>
					<label class="list_title">返信</label><input class="res" type="text" name="res"><br>
					<label class="error_msg">
						<?php if(isset($error['thread_number']) && $error['thread_number'] == 'error1'):?>
							<?php echo '半角以外の数字が入力されています' ;?>
						<?php elseif(isset($error['res']) && $error['res'] == 'error1'): ?>
							<?php echo '数値を入力してください'.'<br>';?>
						<?php elseif($error['res'] == 'error2'): ?>
							<?php echo '返信先が間違っています'; ?>
						<?php elseif(isset($error['max']) && $error['max'] == 'error3'):?>
							<?php  echo 'コメントが100件になりました。新しいRoomを作成してください'; ?>
						<?php endif; ?>
					</label>
				</li>
				<li>
						<label class="list_title">コメント</label><textarea class="text" name="text" raws="30" cols="35" ></textarea><br>
					<label class="error_msg">
						<?php if(isset($error['text']) == 'blank'): ?>
						<?php echo '必ず入力してください'.'<br>'; ?>
						<?php endif; ?>
					</label>
				</li>
				<li>
					<label class="list_title">名前</label><input class="name" type="text" name="user_name"><br>
					<label class="error_msg">
							<?php if(isset($error['user_name']) == 'blank'): ?>
						<?php echo '必ず入力してください' ; ?>
						<?php endif; ?>
					</label>
				</li>
				<li>
					<input class="btn" type="submit" value="登録" ><br>
				</li>
			</ul>
		</form>
	</div>

</body>

</html>
