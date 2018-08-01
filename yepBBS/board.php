<?php

require_once('dbconnect.php');
require_once('structured.php');

const Harf_Width_Error = 1;
const Reverse_Path_Error = 2;
const Insert_Error = 3;
const MaxOverCommentError = 4;
const blank = 5;
const max_comments = 100;
const Over_User_Name_Varchar = 20;

$room_id = intval($_GET['id']);

if (!empty($_POST)) {
	if (!empty($_POST['res'])) {
		$result = preg_match("/^[0-9]+$/",$_POST['res']);
	}
	if ((!empty($_POST['res'])) && ($result == 0)) {
		$error['thread_number'] = Harf_Width_Error;
	}
	if ((isset($_POST['text'])) && ($_POST['text'] == '')) {
		$error['text'] = blank;
	}
	if ((isset($_POST['user_name'])) && ($_POST['user_name'] == '')) {
		$error['user_name'] = blank;
	}
	if ((isset($_POST['user_name'])) && (strlen($_POST['user_name']) > 20)) {
		$error['over_user_name'] = Over_User_Name_Varchar;
	}
	if (isset($_POST['res'])){
		$Res['res'] = $_POST['res'];
	}
}

$num = $db->prepare('select text from comments where room_id = ?');
$num->execute(array($room_id));
$numbers = $num->fetchAll();
$numbers = count($numbers) + 1;
$numbers = intval($numbers);

if(isset($_POST['res'])) {
	$_POST['res'] = intval($_POST['res']);
		if($_POST['res'] == 0) {
			$_POST['res'] = null;
		}
}
if(isset($_POST['res'])) {
$check = $db->prepare('select * from comments where number = ? and room_id = ?');
$check->execute(array(
	$_POST['res'],
	$room_id
));
$check_thread_number = $check->fetch();
}

$text = $db->prepare('select count(text) as count from comments where room_id = ?');
$text->execute(array($room_id));
$total_text = $text->fetch();

if (isset($_POST['res'])) {
	if ($total_text['count'] < $_POST['res']) {
		$error['res'] = Reverse_Path_Error;
	}
}

if ($total_text['count'] < max_comments) {
	if ((empty($error)) && (isset($_POST))) {
		if ((empty($_POST['res'])) && (isset($_POST['text'],$_POST['user_name']))) {
			$bbs = $db->prepare('INSERT INTO comments SET room_id = ?, thread_number = ?, number = ?, text = ?, user_name = ?, modified = NOW(), created = NOW()');
				$test = $bbs->execute(array(
					$room_id,
					null,
					$numbers,
					$_POST['text'],
					$_POST['user_name']
				));
		 } elseif ((isset($_POST['res'])) && (!is_null($_POST['res'])) && (is_null($check_thread_number['thread_number'])) && ($total_text['count'] >= $_POST['res'])) {
			$bbs = $db->prepare('INSERT INTO comments SET room_id = ?, thread_number = ?, number = ?, text = ?, user_name = ?, modified = NOW(), created = NOW()');
				$test = $bbs->execute(array(
					$room_id,
					$_POST['res'],
					$numbers,
					$_POST['text'],
					$_POST['user_name']
				));
		} elseif ((isset($_POST['res'])) && (isset($check_thread_number['thread_number']))) {
			$error['is'] = Insert_Error;
		} elseif ((isset($_POST['res'])) && ($total_text['count'] <= $_POST['res'])) {
			$error['res'] = Reverse_Path_Error;
		}
	}
} else {
	$error['max'] = 'MaxOverCommentError';
	}

$relation = $db->prepare('SELECT * FROM comments WHERE room_id = ? and thread_number IS NULL ORDER BY created ASC');
$relation->execute(array($room_id));
$nullthreads = $relation->fetchAll();

$stanby = $db->prepare('SELECT * FROM comments WHERE room_id = ?  ORDER BY created ASC');
$stanby->execute(array(
	$room_id,
	));
$threads = $stanby->fetchAll();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="style.css">
	<title>yepBBS</title>
</head>

<body>
	<?php if ($numbers >100) : ?>
		 <h4 class="max_comments"><?php h('コメントが100件になりました。ここにはコメントできません。') ?></h4>
	<?php endif; ?>
	<div class="comments_header">
		<h1><span>yep</span>BBS</h1>
	</div>
	<div class="comments_time">
		<?php echo date("Y/m/d"); ?><br>
		<?php echo date("H:i"); ?>

	</div>
<div class="main">
	<div class="text">
		<?php foreach($nullthreads as $nullthread): ?>
			<div class="style_heads">
				<div class="heads">
					<div class="text_number"><?php h($nullthread['number']); ?></div>
					<div class="text_name"><?php h($nullthread['user_name']); ?></div>
					<div class="text_date"><?php h(date('Y/m/d H:i',strtotime($nullthread['created']))); ?></div>
					<div class="text_content"><?php h($nullthread['text']); ?><br><br></div>
				</div>
			<div>
			<div class="thread_text">
				<?php foreach($threads as $thread): ?>
					<?php if($thread['thread_number'] == $nullthread['number']) : ?>
						<div class = "style_threads">
							<div class="threads">
								<div class="res_number"><?php h($thread['number']); ?></div>
								<div class="res_name"><?php h($thread['user_name']); ?></div>
								<div class="res_date"><?php h(date('Y/m/d H:i',strtotime($thread['created']))); ?></div>
							</div>
								<div class="res_content"><?php h($thread['text']); ?><br><br></div>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
	<div class="comments_footer">
		<form method="post" action="" >
			<ul>
				<li class="title_register"><p class="regi">コメント登録</p></li>
				<li>
					<label class="list_title">返信<input class="res" type="text" name="res"></label><br>
					<div class="error_msg">
						<?php if ((isset($error['thread_number'])) && ($error['thread_number'] == Harf_Width_Error)) : ?>
							<?php echo '半角以外の数字が入力されています'; ?>
						<?php elseif ((isset($error['is'])) && ($error['is'] == Insert_Error)) : ?>
							<?php echo 'コメントに失敗しました'; ?>
						<?php elseif (isset($error['res']) && ($error['res'] == Reverse_Path_Error)) : ?>
							<?php echo '返信先が間違っています'; ?>
						<?php elseif ((isset($error['max'])) && ($error['max'] == MaxOverCommentError)) :  ?>
							<?php  echo 'コメントが100件になりました。新しいRoomを作成してください'; ?><br>
						<?php endif; ?>
					</div>
				</li>
				<li>
					<label class="list_title">コメント</label>
					<textarea class="text" name="text" raws="30" cols="35" ></textarea><br>
						<div class="comments_error_msg">
							<?php if(isset($error['text']) == blank) : ?>
								<h4 class="comments_error"><?php echo '必ず入力してください'; ?></h4><br>
							<?php endif; ?>
						</div>
				</li>
				<li>
					<div class="name_list">
						<label class="list_title">名前</label>
						<input class="name" type="text" name="user_name">
						<input class="btn" type="submit" value="登録" >
					</div>
				</li>
				<li>
					<div class="comments_error_msg">
						<?php if(isset($error['user_name']) == blank) : ?>
							<h4 class="comments_error"><?php echo '必ず入力してください'; ?></h4>
						<?php endif; ?>
						<?php if((isset($error['over_user_name'])) && ($error['over_user_name'] == Over_User_Name_Varchar)) : ?>
							<h4 class="comments_error"><?php echo '名前の文字数が20文字を超えています。'; ?></h4>
						<?php endif; ?>
					</div>
				</li>
			</ul>
		</form>
	</div>

</body>
</html>
