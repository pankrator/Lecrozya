/////////////////////////////////INVENTORY SCRIPTS

function itemadd(id,drop)
{
$.post("inv_action.php",{id:id,dropped:drop,action:"additem"},
function(data)
{
if(data.added!=-1)
$(".drop"+data.added).remove();
if(data.greshka!=-1)
alert(data.greshka);
$("div.table_info").html(data.table_info);
$("div.table_inventory").html(data.table_inventory);
if(data.duel==1)
updatedueltrade();
},"json");
}

function itemdrop(bag,slot)
{
$.post("inv_action.php",{slot:slot,bag:bag,action:"drop"},
function(data)
{
$("div.table_info").html(data.table_info);
$("div.table_inventory").html(data.table_inventory);
if(data.duel==1)
updatedueltrade();
},"json");
}

function showinv(number)
{
$.post("inv_action.php",{bag:number,action:"showinv"},
function(data)
{
$("div.table_info").html(data.table_info);
$("div.table_inventory").html(data.table_inventory);
},"json");
}

function unequip(id)
{
$.post("inv_action.php",{id:id,action:"unequip"},
function(data)
{
$("div.table_info").html(data.table_info);
$("div.table_inventory").html(data.table_invetory);
if(data.greshka!=-1)
alert(data.greshka);
if(data.duel==1)
updatedueltrade();
},"json");
}


function hid()
{
$("div.info_item").hide();
}


function item_info(idd,slot)
{
$.post("inv_action.php",{id:idd,action:"info",slot:slot},
function(data)
{
if(data.things!=-1)
thispos=$(".openinv").position();
$("div.info_item").stop().show().
animate({left:thispos.left-data.len-60,top:thispos.top,"width":data.len,"height":data.rows,opacity:"0.8"},200).html(data.things);
if(data.duel==1)
updatedueltrade();
},"json");
}


function doinv(idd,bag,slott)
{
$.post("inv_action.php",{id:idd,bag:bag,slot:slott,action:"use"},
function(data)
{
if(data.greshka!=-1)
alert(data.greshka);
$("div.table_info").html(data.table_info);
$("div.table_inventory").html(data.table_invetory);
if(data.duel==1)
updatedueltrade();
},"json");
}

//////////////////////////////////GENERAL JQUERY



///////////////////////////ADMIN PANEL SCRIPTS


function setname()
{
if($("#itemcreate_kachestvo").val()=="set")
$(".setname").show();
else
$(".setname").hide();
}


function createitem()
{
var i;
var svoistva="";
var stoinosti="";
for(i=1;i<=parseInt(document.getElementById("itemcreate_broisv").value);i+=1)
{
if(i!=1)
{
svoistva+=",";
stoinosti+=",";
}
svoistva+=$('#itemcreate_option'+i).val();
stoinosti+=$('#itemcreate_stoinost'+i).val();
}
$.post("admin.php",{jq:1,action:"itemcreate",svoistva:svoistva,stoinosti:stoinosti,kachestvo:$("#itemcreate_kachestvo").val(),levelreq:$("#itemcreate_levelreq").val(),name:$("#itemcreate_name").val(),type:$("#itemcreate_tip").val(),izrabotka_w:$("#itemcreate_izrabotka_w").val(),izrabotka_a:$("#itemcreate_izrabotka_a").val(),amount:$("#itemcreate_amount").val(),pic:$("#itemcreate_pic").val(),useset:$("#useset").val(),setname:$("#setname").val()},
function(data)
{
if(data.info!="")
alert(data.info);
if(data.created==true)
clearfield();
},"json");
}

function setpic(pic)
{
document.getElementById("itemcreate_pic").value="items/"+pic.id;
}


function setaddprop()
{
var field='';
var now=parseInt(document.getElementById("set_broisv").value);
now+=1;
document.getElementById("set_broisv").value=now;
field+='<span class="prop'+now+'">';
field+='<select id="setsvoistvo'+now+'">';
field+='<option value="attack">Сила</option>';
field+='<option value="maxhp">Живот</option>';
field+='<option value="armor">Защита</option>';
field+='<option value="maxmana">Мана</option>';
field+='</select>';
field+='<input type="text" id="setstoinost'+now+'">';
field+='<br></span>';
$(".setproperties").append(field);
}

