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
		<?php echo date("y/m/d"); ?>
	</div>

	
	
	<div class="footer">
		
	<p>ルーム登録	</p>
		<div class="room-name">ルーム名
		<input class="room" type="text" name="room"><br>
		<span class="insert">必ず入力してください</span><br>
		</div>
		<div class="name">名前
		<input class=""type="text" name="name"><br>
		<input type="submit" value="登録" ><br>
		</div>
	</div>	
	

</body>

</html>
