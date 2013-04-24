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
</head>
<body>
<?php
if($_SESSION['vleznal']==true)
{
$tip=$_GET['type'];
$data=mydata("inf_user","user",$_SESSION['ime']);
switch($tip)
{
case "ressurect":
if (!check_fight() && $data['hp']<1)
{
$cena=$data['maxhp']+$data['armor']*$data['level'];
if($data['zlato']>=$cena)
{
update_data("inf_user","user",$_SESSION['ime'],"hp",$data['maxhp']);
update_data("inf_user","user",$_SESSION['ime'],"zlato",$data['zlato']-$cena);
echo "Ти беше възроден за ".$cena." злато. <a href='index.php'>назад</a>";
}
else echo "Нямаш достатъчно злато, за да се съживиш <a href='index.php'>назад</a>";
}
else
{
echo "Опит за пробив";
}
break;
}
}
?>
</body>
</html>