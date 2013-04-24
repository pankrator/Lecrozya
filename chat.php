<?php
if($_POST['jq']==1)
{
include 'incs/functions.php';
$return=array();
$return['info']="";
switch($_POST['action'])
{

case "send":
$ok=1;
$ot=$_POST['ot'];
$ot=$_SESSION['ime'];
$teksta=htmlspecialchars($_POST['teksta']);
$teksta=mysql_real_escape_string($teksta);
/*
$newtekst=explode("http://",$teksta);
for($i=1;$i<=count($newtekst)-1;$i+=1)
{
$url=explode(" ",$newtekst[$i]);
$serch[$i-1]="http://".$url[0];
$rep[$i-1]="<a href=http://".$url[0].">LINK</a>";
}
$teksta=str_replace($serch,$rep,$teksta);
*/

if($_SESSION['kiril']==1)
{
$serch2=array("sht","iu","ch","Sht","Iu","Ch","Sh","sh","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","r","s","t","u","v","w","x","y","z","q","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
$rep2=array("щ","ю","ч","Щ","Ю","Ч","Ш","ш","а","б","ц","д","е","ф","г","х","и","й","к","л","м","н","о","п","р","с","т","у","ж","в","ь","ъ","з","я","А","Б","Ц","Д","Е","Ф","Г","Х","И","Й","К","Л","М","Н","О","П","Я","Р","С","Т","У","Ж","В","Ь","Ъ","З");
if(strpos($teksta,"!")===0)
{
$r=explode(" ",$teksta);
$space=strpos($teksta," ");
$teksta=substr($teksta,$space+1);
}
$newtekst=explode("[url]",$teksta);
$teksta="";
for($i=0;$i<=count($newtekst)-1;$i+=1)
{
if(substr_count($newtekst[$i],"[/url]")!=1)
$teksta.=str_replace($serch2,$rep2,$newtekst[$i]);
else
$teksta.="[url]".$newtekst[$i];
}
if(isset($r)) $teksta=$r[0]." ".$teksta;
}


$newtekst=explode("[url]",$teksta);
for($i=1;$i<=count($newtekst)-1;$i+=1)
{
$url=explode("[/url]",$newtekst[$i]);
if(count($url)<3 && substr_count($url[0],"http://")==1)
{
$serch[$i-1]="[url]".$url[0]."[/url]";
$rep[$i-1]="<a href=".$url[0].">".$url[0]."</a>";
}
else if(count($url<3) && substr_count($url[0],"http://")!=1)
{
$serch[$i-1]="[url]".$url[0]."[/url]";
$rep[$i-1]=$url[0];
}
}
$teksta=str_replace($serch,$rep,$teksta);



$do="";
if(strpos($teksta,"!")===0)
{
$space=strpos($teksta," ");
$do=substr($teksta,1,$space-1);
if(!check_field_exists("inf_user","user",$do) || $do==$_SESSION['ime'])
{ 
$me=$_SESSION['ime'];
//$return['info']="Няма такъв потребител";
mysql_query("INSERT INTO `chat` (`id`,`ot`,`teksta`,`do`) VALUES ('', 'system', ' Няма такъв потребител !!', '$me') ");
$ok=0;
}
if($ok!=0)
{
$dat=mydata("inf_user2","user",$do);
if($dat['online']<time())
{
$ok=0;
$me=$_SESSION['ime'];
//$return['info']="Този потребител не е в играта в момента";
mysql_query("INSERT INTO `chat` (`id`,`ot`,`teksta`,`do`) VALUES ('', 'system', ' Този потребител не е в играта в момента !!', '$me') ");
}
}
if(trim(substr($teksta,$space+1))=="")
$ok=0;
}
if(trim($teksta)=="")
$ok=0;
if($ok==1)
$sql = mysql_query("INSERT INTO `game`.`chat` (`id`,`ot`, `teksta`,do) VALUES ('','$ot','$teksta','$do')");
break;



case "update":
$ime=$_SESSION['ime'];
$sql = mysql_query("SELECT * FROM `chat` ");
$allres=mysql_num_rows($sql);
$chatline=$_SESSION['chatline'];
if($chatline!=$allres)
{
if($chatline!=1) $chatline++;
$sql = mysql_query("SELECT * FROM `chat` WHERE (`do`='$ime' OR `ot`='$ime' OR `do`='') AND `id`>='$chatline' ORDER BY `chat`.`id` ASC ");
while($r=mysql_fetch_array($sql))
{
if($r['do']=="")
{
$u=$r['ot'];
$usdata=mydata("inf_user","user",$u);
if($_SESSION['ime']==$r['ot'])
{
if($usdata['rang']=="administrator")
$return['chat'].="<br><font color=green size=4><b>[".$r['ot']."]</b>:".$r['teksta']."</font>";
else
$return['chat'].="<br><font size=4><b>[".$r['ot']."]</b>:".$r['teksta']."</font>";
}
else
{
if($usdata['rang']=="administrator")
$return['chat'].="<br><span onclick=whisp('$u')><font color=green size=4><b>[".$r['ot']."]</b>:".$r['teksta']."</font></span>";
else
$return['chat'].="<br><span onclick=whisp('$u')><font size=4><b>[".$r['ot']."]</b>:".$r['teksta']."</font></span>";
}
}
else if($r['do']==$_SESSION['ime'] || $r['ot']==$_SESSION['ime'])
{
$space=strpos($r['teksta']," ");
$tekst=substr($r['teksta'],$space+1);
if($r['do']!=$_SESSION['ime'])
$u=$r['do'];
else
$u=$r['ot'];
$return['chat'].="<br><span style='color:#B73CD9' onclick=whisp('$u')><b>[".$r['ot']."=>".$r['do']."]</b></span>:<font size=4 color=#B73CD9>".$tekst."</font>";
if($_SESSION['ime']==$r['do'])
update_data("chat","id",$r['id'],"seen","da");
}
}
$_SESSION['chatline']=$allres;
}
break;

case "info":
$ime=$_SESSION['ime'];
$sql = mysql_query("SELECT * FROM `chat` ");
$allres=mysql_num_rows($sql);
$chatline=$_SESSION['chatline'];
$sql=mysql_query("SELECT * FROM `chat` where `do`='$ime' ");
while($r=mysql_fetch_array($sql))
{
if($r['seen']!="da")
$return['ans']=1;
}
break;


case "kiril":
if($_SESSION['kiril']==1)
{
$_SESSION['kiril']=0;
$return['kiril']=0;
}
else
{
$return['kiril']=1;
$_SESSION['kiril']=1;
}
break;


}

echo json_encode($return);
}
else
{
//if($_SESSION['startline']+10<$allres)
//$_SESSION['startline']+=2;
$_SESSION['chatline']=$_SESSION['startline'];
//echo '<span style="color:red" onclick="showchat()">покажи чата</span><br>';
echo "<center>";
echo "<div class='kiril' style='position:absolute;top:200px;left:350px;background-color:#86BF42;border:2px solid #117287;color:black;z-index:130;font-size:30px;display:none'></div>";
echo '<div class="chat">
Ако искате да напишете нещо което да се вижда само от определен<br> човек в полето за съобщение сложете ! и след това името на потребителя
<br>
или кликнете с мишката върху неговото име от полето на чата<br>
Ако искате да поставите линк напишете [url]тук поставете линка[/url]
<div  id="feed"></div>
Съобщение:
<br>
<input type="text" id="teksta"><input type="button" id="tbut" onclick="sendchat()" value="изпрати">
<input type="button" value="Кирилица" onclick="kirilica()">
<input type="hidden" id="ot" value='.$_SESSION["ime"].'>
</div>';
echo "</center>";
}
?>