<?php
include 'incs/functions.php';
if($_SESSION['vleznal']==true)
{
if($_POST['jq']==1)
{
$return=array();
$return['info']="";


switch($_POST['action'])
{


case "travel":
$data2=mydata("inf_user2","user",$_SESSION['ime']);
$town=$_POST['townid'];
$towndata=mydata("towns","id",$town);
$townname=$towndata['name'];
$timetravel=$_POST['timetravel']*60+time();
$travel=$townname.";".$timetravel;
if($data2['travel']=="")
{
update_data("inf_user","user",$_SESSION['ime'],"grad","");
update_data("inf_user2","user",$_SESSION['ime'],"travel",$travel);
$return['info']="Започнахте път към ".$towndata['ime'];
}
else $return['info']="В момента сте на път";
break;
}


echo json_encode($return);
}
else
{
include 'wrongdir.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<title>Lecrozya</title>
<script src="jquery.js" type="text/javascript"></script>
<script src="jquery-ui.js" type="text/javascript"></script>
<script src="all_jquery.js" type="text/javascript"></script>
<script text="text/javascript">
$(document).ready(function() {
setTimeout('updatechat()',300);
updateoffers();
$("#teksta").keydown(function(event)
{
if(event.keyCode == 13)
{
$("#tbut").click();
}
});
});
</script>
</head>
<body>
<?php
echo "<link type='text/css' rel='stylesheet' href='style.css'>";
echo '<table align="center" class="table_logo">
<tr>	
<td><span class="logo_name">Лекроция</span></td>
</tr>
</table>';
include 'options.php';
$data=mydata("inf_user","user",$_SESSION['ime']);
$data2=mydata("inf_user2","user",$_SESSION['ime']);
$travel=$data2['travel'];
if($travel!="")
{
$travel=explode(";",$data2['travel']);
$town=mydata("towns","name",$travel[0]);
if($travel[1]<time())
{
echo "<font color=yellow>Добре дошли в ".$town['ime'].".</font>";
update_data("inf_user","user",$_SESSION['ime'],"grad",$travel[0]);
update_data("inf_user2","user",$_SESSION['ime'],"travel","");
$data=mydata("inf_user","user",$_SESSION['ime']);
$data2=mydata("inf_user2","user",$_SESSION['ime']);
}
else
echo "<font color=yellow>Ще пристигнете в ".$town['ime']." след ".abs(floor((time()-$travel[1])/60))." минути.</font>";
}
echo "<div align='center' class='patuvane'>Наеми каравана и патувай до края на света</div>";
echo "<table class='townlist'>
<th>Град/Местност</th>
<th>Пеша</th>
<th>Магаре</th>
<th>Кон</th>
<th>Каруца</th>
<th>Карета</th>
<th>Грифон</th>";
$sql=mysql_query("SELECT * FROM `towns` ");
$i=0;
while($town=mysql_fetch_array($sql))
{
if($town['name']!=$data['grad'])
{
$townid=$town['id'];
//$mytowninfo=mydata("towns","ime",$data['grad']);
$position=1;
$timetravel=abs($town['id']-$position);
if($i%2==0)
echo "<tr bgcolor=#DCD74A>";
else
echo "<tr bgcolor=#CB8938>";
echo "<td>".$town['ime']."</td>
<td>".$timetravel." минути
<br>
<input type='button' onclick='travel(".$townid.",".$timetravel.")' value='Пътувай'>
</td>
</tr>";
}
}
?>
</body>
</html>
<?php
}
}
?>