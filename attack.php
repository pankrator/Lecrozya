<?php
include 'incs/functions.php';
if($_SESSION['vleznal']==true )
{
$ime=$_SESSION['ime'];
$data=mydata("inf_user","user",$ime);
if($data['fight']==1)
{
$mdata=mydata("inf_monster","user",$ime);
$stats=mydata("stat_equip","user",$ime);
update_data("inf_monster","user",$ime,"report","");
$return=array();
$return['table_info']=-1;
$return["m_hpbar"]=-1;
$return['curses']="";
$return["report"]="";
$return['dmg']="";
$return['items']="";

$type=$_POST['type'];


if(check_fight())
{
if($data['hp']>0)
{
$blocked=0;
$mustattack=true;
///////////////////////////////////////////////MY ATTACK///////////////////////////////////////////////////////

switch($type)
{



case "normal":
$attack=rand($data['attack']/2,$data['attack']);
$mastery=$data['mastery'];
$miss=0;
/*
$miss=1;
for($i=1;$i<=rand(1,$mastery);$i+=1)
{
if(rand(1,$mdata['m_armor'])==rand(1,$mdata['m_armor']))
{
$miss=0;
break;
}
}
*/
if(rand(1,$mastery)==rand(1,$mastery))
{
$miss=1;
update_data("inf_user","user",$ime,"mastery",$data['mastery']+1);
}
if($mastery*$data['level']>=$mdata['m_armor'])
$attack-=$mdata['m_armor']/$mastery;
else
$attack-=$mdata['m_armor']/round(rand($mastery/2,$mastery));
$attack=round($attack);
if($attack<1) $miss=1;
if($miss==0)
{
$return['report'].="<font color=green>Атакуваш ".$mdata['m_name']." и нанасяш ".$attack." щети</font><br>";
$return['dmg'].="<font color=white>".$attack."</font><br>";
$mdata['m_health']-=$attack;
$h=$mdata['m_health'];
$myhp=$data['hp'];
update_data("inf_monster","user",$ime,"m_health",$h);
$log = fopen("incs/fight.log", 'a');
fwrite($log,$ime." атакува ".$mdata['m_name']." и нанася ".$attack." щети\r\n");
fclose($log);
}
else
{
$return['report'].="<font color=green>Атакуваш ".$mdata['m_name']." и правиш безуспешен опит да уцелиш</font><br>";
$return['dmg'].="<font color=red>Пропуск</font><br>";
$log = fopen("incs/fight.log", 'a');
fwrite($log,$ime." атакува ".$mdata['m_name']." и прави безуспешен опит да уцели\r\n");
fclose($log);
}
break;




case "magic":
$magic=$_POST['magic'];
$nameonly=explode("|",$magic);
// VARIANT S SWITCH !!
if($nameonly[1]=="cooldown" || $nameonly[1]=="lasting")
$return['report']=curse($magic);
break;


}


///////////////////////////////////////////////MONSTER ATTACK////////////////////////////////////////
$mdata=mydata("inf_monster","user",$ime);
$curses=explode(",",$mdata['curses']);
$cooldowns=explode(",",$mdata['cooldown']);
if(count($curses)>0 && implode(",",$curses)!="")
{
for($i=0;$i<=count($curses)-1;$i+=1)
{
$last_cool=explode("|",$curses[$i]);
switch($last_cool[1])
{
case "cooldown":
switch($last_cool[0])
{
case "curse of doom":
if($cooldowns[$i]>0)
{
$mdata=mydata("inf_monster","user",$ime);
$dmg=rand(1,5);
update_data("inf_monster","user",$ime,"m_health",$mdata['m_health']-$dmg);
$return['report'].="<font color=green>Проклятието ти ".$last_cool[0]." наранява ".$mdata['m_name']." с ".$dmg." жизнени точки.</font><br>";
$return['dmg'].="<font color=yellow>".$last_cool[0].":</font><font color=green>".$dmg."</font><br>";
$cooldowns[$i]-=1;
$return['curses'].="<font color=yellow>".$last_cool[0].":".$cooldowns[$i]."</font> ";
$cooldowns=implode(",",$cooldowns);
update_data("inf_monster","user",$ime,"cooldown",$cooldowns);
$cooldowns=explode(",",$cooldowns);
}
break;////CURSE OF DOOM


case "curse of block":
if($cooldowns[$i]>0)
{
$mdata=mydata("inf_monster","user",$ime);
if(rand(1,10)==rand(1,10))
{
$blocked=1;
$return['report'].="<font color=green>Проклятието ти ".$last_cool[0]." успява да блокира ".$mdata['m_name']." за този рунд</font><br>";
$return['dmg'].="<font color=yellow>".$last_cool[0].":</font><font color=green>блокиране</font><br>";
}
else
$return['report'].="<font color=green>Проклятието ти ".$last_cool[0]." е блокирано</font><br>";
$return['dmg'].="<font color=yellow>".$last_cool[0].":</font><font color=red>неуспешно</font><br>";
$cooldowns[$i]-=1;
$return['curses'].="<font color=yellow>".$last_cool[0].":".$cooldowns[$i]."</font>";
$cooldowns=implode(",",$cooldowns);
update_data("inf_monster","user",$ime,"cooldown",$cooldowns);
$cooldowns=explode(",",$cooldowns);
}
break; //////CURSE OF BLOCK

}
break; /////COOLDOWN



case "lasting":
switch($last_cool[0])
{
case "curse of weakness":
$mdata=mydata("inf_monster","user",$ime);
$decreasedmg=2;
if($cooldowns[$i]>0)
{
$cooldowns[$i]-=1;
$return['curses'].="<font color=yellow>".$last_cool[0].":".$cooldowns[$i]."</font> ";
$cooldowns=implode(",",$cooldowns);
update_data("inf_monster","user",$ime,"cooldown",$cooldowns);
$cooldowns=explode(",",$cooldowns);
if($cooldowns[$i]==4)
{
update_data("inf_monster","user",$ime,"m_attack",$mdata['m_attack']-$decreasedmg);
$return['report'].="<font color=green>Проклятието ти ".$last_cool[0]." намали атаката на упонента ти с ".$decreasedmg." за 5 рунда</font><br>";
$return['dmg'].="<font color=yellow>".$last_cool[0].":</font><font color=green>-".$decreasedmg."</font>";
}
else if($cooldowns[$i]==0)
update_data("inf_monster","user",$ime,"m_attack",$mdata['m_attack']+$decreasedmg);
}
break;/////////////////////////////////CURSE OF WEAKNESS


}
break;////LASTING
}
}
}

$mdata=mydata("inf_monster","user",$ime);
if($mdata['m_health']<1) //If monster is dead
{
update_data("inf_user","user",$ime,"fight",0);
$return['report'].="<font color=lightblue>Ти победи своя враг</font><br>";
$log = fopen("incs/fight.log", 'a');
fwrite($log,"                Ти победи\r\n");
$exp=rand(1,$mdata['m_maxhealth']+$mdata['m_armor']+$mdata['m_attack']+$mdata['m_mastery'])*2;
$gold=rand(4,$mdata['m_maxhealth']);
$return['report'].="<font color=gold>Получаваш ".$exp." опит и ".$gold." злато</font><br>";
fwrite($log,"Получаваш ".$exp." опит и ".$gold." злато\r\n");
update_data("inf_user","user",$ime,"zlato",$data['zlato']+$gold);
leveling($exp);
$chance=rand(1,10);
$chance2=rand(1,15);
if($chance==$chance2) //If you are lucky get 2 more moves
{
update_data("inf_user","user",$ime,"hodove",$data['hodove']+2);
$return['report'].="<font color=gold>имаш късмет и получаваш 2 допълнителни хода</font><br>";
}
$items=explode(",",$mdata['drops']);
if($items[0]!="")
{
fwrite($log,"                Предмети:\r\n");
for($i=0;$i<=count($items)-1;$i++)
{
$itemid=$items[$i];
$itemdata=mydata("inf_items","ID",$itemid);
$itempic=$itemdata['pic'];
$return['report'].="<span style='color:gold' class=drop".$i." onclick=itemadd(".$itemid.",".$i.")>".$itemdata['name']."<img src=".$itempic."></span><br>";
fwrite($log,$itemdata['name']."\r\n");
}
}
$return['dead']=1;
$return['report'].="<font color=lightblue><a href=index.php>Назад</a></font><br>";
fwrite($log,"--------------------Край на битката----------------".date("F j, Y, H:i:s")."\r\n\r\n\r\n\r\n\r\n");
fclose($log);
}
else
{
if($mustattack==true)
{
$m_atack=rand($mdata['m_attack']/2,$mdata['m_attack']);
$m_miss=0;
if(rand(1,$mdata['m_mastery'])==rand(1,$mdata['m_mastery']))
{
$m_miss=1;
update_data("inf_monster","user",$ime,"m_mastery",$mdata['m_mastery']+1);
}
if($mdata['m_mastery']*$data['level']>=$data['armor'])
$m_attack-=$data['armor']/$mdata['m_mastery'];
else
$m_attack-=$data['armor']/round(rand($mdata['m_mastery']/2,$mdata['m_mastery']));
$m_attack=round($attack);
if($m_attack<1) $m_miss=1;
/*
$m_atack=rand($mdata['m_attack']/2,$mdata['m_attack']);
$m_miss=1;
for($i=1;$i<=rand(1,$mdata['m_mastery']);$i+=1)
{
if(rand(1,$data['armor'])==rand(1,$data['armor']))
{
$m_miss=0;
break;
}
}
*/
if($m_miss==0 && $blocked==0)
{
$val=$data['hp']-$m_atack;
update_data("inf_user","user",$ime,"hp",$val);
$return['report'].="<font color=red>".$mdata['m_name']." Атакува и нанася ".$m_atack." щети върху вас</font><br>";
}
else
{
$return['report'].="<font color=red>".$mdata['m_name']." се засилва да те удари но пропуска</font><br>";
}
}
}
}
$data=mydata("inf_user","user",$ime);
if($data['hp']<1)
{
$return['report'].="<font color=lightblue>Ти беше победен</font><br>";
$return['report'].="<font color=lightblue>Върни се в предишната страница <a href=index.php>назад</a></font><br>";
update_data("inf_user","user",$ime,"fight",0);
$return['dead']=1;
}
report_update($return['report']);
//$return['report'].=$return['items'];
$m_data=mydata("inf_monster","user",$ime);
$d=($m_data['m_health']*100)/$m_data['m_maxhealth'];
$return['m_hpbar']=$m_data["m_health"].'/'.$m_data["m_maxhealth"].'<div style="width:100px;height:10px;border:2px solid white;"><div style="width:'.$d.'px;height:10px;background-color:red;"></div></div>';
//$return["report"]=$m_data['report'];
$return['table_info']=update_table_info($_SESSION['ime']);
//$return['table_info']=update_table_info2($_SESSION['ime']);
//$return['table_inventory']=update_table_inventory($_SESIION['ime']);
}
echo json_encode($return);
}
}
?>