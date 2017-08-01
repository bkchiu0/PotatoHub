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
	$user = $_GET["user"];
	$id = $_GET["id"];
	$likes = $_GET["likes"];
	$postID = $_GET["postID"];
	$cmd = "UPDATE `userfeed` SET `Likes` = '$likes' WHERE `User` = '$user' AND `UUID` = '$id' AND `postID` = '$postID'";
	accessDatabase($cmd);
?>