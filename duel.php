<?php
include 'incs/functions.php';
if($_POST['jq']==1)
{
$return=array();
$data=mydata("inf_user","user",$_SESSION['ime']);
$data2=mydata("inf_user2","user",$_SESSION['ime']);
$pl2_id=explode(";",$data2['duel']);
$pl2=$pl2_id[0];
$ID=$pl2_id[1];
$pl2data=mydata("inf_user","user",$pl2);
$pl2data2=mydata("inf_user2","user",$pl2);
$dueldata=mydata("duels","ID",$ID);
switch($_POST['action'])
{


case "attack":
$return['info']="";
if($data['hp']>0 && $pl2data['hp']>0)
{
$report="";
$reportpl2="";
$attacktype=$_POST['type'];
if($dueldata['turn']==$_SESSION['ime'])
{
$attack=rand($data['attack']/2,$data['attack']);
$mastery=$data['mastery'];
$miss=1;
for($i=1;$i<=rand(1,$mastery);$i+=1)
{
if(rand(1,$pl2data['armor'])==rand(1,$pl2data['armor']))
{
$miss=0;
break;
}
}
if ($miss==0)
{
$report.="<font color=green>Атакуваш ".$pl2." и нанасяш ".$attack." щети</font><br>";
$reportpl2.="<font color=red>".$_SESSION['ime']." ви атакува и ви нанася ".$attack." щети</font><br>";
update_data("inf_user","user",$pl2,"hp",$pl2data['hp']-$attack);
}
else
{
$report.="<font color=green>Атакуваш ".$pl2." и правиш безуспешен опит да уцелиш</font><br>";
$reportpl2.="<font color=red>".$_SESSION['ime']." ви атакува и прави безуспешен опит да ви уцели</font><br>";
}
$pl2data=mydata("inf_user","user",$pl2);
if($pl2data['hp']<1)
{
$report.="<font color=green>Бравоо !! Ти спечели дуела <a href=duel.php>назад</a></font><br>";
$reportpl2.="<font color=red>Ти загуби този дуел <a href=duel.php>назад</a></font></br>";
update_data("duels","ID",$ID,"winner",$_SESSION['ime']);
$golds=explode(";",$dueldata['gold']);
$pl_gold=explode(":",$golds[0]);
if($pl_gold[0]==$_SESSION['ime'])
$pl_gold=explode(":",$golds[1]);
$gold=$pl_gold[1];
update_data("inf_user","user",$_SESSION['ime'],"zlato",$data['zlato']+$gold);
update_data("inf_user","user",$pl2,"zlato",$pl2data['zlato']-$gold);
$allitems=count(explode(",",$dueldata['trades']))-1;
$items=explode(",",$dueldata['trades']);
$full=0;
for($j=1;$j<=$allitems;$j++)
{
$invinfo=mydata("inf_user","user",$_SESSION['ime']);
if($full!=1)
{
$itempl=explode(";",$items[$j]);
$itemid=$itempl[0];
$bags=explode(";",$invinfo['inventory']);
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
$bags=implode(";",$bags);
update_data("inf_user","user",$_SESSION['ime'],"inventory",$bags);
if($itemid!=-1)
$full=1;
}
}
}
else
{
$report.="<font color=red>Сега е ред на ".$pl2."</font><br>";
$reportpl2.="<font color=green>Сега е ваш ред</font></br>";
}
$reports=explode(";",$dueldata['report']);
$report1=explode(":",$reports[0]);
$report2=explode(":",$reports[1]);
if($report1[0]==$_SESSION['ime'])
{
$report1[1]=$report;
$report2[1]=$reportpl2;
}
else
{
$report1[1]=$reportpl2;
$report2[1]=$report;
}
$report1=implode(":",$report1);
$report2=implode(":",$report2);
$reports=$report1.";".$report2;
update_data("duels","ID",$ID,"report",$reports);
update_data("duels","ID",$ID,"turn",$pl2);
}
else $return['info'].="Не си ти на ход";
}
break;


case "updateduel":
if(check_field_exists("duels","ID",$ID))
{
if($dueldata['turn']!=$_SESSION['ime'] && $dueldata['turn']!=$pl2)
{
$turns=explode(";",$dueldata['turn']);
$turn1=explode(":",$turns[0]);
$turn2=explode(":",$turns[1]);
if($turn1[1]>$turn2[1])
$turn=$turn1[0];
else
$turn=$turn2[0];
update_data("duels","ID",$ID,"turn",$turn);
if($turn!=$_SESSION['ime'])
{
$report="<font color=lightblue>Първият ход се пада на ".$turn."</font><br>";
$reportpl2="<font color=lightblue>Първият ход се пада на вас</font><br>";
}
else
{
$report="<font color=lightblue>Първият ход се пада на вас</font><br>";
$reportpl2="<font color=lightblue>Първият ход се пада на ".$turn."</font><br>";
}
$reports=explode(";",$dueldata['report']);
$report1=explode(":",$reports[0]);
$report2=explode(":",$reports[1]);
if($report1[0]==$_SESSION['ime'])
{
$report1[1]=$report;
$report2[1]=$reportpl2;
}
else
{
$report1[1]=$reportpl2;
$report2[1]=$report;
}
$report1=implode(":",$report1);
$report2=implode(":",$report2);
$reports=$report1.";".$report2;
update_data("duels","ID",$ID,"report",$reports);
}
$rr=mydata("duels","ID",$ID);
$reports=explode(";",$rr['report']);
$report1=explode(":",$reports[0]);
$report2=explode(":",$reports[1]);
if($report1[0]==$_SESSION['ime'])
$return['report']=$report1[1];
else
$return['report']=$report2[1];
$d=($pl2data['hp']*100)/$pl2data['maxhp'];
$return['m_hpbar']=$pl2data["hp"].'/'.$pl2data["maxhp"].'<div style="width:100px;height:10px;border:2px solid white;"><div style="width:'.$d.'px;height:10px;background-color:red;"></div></div>';
$return['table_info']=update_table_info($_SESSION['ime']);
}
break;


case "startduel":
$d=($pl2data['hp']*100)/$pl2data['maxhp'];
$return['duelinfo'].='<font color=white>'.$pl2.'</font>';
$return['duelinfo'].='<div style="color:red" class="m_hpbar">'.$pl2data["hp"].'/'.$pl2data["maxhp"].'<div style="width:100px;height:10px;border:2px solid white;"><div style="width:'.$d.'px;height:10px;background-color:red;"></div></div></div>';
$return['duelinfo'].='<input type="button" value="Атака" onclick="attack()">';
$return['duelinfo'].='<div class="curses"></div>';
$return['duelinfo'].='<br>';
if($data2['magics']!="")
{
$magics_level=explode(";",$data2['magics']);
$magics=explode(",",$magics_level[0]);
$return['duelinfo'].='<select  class="magic" onchange="magic_info()" name="magic">';
for($k=0;$k<=count($magics)-1;$k++)
{
$idm=$magics[$k];
$sql = mysql_query("SELECT * FROM `inf_magics` WHERE `ID` = $idm ");
$rez=mysql_fetch_array($sql);
$valname=$rez['name'];
$name=explode("|",$valname);
$return['duelinfo'].='<option value="'.$valname.'">'.$name[0].'</option>';
}
$return['duelinfo'].='</select><span class="magic_info"></span><br><input type="button" value="Магия">';
}
$return['duelinfo'].='<div class="report"></div>';
if($dueldata['turn']!=$_SESSION['ime'] && $dueldata['turn']!=$pl2)
{
$roll=rand(1,100);
$turns=explode(";",$dueldata['turn']);
$turn1=explode(":",$turns[0]);
$turn2=explode(":",$turns[1]);
if($turn1[0]==$_SESSION['ime'])
$turn1[1]=$roll;
else $turn2[1]=$roll;
$turn1=implode(":",$turn1);
$turn2=implode(":",$turn2);
$turns=$turn1.";".$turn2;
update_data("duels","ID",$ID,"turn",$turns);
}
break;


case "ready":
$readies=explode(";",$dueldata['ready']);
$ready1=explode(":",$readies[0]);
$ready2=explode(":",$readies[1]);
if(($ready1[1]==1 && $ready2[1]==0) || ($ready1[1]==0 && $ready2[1]==1) || ($ready1[1]==0 && $ready2[1]==0))
{
if($ready1[0]==$_SESSION['ime'])
{
if($ready1[1]==1) $ready1[1]=0;
else $ready1[1]=1;
}
else
{
if($ready2[1]==1) $ready2[1]=0;
else $ready2[1]=1;
}
if($ready1[1]==1 && $ready2[1]==1)
$return['ready']=1;
else
$return['ready']=0;
$ready1=implode(":",$ready1);
$ready2=implode(":",$ready2);
$readies=$ready1.";".$ready2;
update_data("duels","ID",$ID,"ready",$readies);
}
break;


case "itemtotrade":
$readies=explode(";",$dueldata['ready']);
$ready1=explode(":",$readies[0]);
$ready2=explode(":",$readies[1]);
$ready1[1]=0;
$ready2[1]=0;
$ready1=implode(":",$ready1);
$ready2=implode(":",$ready2);
$readies=$ready1.";".$ready2;
update_data("duels","ID",$ID,"ready",$readies);
$itemid=$_POST['itemid'];
$trades=explode(",",$dueldata['trades']);
$allitems=count($trades);
$trades[$allitems]=$itemid.";".$_SESSION['ime'];
$trades=implode(",",$trades);
update_data("duels","ID",$ID,"trades",$trades);
break;


case "untrade":
$readies=explode(";",$dueldata['ready']);
$ready1=explode(":",$readies[0]);
$ready2=explode(":",$readies[1]);
if($ready1[1]==0 || $ready2[1]==0)
{
$ready1[1]=0;
$ready2[1]=0;
$ready1=implode(":",$ready1);
$ready2=implode(":",$ready2);
$readies=$ready1.";".$ready2;
update_data("duels","ID",$ID,"ready",$readies);
$itemid=$_POST['itemid'];
$trades=explode(",",$dueldata['trades']);
$thisitem=explode(";",$trades[$_POST['slot']]);
if($thisitem[0]==$itemid && $thisitem[1]==$_SESSION['ime'])
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
}
}
}
if($itemid==-1)
{
unset($trades[$_POST['slot']]);
$trades=implode(",",$trades);
update_data("duels","ID",$ID,"trades",$trades);
}
$bags=implode(";",$bags);
update_data("inf_user","user",$_SESSION['ime'],"inventory",$bags);
}
$return['table_info']=update_table_info($_SESSION['ime']);
}
break;



