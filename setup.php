<?php
if(!$_POST){
echo"
<html>
<head>
<meta http-equiv='Content-Language' content='zh-tw'>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<title></title>
</head>
<body>
初始化設定<br />
<form method='post' action='setup.php'>
MySQL資料庫位置: <input type='text' name='mysqlHost' value='127.0.0.1'><br />
MySQL使用者帳號: <input type='text' name='mysqlUser' value='root'><br />
MySQL使用者密碼: <input type='text' name='mysqlPass' value='zxcv1234'><br />
MySQL資料庫名稱: <input type='text' name='mysqlDB' value='bid'><br />
<input type='submit'>
</form>
</body>
</html>
";
}
else{
echo"
<html>
<head>
<meta http-equiv='Content-Language' content='zh-tw'>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<title></title>
</head>
<body>
";
$conID=mysql_pconnect($_POST['mysqlHost'],$_POST['mysqlUser'],$_POST['mysqlPass']);
@mysql_select_db($_POST['mysqlDB'], $conID);
mysql_query('SET names=utf8');
mysql_query('SET character_set_client=utf8');
mysql_query('SET character_set_results=utf8');
mysql_query('SET character_set_connection=utf8');
mysql_query('SET collation_connection=utf8_general_ci');
if(!$conID) die("無法連線: ".mysql_error());
if(mysql_query("CREATE DATABASE ${_POST['mysqlDB']}", $conID)) echo "建立資料庫成功: ${_POST['mysqlDB']}<br />";
else echo "建立資料庫錯誤: ".mysql_error()."<br />";

$mysqlSelect="CREATE TABLE boss(
boss_username VARCHAR(255),
boss_password VARCHAR(255),
boss_moneyyear INT(11),
boss_host LONGTEXT,
boss_spacemax INT(11)
)";
$result=mysql_query($mysqlSelect,$conID)or die("建立表格錯誤: $mysqlSelect<br />");
echo "建立表格成功: boss<br />";

$mysqlSelect="CREATE TABLE style(
store_id INT NOT NULL AUTO_INCREMENT,
PRIMARY KEY(store_id),
style_logo VARCHAR(255),
style_about VARCHAR(255),
style_product VARCHAR(255),
style_menu VARCHAR(255)
)";
$result=mysql_query($mysqlSelect,$conID)or die("建立表格錯誤: $mysqlSelect<br />");
echo "建立表格成功: style<br />";

$mysqlSelect="CREATE TABLE page(
store_id INT NOT NULL AUTO_INCREMENT,
PRIMARY KEY(store_id),
page_about LONGTEXT,
page_product LONGTEXT,
page_menu LONGTEXT,
page_aboutps VARCHAR(255),
page_productps VARCHAR(255),
page_menups VARCHAR(255)
)";
$result=mysql_query($mysqlSelect,$conID)or die("建立表格錯誤: $mysqlSelect<br />");
echo "建立表格成功: page<br />";

$mysqlSelect="CREATE TABLE pay1(
pay1_id INT NOT NULL AUTO_INCREMENT,
PRIMARY KEY(pay1_id),
store_id INT(11),
pay1_state VARCHAR(255),
pay1_atm INT(11),
pay1_time DATETIME,
pay1_money INT(11),
pay1_useyear INT(11)
)";
$result=mysql_query($mysqlSelect,$conID)or die("建立表格錯誤: $mysqlSelect<br />");
echo "建立表格成功: pay1<br />";

$mysqlSelect="CREATE TABLE store(
store_id INT NOT NULL AUTO_INCREMENT,
PRIMARY KEY(store_id),
store_time DATETIME,
store_useyear INT(11),
store_url LONGTEXT,
store_name VARCHAR(255),
store_about LONGTEXT,
store_username VARCHAR(255),
store_password VARCHAR(255),
store_phone VARCHAR(255),
store_messenger LONGTEXT,
store_map LONGTEXT
)";
$result=mysql_query($mysqlSelect,$conID)or die("建立表格錯誤: $mysqlSelect<br />");
echo "建立表格成功: store<br />";


$mysqlSelect="CREATE TABLE store2(
store_id INT NOT NULL AUTO_INCREMENT,
PRIMARY KEY(store_id),
store2_opentime LONGTEXT,
store2_notice LONGTEXT,
store2_payment LONGTEXT,
store2_flow LONGTEXT,
store2_postage LONGTEXT,
store2_refund LONGTEXT,
store2_order1 LONGTEXT,
store2_order2 LONGTEXT,
store2_website LONGTEXT,
store2_blog LONGTEXT,
store2_facebook LONGTEXT,
store2_epaper LONGTEXT
)";
$result=mysql_query($mysqlSelect,$conID)or die("建立表格錯誤: $mysqlSelect<br />");
echo "建立表格成功: store2<br />";




$mysqlSelect="CREATE TABLE bbs1(
bbs1_id INT NOT NULL AUTO_INCREMENT,
PRIMARY KEY(bbs1_id),
bbs1_time DATETIME,
bbs1_writer INT(11),
bbs1_title LONGTEXT,
bbs1_content LONGTEXT

)";

