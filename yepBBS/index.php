<?php
require_once('dbconnect.php');

if (!empty($_POST)) {
	if (isset($_POST['title']) && ($_POST['title'] == '')){
		$error['title'] = 'blank';
	}
	if (isset($_POST['user_name']) && ($_POST['user_name'] == '')){
		$error['user_name'] = 'blank';
	}


if (isset($_POST) && (empty($error))) {
	$member = $db->prepare('INSERT INTO rooms SET title=?, user_name=?, modified=NOW(), created=NOW()');
	$member->execute(array(
		$_POST['title'],
		$_POST['user_name']
	));
}}

$bord_p = $db->query('SELECT rooms.*, comments.modified AS comments_modified  FROM rooms JOIN comments ORDER BY modified DESC');
$boards = $bord_p->fetchAll();
//var_dump(max($boards['comments_modified']));

//$modified = $db->prepare('SELECT max(modified) AS modified FROM comments WHERE room_id = ?');
//	$modified->execute(array(
//			$boards['id']
//		));
//$last_comment_modified = $modified->fetchAll();
function h($value) {
	echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

//ローカルタイムの設定
date_default_timezone_set('Asia/Tokyo');
?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="style.css">
	<title>yepBBS</title>
<script language = "javascript">
resizeTo(850,550);
</script>
</head>

<body>
<div class="display-width">
	<div class="header">
		<h1><span>yep</span>BBS</h1>
	</div>
	<div class="time">
		<?php echo date("y/m/d"); ?><br>
		<?php echo date("h/i"); ?>

	</div>
	<div class="main-contents">
	<table class="contents-table" align="center">
			<tr>
				<th class="title-detail">タイトル</th>
				<div class="name_stamp">
				<th>ルーム作成者</th>
				<th>最終更新日</th>
				</div>
			</tr>
		<?php foreach($boards as $board) : ?>
			<tr>
			<td class="detail"><a href="board.php?id = <?php echo $board['id']?>;"><?php h($board['title']); ?></a></td>
				<td class="detail"><?php h($board['user_name']); ?></td>
				<td class="detail"><?php h($board['comments_modified']); ?></td>
			</tr>
		<?php endforeach ; ?>
	</table>
	</div>


	<div class="footer">
		<form method="post" action="" >
			<p class="regi">ルーム登録</p>
			<span class="roomname">ルーム名</span><input class = "room" type = "text" name = "title"><br>
			<span class="error_msg">
				<?php if(isset($error['title']) == 'blank'): ?>
					<?php echo '必ず入力してください'.'<br>';?>
				<?php endif; ?>
			</span>
			<span class="list-name">名前</span><input class="name"type="text" name="user_name">
			<input class="btn" type="submit" value="登録" ><br>
			<span class="error_msg">
				<?php if (isset($error['user_name']) == 'blank'): ?>
					<?php echo '必ず入力してください'.'<br>'; ?>
				<?php endif; ?>
			</span>
		</form>
	</div>
</div>
</body>
</html>
