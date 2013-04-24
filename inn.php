<?php
include 'incs/functions.php';
if($_SESSION['vleznal']==true)
{
if($_POST['jq']==1)
{
$return=array();
$return['info']="";
$data=mydata("inf_user","user",$_SESSION['ime']);
$data2=mydata("inf_user2","user",$_SESSION['ime']);


switch($_POST['action'])
{


case "updateoffers":
$return['show']=0;
$duels=explode(",",$data2['duel_with']);
$return['offers']="<table align='right' style='background-color:rgb(99,109,63);color:white;border:4px inset green;'  class='action_info'>";
if(count($duels)>0 && $duels[0]!="")
{
$return['show']=1;
$return['offers'].="<tr><th>Предложения за дуели</th></tr>";
for($i=0;$i<=count($duels)-1;$i++)
{
$pl2=$duels[$i];
$return['offers'].="<tr><td>".$duels[$i]."<span onclick=goinduel('$pl2','1') style='background-color:green;color:white;'>Приеми</span> <span onclick=goinduel('$pl2','0') style='background-color:red;color:white;'>Откажи</span></td></tr>";
}
}
$return['offers'].="</table></div>";
if($data2['duel']!=-1)
$return['send']="duel";
break;


case "makeduel":
$pl2=$_POST['player2'];
$pl2data=mydata("inf_user","user",$pl2);
$pl2data2=mydata("inf_user2","user",$pl2);
if($pl2==$_SESSION['ime'])
$return['info']="Не можеш да се дуелираш със себе си";
else
{
if($data['level']>$pl2data['level']+10)
$return['info']="Неможеш да се дуелираш с играчи 10 левела под твоя";
else
{
if(in_array($pl2,explode(",",$data2['duel_with'])))
$return['info']="Вече имате предложение от този потребител";
else
{
$duels=explode(",",$pl2data2['duel_with']);
if(in_array($_SESSION['ime'],$duels))
$return['info']="Офертата ви е все още валидна";
else
{
if($duels[0]=="")
$duels[0]=$_SESSION['ime'];
else
$duels[count($duels)]=$_SESSION['ime'];
$duels=implode(",",$duels);
update_data("inf_user2","user",$pl2,"duel_with",$duels);
$return['info']="Офертата е изпратена";
}
}
}
}
break;


case "goinduel":
$ok=1;
$pl2=$_POST['player2'];
$pl2data2=mydata("inf_user2","user",$pl2);
$pl2data=mydata("inf_user","user",$pl2);
if($_POST['answer']==1)
{
if($pl2data2['duel']!=-1 || $pl2data['fight']==1 || $pl2data['tren']!=0 || $pl2data2['online']<time() || $pl2data2['travel']!="" || $pl2data['hp']<1)
{ $ok=0; $return['info']="Този потребител е зает или не е в играта"; }
if(($data['tren']!=0 || $data['fight']==1 || $data2['duel']!=-1 || $data2['travel']!="" || $data['hp']<1) && $ok==1)
{ $ok=0; $return['info']="В момента си зает с друга дейност"; }
if($ok==1 && $data['grad']!=$pl2data['grad'])
{ $ok=0; $return['info']="Трябва да се намирате в една и съща местност или град, за да се дуелирате !"; }
if($ok==1)
{
$a_z="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
$let=rand(0,count($a_z)-1);
$let2=rand(0,count($a_z)-1);
$num=rand(1,200);
$num2=rand(1,200);
$ID=$let.$num.$let2.$num2;
while(check_field_exists("duels","ID",$ID))
{
$let=rand(0,count($a_z)-1);
$let2=rand(0,count($a_z)-1);
$num=rand(1,200);
$num2=rand(1,200);
$ID=$let.$num.$let2.$num2;
}
$im=$_SESSION['ime'];
$gold=$im.":0;".$pl2.":0";
$ready=$im.":0;".$pl2.":0";
$turn=$im.":0;".$pl2.":0";
$report=$im.":;".$pl2.":";
mysql_query("INSERT INTO `game`.`duels` (`ID`, `turn`,`winner`, `trades`,`gold`,`ready`, `report`) VALUES ('$ID', '','','','','', '')");
update_data("duels","ID",$ID,"gold",$gold);
update_data("duels","ID",$ID,"ready",$ready);
update_data("duels","ID",$ID,"turn",$turn);
update_data("duels","ID",$ID,"report",$report);
update_data("inf_user2","user",$_SESSION['ime'],"duel",$pl2.";".$ID);
update_data("inf_user2","user",$pl2,"duel",$_SESSION['ime'].";".$ID);
$duels=explode(",",$pl2data2['duel_with']);
unset($duels[array_search($pl2,$duels)]);
$duels=implode(",",$duels);
update_data("inf_user2","user",$_SESSION['ime'],"duel_with",$duels);
$return['go']=1;
}
}
else
{
$duels=explode(",",$data2['duel_with']);
unset($duels[array_search($pl2,$duels)]);
$duels=implode(",",$duels);
update_data("inf_user2","user",$_SESSION['ime'],"duel_with",$duels);
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
$town=$data['grad'];
echo '<center>';
echo '<div class="content">';
include 'inventory.php';
echo "<div style='background-color:green;color:white'>Посетителите  в странноприемницата на ".$town." са:</div><br>";
$sql = mysql_query("SELECT * FROM `inf_user` WHERE `grad` = '$town'  ");
while($usersintown=mysql_fetch_array($sql))
{
$userr=$usersintown['user'];
$sql2 = mysql_query("SELECT * FROM `inf_user2` WHERE `user` = '$userr'  ");
$useronline=mysql_fetch_array($sql2);
if($useronline['online']>time())
echo "<span style='color:green;background-color:white'>онлайн</span> <span style='font-size:20px;background-color:rgb(100,4,60);color:green'>".$userr."</span> ";
else
echo "<span style=''>".$userr."</span> ";
echo "<span onclick=duel('$userr') style='color:#EF3A8C'>Предложи дуел</span><br>";
}
echo "</div></center>";
?>
</body>
</html>
<?php
}
}
?>