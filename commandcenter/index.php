<?php 

$adminList = populateList(".htaccess");
$accessList = populateList("../.htaccess");

function populateList($filename) {
	$arr = array();
	$file = fopen($filename, "r") or exit("");
	$line = fgets($file);
	$names = explode(' ', fgets($file));
	for ($i=2;$i<count($names);$i++) {
		$name = trim($names[$i]);
		if ($name != "") 
			$arr[] = trim($names[$i]);
	}
	fclose($file);
	return $arr;
}

function listUsers($list, $listName) {
	for ($i=0;$i<count($list);$i++)
		populateRow($list[$i], $listName);
}

function populateRow($name, $listName) {
	echo "<div id='$name' onclick='deleteUser(\"$name\",\"$listName\",this);'>$name: <a href='#' class='deleteX'>[ X ]</a></div>\n";
}

function printList($list) {
	echo '{';
	for ($i=0;$i<count($list)-1;$i++)
		echo '"',$list[$i],'":"',$list[$i], '",';
	echo '"',$list[count($list)-1],'":"',$list[count($list)-1], '"}';
}
?>
<html>
	<head>
		<title>ADMIN :: Georgia Institute of Technology RNA Database</title>
		<script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="../js/jquery-ui-1.8.20.custom.min.js"></script>
		<script type="text/javascript">
			var adminUsers = <?php printList($adminList);?>;
			var accessUsers = <?php printList($accessList);?>;
			var changes = 0;
			function deleteUser(userName, list, ele) {
				if (confirm("Are you sure? Remove user "+userName)) {
					list = list == "admin" ? adminUsers : accessUsers;
					delete list[userName];
					console.debug(list);
					$(ele).html("");
					if (!changes)
						$("#saveBox").fadeIn(2000);
					changes = 1;
				}
			}
			function addUser(listName) {
				list = listName == "admin" ? adminUsers : accessUsers;
				var userName = $("#"+listName+"Name").val();
				list[userName] = userName;
				var ele = $("<div id='"+userName+"' onclick='deleteUser(\""+userName+"\",\""+listName+"\",this);'>"+userName+": <a href='#' class='deleteX'>[ X ]</a></div>");
				$("#"+listName+"ListDiv").append(ele);
				if (!changes)
					$("#saveBox").fadeIn(2000);
				changes = 1;
				$("#"+listName+"Name").val("");
			}
			function saveCurrentUsersOut() {
				var jsonData = {};
				jsonData.admin = adminUsers;
				jsonData.access = accessUsers;
				$.ajax({
					type: 'POST',
					url: "command_api.php?save",
					data: jsonData,
					success:saveCurrentUsersIn
				});
			}
			function saveCurrentUsersIn(data) {
				console.log(data);
				data = JSON.parse(data);
				changes = data.successBool;
				if (!changes)
					$("#saveBox").fadeOut(2000);
				$("#messagesDiv").html(data.result).show().fadeOut(10000);
			}
		</script>
		<style>
			.deleteX {
				color:red;
				font-weight:bold;
			}
			.saveBox {
				border: solid 1px black;
				padding: 15px;
				float: right;
			}
		</style>
	</head>
	<body>
		<div id="saveBox" class="saveBox"><button onclick="saveCurrentUsersOut();">Save Changes...</button></div>
		<div id="messagesDiv"></div>
		<h1>Admin</h1>
		<p><i>These individuals will be able to add or remove admins as well as provide access to the database service.</i></p>
		<div id='adminListDiv'>
			<?php listUsers($adminList, 'admin'); ?>
		</div>
		<div><input id='adminName' /><button onclick="addUser('admin');">Add New Admin</button></div>
		
		<h1>Access</h1>
		<p><i>These individuals will be able view and use the database service.</i></p>
		<div id='accessListDiv'>
			<?php listUsers($accessList, 'access'); ?>
		</div>
		<div><input id='accessName' /><button onclick="addUser('access');">Give New User DB Access</button></div>
	</body>
	<script type="text/javascript">
		$(window).load(function() {
			$("#saveBox").hide();
		});
	</script>
</html>