function setdelprop()
{
var last=parseInt(document.getElementById("set_broisv").value);
if(last>-1)
{
$(".prop"+last).remove();
document.getElementById("set_broisv").value-=1;
}
}

function changeset()
{
var i;
var svoistva="";
var stoinosti="";
for(i=0;i<=parseInt(document.getElementById("set_broisv").value);i+=1)
{
if(i!=0)
{
svoistva+=",";
stoinosti+=",";
}
svoistva+=$('#setsvoistvo'+i).val();
stoinosti+=$('#setstoinost'+i).val();
}
$.post("admin.php",{jq:1,action:"changeset",name:$("#setime").val(),svoistva:svoistva,stoinosti:stoinosti},
function(data)
{
if(data.info!="")
alert(data.info);
},"json");
}

function remfromset(itemid)
{
$.post("admin.php",{jq:1,action:"remfromset",itemid:itemid,setname:$("#setime").val()},
function(data)
{
if(data.info!="")
alert(data.info);
},"json");
}

function addfield()
{
var field='';
var now=parseInt(document.getElementById("itemcreate_broisv").value);
now+=1;
document.getElementById("itemcreate_broisv").value=now;
field+='<span class="itemcreate_svoistva'+now+'">';
field+='Свойство:<select id="itemcreate_option'+now+'"><option value="attack">Атака</option><option value="maxhp">Живот</option>';
field+='<option value="maxmana">Мана</option>';
field+='<option value="armor">Защита</option>';
field+='</select>';
field+='Стойност:<input type="text" id="itemcreate_stoinost'+now+'">';
field+='<br></span>';
$('.options').append(field);
}

function deletefield()
{
var last=parseInt(document.getElementById("itemcreate_broisv").value);
if(last>0)
{
$(".itemcreate_svoistva"+last).remove();
document.getElementById("itemcreate_broisv").value-=1;
}
}

function show_item()
{
$.post("admin.php",{jq:1,action:"showitem",itemid:$("#userinfo_itemid").val()},
function(data)
{
if(data.info=="")
$(".iteminf").html(data.iteminfo);
else
{
alert(data.info);
$(".iteminf").html("");
}
},"json");
}

function addtoinv()
{
$.post("admin.php",{jq:1,action:"adminadditem",itemid:$("#userinfo_itemid").val(),user:$("#userinfo_lastuser").val()},
function(data)
{
if(data.info!="")
alert(data.info);
if(data.success==1)
$("div.table_info").html(data.stat_table);
},"json");
}


function showinvadmin(number)
{
$.post("admin.php",{jq:1,action:"showinv",bag:number,user:$("#userinfo_lastuser").val()},
function(data)
{
$("div.table_info").html(data.stat_table);
},"json");
}


function adminremove(bag,slot)
{
$.post("admin.php",{jq:1,action:"adminremove",bag:bag,slot:slot,user:$("#userinfo_lastuser").val()},
function(data)
{
$("div.table_info").html(data.stat_table);
if(data.info!="")
alert(data.info);
},"json");
}



function getstats()
{
$.post("admin.php",{jq:1,action:"userstats",user:$("#userinf").val()},
function(data)
{
if(data.info!="")
{
document.getElementById("userinfo_lastuser").value="";
alert(data.info);
}
else
{
$("div.table_info").html(data.stat_table);
document.getElementById("userinfo_lastuser").value=data.lastuser;
}
},"json");
}

function clearfield()
{
document.getElementById("itemcreate_name").value="";
var i;
for(i=1;i<=parseInt(document.getElementById("itemcreate_broisv").value);i+=1)
{
$(".itemcreate_svoistva"+i).remove();
}
document.getElementById("itemcreate_broisv").value="0";
document.getElementById("itemcreate_amount").value="";
document.getElementById("itemcreate_levelreq").value="";
document.getElementById("itemcreate_pic").value="";
}

