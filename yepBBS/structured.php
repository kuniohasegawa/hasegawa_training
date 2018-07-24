<?php
//ローカルタイムの設定
date_default_timezone_set('Asia/Tokyo');

//関数
function h($value){
	echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
