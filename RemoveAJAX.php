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
	$cmd = "DELETE FROM `userfriends` WHERE `User` = '$user' AND `UUID` = '$uid' AND `Friend` = '$friend' AND `FriendUUID` = '$idF'";
	accessDatabase($cmd);
	$cmd = "DELETE FROM `userfriends` WHERE `User` = '$friend' AND `UUID` = '$idF' AND `Friend` = '$user' AND `FriendUUID` = '$uid'";
	accessDatabase($cmd);
	//can remove friend request as well as confirmed friends
?>