function change()
{
switch($("#itemcreate_tip").val())
{
case "hppotion":
case "manapotion":
$(".hp_mana").show();
$(".options").hide();
$(".izrabotka_w").hide();
$(".izrabotka_a").hide();
break;
case "weapon":
$(".options").show();
$(".izrabotka_w").show();
$(".izrabotka_a").hide();
$(".hp_mana").hide();
break;
case "armor":
case "helmet":
$(".options").show();
$(".izrabotka_a").show();
$(".hp_mana").hide();
$(".izrabotka_w").hide();
break;
case "shield":
$("options").show();
$(".izrabotka_a").hide();
$(".hp_mana").hide();
$(".izrabotka_w").hide();
break;
}
}

function block()
{
$.post("admin.php",{jq:1,action:"block",user:$("#user").val(),vreme:$("#vreme").val()},
function(data)
{
if(data.info!=-1)
alert(data.info);
if(data.ok==true)
{
document.getElementById("user").value="";
document.getElementById("vreme").value="";
}
},"json");
}

function setinuse()
{
$.post("admin.php",{jq:1,action:"setinuse",set:$("#setinuse").val()},
function(data)
{
$(".setinfo").empty().html(data.setinfo);
},"json");
}

function townadd()
{
$.post("admin.php",{jq:1,action:"addtown",townname:$("#townname").val()},
function(data)
{
if(data.info!="")
alert(data.info);
document.getElementById("townname").value="";
},"json");
}

function change_razdel()
{
$(".itemcreate").hide();
$(".setoptions").hide();
$(".blockuser").hide();
$(".userinfo").hide();
$(".itemedit").hide();
$(".addtown").hide();
$("."+$("#deistvie").val()).show();
}

////////////////////////////////FIGHTING SCRIPTS

function doit(type)
{
//$(".loading").html("<img src='resourse/loadinfo.gif'>");
$.post("attack.php",{type:type,magic:$(".magic").val()},
function(data)
{
//$(".loading").html("");
$(".dmg").css("top",300).css("left",600).show().html(data.dmg).animate({top:100},600,function() { $(this).hide(); });
if(data.m_hpbar!=-1)
$(".m_hpbar").html(data.m_hpbar);
$("div.table_info").html(data.table_info);
$("div.table_inventory").html(data.table_invetory);
if(data.report!=-1)
$(".report").html(data.report);
$(".curses").html(data.curses);
if(data.dead==1)
$(".action").remove();
},"json");
}

function magic_info()
{
$.post("fight.php",{jq:1,action:"magicinfo",magic:$(".magic").val()},
function(data)
{
if(data.magicinfo!="")
$(".magic_info").html(data.magicinfo);
},"json");
}

function allreport()
{
$.post("fight.php",{jq:1},
function(data)
{
$(".report").html(data.report);
},"json");
}

function updatefight()
{
$.post("fight.php",{jq:1,action:"update"},
function(data)
{
////
},"json");
}


///////////////////////////////LEVELUP SCRIPTS

function  upgrade(stat)
{
$.post("levelup.php",{stat:stat,jq:1,action:"upgrade"},
function(data)
{
$(".table_info").html(data.table_info);
$("div.table_inventory").html(data.table_invetory);
if(data.greshka!=-1)
alert(data.greshka);
},"json");
}

function tren(hours)
{
$.post("levelup.php",{jq:1,hours:hours,action:"tren"},
function(data)
{
$(".table_info").html(data.table_info);
$("div.table_inventory").html(data.table_invetory);
if(data.greshka!=-1)
alert(data.greshka);
},"json");
}


///////////////CHAT SCRIPTS

function sendchat()
{
$.post("chat.php",{jq:1,action:"send",ot:$("#ot").val(),teksta:$("#teksta").val()},
function(data)
{
document.getElementById("teksta").value="";
if(data.info!="")
alert(data.info);
},"json");
}

function updatechat()
{
if($(".chat").is(":visible"))
{
$(".chatbuton").html('покажи чат');
$.post("chat.php",{jq:1,action:"update"},
function(data)
{
var bla;
if(document.getElementById("feed").scrollHeight-document.getElementById("feed").scrollTop<500)
bla=1;
$("#feed").append(data.chat);
if(bla==1)
document.getElementById("feed").scrollTop=document.getElementById("feed").scrollHeight;
},"json");
setTimeout('updatechat()',300);
}
else
{
$.post("chat.php",{jq:1,action:"info"},
function(data)
{
///////
if(data.ans==1)
$(".chatbuton").html('покажи чат <font color=yellow>ново!</font>');
else
setTimeout('updatechat()',300);
},"json");
}
}


