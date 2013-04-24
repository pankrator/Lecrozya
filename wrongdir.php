<?php
if($_SESSION['vleznal']==true)
{
$data=mydata("inf_user","user",$_SESSION['ime']);
$data2=mydata("inf_user2","user",$_SESSION['ime']);
/*
if($data['blocked']!=-1)
{
if($data['blocked']<time() && $data['blocked']!=1)
update_data("inf_user","user",$_SESSION['ime'],"blocked",-1);
session_destroy();
header("Location:index.php");
}
update_data("inf_user2","user",$_SESSION['ime'],"online",time()+2*60);
*/
$page=substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);

if($data['fight']==1 && $page!="fight.php")
header("Location:fight.php");
else if($data2['duel']!=-1 && $page!="duel.php")
header("Location:duel.php");
else if($data2['travel']!="" && $page!="travel.php")
header("Location:travel.php");
}
?>