<?php
session_start();
mysql_connect("localhost","root","lolipop");
mysql_select_db("game");


function check_field_exists($table,$searchin,$kriterii)
{
$sql = mysql_query("SELECT * FROM `$table` WHERE `$searchin` = '$kriterii' ");
if(mysql_fetch_array($sql)>0)
return true;
else
return false;
}

function mydata($table,$searchin,$kriterii)
{
$sql = mysql_query("SELECT * FROM `$table` WHERE `$searchin` = '$kriterii' ");
return mysql_fetch_array($sql);
}

function update_data($table,$searchin,$kriterii,$pole,$value)
{
$sql = mysql_query("UPDATE `game`.`$table` SET `$pole` = '$value' WHERE `$table`.`$searchin` = '$kriterii' ");
}

function check_fight()
{
$ime=$_SESSION['ime'];
$dat=mydata("inf_user","user",$ime);
if($dat['fight']==0)
{
return false;
}
else
return true;
}

function change_div($type,$newvalue,$maxvalue,$name,$fillcolor) //types:1=bar,2=value
{
if($type=="bar")
{
$d=($newvalue*100)/$maxvalue;
return $name.$newvalue.'/'.$maxvalue.'<div style="width:100px;height:10px;border:2px solid black;"><div style="width:'.$d.'px;height:10px;background-color:'.$fillcolor.';"></div></div>';
}
else if($type=="value")
{
return $name.':'.$newvalue;
}
}

function leveling($expi)
{
$data=mydata("inf_user","user",$_SESSION['ime']);
if($data['expi']+$expi>=$data['nextlevel'])
{
update_data("inf_user","user",$_SESSION['ime'],"level",$data['level']+1);
update_data("inf_user","user",$_SESSION['ime'],"maxhp",$data['maxhp']+15);
update_data("inf_user","user",$_SESSION['ime'],"hp",$data['maxhp']+15);
update_data("inf_user","user",$_SESSION['ime'],"skills",$data['skills']+2);
update_data("inf_user","user",$_SESSION['ime'],"expi",0);
$newdata=mydata("inf_user","user",$_SESSION['ime']);
$nextlevel=$data['nextlevel']+($newdata['level'])*20;
update_data("inf_user","user",$_SESSION['ime'],"nextlevel",$nextlevel);
$newexp=$data['expi']+$expi-$data['nextlevel'];
if($newexp>0)
leveling($newexp);
}
else
{
update_data("inf_user","user",$_SESSION['ime'],"expi",$data['expi']+$expi);
}
}

function report_update($rep)
{
$ime=$_SESSION['ime'];
$r=mydata("inf_monster","user",$ime);
$newrep=$r['report'];
$newrep.=$rep;
$sql=mysql_query("UPDATE `game`.`inf_monster` SET `report` = '$newrep' WHERE `inf_monster`.`user` = '$ime' LIMIT 1");
}


function get_fight()
{
$ime=$_SESSION['ime'];
$data=mydata("inf_user","user",$ime);
$names=array("Скелет","Картоф","Ученик","Мъртвец","Опосум");
$name=$names[rand(0,4)];
switch($data['grad'])
{

case "Lovech":
if($data['level']<=5)
{
$health=rand(1,$data['level'])*rand(5,15);
$attack=rand(1,$data['level'])*rand(2,4);
$armor=rand(1,$data['level'])*rand(2,4);
$mastery=rand(1,$data['level'])*rand(2,6);
}
else
{
$health=5*rand(5,15);
$attack=5*rand(2,4);
$armor=5*rand(2,8);
$mastery=5*rand(2,6);
}
$report="<font color=lightblue>Срещаш ".$name." и се приготвяш за битка.</font><br>";
$log = fopen("incs/fight.log", 'a');
fwrite($log,$ime." се бие с ".$name." ".date("F j, Y, H:i:s")."\r\n");
fwrite($log,$ime.":живот ".$data['hp']."/".$data['maxhp']." --------- ".$name.":живот ".$health."\r\n");
fclose($log);
update_data("inf_user","user",$ime,"fight",1);
$items=array();
$items[0]="";
$numitems=rand(1,3);
$allitems=array(4,5,7,16,17,18,19);
for($i=0;$i<$numitems;$i++)
$items[$i]=$allitems[rand(0,count($allitems)-1)];
if($items[0]!="")
{
$items=implode(",",$items);
update_data("inf_monster","user",$ime,"drops",$items);
}
update_data("inf_monster","user",$ime,"m_name",$name);
update_data("inf_monster","user",$ime,"m_maxhealth",$health);
update_data("inf_monster","user",$ime,"m_health",$health);
update_data("inf_monster","user",$ime,"m_armor",$armor);
update_data("inf_monster","user",$ime,"m_attack",$attack);
update_data("inf_monster","user",$ime,"m_mastery",$mastery);
update_data("inf_monster","user",$ime,"curses","");
update_data("inf_monster","user",$ime,"cooldown","");
update_data("inf_monster","user",$ime,"report",$report);
update_data("inf_user","user",$ime,"hodove",$data['hodove']-1);
break;

}
}


