<?php
if(isset($_SERVER['HTTP_REFERER']))
{
include 'incs/functions.php';
if($_SESSION['vleznal']==true)
{
$return=array();
$return['greshka']=-1;
$return['things']=-1;
$return['table_info']=-1;
$data=mydata("inf_user","user",$_SESSION['ime']);
$data2=mydata("inf_user2","user",$_SESSION['ime']);
$equip=mydata("stat_equip","user",$_SESSION['ime']);
switch($_POST['action'])
{


case "showinv":
if($_SESSION['invshow']==$_POST['bag'])
$_SESSION['invshow']=-1;
else
$_SESSION['invshow']=$_POST['bag'];
break;


case "unequip":
if($data['fight']!=1)
{
$id=$_POST['id'];
$sql = mysql_query("SELECT * FROM `inf_items` WHERE `ID` = '$id' ");
$rez=mysql_fetch_array($sql);
$types=explode(";",$rez['type']);
$type=$types[0];
$return['type']=$type;
$svoistva=explode(",",$rez['svoistva']);
$stoinosti=explode(",",$rez['stoinosti']);
$bags=explode(";",$data['inventory']);
$numbags=count($bags);
for($j=0;$j<=$numbags-1;$j++)
{
$bag=explode(",",$bags[$j]);
for($i=0;$i<=count($bag)-1;$i+=1)
{
if($bag[$i]==-1)
{
if($rez['kachestvo']=="set")
{
$inset=0;
$setdata=mydata("setitems","ime",$rez['set']);
$setitems=explode(",",$setdata['items']);
for($p=0;$p<count($setitems);$p++)
{
$thisiteminfo=mydata("inf_items","ID",$setitems[$p]);
if($equip[$thisiteminfo['type']]==$setitems[$p])
$inset++;
}
if($inset==count($setitems))
{
$setsvoistva=explode(",",$setdata['svoistvo']);
$setstoinosti=explode(",",$setdata['stoinost']);
for($m=0;$m<count($setsvoistva);$m++)
{
update_data("inf_user","user",$_SESSION['ime'],$setsvoistva[$m],$data[$setsvoistva[$m]]-$setstoinosti[$m]);
}
}
$data=mydata("inf_user","user",$_SESSION['ime']);
$equip=mydata("stat_equip","user",$_SESSION['ime']);
}
for($k=0;$k<=count($svoistva)-1;$k+=1)
{
$svoistvo=$svoistva[$k];
$stoinost=$stoinosti[$k];
update_data("inf_user","user",$_SESSION['ime'],$svoistvo,$data[$svoistvo]-$stoinost);
}
if($types[1]=="2h-sword" || $types[1]=="2h-axe")
{
update_data("stat_equip","user",$_SESSION['ime'],"weapon",-1);
update_data("stat_equip","user",$_SESSION['ime'],"shield",-1);
}
else
update_data("stat_equip","user",$_SESSION['ime'],$type,-1);
$bag[$i]=$id;
$_SESSION['invshow']=$j;
$id=-1;
$bag=implode(",",$bag);
$bags[$j]=$bag;
$bags=implode(";",$bags);
update_data("inf_user","user",$_SESSION['ime'],"inventory",$bags);
break;
}
}
}
if($id!=-1) $return['greshka']="Нямаш място в инвентара";
}
else
{
$return['greshka']="Неможеш докато си в битка";
}
break;



case "additem":
$return['added']=-1;
$itemid=$_POST['id'];
$mdata=mydata("inf_monster","user",$_SESSION['ime']);
if($mdata['m_health']<1)
{
$drops=explode(",",$mdata['drops']);
$dropped=$_POST['dropped'];
if($drops[$dropped]==$itemid)
{
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
$_SESSION['invshow']=$k;
$return['added']=$dropped;
}
}
}
if($itemid!=-1)
$return['greshka']="Нямаш място в инвентара";
$bags=implode(";",$bags);
update_data("inf_user","user",$_SESSION['ime'],"inventory",$bags);
if($itemid==-1)
$drops[$dropped]=-1;
$drops=implode(",",$drops);
update_data("inf_monster","user",$_SESSION['ime'],"drops",$drops);
}
}
break;



