<!DOCTYPE HTML>
<!--Brian Chiu-->
<html>
	<head>
		<title> PotatoHub </title>
		<link rel="icon" href="IMG/Sweet_Potato.png" />
		<style>
			
		</style>
		<link rel = "stylesheet" type = "text/css" href = "CSS/main.css" />
		<link href='https://fonts.googleapis.com/css?family=Product+Sans' rel='stylesheet' type='text/css'>
		<script src = "JS/utilities.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
		<script>
			//JQUERY
			$(function(){
				$(window).on('beforeunload', function() {
					$(window).scrollTop(0);
				});
				$("#HUD").on('click', function(event) {
					$(".background").css("bottom", "0%");
				});
				$("#feedSection").on('click', function(event) {
					$(".background").css("bottom", "100%");
				});
				$("#friendSection").on('click', function(event) {
					$(".background").css("bottom", "200%");
				});
				$("#findSection").on('click', function(event) {
					$(".background").css("bottom", "400%");
				});
				$("#requestSection").on('click', function(event) {
					$(".background").css("bottom", "300%");
				});
				$("a").on('click', function(event) {
					if (this.hash !== "") {
						event.preventDefault();
						var hash = this.hash;
						$('html, body').animate({
							scrollTop: $(hash).offset().top
						}, 1000, function(){
					  });
					}
				});
				$("a").mouseover(function(){
					$(this).css("background-color", "#ddddff");
				});
				$("a").mouseleave(function(){
					$(this).css("background-color", "transparent");
				});
				$("#currentLogin").mouseover(function(){
					$(this).css("background-color", "#5581A4");
				});
				$("#currentLogin").mouseleave(function(){
					$(this).css("background-color", "transparent");
				});
				$("#feedContainer").on("click",".commentButton",function(){
					$(this).parent().parent().children(".commentBox").slideToggle(500,"swing");
				});
				$("#feedContainer").on("mouseover",".statusPost",function(){
					$(this).css("background-color", "transparent");
					$(this).css("color", "white");
					$(this).children(".commentContainer").css("border-color", "#9E9E9E");
				});
				$("#feedContainer").on("mouseleave",".statusPost",function(){
					$(this).css("background-color", "rgba(211,227,232,0.5)");
					$(this).css("color", "black");
					$(this).children(".commentContainer").css("border-color", "#ffffff");
				});
				$("#friendsList").on("mouseover",".friendItm",function(){
					$(this).css("font-size", "24pt");
				});
				$("#friendsList").on("mouseleave",".friendItm",function(){
					$(this).css("font-size", "16pt");
				});
				$("#searchResults").on("mouseover",".searchResult",function(){
					$(this).css("font-size", "24pt");
				});
				$("#searchResults").on("mouseleave",".searchResult",function(){
					$(this).css("font-size", "16pt");
				});
				$("#requests").on("mouseover",".requestItm",function(){
					$(this).css("font-size", "24pt");
				});
				$("#requests").on("mouseleave",".requestItm",function(){
					$(this).css("font-size", "16pt");
				});
			});
			<?php
				$_POST;
				$DB = new PDO("mysql:hostname=localhost;dbname=brother_brian", "root", "");
				$User = $_POST['username'];
				$Password = $_POST['password'];
				$cmd = "SELECT * FROM `useraccess` WHERE `User` = '$User' AND `Password` = '$Password'";
				function accessDatabase($command, $arrayType = PDO::FETCH_NUM)
				{
					global $DB;
					$output = $DB->prepare($command);
					$output->execute();
					return $output->fetchAll($arrayType);
				}
				$result = accessDatabase($cmd);
				if(empty($result))
				{
					header('Location:index.php?login=false');
				}
				else
				{
					session_start();
					$_SESSION["userInfo"] = $result[0];
					echo "userInfo = ".json_encode($_SESSION["userInfo"]).";\n";
					$username = $_SESSION["userInfo"][2];
					$uuid = $_SESSION["userInfo"][4];
					$cmd = "SELECT `Friend`, `FriendUUID` FROM `userfriends` WHERE `User` = '$username' AND `UUID` = '$uuid' AND `Status` = 1";
					$_SESSION["confirmedFriends"] = accessDatabase($cmd);
					$cmd = "SELECT `User`, `UUID` FROM `userfriends` WHERE `Friend` = '$username' AND `FriendUUID` = '$uuid' AND `Status` = 0";
					$_SESSION["unconfirmedFriends"] = accessDatabase($cmd);
					echo "confirmedFriends = ".json_encode($_SESSION["confirmedFriends"]).";\n";
					echo "unconfirmedFriends = ".json_encode($_SESSION["unconfirmedFriends"]).";\n";
					$cmd = "SELECT `Content`,`Likes`,`User`,`UUID`,`postID` FROM `userfeed` WHERE `User` = '$username' AND `UUID` = '$uuid'";
					$_SESSION["feed"] = accessDatabase($cmd);
					foreach($_SESSION["confirmedFriends"] as $friend)
					{
						$cmd = "SELECT `Content`,`Likes`,`User`,`UUID`,`postID` FROM `userfeed` WHERE `User` = '$friend[0]' AND `UUID` = '$friend[1]'";
						$_SESSION["feed"] = array_merge($_SESSION["feed"],accessDatabase($cmd));
					}
					foreach($_SESSION["feed"] as &$post)
					{
						$postID = $post[4];
						$cmd = "SELECT `postID`, `Content`, `Poster`, `PosterID` FROM `usercomments` WHERE `postID` = '$postID'";
						$post[5] = accessDatabase($cmd);
					}
					echo "feed = ".json_encode($_SESSION["feed"]).";\n";
					$cmd = "SELECT `Friend`, `FriendUUID` FROM `userfriends` WHERE `User` = '$username' AND `UUID` = '$uuid' AND `Status` = 0";
					$_SESSION["sentReq"] = accessDatabase($cmd);
					echo "sentRequests = ".json_encode($_SESSION["sentReq"]).";\n";
				}
			?>
			/*
				When a user adds a friend the status is 0 and in the database there will be one 
				row added that adds user and uuid as well as the recipient user and uuid
				When the recipient logs on the invite will be seen and if accepted, it will
				add another row that is similar but in reverse order and also update both statuses
				to 1 which indicates confirmed.
			*/
			function initialize()
			{
				feed.reverse();
				feedElement = document.getElementById("feedContainer");
				postBox = document.getElementById("newContent");
				createPost = document.getElementById("createPost");
				friendsElement = document.getElementById("friendsList");
				requestElement = document.getElementById("requests");
				searchElement = document.getElementById("searchQuery");
				searchResultElement = document.getElementById("searchResults");
				loginElement = document.getElementById("currentLogin");
				loginElement.innerHTML = (userInfo[2]);
				postCtr = feed.length + 1;
				updateLikes();
				display();
			}
			function updateLikes()
			{
				for(var i = 0; i < feed.length; i++)
				{
					feed[i][1] = parseInt(feed[i][1]);
				}
			}
			function determineFriend(info)
			{
				if(info[0] == userInfo[2] && info[1] == userInfo[4])
				{
					return "Self";
				}
				for(var i = 0; i < confirmedFriends.length; i++)
				{
					var temp = confirmedFriends[i];
					if(temp[0] == info[0] && temp[1] == info[1])
					{
						return "ConfirmedFriend";
					}
				}
				for(var i = 0; i < sentRequests.length; i++)
				{
					var temp = sentRequests[i];
					if(temp[0] == info[0] && temp[1] == info[1])
					{
						return "UnconfirmedFriend";
					}
				}
				return "NotFriend";
			}
			function display()
			{
				updateFeed();
				updateFriendsList();
				updateRequests();
			}
			function Logout()
			{
				window.location = "index.php";
			}
			function clearElementofNodes(element)
			{
				while(element.hasChildNodes())
				{
					element.removeChild(element.childNodes[0]);
				}
			}
			function updateRequests()
			{
				clearElementofNodes(requestElement);
				for(var i = 0; i < unconfirmedFriends.length; i++)
				{
					var span = document.createElement("span");
					span.setAttribute("class", "requestItm");
					span.setAttribute("onclick", "acceptRejectRequest(event, this);");
					span.style.cursor = "pointer";
					span.innerHTML = unconfirmedFriends[i][0];
					span.information = unconfirmedFriends[i];
					requestElement.appendChild(span);
					var br = document.createElement("br");
					requestElement.appendChild(br);
				}
			}
			function updateFriendsList()
			{
				clearElementofNodes(friendsElement);
				for(var i = 0; i < confirmedFriends.length; i++)
				{
					var span = document.createElement("span");
					span.setAttribute("class", "friendItm");
					span.setAttribute("onclick", "removeFriend(event, this);");
					span.style.cursor = "pointer";
					span.innerHTML = confirmedFriends[i][0];
					span.information = confirmedFriends[i];
					friendsElement.appendChild(span);
					var br = document.createElement("br");
					friendsElement.appendChild(br);
				}
			}
			function updateFeed()
			{
				clearElementofNodes(feedElement);
				var ctr = 0;
				for(var i = 0; i < feed.length; i++)
				{
					//the own user's feed
					var span = document.createElement("div");
					var content = document.createElement("div");
					content.innerHTML = (feed[i][2] + " - " + feed[i][0]);
					span.appendChild(content);
					
					var img = document.createElement("img");
					img.src = "IMG/Potato.png";
					img.setAttribute("class", "potatoButton");
					img.setAttribute("onclick", "likePost(this);");
					img.style.cursor = "pointer";
					img.information = feed[i];
					img.counter = ctr;
					
					var commentimg = document.createElement("img");
					commentimg.src = "IMG/Comment.png";
					commentimg.setAttribute("class", "commentButton");
					commentimg.setAttribute("onclick", "createCommentBox(this);");
					commentimg.style.cursor = "pointer";
					commentimg.information = feed[i];
					commentimg.counter = ctr;
					commentimg.activated = false;
					
					content = document.createElement("div");
					content.appendChild(img);
					content.appendChild(document.createTextNode(" Give Potato | "));
					content.appendChild(commentimg);
					content.appendChild(document.createTextNode(" Comment"));
					span.appendChild(content);
					
					img = document.createElement("img");
					img.src = "IMG/Potato.png";
					img.setAttribute("class", "potatoButton");
					content = document.createElement("div");
					content.appendChild(img);
					content.appendChild(document.createTextNode(" Earned - " + feed[i][1]));
					span.appendChild(content);
					
					content = document.createElement("div");
					content.setAttribute("class", "commentContainer");
					
					var labelDiv = document.createElement("div");
					labelDiv.setAttribute("class", "subLabel");
					labelDiv.appendChild(document.createTextNode("Comments"));
					content.appendChild(labelDiv);
					
					for(var j = 0; j < feed[i][5].length; j++)
					{
						var p = document.createElement("p");
						
						p.appendChild(document.createTextNode(feed[i][5][j][2] + " - " + feed[i][5][j][1]));
						p.setAttribute("class", "comment");
						content.appendChild(p);
					}
					span.appendChild(content);
					
					span.setAttribute("class", "statusPost");
					feedElement.appendChild(span);
					feedElement.appendChild(document.createElement("br"));
					ctr += 2;
				}
			}
			function updateSearchResults(arr)
			{
				clearElementofNodes(searchResultElement);
				for(var i = 0; i < arr.length; i++)
				{
					var info = [arr[i][2], arr[i][3]];
					var isFriend = determineFriend(info);
					var span = document.createElement("span");
					span.setAttribute("class", "searchResult");
					span.isAdded = isFriend;
					if(isFriend == "ConfirmedFriend" || isFriend == "UnconfirmedFriend" || isFriend == "Self")
					{
						if(isFriend == "ConfirmedFriend")
						{
							span.innerHTML = arr[i][2] + " (" + arr[i][0] + " " + arr[i][1] + ")" + " " + "(Friends)";
						}
						else
						{
							if(isFriend == "UnconfirmedFriend")
								span.innerHTML = arr[i][2] + " (" + arr[i][0] + " " + arr[i][1] + ")" + " " + "(Requested)";
							else
								span.innerHTML = arr[i][2] + " (" + arr[i][0] + " " + arr[i][1] + ")" + " " + "(Myself)";
						}
					}
					else
					{
						span.setAttribute("onclick", "sendRequest(this);");
						span.style.cursor = "pointer";
						span.innerHTML = arr[i][2] + " (" + arr[i][0] + " " + arr[i][1] + ")";
					}
					span.information = arr[i];
					searchResultElement.appendChild(span);
					var br = document.createElement("br");
					searchResultElement.appendChild(br);
				}
			}
			function postFeed()
			{
				var request = new XMLHttpRequest();
					
				request.onreadystatechange = function()
				{
					if (request.readyState == 4)
					{
						var postID = request.responseText;
						feed.unshift([text,0,userInfo[2],userInfo[4],postID,[]]);
						display();
					}
				}
				var text = postBox.value;
				var url = "PostAJAX.php?user=" + userInfo[2] + "&id=" + userInfo[4] + "&content=" + text;
				
				request.open("GET", url, true);
				request.send(null);
				postBox.value = "";
			}
			function acceptRejectRequest(e, item)
			{
				if(!e.shiftKey)
				{
					var req = new XMLHttpRequest();
					req.onreadystatechange = function()
					{
						if (req.readyState == 4)
						{
							//req.responseText;
						}
					}
					var url = "FriendsAJAX.php?friend=" + item.information[0] + "&idF=" + item.information[1] + "&user=" + userInfo[2] + "&uid=" + userInfo[4];
					req.open("GET", url, true);
					req.send(null);
					//Remove things from request and add to friendslist
					requestElement.removeChild(item);
					var span = document.createElement("span");
					span.setAttribute("class", "friendItm");
					span.setAttribute("onclick", "removeFriend(event, this);");
					span.style.cursor = "pointer";
					span.innerHTML = item.information[0];
					span.information = item.information;
					friendsElement.appendChild(span);
					var br = document.createElement("br");
					friendsElement.appendChild(br);
				}
				else
				{
					var request = new XMLHttpRequest();
					request.onreadystatechange = function()
					{
						if(request.readyState == 4)
						{
							//something
						}
					}
					var url = "RemoveAJAX.php?friend=" + item.information[0] + "&idF=" + item.information[1] + "&user=" + userInfo[2] + "&uid=" + userInfo[4];
					request.open("GET", url, true);
					request.send(null);
					requestElement.removeChild(item);
				}
			}
			function searchFriends(e)
			{
				if(e.which == 13)
				{
					var request = new XMLHttpRequest();
					request.onreadystatechange = function()
					{
						if (request.readyState == 4)
						{
							var searchArray = JSON.parse(request.responseText);
							updateSearchResults(searchArray);
						}
					}
					var url = "SearchAJAX.php?query=" + searchElement.value;
					request.open("GET", url, true);
					request.send(null);
					searchElement.value = "";
				}
			}
			function removeFriend(e, item)
			{
				if(e.shiftKey)
				{
					var request = new XMLHttpRequest();
					request.onreadystatechange = function()
					{
						if(request.readyState == 4)
						{
							//something
						}
					}
					var url = "RemoveAJAX.php?friend=" + item.information[0] + "&idF=" + item.information[1] + "&user=" + userInfo[2] + "&uid=" + userInfo[4];
					request.open("GET", url, true);
					request.send(null);
					friendsElement.removeChild(item);
				}
			}
			function sendRequest(item)
			{
				var request = new XMLHttpRequest();
				request.onreadystatechange = function()
				{
					if(request.readyState == 4)
					{
						//something
					}
				}
				var url = "RequestAJAX.php?friend=" + item.information[2] + "&idF=" + item.information[3] + "&user=" + userInfo[2] + "&uid=" + userInfo[4];
				request.open("GET", url, true);
				request.send(null);
				searchResultElement.removeChild(item);
			}
			function likePost(item)
			{
				var request = new XMLHttpRequest();
				request.onreadystatechange = function()
				{
					if(request.readyState == 4)
					{
						//something
					}
				}
				var likes = item.information[1] + 1;
				var url = "LikeAJAX.php?user=" + item.information[2] + "&id=" + item.information[3] + "&likes=" + likes + "&content=" + item.information[0] + "&postID=" + item.information[4];
				//Later Patch Gives the posts a UUID incase some have the same content
				request.open("GET", url, true);
				request.send(null);
				feed[item.counter/2][1] = likes;
				display();
			}
			function createCommentBox(item)
			{
				var post = feedElement.childNodes[item.counter];
				if(!item.activated)
				{
					var textarea = document.createElement("textarea");
					textarea.style.resize = "none";
					textarea.information = item.information;
					textarea.counter = item.counter;
					textarea.setAttribute("onkeypress", "commentPost(event, this);");
					textarea.setAttribute("class", "commentBox");
					textarea.style.display = "none";
					post.insertBefore(textarea, post.childNodes[3]);
					item.activated = !item.activated;
				}
			}
			function commentPost(e, item)
			{
				if(e.which == 13)
				{
					var request = new XMLHttpRequest();
					request.onreadystatechange = function()
					{
						if(request.readyState == 4)
						{
							var commentArr = JSON.parse(request.responseText);
							feed[item.counter/2][5].push(commentArr);
							display();
						}
					}
					var url = "CommentAJAX.php?content=" + item.value + "&postID=" + item.information[4] + "&poster=" + userInfo[2] + "&posterID=" + userInfo[4];
					request.open("GET", url, true);
					request.send(null);
				}
			}
		</script>
	</head>
	<body onload = "initialize();">
		<img src = "IMG/MasterWallpaper.png" class = "background" />
		<div id = "scrollbar">
			<a href = "#HUD" class = "scroll"> Post </a>
				<br />
			<a href = "#feedSection" class = "scroll"> Feed </a>
				<br />
			<a href = "#friendSection" class = "scroll"> Friends </a>
				<br />
			<a href = "#requestSection" class = "scroll"> Friend Requests </a>
				<br />
			<a href = "#findSection" class = "scroll"> Find Friends </a>
				<br />
			<a onclick = "Logout();" class = "scroll"> Logout </a>
				<br />
			<span id = "currentLogin" class = "scroll">  </span>
		</div>
		<div id = "HUD">
			<div id = "createPost">
				<div id = "companyHeader">
					<img src = "IMG/Logo.png" class = "webLogo" />
					PotatoHub
				</div>
				<textarea id = "newContent" placeholder = "Type whatever you want to post here."></textarea>
				<br />
				<button id = "post" onclick = "postFeed();"> POST </button>
			</div>
		</div>
		<div id = "feedSection">
			<p class = "label">
				FEED
			</p>
			<div id = "feedContainer">
				
			</div>
		</div>
		<div id = "friendSection">
			<p class = "label">
				FRIENDS
			</p>
			<div id = "friendsList">
				
			</div>
		</div>
		<div id = "requestSection">
			<p class = "label">
				FRIEND REQUESTS
			</p>
			<div id = "requests">
				
			</div>
		</div>
		<div id = "findSection">
			<p class = "label">
				FIND FRIENDS
			</p>
			<div>
				<input id = "searchQuery" placeholder = "Search For Users" type = "text" onkeypress = "searchFriends(event);"/>
			</div>
			</br>
			<div id = "searchResults">
				
			</div>
		</div>
	</body>
</html>