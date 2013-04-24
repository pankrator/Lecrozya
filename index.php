<?php
include 'incs/functions.php';
include 'wrongdir.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<title>Lecrozya</title>
<script src="jquery.js" type="text/javascript"></script>
<script src="all_jquery.js" type="text/javascript"></script>
<script src="jquery-ui.js" type="text/javascript"></script>
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
echo '<table align="center" class="table_logo">
<tr>	
<td><span class="logo_name">Лекроция</span></td>
</tr>
</table>';
if($_SESSION['vleznal']==true)
{
$data=mydata("inf_user","user",$_SESSION['ime']);
$equip=mydata("stat_equip","user",$_SESSION['ime']);
$town=mydata("towns","name",$data['grad']);
include "options.php";
echo '<center>';
echo "<div class='content'>";
echo "<font color=white>--".$town['ime']."--</font>";
include 'inventory.php';
echo '</div>';
echo '</center>';
}
else
{
echo '<form id="login" method="post" action="login.php">
<label>Потребител:
<input type="text" name="user" class="text"></label>
<label>Парола:
<input type="password" name="pass" class="text"><br>
<input type="submit" value="Влез" class="buttons"></label>
</form>
<a href="reg.php">Регистрирай се сега</a>
';
echo "<center><img src='resourse/swords.png'></center>";
}
?>
</body>
</html>