function curse($magic)
{
$data2=mydata("inf_user2","user",$_SESSION['ime']);
$data=mydata("inf_user","user",$_SESSION['ime']);
$mdata=mydata("inf_monster","user",$_SESSION['ime']);
$cursed=0;
$curses=explode(",",$mdata['curses']);
$cooldowns=explode(",",$mdata['cooldown']);
if($mdata['curses']=="")
{
$curses=implode(",",$curses);
$curses=$magic;
$cooldowns=implode(",",$cooldowns);
$cooldowns=5;
$cursed=1;
}
else if(count($curses)<2)
{
if($curses[0]!=$magic)
{
$curses[count($curses)]=$magic;
$curses=implode(",",$curses);
$cooldowns[count($cooldowns)]=5;
$cooldowns=implode(",",$cooldowns);
$cursed=1;
}
else
{
$cursed=2;
$curses=implode(",",$curses);
if($cooldowns[0]==0)
{
$cooldowns[0]=5;
$cursed=1;
}
$cooldowns=implode(",",$cooldowns);
}
}
else
{
for($i=0;$i<=count($curses)-1;$i++)
{
if($curses[$i]==$magic)
{
$slot=$i;
$cursed=2;
break;
}
}
if($cursed!=2)
{
$curses[count($curses)]=$magic;
$curses=implode(",",$curses);
$cooldowns[count($cooldowns)]=5;
$cooldowns=implode(",",$cooldowns);
$cursed=1;
}
else
{
if($cooldowns[$slot]==0)
{
$cooldowns[$slot]=5;
$cursed=1;
}
$curses=implode(",",$curses);
$cooldowns=implode(",",$cooldowns);
}
}
if($cursed==1)
{
if($data['mana']>=40)
{
$ime=explode("|",$magic);
update_data("inf_user","user",$_SESSION['ime'],"mana",$data['mana']-40);
update_data("inf_monster","user",$_SESSION['ime'],"curses",$curses);
update_data("inf_monster","user",$_SESSION['ime'],"cooldown",$cooldowns);
return "<font color=yellow>Ти използва проклятието ".$ime[0]." върху ".$mdata['m_name']."</font><br>";
}
else return "<font color=yellow>Nqmash dostatachno mana</font><br>";
}
else return "<font color=yellow>Tova proklqtie vse oshte deistva</font><br>";
}


