<?php
session_start();


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

	<form method="post" action="">		
		<p class="regi">ルーム登録</p>
		<span class="roomname">ルーム名</span><input class="room" type="text" name="room"><br>
		<span class="list-name">名前</span><input class="name"type="text" name="name">
		<input class="btn" type="submit" value="登録" ><br>
	</div>	
	</form>

</body>

</html>
