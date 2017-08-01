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
	function generateID()
	{
		$UUID = uniqid("", true);
		return $UUID;
	}
	$user = $_GET["user"];
	$UUID = $_GET["id"];
	$Content = $_GET["content"];
	$postID = generateID();
	$cmd = "INSERT INTO `userfeed`(`User`, `UUID`, `Content`, `Likes`, `postID`) VALUES ('$user','$UUID','$Content',0,'$postID')";
	accessDatabase($cmd);
	echo $postID;
?>