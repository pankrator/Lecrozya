<?php
include 'incs/functions.php';
if($_SESSION['vleznal']!=true)
{
$user=htmlspecialchars($_POST['user']);
$user=addslashes($user);
$user=mysql_real_escape_string($user);
$pass=md5($_POST['pass']);
$sql = mysql_query("SELECT * FROM `inf_user` WHERE `user` = '$user' AND `pass` = '$pass' ");
if($r=mysql_fetch_array($sql))
{
$_SESSION['vleznal']=true;
$_SESSION['ime']=$user;
$data=mydata("inf_user","user",$_SESSION['ime']);
if($data['blocked']<time() && $data['blocked']!=1)
{
$sql = mysql_query("SELECT * FROM `chat` ");
$allres=mysql_num_rows($sql);
$_SESSION['kiril']=0;
$_SESSION['startline']=$allres;
$_SESSION['chatline']=$allres;
$_SESSION['invshow']=-1;
update_data("inf_user","user",$_SESSION['ime'],"blocked",-1);
header("Location:index.php");
}
else
{
session_destroy();
$mins=floor((time()-$data['blocked'])/60);
echo "Този потребител е блокиран за още ".$mins." минути";
}
}
else
{
echo "Грешно име или парола";
}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<title>Lecrozya</title>
</head>
</html>