case "info":
$id=$_POST['id'];
$return['slot']=$_POST['slot'];
$sql = mysql_query("SELECT * FROM `inf_items` WHERE `ID` = '$id' ");
$rez=mysql_fetch_array($sql);
$types=explode(";",$rez['type']);
$kachestvo=$rez['kachestvo'];
$imena=explode(",",$rez['imena']);
$stoinosti=explode(",",$rez['stoinosti']);
$rows=0;
$return['things']="";
$return['things']="<center><span style='color:white;font-weight:bold;'>".$rez['name'];
if($data['level']>=$rez['levelreq'])
$return['things'].="(ниво:".$rez['levelreq'].")</span></center>";
else
$return['things'].="</span><span style='color:red;font-weight:bold;'>(ниво:".$rez['levelreq'].")</span></center>";
if(count($types)>1)
{
$naucheni=explode(";",$equip['naucheni']);
$rows=1;
if(in_array($types[1],$naucheni))
$return['things'].="<span style='color:white'>";
else
$return['things'].="<span style='color:red'>";
switch($types[1])
{
case "1h-sword":
$return['things'].="Меч 1 ръка";
break;
case "2h-sword":
$return['things'].="Меч 2 ръце";
break;
case "1h-axe":
$return['things'].="Брадва 1 ръка";
break;
case "2h-axe":
$return['things'].="Брадва 2 ръце";
break;
case "bow":
$return['things'].="Лък";
break;
case "cloth":
$return['things'].="леко облекло";
break;
case "leather":
$return['things'].="по-тежко облекло";
break;
case "mail":
$return['things'].="най-тежко облекло";
break;
}
$return['things'].="</span>";
}
$return['things'].="<hr width=100%>";
$rows+=2;
$longest=strlen($rez['name']."(ниво:".$rez['levelreq'].")");
$allthings=count($imena);
for($i=0;$i<=$allthings-1;$i+=1)
{
$return['things'].="<span style='color:white;'>".$imena[$i].":".$stoinosti[$i]."</span><br>";
if($longest<strlen($imena[$i].":".$stoinosti[$i])) $longest=strlen($imena[$i].":".$stoinosti[$i]);
$rows+=1;
}
if($kachestvo=="set")
{
$inset=0;
$setdata=mydata("setitems","ime",$rez['set']);
$setitems=explode(",",$setdata['items']);
if($longest<strlen($rez['set'])) $longest=strlen($rez['set']);
$return['things'].="<br><center><font color=#88DD3E>".$rez['set']."</font></center><br>";
for($i=0;$i<count($setitems);$i++)
{
$thisitemdata=mydata("inf_items","ID",$setitems[$i]);
if($setitems[$i]==$equip[$thisitemdata['type']])
{
$return['things'].="<font color=green>".$thisitemdata['name']."(".$thisitemdata['type'].")</font><br>";
$inset+=1;
}
else
$return['things'].="<font color=red>".$thisitemdata['name']."(".$thisitemdata['type'].")</font><br>";
if($longest<strlen($thisitemdata['name']."(".$thisitemdata['type'].")")) $longest=strlen($thisitemdata['name']."(".$thisitemdata['type'].")");
$rows+=1;
}
$rows+=3;
if($inset==count($setitems))
{
$setoptions=explode(",",$setdata['svoistvo']);
$setvalues=explode(",",$setdata['stoinost']);
for($i=0;$i<count($setoptions);$i++)
{
$return['things'].="<font color=#88DD3E>";
switch($setoptions[$i])
{
case "armor":
$return['things'].="Защита";
break;
case "attack":
$return['things'].="Сила";
break;
case "maxhp":
$return['things'].="Живот";
break;
case "maxmana":
$return['things'].="Мана";
}
$return['things'].=":".$setvalues[$i];
$return['things'].="</font><br>";
$rows+=1;
}
}
}
$return['len']=$longest*10;
$return['rows']=$rows*20;
break;



