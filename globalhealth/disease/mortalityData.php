<?php 
header('Content-Type: application/json');
$country = $_REQUEST['country'];

// Connecting, selecting database
$link = mysql_connect('localhost', 'access', 'access')
    or die('Could not connect: ' . mysql_error());
	//echo 'Connected successfully';
	mysql_select_db('disease') or die('Could not select database');

    $query = "select * from mortality where countryName = '$country';";
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());

	$mortality = array();
	while($r = mysql_fetch_assoc($result)) {
    	$mortality[] = $r;
	}


	echo json_encode($mortality);


    //mysql_close($server); ?>
