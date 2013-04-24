<?php
include 'incs/functions.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<title>Lecrozya</title>
<script src="jquery.js" type="text/javascript"></script>
<script src="jquery-ui.js" type="text/javascript"></script>
<script src="all_jquery.js" type="text/javascript"></script>
</head>
<body>
<?php
echo "<link type='text/css' rel='stylesheet' href='style.css'>";
echo '<table align="center" class="table_logo">
<tr>	
<td><span class="logo_name">Лекроция</span></td>
</tr>
</table>';
if($_SESSION['vleznal']==true)
{
echo "Добре дошли в магазина на Джорджо (изберете и закупете) ";
echo "<center>";

$sql=mysql_query("SELECT * FROM `inf_items` WHERE `kachestvo`='normal' ");
echo "<table><tr>";
while($item=mysql_fetch_array($sql))
{
$pic=$item['pic'];
echo "<td><img src=".$pic." width=32 height=32></td>";
}
echo "</tr></table>";
echo "</center>";
}
else
echo "Nqmate dostap do tazi stranica";
?>
</body>
</html>