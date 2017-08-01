<?php
	$_GET;
	$DB = new PDO("mysql:hostname=localhost;dbname=brother_brian", "root", "");
	function accessDatabase($command, $arrayType = PDO::FETCH_NUM)
	{
		global $DB;
		$output = $DB->prepare($command);
		$output->execute();
		return $output->fetchAll($arrayType);
	}
	$friend = $_GET["friend"];
	$idF = $_GET["idF"];
	$user = $_GET["user"];
	$uid = $_GET["uid"];
	$cmd = "UPDATE `userfriends` SET `Status`= 1 WHERE `User` = '$friend' AND `UUID` = '$idF' AND `Friend` = '$user' AND `FriendUUID` = '$uid'";
	accessDatabase($cmd);
	$cmd = "INSERT INTO `userfriends`(`User`, `UUID`, `Friend`, `FriendUUID`, `Status`) VALUES ('$user','$uid','$friend','$idF', 1)";
	accessDatabase($cmd);
?>