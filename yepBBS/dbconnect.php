<?php
try{
	$db=new PDO('mysql:dbname=yepbbs; host=127.0.0.1;charset=utf-8','root','');
}catch(PDOException $e){
	echo 'DB接続エラー: '.$e->getMessage();
}

?>