/*
function update_table_inventory($user)
{
$data=mydata("inf_user","user",$user);
$bags=explode(";",$data['inventory']);
$numbags=count($bags);

$table='<table class="main" onmouseout="hid()">';
$table.='<th><span class="openinv">Инвентар</span></th>';
$table.='<tr><td><table>';
for($i=0;$i<=$numbags-1;$i++)
{
$num=$i+1;
if($_SESSION['invshow']==$i)
{
if($_SESSION['ime']==$user)
$table.='<td><span style="border:1px solid yellow;background-color:blue" onclick="showinv('.$i.')">'.$num.'</span></td>';
else
$table.='<td><span style="border:1px solid yellow;background-color:blue" onclick="showinvadmin('.$i.')">'.$num.'</span></td>';
}
else
{
if($_SESSION['ime']==$user)
$table.='<td><span style="border:1px solid yellow" onclick="showinv('.$i.')">'.$num.'</span></td>';
else
$table.='<td><span style="border:1px solid yellow" onclick="showinvadmin('.$i.')">'.$num.'</span></td>';
}
}
$table.='</tr>';
for($i=0;$i<=$numbags-1;$i+=1)
{
$table.='<tr>';
$bag=explode(",",$bags[$i]);
for($k=0;$k<=count($bag)-1;$k+=1)
{
$search=$bag[$k];
if($search!=-1)
{
$res=mydata("inf_items","ID",$search);
$idd=$res['ID'];
if($_SESSION['invshow']==$i)
{
if($k%2==0) $table.='</tr><tr>';
$table.='<td><div class="inv'.$i.'">';
if($user==$_SESSION['ime'])
$table.='<span style="position:relative" class="blah'.$k.'"><span class="onhover" onmouseover="item_info('.$idd.','.$k.')" onclick="doinv('.$idd.','.$i.','.$k.')"><img src='.$res["pic"].'></span><img onclick="itemdrop('.$i.','.$k.')" src=resourse/down_arrow.bmp width=16 height=16></span>';
else
$table.='<span style="position:relative" class="blah'.$k.'"><span class="onhover" onmouseover="item_info('.$idd.','.$k.')"><img src='.$res["pic"].'></span><img onclick="adminremove('.$i.','.$k.')" src=resourse/down_arrow.bmp width=16 height=16></span>';
$table.='</div></td>';
}
}
else
{
if($_SESSION['invshow']==$i)
{
if($k%2==0) $table.='</tr><tr>';
$table.='<td><div class="inv'.$i.'"><span class="blah'.$k.'"><img src=resourse/slot.png></span></div></td>';
}
}
}
}
$table.='</table></td></tr>';
$table.='<th>Екипировка</th>';
$table.='<tr><td><table>';
$equip=mydata("stat_equip","user",$user);
$weapon=$equip['weapon'];
$rezult=mydata("inf_items","ID",$weapon);
$table.='<tr>';
if($weapon!=-1)
{
if($user==$_SESSION['ime'])
$table.='<td><span style="position:relative" class="equip_weapon"><span onmouseover="item_info('.$weapon.')"  onclick="unequip('.$weapon.')"><img src='.$rezult["pic"].' width="32" height="32"></span></span></td>';
else
$table.='<td><span style="position:relative" class="equip_weapon"><span onmouseover="item_info('.$weapon.')"><img src='.$rezult["pic"].' width="32" height="32"></span></span></td>';
}
else
$table.='<td><span style="position:relative" class="equip_weapon">Няма</span></td>';
$armor=$equip['armor'];
$rezult=mydata("inf_items","ID",$armor);
if($armor!=-1)
{
if($user==$_SESSION['ime'])
$table.='<td><span style="position:relative" class="equip_armor"><span onmouseover="item_info('.$armor.')"  onclick="unequip('.$armor.')"><img src='.$rezult["pic"].' width="32" height="32"></span></span></td>';
else
$table.='<td><span style="position:relative" class="equip_armor"><span onmouseover="item_info('.$armor.')"><img src='.$rezult["pic"].' width="32" height="32"></span></span></td>';
}
else
$table.='<td><span style="position:relative" class="equip_armor">Няма</span></td>';
$table.='</tr><tr>';
$helmet=$equip['helmet'];
$rezult=mydata("inf_items","ID",$helmet);
if($helmet!=-1)
{
if($user==$_SESSION['ime'])
$table.='<td><span style="position:relative" class="equip_helmet"><span onmouseover="item_info('.$helmet.')" onclick="unequip('.$helmet.')"><img src='.$rezult["pic"].' width="32" height="32"></span></span></td>';
else
$table.='<td><span style="position:relative" class="equip_helmet"><span onmouseover="item_info('.$helmet.')"><img src='.$rezult["pic"].' width="32" height="32"></span></span></td>';
}
else
$table.='<td><span style="position:relative" class="equip_helmet">Няма</span></td>';
$shield=$equip['shield'];
$rezult=mydata("inf_items","ID",$shield);
if($shield!=-1)
{
if($user==$_SESSION['ime'])
$table.='<td><span class="equip_shield"><span onmouseover="item_info('.$shield.')" onclick="unequip('.$shield.')"><img src='.$rezult["pic"].' width="32" height="32"></span></span></td>';
else
$table.='<td><span style="position:relative" class="equip_shield"><span onmouseover="item_info('.$shield.')"><img src='.$rezult["pic"].' width="32" height="32"></span></span></td>';
}
else
$table.='<td><span style="position:relative" class="equip_shield">Няма</span></td>';
$table.='</tr>';
$table.='</table></td></tr>';
$table.='</table>';
return $table;
}

function update_table_info2($user)
{
$data=mydata("inf_user","user",$user);
if($data['hp']>$data['maxhp'])
{
update_data("inf_user","user",$user,"hp",$data['maxhp']);
$data=mydata("inf_user","user",$user);
}
if($data['mana']>$data['maxmana'])
{
update_data("inf_user","user",$user,"mana",$data['maxmana']);
$data=mydata("inf_user","user",$user);
}
$mhp=($data['hp']*100)/$data['maxhp'];
$mmana=($data['mana']*100)/$data['maxmana'];
$expbar=($data['expi']*100)/$data['nextlevel'];

$table='<table class="main">';
$table.='<th>'.$user.'</th>';
$table.='<tr><td>';
$table.='<span id="ofs" onclick=$(".offers").toggle("slow") style="display:none;background-color:red;color:black;">Нови оферти !</span><br>';
$table.='<span class="chatbuton" onclick=showchat();updatechat()>покажи чат</span>';
$table.='<table>';
$table.='<tr style="background-color:rgb(156,147,82)"><td><div class="level">Левел:'.$data["level"].'</div></td></tr>';
$table.='<tr style="background-color:rgb(156,127,65)"><td><div class="expbar">Опит:'.$data["expi"].'/'.$data["nextlevel"].'<div style="width:100px;height:10px;border:2px solid black;"><div style="width:'.$expbar.'px;height:10px;background-color:white;"></div></div></div></td></tr>';
$table.='<tr style="background-color:rgb(156,147,82)"><td><div class="hpbar">Живот:'.$data["hp"].'/'.$data["maxhp"].'<div style="width:100px;height:10px;border:2px solid black;"><div style="width:'.$mhp.'px;height:10px;background-color:red;"></div></div></div></td></tr>';
$table.='<tr style="background-color:rgb(156,127,65)"><td><div class="manabar">Мана:'.$data["mana"].'/'.$data["maxmana"].'<div style="width:100px;height:10px;border:2px solid black;"><div style="width:'.$mmana.'px;height:10px;background-color:blue;"></div></div></div></td></tr>';
$table.='<tr style="background-color:rgb(156,147,82)"><td><div class="damage">Сила:'.($data["attack"]/2).'-'.$data["attack"].'</div></td></tr>';
$table.='<tr style="background-color:rgb(156,147,82)"><td><div class="damage">Защита:'.$data["armor"].'</div></td></tr>';
$table.='<tr style="background-color:rgb(156,127,65)"><td><div class="goldvalue">Злато:'.$data["zlato"].'</div></td></tr>';
$table.='<tr style="background-color:rgb(156,147,82)"><td><div class="skills">Точки:'.$data['skills'].'</div></td></tr>';
$table.='<tr style="background-color:rgb(156,127,65)"><td><div class="moves">Ходове:'.$data["hodove"].'</div></td></tr>';
$table.='</table></td></tr>';
$table.='</table>';
return $table;
}
*/


