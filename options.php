<?php
$towndata=mydata("towns","name",$data['grad']);
echo "<center>";
echo '<div class="options">';
echo '<div class="inoptions">';
echo '<a href="index.php">Начало</a> 
<a href="gora.php">Гората</a> 
';
if($towndata['inn']=="da")
{
echo '<a href="inn.php">Странноприемница</a> ';
}
/*
if($towndata['shop']=="da")
{
echo '<a href="shop.php">Магазин</a>';
}
*/
if($data['hp']<1)
{
$cena=$data['maxhp']+$data['armor']*$data['level'];
echo '<a href="do.php?type=ressurect">Възкреси ме</a> <font color=purple>'.$cena.'</font>';
}
if($towndata['trainer']=="da")
{
echo '<a href="levelup.php">Развитие</a>';
}
echo '<a href="travel.php">Каравани</a>';
$data=mydata("inf_user","user",$_SESSION['ime']);
if($data['rang']=="administrator")
{
echo '<a href="admin.php">Админ панел</a>';
}
echo '<a href="logout.php">Излез</a>';
echo '</div>';


include 'incs/actions_info.php';

echo '</div>';
echo "</center>";
include 'chat.php';
?>