case "updatetrade":
$return['max']=-1;
$readies=explode(";",$dueldata['ready']);
$ready1=explode(":",$readies[0]);
$ready2=explode(":",$readies[1]);
if($ready1[1]==1 && $ready2[1]==1)
$return['ready']=1;
else
$return['ready']=0;
if($return['ready']!=1)
{
$golds=explode(";",$dueldata['gold']);
$gold_pl=explode(":",$golds[0]);
if($gold_pl[0]==$_SESSION['ime'])
{
if($_POST['gold']!=$gold_pl[1])
{
$readies=explode(";",$dueldata['ready']);
$ready1=explode(":",$readies[0]);
$ready2=explode(":",$readies[1]);
$ready1[1]=0;
$ready2[1]=0;
$ready1=implode(":",$ready1);
$ready2=implode(":",$ready2);
$readies=$ready1.";".$ready2;
update_data("duels","ID",$ID,"ready",$readies);
}
if($_POST['gold']>$data['zlato'] && is_numeric($_POST['gold']))
{
$gold=$_SESSION['ime'].":".$data['zlato'].";";
$return['max']=$data['zlato'];
}
else
{
if(is_numeric($_POST['gold']))
$gold=$_SESSION['ime'].":".round($_POST['gold']).";";
else
{
$gold=$_SESSION['ime'].":0;";
$return['max']=0;
}
}
$gold_pl=explode(":",$golds[1]);
$gold.=$pl2.":".$gold_pl[1];
}
else
{
$gold=$pl2.":".$gold_pl[1].";";
$gold_pl=explode(":",$golds[1]);
if($_POST['gold']!=$gold_pl[1])
{
$readies=explode(";",$dueldata['ready']);
$ready1=explode(":",$readies[0]);
$ready2=explode(":",$readies[1]);
$ready1[1]=0;
$ready2[1]=0;
$ready1=implode(":",$ready1);
$ready2=implode(":",$ready2);
$readies=$ready1.";".$ready2;
update_data("duels","ID",$ID,"ready",$readies);
}
if($_POST['gold']>$data['zlato'] && is_numeric($_POST['gold']))
{
$gold.=$_SESSION['ime'].":".$data['zlato'].";";
$return['max']=$data['zlato'];
}
else
{
if(is_numeric($_POST['gold']))
$gold.=$_SESSION['ime'].":".round($_POST['gold']).";";
else
{
$gold.=$_SESSION['ime'].":0;";
$return['max']=0;
}
}
}
update_data("duels","ID",$ID,"gold",$gold);
}
$dueldata=mydata("duels","ID",$ID);
$return['trades']='<table style="background-color:rgb(99,109,63);color:white;border:4px inset green;"><tr>';
$bags=explode(";",$data['inventory']);
$numbags=count($bags);
for($i=0;$i<=$numbags-1;$i++)
{
$bag=explode(",",$bags[$i]);
for($k=0;$k<=count($bag)-1;$k+=1)
{
$search=$bag[$k];
if($search!=-1)
{
$res=mydata("inf_items","ID",$search);
if($k%2==0) $return['trades'].='</tr><tr>';
$idd=$res['ID'];
$return['trades'].='<td>';
$return['trades'].='<span onclick="itemdrop('.$i.','.$k.');itemtrade('.$idd.')" onmouseover="iteminfo2('.$idd.')"><img src='.$res["pic"].'></span>';
$return['trades'].='</td>';
}
else
{
if($k%2==0) $return['trades'].='</tr><tr>';
$return['trades'].='<td>';
$return['trades'].='<img src="resourse/slot.png"></td>';
}
}
}
$return['trades'].='</table>';