case "use":
$equiped=0;
$slot=$_POST['slot'];
$return['slot']=$slot;
$id=$_POST['id'];
$bagnum=$_POST['bag'];
//Proverka za nesashtestvuvasht predmet v inventara
$ok=1;
$bags=explode(";",$data['inventory']);
$numbags=count($bags);
if($bagnum>$numbags) $ok=0;
if($ok==1)
{
$itemsinbag=explode(",",$bags[$bagnum]);
if(!in_array($id,$itemsinbag)) $ok=0;
}
//Krai na proverkata
if($ok==1)
{
$sql = mysql_query("SELECT * FROM `inf_items` WHERE `ID` = $id ");
$rez=mysql_fetch_array($sql);
$svoistva=explode(",",$rez['svoistva']);
$stoinosti=explode(",",$rez['stoinosti']);
$types=explode(";",$rez['type']);
$type=$types[0];
if($data['hp']>0)
{
if($type=="hppotion")
{
$key=array_search("heal",$svoistva);
update_data("inf_user","user",$_SESSION['ime'],"hp",$data['hp']+$data['maxhp']*$stoinosti[$key]/100);
$bags=explode(";",$data['inventory']);
$bag=explode(",",$bags[$bagnum]);
$bag[$slot]="-1";
$bag=implode(",",$bag);
$bags[$bagnum]=$bag;
$bags=implode(";",$bags);
update_data("inf_user","user",$_SESSION['ime'],"inventory",$bags);
}
else if($type=="manapotion")
{
$key=array_search("regen",$svoistva);
update_data("inf_user","user",$_SESSION['ime'],"mana",$data['mana']+$data['maxmana']*$stoinosti[$key]/100);
$bags=explode(";",$data['inventory']);
$bag=explode(",",$bags[$bagnum]);
$bag[$slot]="-1";
$bag=implode(",",$bag);
$bags[$bagnum]=$bag;
$bags=implode(";",$bags);
update_data("inf_user","user",$_SESSION['ime'],"inventory",$bags);
}
else
{
if($data['fight']!=1)
{
if($data['level']>=$rez['levelreq'])
{


switch($type)
{
case "weapon":
if($equip['weapon']==-1)
{
$naucheni=explode(";",$equip['naucheni']);
if(in_array($types[1],$naucheni))
{
if(($types[1]=="2h-sword" || $types[1]=="2h-axe"))
{
if($equip['shield']==-1)
{
update_data("stat_equip","user",$_SESSION['ime'],"shield",$id);
for($i=0;$i<=count($svoistva);$i+=1)
{
$svoistvo=$svoistva[$i];
$stoinost=$stoinosti[$i];
update_data("inf_user","user",$_SESSION['ime'],$svoistvo,$data[$svoistvo]+$stoinost);
}
update_data("stat_equip","user",$_SESSION['ime'],$type,$id);
$bags=explode(";",$data['inventory']);
$bag=explode(",",$bags[$bagnum]);
$bag[$slot]="-1";
$bag=implode(",",$bag);
$bags[$bagnum]=$bag;
$bags=implode(";",$bags);
update_data("inf_user","user",$_SESSION['ime'],"inventory",$bags);
$equiped=1;
}
else
$return['greshka']="Не можеш да носиш това оръжие с щит";
}
else
{
for($i=0;$i<=count($svoistva);$i+=1)
{
$svoistvo=$svoistva[$i];
$stoinost=$stoinosti[$i];
update_data("inf_user","user",$_SESSION['ime'],$svoistvo,$data[$svoistvo]+$stoinost);
}
update_data("stat_equip","user",$_SESSION['ime'],$type,$id);
$bags=explode(";",$data['inventory']);
$bag=explode(",",$bags[$bagnum]);
$bag[$slot]="-1";
$bag=implode(",",$bag);
$bags[$bagnum]=$bag;
$bags=implode(";",$bags);
update_data("inf_user","user",$_SESSION['ime'],"inventory",$bags);
$equiped=1;
}
}
else
$return['greshka']="Не умееш да боравиш с това оръжие";
}
else
$return['greshka']="Първо премахни предишното оръжие";
break;


case "helmet":
if($equip['helmet']==-1)
{
$naucheni=explode(";",$equip['naucheni']);
if(in_array($types[1],$naucheni))
{
for($i=0;$i<=count($svoistva);$i+=1)
{
$svoistvo=$svoistva[$i];
$stoinost=$stoinosti[$i];
update_data("inf_user","user",$_SESSION['ime'],$svoistvo,$data[$svoistvo]+$stoinost);
}
update_data("stat_equip","user",$_SESSION['ime'],$type,$id);
$bags=explode(";",$data['inventory']);
$bag=explode(",",$bags[$bagnum]);
$bag[$slot]="-1";
$bag=implode(",",$bag);
$bags[$bagnum]=$bag;
$bags=implode(";",$bags);
update_data("inf_user","user",$_SESSION['ime'],"inventory",$bags);
$equiped=1;
}
else
$return['greshka']="Не можеш да носиш такава каска";
}
else
$return['greshka']="Първо премахни предишната каска";
break;


case "shield":
if($equip['shield']==-1)
{
for($i=0;$i<=count($svoistva);$i+=1)
{
$svoistvo=$svoistva[$i];
$stoinost=$stoinosti[$i];
update_data("inf_user","user",$_SESSION['ime'],$svoistvo,$data[$svoistvo]+$stoinost);
}
update_data("stat_equip","user",$_SESSION['ime'],$type,$id);
$bags=explode(";",$data['inventory']);
$bag=explode(",",$bags[$bagnum]);
$bag[$slot]="-1";
$bag=implode(",",$bag);
$bags[$bagnum]=$bag;
$bags=implode(";",$bags);
update_data("inf_user","user",$_SESSION['ime'],"inventory",$bags);
$equiped=1;
}
else
$return['greshka']="Първо освободи място за щита";
break;



case "armor":
if($equip['armor']==-1)
{
$naucheni=explode(";",$equip['naucheni']);
if(in_array($types[1],$naucheni))
{
for($i=0;$i<=count($svoistva);$i+=1)
{
$svoistvo=$svoistva[$i];
$stoinost=$stoinosti[$i];
update_data("inf_user","user",$_SESSION['ime'],$svoistvo,$data[$svoistvo]+$stoinost);
}
update_data("stat_equip","user",$_SESSION['ime'],$type,$id);
$bags=explode(";",$data['inventory']);
$bag=explode(",",$bags[$bagnum]);
$bag[$slot]="-1";
$bag=implode(",",$bag);
$bags[$bagnum]=$bag;
$bags=implode(";",$bags);
update_data("inf_user","user",$_SESSION['ime'],"inventory",$bags);
$equiped=1;
}
else
$return['greshka']="Не можеш да носиш такава броня";
}
else
$return['greshka']="Първо премахни предишната броня";
break;


}


if($rez['kachestvo']=="set" && $equiped==1)
{
$data=mydata("inf_user","user",$_SESSION['ime']);
$equip=mydata("stat_equip","user",$_SESSION['ime']);
$inset=0;
$setdata=mydata("setitems","ime",$rez['set']);
$setitems=explode(",",$setdata['items']);
for($i=0;$i<count($setitems);$i++)
{
$thisiteminfo=mydata("inf_items","ID",$setitems[$i]);
if($equip[$thisiteminfo['type']]==$setitems[$i])
$inset++;
}
if($inset==count($setitems))
{
$setsvoistva=explode(",",$setdata['svoistvo']);
$setstoinosti=explode(",",$setdata['stoinost']);
for($i=0;$i<count($setsvoistva);$i++)
{
update_data("inf_user","user",$_SESSION['ime'],$setsvoistva[$i],$data[$setsvoistva[$i]]+$setstoinosti[$i]);
}
}
}


}
else $return['greshka']="Нямаш нужното ниво за този предмет";
}
else $return['greshka']="Неможеш докато си в битка";
}
}
else $return['greshka']="Нямаш право докато си мъртав";
}
break;



case "drop":
$slot=$_POST['slot'];
$bagnum=$_POST['bag'];
$bags=explode(";",$data['inventory']);
$bag=explode(",",$bags[$bagnum]);
$bag[$slot]="-1";
$bag=implode(",",$bag);
$bags[$bagnum]=$bag;
$bags=implode(";",$bags);
update_data("inf_user","user",$_SESSION['ime'],"inventory",$bags);
break;


}
if($data2['duel']!=-1)
{
$duel=explode(";",$data2['duel']);
$duelid=$duel[1];
$dueldata=mydata("duels","ID",$duelid);
$readies=explode(";",$dueldata['ready']);
$ready1=explode(":",$readies[0]);
$ready2=explode(":",$readies[1]);
if($ready1[1]!=1 || $ready2[1]!=1)
$return['duel']=1;
}
//$return['table_info']=update_table_info2($_SESSION['ime']);
//$return['table_inventory']=update_table_inventory($_SESSION['ime']);
$return['table_info']=update_table_info($_SESSION['ime']);
echo json_encode($return);
}
}
?>