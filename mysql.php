<?php


if(isset($_GET['act'])){
	$dbname='church';
	$conID=mysql_pconnect('localhost', 'root', '');
	//$dbname='mingfang_church'; //By Server	
	//$conID=mysql_pconnect('localhost', 'mingfang_church', 'zxcv1234')or die('Error: '.mysql_error().'<br />'); //By Server
	@mysql_select_db($dbname, $conID);
	if($_GET['act'] == 'createDatabase'){
		@mysql_query('Set names=utf8');
		@mysql_query('Set character_set_client=utf8')or die('Error: '.mysql_error().'<br />');
		@mysql_query('Set character_set_results=utf8')or die('Error: '.mysql_error().'<br />');
		@mysql_query('Set character_set_connection=utf8')or die('Error: '.mysql_error().'<br />');
		@mysql_query('Set collation_connection=utf8_general_ci')or die('Error: '.mysql_error().'<br />');
		echo '<h2>Database</h2>';
		@mysql_query('Drop Database '.$dbname, $conID);
		@mysql_query('Create Database '.$dbname, $conID);
		@mysql_select_db($dbname, $conID);
		echo 'Okay: '.$dbname.'<br />';
		echo '<h2>Table</h2>';
		$sql='Create Table boss(boss_id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(boss_id), boss_name VARCHAR(255), boss_password VARCHAR(255))';
		$result=@mysql_query($sql, $conID);
		$sql='Create Table user(user_id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(user_id), chmodType_id INT(11), user_nickname VARCHAR(255), user_name VARCHAR(255), user_password VARCHAR(255))';
		$result=@mysql_query($sql, $conID);
		$sql='Create Table item(item_id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(item_id), itemType_id INT(11), item_name INT(11))';
		$result=@mysql_query($sql, $conID);
		$sql='Create Table itemType(itemType_id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(itemType_id), itemType_name VARCHAR(255))';
		$result=@mysql_query($sql, $conID);
		$sql='Create Table chmodType(chmodType_id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(chmodType_id), chmodType_name VARCHAR(255))';		
		$result=@mysql_query($sql, $conID);
		$sql='Create Table chmod(chmod_id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(chmod_id), chmodType_id INT(11), item_id INT(11), chmod_write BOOLEAN, chmod_read BOOLEAN, chmod_execute BOOLEAN)';		
		$result=@mysql_query($sql, $conID);
		echo 'Okay: Table<br />';
		echo '<h2>Write</h2>';
		$data=array(
			array('boss_id'=>'1', 'boss_name'=>'admin', 'boss_password'=>'admin'),						
		);
		for($i=0; $i < count($data); $i++){
			$sql='Insert Into boss(boss_id, boss_name, boss_password) Values ('.$data[$i]['boss_id'].', "'.$data[$i]['boss_name'].'", "'.$data[$i]['boss_password'].'");';
			$result=@mysql_query($sql, $conID);
		}
		echo 'MySQL quite ready now.<br />';
	} else{
		@mysql_select_db($dbname, $conID);
		if(isset($_GET['where'])) $where='where '.$_GET['where'];
		else $where='';
		if(isset($_GET['table'])){
			if($_GET['act'] == 'read'){
				$where='';
				$result=mysql_query('Select * From '.$_GET['table'].' '.$where, $conID)or die('Error: '.mysql_error().'<br />');
				while($row=@mysql_fetch_assoc($result)){
					$rows[]=$row;
				}
				@mysql_free_result($result);
				header('Content-Type: application/json; charset=utf-8'); 
				echo json_encode($rows);
			} else if($_GET['act'] == 'create'){
				$data=json_decode(file_get_contents('php://input'));
				$sql='Select * From '.$_GET['table'].' '.$where;
				$result=@mysql_query($sql, $conID)or die('Error: '.$sql.'<br />');
				// Get Column //
				for($i=0; $i < @mysql_num_fields($result); $i++){
					$meta=@mysql_fetch_field($result, $i);
					$columnArr[$i]=$meta->name;
					if($meta->primary_key){
						$primary_key=$meta->name;
						break;
					}
				}
				@mysql_free_result($result);
				$sql='Select Max('.$primary_key.') as max From '.$_GET['table'].' '.$where;
				$result=@mysql_query($sql, $conID)or die('Error: '.$sql.'<br />');
				$rows=@mysql_fetch_row($result);
				@mysql_free_result($result);
				$max=$rows[0]+1;
				$sql='Select * From '.$_GET['table'].' '.$where;
				$result=@mysql_query($sql, $conID)or die('Error: '.$sql.'<br />');
				// Get Column //
				for($i=0; $i < @mysql_num_fields($result); $i++){
					$meta=@mysql_fetch_field($result, $i);
					$columnArr[$i]=$meta->name;
					if($meta->primary_key){
						$valueArr[$i]=$max;
					}
					else{
						if($meta->type == 'int'){
							$valueArr[$i]=$data->{$meta->name};
						}
						else{
							$valueArr[$i]='"'.$data->{$meta->name}.'"';
						}
					}
				}
				@mysql_free_result($result);
				$column=implode(', ', $columnArr);
				$value=implode(', ', $valueArr);
				$sql='Insert Into '.$_GET['table'].'('.$column.') Values ('.$value.');';
				$result=@mysql_query($sql, $conID)or die('Error: '.$sql.'<br />');
			} else if($_GET['act'] == 'update'){
				$data=json_decode(file_get_contents('php://input'));
				$sql='Select * From '.$_GET['table'].' '.$where;
				$result=@mysql_query($sql, $conID)or die('Error: '.$sql.'<br />');
				// Get Column //
				for($i=0; $i < @mysql_num_fields($result); $i++){
					$meta=@mysql_fetch_field($result, $i);
					$columnArr[$i]=$meta->name;
					if($meta->primary_key){
						$primary_key=$meta->name.'='.$data->{$meta->name};
					}
					else{
						if($meta->type == 'int'){
							$setArr[$i]=$meta->name.'='.$data->{$meta->name};
						}
						else{
							$setArr[$i]=$meta->name.'="'.$data->{$meta->name}.'"';
						}
					}
				}
				@mysql_free_result($result);
				$set=implode(', ', $setArr);
				$sql='Update '.$_GET['table'].' SET '.$set.' '.$where;
				$result=@mysql_query($sql, $conID)or die('Error: '.$sql.'<br />');
				@mysql_free_result($result);
			} else if($_GET['act'] == 'delete'){
				$data=json_decode(file_get_contents('php://input'));
				$sql='Delete From '.$_GET['table'].' '.$where;
				$result=@mysql_query($sql, $conID)or die('Error: '.$sql.'<br />');
			}
		}
	} 
}
?>