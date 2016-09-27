<?php

if (!@$_POST['submit']) {
	$Code = <<<Code
<html>
<head>
<meta charset="UTF-8">
<title>MySQL数据库字典生成工具</title>
<!-- 新 Bootstrap 核心 CSS 文件 -->
<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.0/css/bootstrap.min.css">
<!-- 可选的Bootstrap主题文件（一般不用引入） -->
<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.0/css/bootstrap-theme.min.css">
<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="http://cdn.bootcss.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
</head>
<body>
<div class="panel panel-default" style="margin-left:auto;margin-right: auto;margin-top: 5%;width:30%;">
	<div class="panel-heading">
    <h3 class="panel-title" style="text-align:center">MySQL数据库字典生成工具</h3>
  </div>
  <div class="panel-body">
    <div>
	<form action="" method="POST" role="form">
		<div class="form-group">
		<label for="exampleInput">数据库地址</label>
		<input type="text" class="form-control" id="exampleInput" placeholder="数据库地址" name="host" value="localhost">
		 </div>

		<div class="form-group">
		<label for="exampleInput">用户名</label>
		<input type="text" class="form-control" id="exampleInput" placeholder="数据库用户" name="user" value="root">
		 </div>
		<div class="form-group">
		<label for="exampleInput">密码</label>
		<input type="text" class="form-control" id="exampleInput" placeholder="数据库密码" name="pwd" >
		 </div>
		<div class="form-group">
		<label for="exampleInput">数据库</label>
		<input type="text" class="form-control" id="exampleInput" placeholder="数据库" name="db" value='test'>
		 </div>
		<div class="form-group">
		<input class="btn btn-default" type="submit" name="submit" value="生成字典">

		 </div>
	</form>
	</div>
  </div>
  <div class="panel-footer">By Sunny</div>
  </div>

</body>
</html>
Code;
	echo $Code;
	exit;
}
//配置数据库
foreach ($_POST as $k => $v) {
	$_POST[$k] = trim($v);
}
$dbserver = $_POST['host'];
$dbusername = $_POST['user']; //数据库用户名
$dbpassword = $_POST['pwd']; //密码
$database = $_POST['db']; //数据库名

if (empty($dbserver) || empty($dbusername) || empty($database)) {
	die("数据库地址、数据库用户名、数据库不能为空 <a href='' onclick='history.go(-1);'>返回</a>");
}

$title = "[" . $_POST['db'] . ']数据库字典';
//下面是连接数据库
$mysql_conn = @mysql_connect("$dbserver", "$dbusername", "$dbpassword") or die("Mysql connect is error.");
mysql_select_db($database, $mysql_conn);
mysql_query('SET NAMES utf8', $mysql_conn);
$table_result = mysql_query('show tables', $mysql_conn);
//取得所有的表名
while ($row = mysql_fetch_array($table_result)) {
	$tables[]['TABLE_NAME'] = $row[0];
}
//循环取得所有表的备注
foreach ($tables AS $k => $v) {
	$sql = 'SELECT * FROM ';
	$sql .= 'INFORMATION_SCHEMA.TABLES ';
	$sql .= 'WHERE ';
	$sql .= "table_name = '{$v['TABLE_NAME']}'  AND table_schema = '{$database}'";
	$table_result = mysql_query($sql, $mysql_conn);
	while ($t = mysql_fetch_array($table_result)) {
		$tables[$k]['TABLE_COMMENT'] = $t['TABLE_COMMENT'];
	}
	$sql = 'SELECT * FROM ';
	$sql .= 'INFORMATION_SCHEMA.COLUMNS ';
	$sql .= 'WHERE ';
	$sql .= "table_name = '{$v['TABLE_NAME']}' AND table_schema = '{$database}'";
	$fields = array();
	$field_result = mysql_query($sql, $mysql_conn);
	while ($t = mysql_fetch_array($field_result)) {
		$fields[] = $t;
	}
	$tables[$k]['COLUMN'] = $fields;}
mysql_close($mysql_conn);

$html = '';
//循环所有表拼接字符串
foreach ($tables as $k => $v) {
	$html .= '<h2 style="text-align:center">' . $v['TABLE_COMMENT'] . ' </h2>';
	$html .= '<table  class="table table-bordered" style="width:80%;margin-left: auto;
margin-right: auto;text-align:center">';
	$html .= '<caption>' . $v['TABLE_NAME'] . '</caption>';
	$html .= '<tbody><tr>
	 <th style="text-align:center">字段名</th>
	 <th style="text-align:center">数据类型</th>
	 <th style="text-align:center">默认值</th>
     <th style="text-align:center">允许非空</th>
     <th style="text-align:center">自动递增</th><th>备注</th></tr>';
	$html .= '';

	foreach ($v['COLUMN'] as $f) {
		$html .= '<tr><td class="c1">' . $f['COLUMN_NAME'] . '</td>';
		$html .= '<td>' . $f['COLUMN_TYPE'] . '</td>';
		$html .= '<td> ' . $f['COLUMN_DEFAULT'] . '</td>';
		$html .= '<td > ' . $f['IS_NULLABLE'] . '</td>';
		$html .= '<td >' . ($f['EXTRA'] == 'auto_increment' ? '是' : ' ') . '</td>';
		$html .= '<td > ' . $f['COLUMN_COMMENT'] . '</td>';
		$html .= '</tr>';
	}
	$html .= '</tbody></table></p>';
}
//打印结果
echo '<html>
 <head>
 <!-- 新 Bootstrap 核心 CSS 文件 -->
<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.0/css/bootstrap.min.css">
<!-- 可选的Bootstrap主题文件（一般不用引入） -->
<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.0/css/bootstrap-theme.min.css">
<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="http://cdn.bootcss.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
</head>
 <title>' . $title . '</title>
 <meta charset="UTF-8">
 </head>
 <body>';
echo '<h1 style="text-align:center;">' . $title . '</h1>';
echo $html;
echo '</body></html>';
?>
