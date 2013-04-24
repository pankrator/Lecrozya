<?php
include("incs/functions.php");
if($_SESSION['vleznal']==true)
{
if($_POST['jq']==1)
{
$return=array();
$return['magicinfo']="";

switch($_POST['action'])
{
case "magicinfo":
if(isset($_POST['magic']))
{
$magic=$_POST['magic'];
$sql = mysql_query("SELECT * FROM `inf_magics` WHERE `name` = '$magic' ");
$res=mysql_fetch_array($sql);
$ime=explode("|",$magic);
$return['magicinfo']="<font color=darkogrange>".$ime[0].":<br>".$res['desc']."</font>";
}
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
$("#teksta").keydown(function(event)
{
if(event.keyCode == 13)
{
$("#tbut").click();
}
});
});
</script>
<link type='text/css' rel='stylesheet' href='style.css'>
</head>
<body>
<?php
$ime=$_SESSION['ime'];
$data=mydata("inf_user","user",$ime);
$data2=mydata("inf_user2","user",$ime);
?>
<table align="center" class="table_logo">
<tr>
<td><span class="logo_name">Лекроция</span></td>
</tr>
</table>
<?php
include 'inventory.php';
if($data['fight']!=0)
{
$mdata=mydata("inf_monster","user",$ime);
echo '<div class="action">
<input type="button" value="Атака" onclick=doit("normal")>
<br>';
$magics_level=explode(";",$data2['magics']);
$magics=explode(",",$magics_level[0]);
echo '<select  class="magic" onchange="magic_info()" name="magic">';
for($k=0;$k<=count($magics)-1;$k++)
{
$id=$magics[$k];
$sql = mysql_query("SELECT * FROM `inf_magics` WHERE `ID` = $id ");
$rez=mysql_fetch_array($sql);
$valname=$rez['name'];
$name=explode("|",$valname);
echo '<option value="'.$valname.'">'.$name[0].'</option>';
}
echo '
</select>
<span class="magic_info"></span>
<br>
<input type="button" value="Магия" onclick=doit("magic")>
</div><br>';
echo '<div class="all">';
echo '<div class="curses"></div>';
echo "<div style='position:absolute;top:300px;left:100px;'>";
echo "<font color=white>".$_SESSION['ime']."</font>";
echo "</div>";
echo "<div style='position:absolute;top:300px;left:600px;' >";
$d=($mdata['m_health']*100)/$mdata['m_maxhealth'];
echo "<font color=white>".$mdata['m_name']."</font>";
echo '<div style="color:red" class="m_hpbar">'.$mdata["m_health"].'/'.$mdata["m_maxhealth"].'<div style="width:100px;height:10px;border:2px solid white;"><div style="width:'.$d.'px;height:10px;background-color:red;"></div></div></div>';
echo "</div>";
//echo '<div class="loading"></div>';
echo '<div class="report">'.$mdata["report"].'</div>';
echo "</div>";///Close all tag
echo '<div class="dmg" style="position:absolute;display:none"></div>';
}
else
{
if($data['hodove']>0 and $data['hp']>0 && $data['tren']==0)
{
get_fight();
$data=mydata("inf_user","user",$ime);
$mdata=mydata("inf_monster","user",$ime);
echo '<div class="action">
<input type="button" value="Атака" onclick=doit("normal")>
<br>';
$magics_level=explode(";",$data2['magics']);
$magics=explode(",",$magics_level[0]);
echo '<select  class="magic" onchange="magic_info()" name="magic">';
for($k=0;$k<=count($magics)-1;$k++)
{
$id=$magics[$k];
$sql = mysql_query("SELECT * FROM `inf_magics` WHERE `ID` = $id ");
$rez=mysql_fetch_array($sql);
$valname=$rez['name'];
$name=explode("|",$valname);
echo '<option value="'.$valname.'">'.$name[0].'</option>';
}
echo '
</select>
<span class="magic_info"></span>
<br>
<input type="button" value="Магия" onclick=doit("magic")>
</div><br>';
echo '<div class="all">';
echo '<div class="curses"></div>';
echo "<div style='position:absolute;top:300px;left:100px;'>";
echo "<font color=white>".$_SESSION['ime']."</font>";
echo "</div>";
echo "<div style='position:absolute;top:300px;left:600px;' >";
$d=($mdata['m_health']*100)/$mdata['m_maxhealth'];
echo "<font color=white>".$mdata['m_name']."</font>";
echo '<div style="color:red" class="m_hpbar">'.$mdata["m_health"].'/'.$mdata["m_maxhealth"].'<div style="width:100px;height:10px;border:2px solid white;"><div style="width:'.$d.'px;height:10px;background-color:red;"></div></div></div>';
echo "</div>";
//echo '<div class="loading"></div>';
echo '<div class="report">'.$mdata["report"].'</div>';
echo "</div>";///Close all tag
echo '<div class="dmg" style="position:absolute;display:none"></div>';
}
else if($data['hodove']<1) echo "Нямаш достатъчно ходове за това действие ";
else if($data['hp']<1) echo "Неможеш да се биеш щом си мъртав ";
else if($data['tren']!=0) echo "В момента си на тренировка ";
//echo '<a href="index.php">Назад</a><br><br>';
}
?>
</body>
</html>
<?php
}
}
?>