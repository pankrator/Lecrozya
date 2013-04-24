<?php
include("incs/functions.php");

if($_POST['jq']==1)
{
$return=array();
$return['info']="";

switch($_POST['action'])
{

case "changeset":
$ok=1;
$set=$_POST['name'];
if(!check_field_exists("setitems","id",$set))
{$ok=0; $return['info'].="Не е намерен такъв комплект\n";}
if($ok==1)
{
$setdata=mydata("setitems","id",$set);
$svoistva=explode(",",$_POST['svoistva']);
$stoinosti=explode(",",$_POST['stoinosti']);
for($i=0;$i<=count($stoinosti)-1;$i++)
{
if(!is_numeric($stoinosti[$i]) || $stoinosti[$i]=="" || (int)$stoinosti[$i]<1)
{
$return['info'].="Некоректни или непопълнени данни в полетата за свойства\n";
$ok=0;
break;
}
}
$svoistva=implode(",",$svoistva);
$stoinosti=implode(",",$stoinosti);
}
if($ok==1)
{
update_data("setitems","id",$set,"svoistvo",$svoistva);
update_data("setitems","id",$set,"stoinost",$stoinosti);
$return['info']="Промените са запаметени";
}
/*
$svoistva=explode(",",$_POST['svoistva']);
$stoinosti=explode(",",$_POST['stoinosti']);
for($i=0;$i<=count($stoinosti)-1;$i++)
{
if(!is_numeric($stoinosti[$i]) || $stoinosti[$i]=="" || (int)$stoinosti[$i]<1)
{
$return['info'].="Некоректни или непопълнени данни в полетата за свойства\n";
$ok=0;
break;
}
}

$svoistva=implode(",",$svoistva);
$stoinosti=implode(",",$stoinosti);
*/
break;


case "setinuse":
$set=urldecode($_POST['set']);
$setdata=mydata("setitems","ime",$set);
$setid=$setdata['id'];
$svoistva=$setdata['svoistvo'];
$svoistvo=explode(",",$svoistva);
if($svoistva=="")
$setbroisv=-1;
else
$setbroisv=count($svoistvo)-1;
$return['setinfo']="<font color=33FF00>".$setdata['ime']."</font>";
$return['setinfo'].="<br><input type='button' value='Добави свойство' onclick='setaddprop()'><input type='button' value='Махни последното свойство' onclick='setdelprop()'><input type='hidden' value=".$setbroisv." id='set_broisv'><input type='hidden' value=".$setid." id='setime'>";
$return['setinfo'].="<br><span class='setproperties'>";
$stoinosti=$setdata['stoinost'];
$stoinost=explode(",",$stoinosti);
for($i=0;$i<=count($svoistvo)-1;$i+=1)
{
$return['setinfo'].="<span class='prop".$i."'>";
$return['setinfo'].="<select id=setsvoistvo".$i.">";
switch($svoistvo[$i])
{
case "attack":
$imeto="Сила";
break;
case "maxhp":
$imeto="Живот";
break;
case "armor":
$imeto="Защита";
break;
case "maxmana":
$imeto="Мана";
break;
}
$return['setinfo'].="<option value=".$svoistvo[$i].">".$imeto."</option>";
if($svoistvo[$i]!="attack")
$return['setinfo'].="<option value='attack'>Сила</option>";
if($svoistvo[$i]!="maxhp")
$return['setinfo'].="<option value='maxhp'>Живот</option>";
if($svoistvo[$i]!="armor")
$return['setinfo'].="<option value='armor'>Защита</option>";
if($svoistvo[$i]!="maxmana")
$return['setinfo'].="<option value='maxmana'>Мана</option>";
$return['setinfo'].="</select>";
$return['setinfo'].="<input type='text' id='setstoinost".$i."' value='".$stoinost[$i]."'>";
$return['setinfo'].="<br></span>";
}
$return['setinfo'].="</span>";
$return['setinfo'].="<input type='button' value='Запамети' onclick='changeset()'>";
$return['setinfo'].="<br>";
$setitems=explode(",",$setdata['items']);
$return['setinfo'].="<table border=1>";
for($i=0;$i<count($setitems);$i++)
{
$thisiteminfo=mydata("inf_items","ID",$setitems[$i]);
$return['setinfo'].="<tr><td><img width=32 height=32 src=".$thisiteminfo['pic']."></td>";
$imena=explode(",",$thisiteminfo['imena']);
$stoinosti=explode(",",$thisiteminfo['stoinosti']);
$return['setinfo'].="<td><b>".$thisiteminfo['name']."</b>(ниво:".$thisiteminfo['levelreq'].")<br>";
$allthings=count($imena);
for($k=0;$k<=$allthings-1;$k+=1)
$return['setinfo'].=$imena[$k].":".$stoinosti[$k]."<br>";
//$thisid=$setitems[$i];
$return['setinfo'].="<input type='button' value='Премахни' onclick='remfromset(".$setitems[$i].")'>";
$return['setinfo'].="</td></tr>";
}
$return['setinfo'].="</table>";
break;



case "remfromset":
$setname=$_POST['setname'];
$itemtorem=$_POST['itemid'];
$ok=1;
if(!check_field_exists("setitems","id",$setname))
{$ok=0;$return['info'].="Не е намерен такъв комплект!\n";}
if($ok==1)
$setdata=mydata("setitems","id",$setname);
$setitems=explode(",",$setdata['items']);
$setitemtypes=explode(",",$setdata['itemtype']);
if(!check_field_exists("inf_items","ID",$itemtorem) || !in_array($itemtorem,$setitems))
{$ok=0;$return['info'].="Няма такъв предмет в комплекта!\n";}
else $itemkey=array_search($itemtorem,$setitems);
if($ok==1)
{
unset($setitems[$itemkey]);
unset($setitemtypes[$itemkey]);
$setitems=implode(",",$setitems);
$setitemtypes=implode(",",$setitemtypes);
update_data("setitems","id",$setname,"items",$setitems);
update_data("setitems","id",$setname,"itemtype",$setitemtypes);
update_data("inf_items","ID",$itemtorem,"kachestvo","normal");
update_data("inf_items","ID",$itemtorem,"set","");
}
break;


case "showinv":
$user=$_POST['user'];
if($_SESSION['invshow']==$_POST['bag'])
$_SESSION['invshow']=-1;
else
$_SESSION['invshow']=$_POST['bag'];
$return['stat_table']=update_table_info($user);
break;



case "showitem":
$ok=1;
$itemid=$_POST['itemid'];
if($itemid=="" || !is_numeric($itemid))
{ $ok=0; $return['info']="Некоректни данни в полето за предмет"; }
if($ok==1 && !check_field_exists("inf_items","ID",$itemid))
{ $ok=0; $return['info']="Не съществува предмет с този номер"; }
if($ok==1)
{
$iteminfo=mydata("inf_items","ID",$itemid);
$kachestvo=$iteminfo['kachestvo'];
$imena=explode(",",$iteminfo['imena']);
$stoinosti=explode(",",$iteminfo['stoinosti']);
$return['iteminfo']="";
$return['iteminfo']="<img src=".$iteminfo['pic']."><b>".$iteminfo['name']."</b>(ниво:".$iteminfo['levelreq'].")<br>";
$allthings=count($imena);
for($i=0;$i<=$allthings-1;$i+=1)
$return['iteminfo'].=$imena[$i].":".$stoinosti[$i]."<br>";
if($kachestvo=="set")
{
$setdata=mydata("setitems","ime",$iteminfo['set']);
$setitems=explode(",",$setdata['items']);
$return['iteminfo'].="<br><font color=#88DD3E>".$iteminfo['set']."</font><br>-----------------<br>";
for($i=0;$i<count($setitems);$i++)
{
$thisitemdata=mydata("inf_items","ID",$setitems[$i]);
$return['iteminfo'].="<font color=red>".$thisitemdata['name']."(".$thisitemdata['type'].")</font><br>";
}
}
}
break;



case "adminremove":
$ok=1;
$slot=$_POST['slot'];
$bagnum=$_POST['bag'];
$user=$_POST['user'];
if($user=="")
{ $ok=0; $return['info'].="Не е избран потребител\n"; }
if($ok==1)
{
$data=mydata("inf_user","user",$user);
$bags=explode(";",$data['inventory']);
$bag=explode(",",$bags[$bagnum]);
$bag[$slot]="-1";
$bag=implode(",",$bag);
$bags[$bagnum]=$bag;
$bags=implode(";",$bags);
update_data("inf_user","user",$user,"inventory",$bags);
$return['stat_table']=update_table_info($user);
}
break;



case "adminadditem":
$ok=1;
$itemid=$_POST['itemid'];
$user=$_POST['user'];
if($user=="")
{ $ok=0; $return['info'].="Не е избран потребител\n"; }
if($itemid=="" || !is_numeric($itemid))
{ $ok=0; $return['info']="Некоректни данни в полето за предмет\n"; }
if($ok==1 && !check_field_exists("inf_items","ID",$itemid))
{ $ok=0; $return['info']="Не съществува предмет с този номер\n"; }
if($ok==1)
{
$data=mydata("inf_user","user",$user);
$bags=explode(";",$data['inventory']);
$numbags=count($bags);
for($k=0;$k<=$numbags-1;$k+=1)
{
$bag=explode(",",$bags[$k]);
for($i=0;$i<=count($bag)-1;$i+=1)
{
if($bag[$i]==-1 && $itemid!=-1)
{
$bag[$i]=$itemid;
$bag=implode(",",$bag);
$bags[$k]=$bag;
$itemid=-1;
}
}
}
if($itemid!=-1)
$return['info']="Нямаш място в инвентара";
else
$return['info']="Предмета е добавен успешно";
$bags=implode(";",$bags);
update_data("inf_user","user",$user,"inventory",$bags);
$return['stat_table']=update_table_info($user);
$return['success']=1;
}
break;



case "userstats":
$user=$_POST['user'];
if(check_field_exists("inf_user","user",$user))
{
$return['stat_table']=update_table_info($user);
$return['lastuser']=$user;
}
else $return['info'].="Не съществува такъв потребител\n";
break;


case "addtown":
$townname=$_POST['townname'];
$townid=rand(5,10);
$sql=mysql_query("INSERT INTO `game`.`towns` (`id`, `ime`) VALUES ('$townid', '$townname') ");
if($sql)
$return['info']="Градът е успешно добавен";
else
$return['info']="Грешка";
break;



case "block":
$ok=1;
if($_POST['user']!="")
$user=$_POST['user'];
else { $return['info'].="Непопълнено поле за име на потребителя\n"; $ok=0; }
if(!check_field_exists("inf_user","user",$user))
{ $return['info'].="Не съществува такъв потребител\n"; $ok=0; }
if(is_numeric((int)$_POST['time']) && (int)$_POST['vreme']>0 && $_POST['vreme']!="")
{
$time=(int)$_POST['vreme'];
$time=time()+$time*60;
}
else { $return['info'].="Некоректни данни в полето за време\n"; $ok=0; }
if($ok==1)
{
$sql = mysql_query("UPDATE `game`.`inf_user` SET `blocked` = '$time' WHERE `inf_user`.`user` = '$user' ");
if($sql)
{
$return['info']="Блокирването е извършено";
$return['ok']=true;
}
else
$return['info']="Грешка при извършване на операцията";
}
break;


case "itemcreate":
$ok=1;
$kachestvo=$_POST['kachestvo'];
//if($kachestvo!="normal" || $kachestvo!="admin" || $kachestvo!="special" || $kachestvo!="set")
//{ $return['info'].="Не може да бъде дадено такова качество"; $ok=0; }
$setname="";
if($_POST['name']!="")
$name=$_POST['name'];
else { $return['info'].="Не е въведено име на предмета\n"; $ok=0; }
$levelreq=0;
if($_POST['pic']!="")
$pic=$_POST['pic'];
else { $return['info'].="Не е избрана снимка \n"; $ok=0; }
$type=$_POST['type'];
$prevtype=$_POST['type'];
switch($type)
{
case "weapon":
$type.=";".$_POST['izrabotka_w'];
break;
case "armor":
case "helm":
$type.=";".$_POST['izrabotka_a'];
break;
}
switch($prevtype) 
{

case "manapotion":
$kachestvo="normal";
$svoistva="regen";
if($_POST['amount']!="" && is_numeric($_POST['amount']) && (int)$_POST['amount']>0 && (int)$_POST['amount']<=100)
$stoinosti=$_POST['amount'];
else { $return['info'].="Не е въведена коректна стойност или полето за стойноста на пълнене е празно\n"; $ok=0; }
$names="Увеличава маната (в %) с";
break;


case "hppotion":
$kachestvo="normal";
$svoistva="heal";
if($_POST['amount']!="" && is_numeric($_POST['amount']) && (int)$_POST['amount']>0 && (int)$_POST['amount']<=100)
$stoinosti=$_POST['amount'];
else { $return['info'].="Не е въведена коректна стойност или полето за стойноста на пълнене е празно\n"; $ok=0; }
$names="Увеличава живота (в %) с ";
break;



case "shield":
case "helmet":
case "armor":
case "weapon":
if($kachestvo=="set")
{
if($_POST['setname']!="")
$setname=$_POST['setname'];
else
$setname=urldecode($_POST['useset']);
}
if($_POST['levelreq']!="" && is_numeric($_POST['levelreq']))
$levelreq=$_POST['levelreq'];
else { $return['info'].="Полето за ниво на предмета е попълнено некоректно или е празно\n"; $ok=0; }
$svoistva=explode(",",$_POST['svoistva']);
$stoinosti=explode(",",$_POST['stoinosti']);
for($i=0;$i<=count($stoinosti)-1;$i++)
{
if(!is_numeric($stoinosti[$i]) || $stoinosti[$i]=="" || (int)$stoinosti[$i]<1)
{
$return['info'].="Некоректни или непопълнени данни в полетата за свойства\n";
$ok=0;
break;
}
}
$names="";
for($i=0;$i<=count($svoistva)-1;$i++)
{
switch($svoistva[$i])
{
case "armor":
$names[$i].="Защита";
break;
case "attack":
$names[$i].="Сила";
break;
case "maxhp":
$names[$i].="Живот";
break;
case "maxmana":
$names[$i].="Мана";
}
}
$names=implode(",",$names);
$svoistva=implode(",",$svoistva);
$stoinosti=implode(",",$stoinosti);
break;


}
if($ok==1)
{
$sql = mysql_query("INSERT INTO `game`.`inf_items` (`ID`, `name`, `type`,`kachestvo`, `set`, `svoistva`, `stoinosti`, `imena`, `levelreq`, `pic`) VALUES (NULL, '$name', '$type','$kachestvo', '$setname','$svoistva', '$stoinosti', '$names', '$levelreq', '$pic')");
$lastid=mysql_insert_id();
if($kachestvo=="set")
{
if(!check_field_exists("setitems","ime",$setname))
{
$sql2=mysql_query("INSERT INTO `game`.`setitems` (`id`, `ime`, `items`, `itemtype`) VALUES ('', '$setname', '$lastid', '$prevtype') ");
}
else
{
$setinfo=mydata("setitems","ime",$setname);
$items=$setinfo['items'];
$itemtype=$setinfo['itemtype'];
$items.=",".$lastid;
$itemtype.=",".$prevtype;
update_data("setitems","ime",$setname,"items",$items);
update_data("setitems","ime",$setname,"itemtype",$itemtype);
}
}
if($sql)
{
$return['info']="Предмета е създаден успешно";
$return['created']=true;
}
else
$return['info']="Има грешка при създаването на предмета";
}
break;



}
echo json_encode($return);
}
else
{
//include 'wrongdir.php';
$data=mydata("inf_user","user",$_SESSION['ime']);
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
if($data['rang']=="administrator")
{
echo '<font color=lightblue>Избери дейност<br>
<select id="deistvie" onchange="change_razdel()">
<option value="itemcreate">Създаване на предмет</option>
<option value="setoptions">Бонуси на комплектите</option>
<option value="addtown">Добавяне на град</option>
<option value="blockuser">Блокирване на потребители</option>
<option value="userinfo">Информация за потребител</option>
';
//<option value="itemedit">Променяне на предмети</option>
echo '</select>
<br><br><br>';
echo'<span class="itemcreate">
Създаване на предмет<br>
Име:<input type="text" id="itemcreate_name"><br>
Вид на предмета:<select id="itemcreate_tip" onchange="change()">
<option value="hppotion">Колба живот</option>
<option value="manapotion">Колба мана</option>
<option value="weapon">Оръжие</option>
<option value="helmet">Каска</option>
<option value="armor">Броня</option>
<option value="shield">Щит</option>
</select>
<br>
<span style="display:none" class="izrabotka_w">
Изработка на предмета:<select id="itemcreate_izrabotka_w">
<option value="1h-sword">Меч 1 ръка</option>
<option value="2h-sword">Меч 2 ръце</option>
<option value="1h-axe">Брадва 1 ръка</option>
<option value="2h-axe">Брадва 2 ръце</option>
<option value="bow">Лък</option>
</select>
</span>
<span style="display:none" class="izrabotka_a">
Изработка на предмета:<select id="itemcreate_izrabotka_a">
<option value="cloth">леко облекло</option>
<option value="leather">по-тежко облекло</option>
<option value="mail">най-тежко облекло</option>
</select>
</span>
<br>
Качество на предмета:<select id="itemcreate_kachestvo" onchange="setname()">
<option value="normal">Нормално</option>
<option value="admin">Администраторско</option>
<option value="special">Специално</option>
<option value="set">Комплект</option>
</select>
<span style="display:none" class="setname">';
$sql=mysql_query("SELECT * FROM `setitems` ");
$all=mysql_num_rows($sql);
echo 'Избери комплект<select id="useset">';
$sql=mysql_query("SELECT * FROM `setitems` ");
while($sets=mysql_fetch_array($sql))
{
$setime=$sets['ime'];
echo '<option value='.urlencode($setime).'>'.$setime.'</option>';
}
echo '</select> или ';
echo 'Име на новия комплект<input type="text" id="setname">
</span>
<br>
<span class="hp_mana">
Колко да се увеличава с 1 колба (в %)<input type="text" id="itemcreate_amount"><br>
</span>
<span class="options" style="display:none">
Изискване за ниво:<input type="text" id="itemcreate_levelreq"><br>
Свойства:<input type="button" value="Добави свойство" onclick="addfield()"><input type="button" value="Махни последното свойство" onclick="deletefield()"><input type="hidden" value="0" id="itemcreate_broisv"><br>
</span>
<br>
Изберете картинка за предмета:';
$picdir=opendir("items");
while(false!==($file=readdir($picdir)))
if ($file != "." && $file != "..")
{
$pic=$file;
echo '<img border="1" id='.$pic.' onclick=setpic(this) src="items/'.$pic.'"> ';
}
echo'
<input type="hidden" id="itemcreate_pic">
<br>
<input type="button" onclick="createitem()" name="create" value="Създай">
</span>';


echo '<span class="setoptions" style="display:none">
';
echo 'Избери комплект<select id="setinuse" onchange="setinuse()">';
echo '<option value=""></option>';
$sql=mysql_query("SELECT * FROM `setitems` ");
while($sets=mysql_fetch_array($sql))
{
echo '<option value='.urlencode($sets["ime"]).'>'.$sets["ime"].'</option>';
}
echo '</select><br>
<span class="setinfo">';
echo '</span></span>';


echo '<span class="blockuser" style="display:none">
Блокирване на потребители<br>
Потребител:<input type="text" id="user">
За време(в минути):<input type="text" id="vreme">
<br>
<input type="button" onclick="block()" value="Блокирай">
</span>';

echo '<span class="addtown" style="display:none">
Град:<input type="text" id="townname"><br>
Връзки...<br>
<input type="button" onclick="townadd()" value="Добави">
</span>';

echo '<span class="userinfo" style="display:none">
Информация за потребител<br>
Потребител:<input type="text" id="userinf">
<input type="button" value="Покажи" onclick="getstats()"><br>';
//echo 'Номер на предмета:<input type="text" id="userinfo_itemid" onchange="show_item()">';
echo '<input type="hidden" id="userinfo_lastuser">';
$sqlitems=mysql_query("SELECT * FROM `inf_items` ");
echo 'Избери предмет:<select id="userinfo_itemid" onchange="show_item()">';
while($it=mysql_fetch_array($sqlitems))
{
echo '<option value='.$it["ID"].'>'.$it["name"].'</option>';
}
echo '</select>';
echo '<input type="button" value="Постави в инвентара" onclick="addtoinv()"><br>
<span class="iteminf"></span>
<div class="table_info"></div>
<div id="info_item" class="info_item" style="display:none;position:absolute;left:200px;border:2px solid #68695E;top:50px;width:300px;height:200px;background-color:black;">Място за информация за предметите</div>
</span></font>';

/*
echo '<span class="itemedit" style="display:none">
Променяне на предмети<br>
<select id="itemedit_item" onchange="loaditem()">
';
$sql = mysql_query("SELECT * FROM `inf_items` ");
while($rez=mysql_fetch_array($sql))
{
echo '<option value='.$rez["ID"].'>'.$rez['name'].'</option>';
}
echo '</select>';
echo '';
echo '</span>';
*/
?>
</body>
</html>
<?php
}
else
echo "Само Администратор има достъп до тази страница !";
}
?>