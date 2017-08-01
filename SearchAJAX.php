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
	$query = $_GET["query"];
	
	$cmd = "SELECT `First`,`Last`,`User`,`UUID`,`DOB` FROM `useraccess` WHERE `User` = '$query'";
	$results = accessDatabase($cmd);
	echo json_encode($results);
?>