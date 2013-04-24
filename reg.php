<?php
include 'incs/functions.php';
if($_SESSION['vleznal']!=true)
{
echo '<table align="center" class="table_logo">
<tr>	
<td><span class="logo_name">Лекроция</span></td>
</tr>
</table>';
echo '<form id="reg" method="post" action="reg.php">
<table>
<tr><td>
Потребителско име
</td><td>
<input type="text" name="user">
</td></tr>
<tr><td>
Парола
</td><td>
<input type="password" name="pass">
</td></tr>
<tr><td>
Повтори парола
</td>
<td>
<input type="password" name="pass2">
</td></tr>
<tr><td>
<input type="submit" name="submit" value="Регистрирай ме">
</tr></td>
</form>
';
if(isset($_POST['submit']))
{
$user=htmlspecialchars($_POST['user']);
$user=addslashes($user);
$user=mysql_real_escape_string($user);
$pass=md5($_POST['pass']);
$pass2=md5($_POST['pass2']);
if($pass!=$pass2)
echo "<font color=yellow>Паролите не съвпадат</font>";
else
{
if(strlen($user)<4)
echo "<font color=yellow>Полето за потребител трябва да е поне 4 символа</font>";
else
{
if(!check_field_exists("inf_user","user",$user))
{
$sql = mysql_query("INSERT INTO `game`.`inf_user` (`ID`, `user`, `pass`, `maxhp`, `hp`,`maxmana`,`mana`, `attack`, `mastery`, `armor`, `fight`,`tren`, `level`, `expi`, `nextlevel`, `zlato`, `hodove`, `inventory`,`grad`, `skills`,`rang`,`blocked`) VALUES (NULL, '$user', '$pass', '100', '100','100','100', '5', '2', '2', '0','0', '1', '0', '400', '50', '15', '-1,-1,-1,-1,-1,-1', 'Lovech', '5','user','-1')");
if($sql)
{
$sql2=mysql_query("INSERT INTO `game`.`inf_monster` (`user`, `m_name`, `m_maxhealth`, `m_health`, `m_armor`, `m_attack`,`curses`,`cooldown`, `report`) VALUES ('$user', '', '', '', '','','', '', '')");
mysql_query("INSERT INTO `game`.`stat_equip` (`user`, `weapon`,`helmet`,`armor`) VALUES ('$user', '-1','-1','-1')");
mysql_query("INSERT INTO `game`.`inf_user2` (`ID`,`user`, `magics`, `buffs`, `travel`, `online`,`duel`,`duel_with`) VALUES (NULL,'$user', '', '', '', '','-1','')");
$_SESSION['ime']=$user;
$_SESSION['vleznal']=true;
$sql5 = mysql_query("SELECT * FROM `chat` ");
$allres=mysql_num_rows($sql5);
$_SESSION['startline']=$allres;
//$_SESSION['chatline']=1;
$_SESSION['invshow']=-1;
echo '<font color=white>Влезте от </font><a href="index.php">тук</a>';
}
else
echo "Грешка при регистрацията";
}
else echo "<font color=yellow>Съществува потребител с това име</font>	";
}
}
}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<title>Lecrozya</title>
<script src="jquery.js" type="text/javascript"></script>
<script src="all_jquery.js" type="text/javascript"></script>
</head>
<link type='text/css' rel='stylesheet' href='style.css'>
</html>