function kirilica()
{
$.post("chat.php",{jq:1,action:"kiril"},
function(data)
{
if(data.kiril==1)
$(".kiril").stop(true,true).css("background-color","#86BF42").show().html("Кирилицата е включена").fadeOut(2000);
else if(data.kiril==0)
$(".kiril").stop(true,true).css("background-color","#F4255F").show().html("Кирилицата е изключена").fadeOut(2000);
},"json");
}


function showchat()
{
$(".chat").toggle("slow");
}

function whisp(user)
{
document.getElementById("teksta").focus();
document.getElementById("teksta").value="!"+user+" ";
}



///////////////////// TRAVELING SCRIPTS

function travel(townid,timetravel)
{
$.post("travel.php",{jq:1,action:"travel",townid:townid,timetravel:timetravel},
function(data)
{
//////
if(data.info!="")
alert(data.info);
},"json");
}



/////////////DUEL SCRIPTS

function duel(player2)
{
$.post("inn.php",{jq:1,player2:player2,action:"makeduel"},
function(data)
{
if(data.info!="")
alert(data.info);
},"json");
}

function goinduel(pl2,answer)
{
$.post("inn.php",{jq:1,player2:pl2,answer:answer,action:"goinduel"},
function(data)
{
if(data.info!="")
alert(data.info);
if(data.go==1)
window.location="duel.php";
},"json");
}

function iteminfo2(idd)
{
$.post("inv_action.php",{id:idd,action:"info"},
function(data)
{
if(data.things!=-1)
{
//thispos=$(".openinv").position();

$("div.infoitem").stop().show().
animate({"width":data.len,"height":data.rows,opacity:"0.8"},200).html(data.things);
}
},"json");
}


function ready()
{
$.post("duel.php",{jq:1,action:"ready"},
function(data)
{
updatedueltrade();
if(data.ready==1)
{
$(".zlato").remove();
$("div.infoitem").remove();
startduel();
}
},"json");
}

function updatedueltrade()
{
$.post("duel.php",{jq:1,action:"updatetrade",gold:$("#myzlato").val()},
function(data)
{
if(data.max!=-1)
document.getElementById("myzlato").value=data.max;
if(data.ready==1)
{
$(".zlato").remove();
$("div.infoitem").remove();
startduel();
}
else
{
$("span.trades").html(data.trades);
setTimeout('updatedueltrade()',2000);
}
},"json");
}

function itemtrade(id)
{
$.post("duel.php",{jq:1,action:"itemtotrade",itemid:id},
function(data)
{
/////////
updatedueltrade();
},"json");
}

function untrade(slot,id)
{
$.post("duel.php",{jq:1,action:"untrade",slot:slot,itemid:id},
function(data)
{
/////////
$("div.table_info").html(data.table_info);
$("div.table_inventory").html(data.table_invetory);
updatedueltrade();
},"json");
}

function startduel()
{
$.post("duel.php",{jq:1,action:"startduel"},
function(data)
{
$("span.trades").html(data.duelinfo);
updateduel();
},"json");
}


function updateduel()
{
$.post("duel.php",{jq:1,action:"updateduel"},
function(data)
{
$(".m_hpbar").html(data.m_hpbar);
$(".report").html(data.report);
$("div.table_info").html(data.table_info);
$("div.table_inventory").html(data.table_invetory);
setTimeout('updateduel()',400);
},"json");
}

function attack()
{
$.post("duel.php",{jq:1,action:"attack",type:"normal"},
function(data)
{
if(data.info!="")
alert(data.info);
},"json");
}


///////////////OFFERS SCRIPTS

function updateoffers()
{
$.post("inn.php",{jq:1,action:"updateoffers"},
function(data)
{
if(data.show!=0)
$("#ofs").show();
else
$("#ofs").hide();
$(".offers").html(data.offers);
if(data.send=="duel")
window.location="duel.php";
setTimeout('updateoffers()',1000);

},"json");
}

