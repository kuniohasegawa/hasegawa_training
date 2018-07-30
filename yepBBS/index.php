<?php
require_once('dbconnect.php');
require_once('structured.php');
if (!empty($_POST)) {
	if ((isset($_POST['title'])) && ($_POST['title'] == '')){
		$error['title'] = 'blank';
	}
	if ((isset($_POST['user_name'])) && ($_POST['user_name'] == '')){
		$error['user_name'] = 'blank';
	}


	if ((isset($_POST)) && (empty($error))) {
	$member = $db->prepare('INSERT INTO rooms SET title = ?, user_name = ?, modified = NOW(), created = NOW()');
	$member->execute(array(
		$_POST['title'],
		$_POST['user_name']
	));
	} else {
		echo h('登録エラー：登録できませんでした。ルーム名、名前の登録');
	}
}
$board_p = $db->prepare('
	select * 
	from rooms,(select max(modified) as comments_modified, room_id from comments group by room_id) c 
	where rooms.id = c.room_id');
$board_p->execute();
$boards = $board_p->fetchAll();

//代替案 2分割
$aaa = $db->prepare('select id, title, user_name from rooms group by id');
$aaa->execute();
$rooms_contents = $aaa->fetchAll();

$bbb = $db->prepare('select max(modified) as comments_modified, room_id from comments group by room_id');
$bbb->execute();
$comments_last_modified = $bbb->fetchAll();


/*			<?php if (isset($boards)) : ?>
				<?php foreach ($boards as $board) : ?>  
	<!--	<td class="detail"><a href="board.php?id=<?php h($board['id']); ?>"><?php h($board['title']); ?></a></td>
					<td class="detail"><?php h($board['user_name']); ?></td>
					<td class="detail"><?php h(date('Y/m/d/h/i', strtotime($board['comments_modified']))); ?></td>  -->

*/
?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="style.css">
	<title>yepBBS</title>
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

		<!-- 代替案ver -->
			<?php if (isset($rooms_contents)) : ?>
				<?php foreach ($rooms_contents as $rooms_content) : ?> 
				<tr>
							<!-- 代替案ver -->
					<td class="detail"><a href="board.php?id=<?php h($rooms_content['id']); ?>"><?php h($rooms_content['title']); ?></a></td>
					<td class="detail"><?php h($rooms_content['user_name']); ?></td>
						<?php if ((isset($comments_last_modified)) && (!empty($comments_last_modified))) : ?>
							<?php foreach ($comments_last_modified as $comments_last_mod) : ?>
								<?php if($comments_last_mod['room_id'] == $rooms_content['id']) : ?>
									<td class="detail"><?php h(date('Y/m/d',strtotime($comments_last_mod['comments_modified']))); ?></td>
								<?php endif; ?>
							<?php endforeach ;?>
						<?php endif; ?>
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
