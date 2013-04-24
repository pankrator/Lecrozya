<?php
if($_SESSION['vleznal']==true)
{

$data=mydata("inf_user","user",$_SESSION['ime']);
$mhp=($data['hp']*100)/$data['maxhp'];
$mmana=($data['mana']*100)/$data['maxmana'];
$expbar=($data['expi']*100)/$data['nextlevel'];
echo '<input type="hidden" id="invshow">';
echo '<div class="table_info">';
echo '<table class="main" onmouseout="hid()">
<th>'.$_SESSION["ime"].'</th>';
echo '<tr><td>';
echo '<span id="ofs" onclick=$(".offers").toggle("slow") style="display:none;background-color:red;color:black;">Нови оферти !</span><br>';
echo '<span class="chatbuton" onclick=showchat();updatechat()>покажи чат</span>';
echo '<table>
<tr style="background-color:rgb(156,147,82)"><td>
<div class="level">Левел:'.$data["level"].'</div>
</tr></td>
<tr style="background-color:rgb(156,127,65)"><td>
<div class="expbar">Опит:'.$data["expi"].'/'.$data["nextlevel"].'<div style="width:100px;height:10px;border:2px solid black;"><div style="width:'.$expbar.'px;height:10px;background-color:white;"></div></div></div>
</td></tr>
<tr style="background-color:rgb(156,147,82)"><td>
<div class="hpbar">Живот:'.$data["hp"].'/'.$data["maxhp"].'<div style="width:100px;height:10px;border:2px solid black;"><div style="width:'.$mhp.'px;height:10px;background-color:red;"></div></div></div>
</td></tr>
<tr style="background-color:rgb(156,127,65)"><td>
<div class="manabar">Мана:'.$data["mana"].'/'.$data["maxmana"].'<div style="width:100px;height:10px;border:2px solid black;"><div style="width:'.$mmana.'px;height:10px;background-color:blue;"></div></div></div>
</td></tr>
<tr style="background-color:rgb(156,147,82)"><td>
<div class="damage">Сила:'.($data["attack"]/2).'-'.$data["attack"].'</div>
</td></tr>
<tr style="background-color:rgb(156,127,65)"><td>
<div class="def">Защита:'.$data["armor"].'</div>
</td></tr>
<tr style="background-color:rgb(156,127,65)"><td>
<div class="goldvalue">Злато:'.$data["zlato"].'</div>
</td></tr>
<tr style="background-color:rgb(156,147,82)"><td>
<div class="skills">Точки:'.$data['skills'].'</div>
</td></tr>
<tr style="background-color:rgb(156,127,65)"><td>
<div class="moves">Ходове:'.$data["hodove"].'</div>
</td></tr>
</table></td></tr>';
//echo '</table>';
//echo '</div>';


/////////////////////////////////////////////////////////////////////////////////////////////////////////// RAZDELQNE	////////////////////////////////////////////////////////////////////////////////////////////////////////////
$data=mydata("inf_user","user",$_SESSION['ime']);
$bags=explode(";",$data['inventory']);
$numbags=count($bags);
//echo '<input type="hidden" id="invshow">';
//echo '<div class="table_inventory">';
//echo '<table class="main" onmouseout="hid()">';
echo '<th>
<span class="openinv">Инвентар</span>
</th>
<tr><td>
<table><tr><td>';
for($i=0;$i<=$numbags-1;$i++)
{
$num=$i+1;
if($_SESSION['invshow']==$i)
echo '<span style="border:1px solid yellow;background-color:blue" onclick="showinv('.$i.')">'.$num.'</span> ';
else
echo "<span style='border:1px solid yellow' onclick='showinv(".$i.")'>".$num."</span> ";
}
echo "</td></tr>";
for($i=0;$i<=$numbags-1;$i+=1)
{
if($_SESSION['invshow']==$i)
{
echo '<tr>';
$bag=explode(",",$bags[$i]);
for($k=0;$k<=count($bag)-1;$k+=1)
{
$search=$bag[$k];
if($search!=-1)
{
$res=mydata("inf_items","ID",$search);
$idd=$res['ID'];
if($k%2==0) echo '</tr><tr>';
echo '<td>';
echo '<div class="inv'.$i.'">';
echo '
<span style="position:relative" class="blah'.$k.'"><span class="onhover" onmouseover="item_info('.$idd.','.$k.')" onclick="doinv('.$idd.','.$i.','.$k.')"><img src='.$res["pic"].'></span><img onclick="itemdrop('.$i.','.$k.')" src=resourse/down_arrow.bmp width=16 height=16></span>
</div></td>';
}
else
{
if($_SESSION['invshow']==$i)
{
if($k%2==0) echo '</tr><tr>';
echo '<td>';
echo '<div class="inv'.$i.'">';
echo'
<span class="blah'.$k.'"><img src=resourse/slot.png></span>
</div></td>';
}
}
}
}
}
echo '</table></td></tr>';

////////////// EKIPIROVKA
echo '<th>
Екипировка
</th>';
echo "<tr><td>";
$equip=mydata("stat_equip","user",$_SESSION['ime']);

$helmet=$equip['helmet'];
$rezult=mydata("inf_items","ID",$helmet);
echo "<center>";
if($helmet!=-1)
echo '<span style="position:relative" class="equip_helmet"><span onmouseover="item_info('.$helmet.')" onclick="unequip('.$helmet.')"><img src='.$rezult["pic"].' width="32" height="32"></span></span>';
else
echo '<span style="position:relative" class="equip_helmet">HELM</span>';
echo "</br></br>";

$weapon=$equip['weapon'];
$rezult=mydata("inf_items","ID",$weapon);
if($weapon!=-1)
echo '<span class="equip_weapon"><span onmouseover="item_info('.$weapon.')" onclick="unequip('.$weapon.')"><img src='.$rezult["pic"].' width="32" height="32"></span></span>';
else
echo '<span class="equip_weapon">WEAP</span>';
echo "   ";
$armor=$equip['armor'];
$rezult=mydata("inf_items","ID",$armor);
if($armor!=-1)
echo '<span class="equip_armor"><span onmouseover="item_info('.$armor.')" onclick="unequip('.$armor.')"><img src='.$rezult["pic"].' width="32" height="32"></span></span>';
else
echo '<span class="equip_armor">ARMOR</span>';
echo "   ";
$shield=$equip['shield'];
$rezult=mydata("inf_items","ID",$shield);
if($shield!=-1)
echo '<span class="equip_shield"><span onmouseover="item_info('.$shield.')" onclick="unequip('.$shield.')"><img src='.$rezult["pic"].' width="32" height="32"></span></span>';
else
echo '<span class="equip_shield">SHIELD</span>';

echo "</center>";

echo '</td></tr></table></div>';
/// DO TUK S EKIPIROVKATA
echo '<div id="info_item" class="info_item" style="display:none;position:absolute;left:200px;border:2px solid #68695E;top:50px;width:300px;height:200px;background-color:black;">Място за информация за предметите</div>';
echo '<input type="hidden" id="mouse_x" name="mouse_x" value="">
<input type="hidden" id="mouse_y" name="mouse_y" value="">';
}

?>