$return['trades'].='<table style="position:fixed;top:140px;left:400px;" border="0">';
$return['trades'].='<th>'.$_SESSION["ime"].'</th><th>'.$pl2.'</th>';
$return['trades'].='<tr><td>';
$return['trades'].='<table style="background-color:rgb(99,109,63);color:white;border:4px inset green;">';
$golds=explode(";",$dueldata['gold']);
$gold_pl=explode(":",$golds[0]);
if($gold_pl[0]!=$_SESSION['ime'])
{
$othergold=$gold_pl[1];
$gold_pl=explode(":",$golds[1]);
$mygold=$gold_pl[1];
}
else
{
$mygold=$gold_pl[1];
$gold_pl=explode(":",$golds[1]);
$othergold=$gold_pl[1];
}
$return['trades'].='<tr><td>злато:'.$mygold.'</td></tr>';

$trades=explode(",",$dueldata['trades']);
if($trades[1]!="")
{
for($i=1;$i<=count($trades)-1;$i++)
{
$itemid_name=explode(";",$trades[$i]);
$itemid=$itemid_name[0];
if($itemid_name[1]==$_SESSION['ime'])
{
$iteminfo=mydata("inf_items","ID",$itemid);
$itempic=$iteminfo['pic'];
$return['trades'].='<tr><td><span onclick="untrade('.$i.','.$itemid.')" onmouseover="iteminfo2('.$itemid.')"><img src='.$itempic.'></span></td></tr>';
}
}
}
$return['trades'].='</table>';
$return['trades'].='</td><td>';
$return['trades'].='<table style="background-color:rgb(99,109,63);color:white;border:4px inset green;">';
$return['trades'].='<tr><td>злато:'.$othergold.'</td></tr>';
if($trades[1]!="")
{
for($i=1;$i<=count($trades)-1;$i++)
{
$itemid_name=explode(";",$trades[$i]);
$itemid=$itemid_name[0];
if($itemid_name[1]!=$_SESSION['ime'])
{
$iteminfo=mydata("inf_items","ID",$itemid);
$itempic=$iteminfo['pic'];
$return['trades'].='<tr><td><span onmouseover="iteminfo2('.$itemid.')"><img src='.$itempic.'></span></td></tr>';
}
}
}
$return['trades'].='</table>';
$return['trades'].='</td></tr>';
$return['trades'].='<tr>';

