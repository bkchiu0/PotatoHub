<!DOCTYPE HTML>
<!--Brian Chiu-->
<html>
	<head>
		<title> Login </title>
		<link rel="icon" href="IMG/Sweet_Potato.png" />
		<style>
			
		</style>
		<link rel = "stylesheet" type = "text/css" href = "CSS/index.css" />
		<link href='https://fonts.googleapis.com/css?family=Product+Sans' rel='stylesheet' type='text/css'>
		<script src = "JS/utilities.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
		<script>
			$(function(){
				$("#loginLabel").on("click", function(){
					$("#login").slideDown(500,"swing");
					$("#registerInfo").slideUp(500,"swing");
				});
				$("#registerLabel").on("click", function(){
					$("#registerInfo").slideDown(500,"swing");
					$("#login").slideUp(500,"swing");
				});
			});
			<?php
				$_POST;
				$_GET;
				session_start();
				session_unset();
				session_destroy();
				$DB = new PDO("mysql:hostname=localhost;dbname=brother_brian", "root", "");
				function accessDatabase($command, $arrayType = PDO::FETCH_BOTH)
				{
					global $DB;
					$output = $DB->prepare($command);
					$output->execute();
					return $output->fetchAll($arrayType);
				}
				function generateUUID($prefix)
				{
					$UUID = uniqid($prefix, true);
					return $UUID;
				}
				if(!empty($_POST))
				{
					$first = $_POST['firstName'];
					$last = $_POST['lastName'];
					$user = $_POST['newUser'];
					$password = $_POST['newPass'];
					$DOB = $_POST['DOB'];
					$ID = generateUUID();
					$cmd = "INSERT INTO `useraccess`(`First`, `Last`, `User`, `Password`, `UUID`, `DOB`) VALUES ('$first', '$last', '$user', '$password', '$ID', '$DOB')";
					accessDatabase($cmd);
					echo "registered = true;\n";
				}
				else
				{
					echo "registered = false;\n";
				}
				if(!empty($_GET))
				{
					echo "login = false;\n";
				}
				else
				{
					echo "login = true;\n";
				}
			?>
			function initialize()
			{
				registerElement = document.getElementById("registerInfo");
				loginElement = document.getElementById("login");
				buttonElement = document.getElementById("button");
				formElement = document.getElementById("form");
				checkElement = document.getElementById("passCheck");
				createPassElement = document.getElementById("newPass");
				confirmPassElement = document.getElementById("confirmPass");
				logElement = document.getElementById("log");
				inputs = registerElement.getElementsByTagName("input");
				if(registered)
				{
					logElement.innerHTML = "Congratulations! You have created your account!";
				}
				if(!login)
				{
					logElement.innerHTML = "Failed to login. Please try again.";
				}
			}
			function toggleReg(state)
			{
				if(state)
				{
					/*
					registerElement.style.display = "block";
					registerElement.style.opacity = "1";
					loginElement.style.display = "none";
					loginElement.style.display = "0";
					*/
					buttonElement.value = "Register";
					buttonElement.disabled = true;
					formElement.action = "index.php";
				}
				else
				{
					/*
					registerElement.style.display = "none"
					registerElement.style.opacity = "0";
					loginElement.style.display = "block";
					loginElement.style.display = "1";
					*/
					buttonElement.value = "Login";
					buttonElement.disabled = false;
					formElement.action = "main.php";
				}
			}
			function checkPasswords(e)
			{
				var newPass = createPassElement.value;
				var confirmPass = confirmPassElement.value;
				if(newPass == confirmPass)
				{
					if(newPass == "")
					{
						checkElement.innerHTML = "You did not type a password!";
						buttonElement.disabled = true;
					}
					else
					{
						checkElement.innerHTML = "The passwords match! You are good to go!";
						buttonElement.disabled = false;
					}
				}
				else
				{
					checkElement.innerHTML = "Nope the passwords do not match! Something went wrong.";
					buttonElement.disabled = true;
				}
			}
			function checkRegister(e)
			{
				checkPasswords(e);
				for(var i = 0; i < inputs.length; i++)
				{
					if(inputs[i].value == "")
					{
						buttonElement.disabled = true;
						return null;
					}
				}
			}
		</script>
	</head>
	<body onload = "initialize();">
		<img src = "IMG/indexWallpaper.png" class = "background"/>
		<form id = "form" method = "post" action = "main.php">
			<div id = "companyHeader">
				<img src = "IMG/Logo.png" class = "webLogo" />
				PotatoHub
			</div>
			<div id = "login">
				<input class = "loginInput" type = "text" name = "username" placeholder = "Username"></input>
					<br />
				<input class = "loginInput" type = "password" name = "password" placeholder = "Password"></input>
			</div>
			<div id = "registerInfo" class = "registerInfo" onkeyup = "checkRegister(event);" onmousedown = "checkRegister(event);">
				<input class = "registerInput" type = "text" name = "firstName" placeholder = "First Name"></input>
					<br />
				<input class = "registerInput" type = "text" name = "lastName" placeholder = "Last Name"></input>
					<br />
				<input class = "registerInput" type = "text" name = "newUser" placeholder = "New Username"></input>
					<br />
				<input class = "registerInput" id = "newPass" type = "password" name = "newPass" placeholder = "New Password"></input>
					<br />
				<input class = "registerInput" id = "confirmPass" type = "password" name = "confirmPass" placeholder = "Confirm Password"></input>
					<br />
				<input class = "registerInput" type = "date" name = "DOB"></input>
				<div id = "passCheck">
					You don't have a password!
				</div>
			</div>
			
			<label id = "loginLabel" onclick = "toggleReg(false);"> Login <input type = "radio" name = "radioLogin" value = "login" checked /></label>
			<label id = "registerLabel" onclick = "toggleReg(true);"> Register <input type = "radio" name = "radioLogin" value = "register" /></label>
				<br />
			<input id = "button" type = "submit" value = "Login"/>
				<br />
			<span id = "log"></span>
		</form>
	</body>
</html>