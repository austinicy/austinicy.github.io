<?php 
header('Content-Type: application/json');
$country = $_REQUEST['country'];

// Connecting, selecting database
$link = mysql_connect('localhost', 'access', 'access')
    or die('Could not connect: ' . mysql_error());
	//echo 'Connected successfully';
	mysql_select_db('workforce') or die('Could not select database');

    $query1 = "select * from workforce where countryName = '$country';";
	$result1 = mysql_query($query1) or die('Query failed: ' . mysql_error());

	$mortality = array();
	while($r = mysql_fetch_assoc($result1)) {
    	$mortality[] = $r;
	}

	//echo "{ 'Cancer':";
	echo json_encode($mortality);

    /*$query2 = "select * from cutaneous where countryName = '$country';";
	$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());

	$cutaneous = array();
	while($r = mysql_fetch_assoc($result2)) {
    	$cutaneous[] = $r;
	}
	echo ",'Cutaneous':";
	echo json_encode($cutaneous);
	echo "}";*/

    /*$mortality=array();
    while($row=$mysql_fetch_object($query)){
    $mortality[] = $row;
    unset($row);
     }
    echo json_encode($mortality);

echo "<table>\n";
while ($line = mysql_fetch_array($rows, MYSQL_ASSOC)) {
    echo "\t<tr>\n";
    foreach ($line as $col_value) {
        echo "\t\t<td>$col_value</td>\n";
    }
    echo "\t</tr>\n";
}
echo "</table>\n";*/

    //mysql_close($server); ?>