$readies=explode(";",$dueldata['ready']);
$ready1=explode(":",$readies[0]);
$ready2=explode(":",$readies[1]);
if($ready1[0]==$_SESSION['ime'])
{
$ready=$ready1[1];
$pl2ready=$ready2[1];
}
else
{
$ready=$ready2[1];
$pl2ready=$ready1[1];
}
if($ready==1)
$return['trades'].='<td><input type="button" onclick="ready()" value="ДА" style="background-color:green"></td>';
else
$return['trades'].='<td><input type="button" onclick="ready()" value="НЕ" style="background-color:red"></td>';
if($pl2ready==1)
$return['trades'].='<td><input type="button" disabled="disabled" value="ДА" style="background-color:green"></td>';
else
$return['trades'].='<td><input type="button" disabled="disabled" value="НЕ" style="background-color:red"></td>';
$return['trades'].='</tr></table>';
break;

}

echo json_encode($return);
}
else
{
include 'wrongdir.php';
$data=mydata("inf_user","user",$_SESSION['ime']);
$data2=mydata("inf_user2","user",$_SESSION['ime']);
$pl2_id=explode(";",$data2['duel']);
$pl2=$pl2_id[0];
$ID=$pl2_id[1];

$dueldata=mydata("duels","ID",$ID);
if($dueldata['winner']!="" || (!check_field_exists("duels","ID",$ID) && $data2['duel']!=-1))
{
update_data("inf_user2","user",$_SESSION['ime'],"duel",-1);
mysql_query("DELETE FROM `game`.`duels` WHERE `duels`.`ID` = '$ID' ");
header("Location:index.php");
}
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
updatedueltrade();
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
if(check_field_exists("duels","ID",$ID))
{
$pl2data=mydata("inf_user","user",$pl2);
$pl2data2=mydata("inf_user2","user",$pl2);
$dueldata=mydata("duels","ID",$ID);
$readies=explode(";",$dueldata['ready']);
$ready1=explode(":",$readies[0]);
$ready2=explode(":",$readies[1]);
include 'inventory.php';
if($ready1[1]!=1 || $ready2[1]!=1)
{
$golds=explode(";",$dueldata['gold']);
$gold_pl=explode(":",$golds[0]);
if($gold_pl[0]!=$_SESSION['ime'])
{
$othergold=$gold_pl[1];
$gold_pl=explode(":",$golds[1]);
$mygold=$gold_pl[1];
}
else
{
$mygold=$gold_pl[1];
$gold_pl=explode(":",$golds[1]);
$othergold=$gold_pl[1];
}
echo '<span class="zlato"><font color=white>злато:</font><input value="'.$mygold.'" type="text" size="6" id="myzlato"></span>';
}
echo '<span class="trades">';
if($ready1[1]!=1 || $ready2[1]!=1)
{
echo '<table style="background-color:rgb(99,109,63);color:white;border:4px inset green;">
<tr>';
$bags=explode(";",$data['inventory']);
$numbags=count($bags);
for($i=0;$i<=$numbags-1;$i++)
{
$bag=explode(",",$bags[$i]);
for($k=0;$k<=count($bag)-1;$k+=1)
{
$search=$bag[$k];
if($search!=-1)
{
$res=mydata("inf_items","ID",$search);
if($k%2==0) echo '</tr><tr>';
$idd=$res['ID'];
echo '<td>';
echo '<span onmouseover="iteminfo2('.$idd.')"><img src='.$res["pic"].'></span><img onclick="itemdrop('.$i.','.$k.');itemtrade('.$idd.')" src=resourse/down_arrow.bmp width=16 height=16>
</td>';
}
else
{
if($k%2==0) echo '</tr><tr>';
echo '<td>';
echo '<img src="resourse/slot.png">
</td>';
}
}
}
echo '</table>';


echo '<table align="center" border="0">
<th>'.$_SESSION["ime"].'</th><th>'.$pl2.'</th>
<tr><td>';
echo '<table style="background-color:rgb(99,109,63);color:white;border:4px inset green;">
<tr><td>злато:'.$mygold.'</td></tr>';
$trades=explode(",",$dueldata['trades']);
if($trades[1]!="")
{
for($i=1;$i<=count($trades)-1;$i++)
{
$itemid_name=explode(";",$trades[$i]);
$itemid=$itemid_name[0];
if($itemid_name[1]==$_SESSION['ime'])
{
$iteminfo=mydata("inf_items","ID",$itemid);
$itempic=$iteminfo['pic'];
echo '<tr><td><span onmouseover="iteminfo2('.$itemid.')"><img src='.$itempic.'></span></td></tr>';
}
}
}
echo '</table></td><td>';


echo '<table style="background-color:rgb(99,109,63);color:white;border:4px inset green;">
<tr><td>злато:'.$othergold.'</td></tr>';
if($trades[1]!="")
{
for($i=1;$i<=count($trades)-1;$i++)
{
$itemid_name=explode(";",$trades[$i]);
$itemid=$itemid_name[0];
if($itemid_name[1]!=$_SESSION['ime'])
{
$iteminfo=mydata("inf_items","ID",$itemid);
$itempic=$iteminfo['pic'];
echo '<tr><td><span onmouseover="iteminfo2('.$itemid.')"><img src='.$itempic.'></span></td></tr>';
}
}
}
echo '</table>';
echo '</td></tr>
<tr>
<td><input type="button" onclick="ready()" value="ДА"></td>
<td><input type="button" disabled="disabled" value="ДА"</td>
</tr></table>';
}
echo '</span>';
//echo '<div class="infoitem" style="position:fixed;top:120px;left:550px;width:300px;height:200px;background-color:yellow;"></div>';
echo '<div id="infoitem" class="infoitem" style="display:none;position:absolute;left:200px;border:2px solid #68695E;top:50px;width:300px;height:200px;background-color:black;"></div>';
}
else
{
echo "<font color=red>Не съществува такъв дуел</font>";
}
?>
</body>
</html>
<?php
}
?>