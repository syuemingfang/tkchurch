<?php
/*
$mysqlHost='localhost';
$mysqlUser='mingfang_church';
$mysqlPass='zxcv1234';
$mysqlDB='mingfang_church';
*/
$mysqlHost='localhost';
$mysqlUser='root';
$mysqlPass='';
$mysqlDB='church';
if(isset($_GET['act'])){
	$conID=mysql_pconnect($mysqlHost, $mysqlUser, $mysqlPass)or die('Error: '.mysql_error().'<br />');
	@mysql_select_db($mysqlDB, $conID);
	if($_GET['act'] == 'createDatabase'){
		@mysql_query('Set names=utf8');
		@mysql_query('Set character_set_client=utf8')or die('Error: '.mysql_error().'<br />');
		@mysql_query('Set character_set_results=utf8')or die('Error: '.mysql_error().'<br />');
		@mysql_query('Set character_set_connection=utf8')or die('Error: '.mysql_error().'<br />');
		@mysql_query('Set collation_connection=utf8_general_ci')or die('Error: '.mysql_error().'<br />');
		echo '<h2>Database</h2>';
		@mysql_query('Drop Database '.$mysqlDB, $conID);
		@mysql_query('Create Database '.$mysqlDB, $conID);
		@mysql_select_db($mysqlDB, $conID);
		echo 'Okay: '.$mysqlDB.'<br />';
		echo '<h2>Table</h2>';
		$query='Create Table boss(boss_id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(boss_id), boss_name VARCHAR(255), boss_password VARCHAR(255))';
		$result=@mysql_query($query, $conID);
		$query='Create Table user(user_id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(user_id), chmodType_id INT(11), user_nickname VARCHAR(255), user_name VARCHAR(255), user_password VARCHAR(255))';
		$result=@mysql_query($query, $conID);
		$query='Create Table item(item_id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(item_id), itemType_id INT(11), item_name INT(11))';
		$result=@mysql_query($query, $conID);
		$query='Create Table itemType(itemType_id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(itemType_id), itemType_name VARCHAR(255))';
		$result=@mysql_query($query, $conID);
		$query='Create Table chmodType(chmodType_id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(chmodType_id), chmodType_name VARCHAR(255))';		
		$result=@mysql_query($query, $conID);
		$query='Create Table chmod(chmod_id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(chmod_id), chmodType_id INT(11), item_id INT(11), chmod_write BOOLEAN, chmod_read BOOLEAN, chmod_execute BOOLEAN)';		
		$result=@mysql_query($query, $conID);
		echo 'Okay: Table<br />';
		echo '<h2>Write</h2>';
		$data=array(
			array('boss_id'=>'1', 'boss_name'=>'admin', 'boss_password'=>'admin'),						
		);
		for($i=0; $i < count($data); $i++){
			$query='Insert Into boss(boss_id, boss_name, boss_password) Values ('.$data[$i]['boss_id'].', "'.$data[$i]['boss_name'].'", "'.$data[$i]['boss_password'].'");';
			$result=@mysql_query($query, $conID);
		}
		echo 'MySQL quite ready now.<br />';
	} else{
		$conID=mysql_pconnect($mysqlHost, $mysqlUser, $mysqlPass)or die('Error: '.mysql_error().'<br />');
		@mysql_select_db($mysqlDB, $conID);
		if(isset($_GET['zone'])){
			if($_GET['act'] == 'view'){
				$result=mysql_query("Select * From boss", $conID)or die('Error: '.mysql_error().'<br />');
				while(list($boss_id, $boss_name, $boss_password)=@mysql_fetch_row($result)){
					echo $boss_name.'<br />';
				}
			} else if($_GET['act'] == 'read'){
				if(isset($_GET[$_GET['zone'].'_id'])){
					$result=mysql_query('Select * From '.$_GET['zone'].' Where '.$_GET['zone'].'_id='.$_GET[$_GET['zone'].'_id'], $conID)or die('Error: '.mysql_error().'<br />');			
				}
				else{
					$result=mysql_query('Select * From '.$_GET['zone'], $conID)or die('Error: '.mysql_error().'<br />');
				}
				while($row=@mysql_fetch_assoc($result)){
					$rows[]=$row;
				}
				header('Content-Type: application/json; charset=utf-8'); 
				echo json_encode($rows);
			} else if($_GET['act'] == 'create'){
				$data=json_decode(file_get_contents('php://input'));
				$query='Select Max('.$_GET['zone'].'_id) as newId From boss';
				$result=@mysql_query($query, $conID)or die('Error: '.$query.'<br />');
				$rows=@mysql_fetch_row($result);
				$newId=$rows[0]+1;
				$query='Insert Into boss(boss_id, boss_name, boss_password) Values ('.$newId.', "'.$data->{'boss_name'}.'", "'.$data->{'boss_password'}.'");';
				$result=@mysql_query($query, $conID)or die('Error: '.$query.'<br />');
			} else if($_GET['act'] == 'update'){
				$data=json_decode(file_get_contents('php://input'));
				$query='Update boss SET boss_id='.$data->{'boss_id'}.', boss_name="'.$data->{'boss_name'}.'", boss_password="'.$data->{'boss_password'}.'" Where boss_id='.$data->{'boss_id'};
				$result=@mysql_query($query, $conID)or die('Error: '.$query.'<br />');
			} else if($_GET['act'] == 'delete'){
				$data=json_decode(file_get_contents('php://input'));
				$query='Delete From boss Where boss_id='.$data->{'boss_id'};
				$result=@mysql_query($query, $conID)or die('Error: '.$query.'<br />');
			}
		}
	} 
} else{
	header('Content-Type:text/html; charset=utf-8');
	echo'
	<ol>
		<li><a href="mysql.php?act=createDatabase">Create Database</a></li>
		<li><a href="mysql.php?act=view">View Data</a></li></li>
		<li><a href="mysql.php?act=json">View JSON</a></li>
	</ol>
	';
}
?>