<?php
include 'incs/functions.php';
if($_SESSION['vleznal']==true)
{
if($_POST['jq']==1)
{
$return=array();
$return['greshka']=-1;
$data=mydata("inf_user","user",$_SESSION['ime']);
$town=mydata("towns","name",$data['grad']);
switch($_POST['action'])
{

case "tren":
if($data['tren']==0)
{
$hours=$_POST['hours'];
$time=time()+3600*$hours;
$time.=":".$hours;
if($data['zlato']>=$hours*300)
{
update_data("inf_user","user",$_SESSION['ime'],"tren",$time);
update_data("inf_user","user",$_SESSION['ime'],"zlato",$data['zlato']-300);
}
else $return['greshka']="Нямаш достатъчно злато";
}
else $return['greshka']="Вече си на тренировки";
break;


case "upgrade":
if($data['level']<=$town['level'])
{
switch($_POST['stat'])
{
/////////        1="hp"   2=mana

case 1:
if($data['skills']>0)
{
update_data("inf_user","user",$_SESSION['ime'],"maxhp",$data['maxhp']+2);
update_data("inf_user","user",$_SESSION['ime'],"skills",$data['skills']-1);
}
else $return['greshka']="Нямаш достатъчно точки";
break;

case 2:
if($data['skills']>0)
{
update_data("inf_user","user",$_SESSION['ime'],"maxmana",$data['maxmana']+2);
update_data("inf_user","user",$_SESSION['ime'],"skills",$data['skills']-1);
}
else $return['greshka']="Нямаш достатъчно точки";
break;


}
break;
}
}
$return['table_info']=update_table_info($_SESSION['ime']);
//$return['table_info']=update_table_info2($_SESSION['ime']);
//$return['table_inventory']=update_table_inventory($_SESSION['ime']);
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
?>
<table align="center" class="table_logo">
<tr>
<td><span class="logo_name">Лекроция</span></td>
</tr>
</table>
<?php
include "options.php";
echo "<center>";
echo "<div class='content'>";
include 'inventory.php';
switch($data['grad'])
{


case "Lovech":
if($data['level']<6)
{
echo  'Увеличи живота си с 2 (1 точка).<span onclick="upgrade(1)"><img src=resourse/plus.png width=20 height=20></span><br>';
echo 'Увеличи маната си с 2 (1 точка).<span onclick="upgrade(2)"><img src=resourse/plus.png width=20 height=20></span><br>';
echo 'Изпрати героя си на тренировка:<input type="button" value="1 час (300 злато)" onclick=tren(1)><br>';
if($data['tren']!=0)
{
$time=explode(":",$data['tren']);
if($time[0]<time())
{
$trains=0;
for($i=1;$i<=$time[1]*60;$i++)
if(rand(1,10*$data['level'])==rand(1,10*$data['level']))
$trains+=1;
if($trains>0)
{ update_data("inf_user","user",$_SESSION['ime'],"mastery",$data['mastery']+$trains); echo "Тренировката ти беше пълноценна и ти получи ".$trains." точка умение за оръжията"; }
else echo "Тази тренировка не те научи на нищо може би следващия път";
update_data("inf_user","user",$_SESSION['ime'],"tren",0);
}
}
}
else
{
echo "Прекалено висок левел си за да продължиш обучението си тук.";
}
break;



}
echo "</div></center>";
}
}
?>