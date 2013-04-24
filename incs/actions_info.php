<?php
$data2=mydata("inf_user2","user",$_SESSION['ime']);
echo '<div class="offers" style="display:none;position:absolute;z-index:100;top:100px;left:900px;width:260px;">';
echo '<table align="right" style="background-color:rgb(99,109,63);color:white;border:4px inset green;"  class="action_info">';
$duels=explode(",",$data2['duel_with']);
if(count($duels)>0 && $duels[0]!="")
{
echo '<tr><th>Предложения за дуели</th></tr>';
for($i=0;$i<=count($duels)-1;$i++)
{
$pl2=$duels[$i];
echo "<tr><td>".$duels[$i]." <span onclick=goinduel('$pl2','1') style='background-color:green;color:white;'>Приеми</span> <span onclick=goinduel('$pl2','0') style='background-color:red;color:white;'>Откажи</span></td></tr>";
}
}
echo '</table>';
echo '</div>';
?>