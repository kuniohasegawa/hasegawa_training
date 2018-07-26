<?php
require_once('dbconnect.php');
require_once('structured.php');
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

$board_p = $db->prepare('SELECT r.*, c.modified AS comments_modified  FROM rooms r LEFT INNER JOIN comments c ON r.id = c.room_id ORDER BY r.modified DESC');
$board_p->execute();
$boards = $board_p->fetchAll();
var_dump(max($boards['comments_modified']));
var_dump($boards['id']);
echo '<pre>';
var_dump($boards);
echo '</pre>';

//$modified = $db->prepare('SELECT max(modified) AS modified FROM comments WHERE room_id = ?');
//	$modified->execute(array(
//			$boards['id']
//		));
//$last_comment_modified = $modified->fetchAll();
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
		<?php if (isset($board)) : ?>
			<?php foreach($boards as $board) : ?>
				<tr>
				<td class="detail"><a href="board.php?id = <?php echo $board['id'] ;?>;"><?php h($board['title']); ?></a></td>
					<td class="detail"><?php h($board['user_name']); ?></td>
					<td class="detail"><?php h(max($board['comments_modified'])); ?></td>
				</tr>
			<?php endforeach ; ?>
		<?php endif; ?>
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