function update_table_info($user)
{
$data=mydata("inf_user","user",$user);
$bags=explode(";",$data['inventory']);
$numbags=count($bags);
if($data['hp']>$data['maxhp'])
{
update_data("inf_user","user",$user,"hp",$data['maxhp']);
$data=mydata("inf_user","user",$user);
}
if($data['mana']>$data['maxmana'])
{
update_data("inf_user","user",$user,"mana",$data['maxmana']);
$data=mydata("inf_user","user",$user);
}
$mhp=($data['hp']*100)/$data['maxhp'];
$mmana=($data['mana']*100)/$data['maxmana'];
$expbar=($data['expi']*100)/$data['nextlevel'];

$table='<table class="main" onmouseout="hid()">';
$table.='<th>'.$user.'</th>';
$table.='<tr><td>';
$table.='<span id="ofs" onclick=$(".offers").toggle("slow") style="display:none;background-color:red;color:black;">Нови оферти !</span><br>';
$table.='<span class="chatbuton" onclick=showchat();updatechat()>покажи чат</span>';
$table.='<table>';
$table.='<tr style="background-color:rgb(156,147,82)"><td><div class="level">Левел:'.$data["level"].'</div></td></tr>';
$table.='<tr style="background-color:rgb(156,127,65)"><td><div class="expbar">Опит:'.$data["expi"].'/'.$data["nextlevel"].'<div style="width:100px;height:10px;border:2px solid black;"><div style="width:'.$expbar.'px;height:10px;background-color:white;"></div></div></div></td></tr>';
$table.='<tr style="background-color:rgb(156,147,82)"><td><div class="hpbar">Живот:'.$data["hp"].'/'.$data["maxhp"].'<div style="width:100px;height:10px;border:2px solid black;"><div style="width:'.$mhp.'px;height:10px;background-color:red;"></div></div></div></td></tr>';
$table.='<tr style="background-color:rgb(156,127,65)"><td><div class="manabar">Мана:'.$data["mana"].'/'.$data["maxmana"].'<div style="width:100px;height:10px;border:2px solid black;"><div style="width:'.$mmana.'px;height:10px;background-color:blue;"></div></div></div></td></tr>';
$table.='<tr style="background-color:rgb(156,147,82)"><td><div class="damage">Сила:'.($data["attack"]/2).'-'.$data["attack"].'</div></td></tr>';
$table.='<tr style="background-color:rgb(156,147,82)"><td><div class="damage">Защита:'.$data["armor"].'</div></td></tr>';
$table.='<tr style="background-color:rgb(156,127,65)"><td><div class="goldvalue">Злато:'.$data["zlato"].'</div></td></tr>';
$table.='<tr style="background-color:rgb(156,147,82)"><td><div class="skills">Точки:'.$data['skills'].'</div></td></tr>';
$table.='<tr style="background-color:rgb(156,127,65)"><td><div class="moves">Ходове:'.$data["hodove"].'</div></td></tr>';
$table.='</table></td></tr>';


$table.='<th><span class="openinv">Инвентар</span></th>';
$table.='<tr><td><table><tr><td>';
for($i=0;$i<=$numbags-1;$i++)
{
$num=$i+1;
if($_SESSION['invshow']==$i)
{
if($_SESSION['ime']==$user)
$table.='<span style="border:1px solid yellow;background-color:blue" onclick="showinv('.$i.')">'.$num.'</span> ';
else
$table.='<span style="border:1px solid yellow;background-color:blue" onclick="showinvadmin('.$i.')">'.$num.'</span> ';
}
else
{
if($_SESSION['ime']==$user)
$table.='<span style="border:1px solid yellow" onclick="showinv('.$i.')">'.$num.'</span> ';
else
$table.='<span style="border:1px solid yellow" onclick="showinvadmin('.$i.')">'.$num.'</span> ';
}
}
$table.='</td></tr>';
for($i=0;$i<=$numbags-1;$i+=1)
{
if($_SESSION['invshow']==$i)
{
$table.='<tr>';
$bag=explode(",",$bags[$i]);
for($k=0;$k<=count($bag)-1;$k+=1)
{
$search=$bag[$k];
if($search!=-1)
{
$res=mydata("inf_items","ID",$search);
$idd=$res['ID'];
if($k%2==0) $table.='</tr><tr>';
$table.='<td><div class="inv'.$i.'">';
if($user==$_SESSION['ime'])
$table.='<span style="position:relative" class="blah'.$k.'"><span class="onhover" onmouseover="item_info('.$idd.','.$k.')" onclick="doinv('.$idd.','.$i.','.$k.')"><img src='.$res["pic"].'></span><img onclick="itemdrop('.$i.','.$k.')" src=resourse/down_arrow.bmp width=16 height=16></span>';
else
$table.='<span style="position:relative" class="blah'.$k.'"><span class="onhover" onmouseover="item_info('.$idd.','.$k.')"><img src='.$res["pic"].'></span><img onclick="adminremove('.$i.','.$k.')" src=resourse/down_arrow.bmp width=16 height=16></span>';
$table.='</div></td>';
}
else
{
if($k%2==0) $table.='</tr><tr>';
$table.='<td><div class="inv'.$i.'"><span class="blah'.$k.'"><img src=resourse/slot.png></span></div></td>';
}
}
}
}
$table.='</table></td></tr>';
$table.='<th>Екипировка</th>';
$table.='<tr><td>';
$equip=mydata("stat_equip","user",$user);

$helmet=$equip['helmet'];
$rezult=mydata("inf_items","ID",$helmet);
$table.="<center>";
if($helmet!=-1)
{
if($user==$_SESSION['ime'])
$table.='<span style="position:relative" class="equip_helmet"><span onmouseover="item_info('.$helmet.')" onclick="unequip('.$helmet.')"><img src='.$rezult["pic"].' width="32" height="32"></span></span>';
else
$table.='<span style="position:relative" class="equip_helmet"><span onmouseover="item_info('.$helmet.')"><img src='.$rezult["pic"].' width="32" height="32"></span></span>';
}
else
$table.='<span style="position:relative" class="equip_helmet">HELM</span>';
$table.='</br></br>';

$weapon=$equip['weapon'];
$rezult=mydata("inf_items","ID",$weapon);
if($weapon!=-1)
{
if($user==$_SESSION['ime'])
$table.='<span style="position:relative" class="equip_weapon"><span onmouseover="item_info('.$weapon.')"  onclick="unequip('.$weapon.')"><img src='.$rezult["pic"].' width="32" height="32"></span></span>';
else
$table.='<span style="position:relative" class="equip_weapon"><span onmouseover="item_info('.$weapon.')"><img src='.$rezult["pic"].' width="32" height="32"></span></span>';
}
else
$table.='<span style="position:relative" class="equip_weapon">WEAP</span>';
$table.="   ";

$armor=$equip['armor'];
$rezult=mydata("inf_items","ID",$armor);
if($armor!=-1)
{
if($user==$_SESSION['ime'])
$table.='<span style="position:relative" class="equip_armor"><span onmouseover="item_info('.$armor.')"  onclick="unequip('.$armor.')"><img src='.$rezult["pic"].' width="32" height="32"></span></span>';
else
$table.='<span style="position:relative" class="equip_armor"><span onmouseover="item_info('.$armor.')"><img src='.$rezult["pic"].' width="32" height="32"></span></span>';
}
else
$table.='<span style="position:relative" class="equip_armor">ARMOR</span>';
$table.="   ";

$shield=$equip['shield'];
$rezult=mydata("inf_items","ID",$shield);
if($shield!=-1)
{
if($user==$_SESSION['ime'])
$table.='<span class="equip_shield"><span onmouseover="item_info('.$shield.')" onclick="unequip('.$shield.')"><img src='.$rezult["pic"].' width="32" height="32"></span></span>';
else
$table.='<span style="position:relative" class="equip_shield"><span onmouseover="item_info('.$shield.')"><img src='.$rezult["pic"].' width="32" height="32"></span></span>';
}
else
$table.='<span style="position:relative" class="equip_shield">SHIELD</span>';
$table.="</center>";
$table.='</td></tr>';
$table.='</table>';

return $table;
}


if($_SESSION['vleznal']==true)
{
$data=mydata("inf_user","user",$_SESSION['ime']);
if($data['blocked']!=-1)
{
if($data['blocked']<time() && $data['blocked']!=1)
update_data("inf_user","user",$_SESSION['ime'],"blocked",-1);
session_destroy();
header("Location:index.php");
}
update_data("inf_user2","user",$_SESSION['ime'],"online",time()+2*60);
}



?>