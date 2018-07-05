<?php
try{
	$db=new PDO('mysql:dbname=yepbbs; host=192.168.2.59 ;charset=utf8','root','P@ssw0rd');
}catch(PDOException $e){
	echo 'DB接続エラー: '.$e->getMessage();
}

?>
