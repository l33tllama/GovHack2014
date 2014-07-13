<!DOCTYPE html/>
<?php
	echo $_SERVER['PHP_SELF'] . "<br/>";
	echo $_SERVER['DOCUMENT_ROOT'] . "<br/>";	
	echo $_SERVER['REMOTE_ADDR'];
	
	echo "<br/>";
	echo __FILE__;
	
	echo $_SERVER['REMOTE_ADDR'];
	echo "THING: ";
	
	echo $_SERVER['REMOTE_ADDR'] . str_replace(basename($_SERVER['PHP_SELF']), "", $_SERVER['PHP_SELF']);
	echo "<br/>";
	echo basename($_SERVER['PHP_SELF']);
	
?>
<script>
	//console.log( DOCUMENT_ROOT);
	console.log(window.location);
	console.log(window.name);
	function getPath(){
		var windowLocS = window.location.pathname.split("/");
		var winFileName = windowLocS[windowLocS.length-1];
		var basePaths = window.location.origin + "/";
		for (var i = 0; i < windowLocS.length-1; i++){
			basePaths += windowLocS[i];
		}
		var docRot = windowLocS[windowLocS.length-2];
		console.log(basePaths + "/" + winFileName);
		return basePaths + "/";
	}
	
	var basePath = getPath();
	console.log(basePath);
</script>