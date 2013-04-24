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
echo '<table align="center" class="table_logo">
<tr>	
<td><span class="logo_name">Лекроция</span></td>
</tr>
</table>';
if($_SESSION['vleznal']==true)
{
include "options.php";
echo "<center>";
echo "<div class='content'>";
echo "<font color=white>--Гората--</font>";
include 'inventory.php';
//echo "<div style='float:left';width:400px;>";
echo "<br>";
echo "<font color=lightblue>Гората на ".$data['grad']." прикритие и дом на много създания, изгнаници и мистични<br> чудовища.Но ще откриете че не всеки път тя крие големи опасности,<br> по някой път гората помага на нуждаещите се от помощ и изгубили се<br> пътешественици и им поднася подарак.
<br><br>
Наистина ли сте готов за това приключение.Ако мислите че сте готов:<a href='fight.php'>разходете се</a><br>
ако не върнете се в града:<a href='index.php'>назад</a>
</font>";
//echo "</div>";
echo "</div>";
echo "</center>";
}
else
{
echo "<font color=red>Не си влязъл с профила си</font>";
}
?>
</body>
</html>
