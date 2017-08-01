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
	$content = $_GET["content"];
	$postID = $_GET["postID"];
	$poster = $_GET["poster"];
	$posterID = $_GET["posterID"];
	$cmd = "INSERT INTO `usercomments`(`postID`, `Content`, `Poster`, `PosterID`) VALUES ('$postID','$content','$poster','$posterID')";
	accessDatabase($cmd);
	$returnArr = [$postID, $content, $poster, $posterID];
	echo json_encode($returnArr);
?>