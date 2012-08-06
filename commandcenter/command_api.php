<?php 

if (isset($_GET['save'])) {
	saveUsers("../.htaccess", $_POST['access']);
	saveUsers(".htaccess", $_POST['admin']);
	$json = array();
	$json['successBool']=1;
	$json['result']="<span style='color:green'>Users saved successfully</span>";
	echo json_encode($json);
}
function saveUsers($filename, $nameArr) {
	$file = fopen($filename, "w") or exit("");
	fwrite($file, "AuthType Cas\n");
	fwrite($file, "Require user ");
	foreach($nameArr as $name) {
		fwrite($file, "$name ");
	}
	fclose($file);
}

?>