$result=mysql_query($mysqlSelect,$conID)or die("建立表格錯誤: $mysqlSelect<br />");
echo "建立表格成功: bbs1<br />";

$mysqlSelect="CREATE TABLE bbs2(
bbs2_id INT NOT NULL AUTO_INCREMENT,
PRIMARY KEY(bbs2_id),
bbs1_id  INT(11),
bbs2_time DATETIME,
bbs2_writer INT(11),
bbs2_content LONGTEXT

)";

$result=mysql_query($mysqlSelect,$conID)or die("建立表格錯誤: $mysqlSelect<br />");
echo "建立表格成功: bbs2<br />";



$mysqlSelect="CREATE TABLE template(
template_id INT NOT NULL AUTO_INCREMENT,
PRIMARY KEY(template_id),
template_time DATETIME,
template_name VARCHAR(255),
template_about LONGTEXT,
template_color VARCHAR(255),
template_usermax VARCHAR(255)
)";
$result=mysql_query($mysqlSelect,$conID)or die("建立表格錯誤: $mysqlSelect<br />");
echo "建立表格成功: template<br />";

$mysqlSelect="CREATE TABLE template_tag(
template_id INT(11),
template_tag_name VARCHAR(255)
)";
$result=mysql_query($mysqlSelect,$conID)or die($mysqlSelect);
echo "建立表格成功: template_tag<br />";

$mysqlSelect="INSERT INTO boss(boss_username, boss_password, boss_moneyyear, boss_moneypoint, boss_host, boss_spacemax) VALUES ('test', 'test', '980', '280', 'http://${_SERVER['HTTP_HOST']}/bid', '10');";
$result=mysql_query($mysqlSelect,$conID)or die($mysqlSelect);
echo "寫入表格成功: boss<br />";

$mysqlSelect="INSERT INTO template(template_id,template_time,template_name,template_about,template_color,template_usermax) VALUES ('1', '2011-01-01', '基礎亮黑', '無。', '#CCCCCC', '100');";
$result=mysql_query($mysqlSelect,$conID)or die($mysqlSelect);
$mysqlSelect="INSERT INTO template(template_id,template_time,template_name,template_about,template_color,template_usermax) VALUES ('2', '2011-01-01', '經典黑白', '無。', '#E0E0E0', '100');";
$result=mysql_query($mysqlSelect,$conID)or die($mysqlSelect);
$mysqlSelect="INSERT INTO template(template_id,template_time,template_name,template_about,template_color,template_usermax) VALUES ('3', '2011-01-01', '手繪咖褐', '無。', '#F8EBD8', '100');";
$result=mysql_query($mysqlSelect,$conID)or die($mysqlSelect);
$mysqlSelect="INSERT INTO template(template_id,template_time,template_name,template_about,template_color,template_usermax) VALUES ('4', '2011-01-01', '華麗桃紅', '無。', '#FFD2F0', '100');";
$result=mysql_query($mysqlSelect,$conID)or die($mysqlSelect);
$mysqlSelect="INSERT INTO template(template_id,template_time,template_name,template_about,template_color,template_usermax) VALUES ('5', '2011-01-01', '時尚白銀', '無。', '#EEEEEE', '100');";
$result=mysql_query($mysqlSelect,$conID)or die($mysqlSelect);
echo "寫入表格成功: template<br />";


$main="<?php
\$mysqlHost=\"${_POST['mysqlHost']}\";
\$mysqlUser=\"${_POST['mysqlUser']}\";
\$mysqlPass=\"${_POST['mysqlPass']}\";
\$mysqlDB=\"${_POST['mysqlDB']}\";
\$conID=mysql_pconnect(\$mysqlHost,\$mysqlUser,\$mysqlPass);
@mysql_select_db(\$mysqlDB,\$conID);
mysql_query(\"SET NAMES 'utf8'\");
mysql_query(\"SET CHARACTER_SET_CLIENT=utf8\"); 
mysql_query(\"SET CHARACTER_SET_RESULTS=utf8\"); 
\$nowTime=date(\"Y-n-j G:i:s\");
\$host=isset(\$_SERVER['HTTP_X_FORWARDED_HOST'])?\$_SERVER['HTTP_X_FORWARDED_HOST']:(isset(\$_SERVER['HTTP_HOST'])?\$_SERVER['HTTP_HOST']:'');
?>";
$datpath="info.php";
$f=fopen($datpath,"w+");
fwrite($f,$main); 
fclose($f);
echo "建立檔案成功: info.php<br />";

mkdir("store", 0777)or die("無法建立資料夾: store<br />");
echo "建立資料夾成功: store<br />";

mkdir("template", 0777)or die("無法建立資料夾: template<br />");
echo "建立資料夾成功: template<br />";

echo "完成，<a href='index.php'>按我開始使用</a></body></html>";
}
?>



