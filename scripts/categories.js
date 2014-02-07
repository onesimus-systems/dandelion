function pop_cat_2(o)
{
    d=document.getElementById('cat_2');
    if(!d){return;}			
    var mitems=new Array();
    mitems['Desktop']=[''];
    mitems['Appliances']=['Select:','Struxureware','bomgar','campusmgr','copysense','dr4000','fish1','fish2','hr_ibm6400','kace','procerastat','wlc','wlse'];
    mitems['Network']=['Select:','MonitorLogs','Nexus1','Nexus2','admin','housing','ilight','labs','power','storage','unix'];
    mitems['Servers']=['Select:','novell','unix','VMservers','windows'];
    mitems['UPS']=[''];
    d.options.length=0;
    cur=mitems[o.options[o.selectedIndex].value];
    if(!cur){return;}
    d.options.length=cur.length;
    for(var i=0;i<cur.length;i++)
    {
        d.options[i].text=cur[i];
        d.options[i].value=cur[i];
    }
}

function pop_cat_3(o)
{
    d=document.getElementById('cat_3');
    if(!d){return;}			
    var mitems=new Array();
    mitems['novell']=['Select:','academic','archive','arcserver','garcon','gw2ucs','printing','pub','ucs'];
    mitems['unix']=['Select:','linux','sun'];
    mitems['VMservers']=['Select:','admin','labs'];
    mitems['windows']=['Select:','Best','FIM','PMIC','Sequoia','Titanium','WhatsUpVer16','acorde','act1000','ad','adc2k8v1','adc40','adc111','adc7','adfs','advtrac','amrtg','appworx','atlas','atlas2','authentica1','authentica2','avg8master','awhatsup','backdns','bberry','bbtsusi','bbtsusidb','beetlejuz','buelto4','buelto5','cecil','ceo','certserver','coblabssql','cognos','costcalc','counselsvr','darwin','dataone','delphi','dhcp110','docsa','docsb','docsc','docxfs','email','emailnew','ems2','emschedule','envision','epos','extend','extserv','fim','hbs1','helptrak','housingdns','hpdc','hpdentalclinic','hwhatsup','images','instrsh','ipswitch','johnson','la0118-media','labsprnone','labsupdate','labtape','ldhcp','license1','live','lmrtg','lwhatsup','mydocs','netmonitorpc','psserv','pub','quant','rmsdata','rmstest','rmsweb','sanjuan','scantron','scheduler','secserv','snhp7','snhpexam','sql2k8','student1','student2','swistem','t-serv','t-serve','testatlas','testprn','tranesql','updates','usiwebserver','vianet','web4alumni','win2k3a','win2kdns','winprn32','winprnone','work4usi','workforce','www','www2k8','wwwdev','wwwprod','xerox','yawin2k','yawin2k2'];
    mitems['storage']=['Select:','6140','brocade_LA','brocade_orr','equalogic'];
    mitems['labs']=['Select:','buseng','fa','hp','l-netmri','l6509','la','lab4507r','lcore_switches','lib','lmars','lpix','lpl7600','lps4500','orr','pac','rfc','sc','scedu','tc','uc','wireless'];
    mitems['admin']=['Select:','a-netmri','a6509','amars','apix','apl7600','aps6500','buseng','core_switches','fa','housing','hp','la','lib','orr','pac-rfc','pp-grounds','pub-child','sc','scedu','security','tc','uc','vpn','wa','wireless'];
    mitems['housing']=['Select:','core_switches','governors','h-netmri','h4507core','h6509','hmars','hpix','hpl7600','hps6500isp','jackson','newman','obannon','odan_north','odan_south','ruston','willard','wireless'];
    d.options.length=0;
    cur=mitems[o.options[o.selectedIndex].value];
    if(!cur){return;}
    d.options.length=cur.length;
    for(var i=0;i<cur.length;i++)
    {
        d.options[i].text=cur[i];
        d.options[i].value=cur[i];
    }
}

function pop_cat_4(o)
{
    d=document.getElementById('cat_4');
    if(!d){return;}			
    var mitems=new Array();
    mitems['FIM']=['Select:','usifimserv2'];
    mitems['adfs']=['Select:','adfsproxy15'];
    mitems['fim']=['Select:','usi-fimserv2'];
    mitems['admin']=['Select:','vm4pmic','vm4server0','vm4server1'];
    mitems['labs']=['Select:','vm4serverlabs'];
    mitems['linux']=['Select:','amrtg','auction','basket','blosxom','bmx','cacti','callusi','campuseai','centos1','citrix','collabra','extapps','faq','hmrtg','HousingDNS','hsnort','intproc','ipaudit','learn','lime','linux','lists','lmrtg','login','lproxy','lsnort','misc','my','netreg','netsaint','netxen1','netxen2','netxen3','ninja','ninja2','ninja3','onemysql','pamela','pipeline','radA','radH','radL','reqtrack','resreg','sandbox','sandbox2','tangelo','scaccess','tgkarnac','tk20','tk20app','tk20db','usibr01','usibr02','util'];
    mitems['sun']=['Select:','6140','Blade','apocalypse','baninb','banprod','bantest','cognux','cogprod','content','coolstack','credit','drmlg','eai','eaimail','inb','jump','lamplite','library','lite','lnximag1','logjam','lum333','mail','myusi','newlibrary','newmail','ods','ods2','odsprod','oldmail','oraman','sctap1','sctap2','sctap3','sctdbms','sctdbms2','sctinb','sctssb','scttest','service2','services','testcp','testinb','testlum','testlum4','testmx','testods','testssb','thumper'];
    mitems['wireless']=['Select:','e51101-1','e5110-2','l-hipath','l-hipath2','h-hipath','h-hipath2','prime','wlc'];
    mitems['buseng']=['Select:','a4200','a4507r','switches','l4200'];
    mitems['core_switches']=['Select:','campus4507'];
    mitems['fa']=['Select:','switches'];
    mitems['housing']=['Select:','switches'];
    mitems['hp']=['Select:','a4507','l4006','switches'];
    mitems['la']=['Select:','a4507','l4006','switches'];
    mitems['lib']=['Select:','lib4507','l4507R','switches'];
    mitems['orr']=['Select:','a4507','l4006','switches'];
    mitems['pac']=['Select:','switches'];
    mitems['rfc']=['Select:','switches'];
    mitems['sc']=['Select:','a4507','switches'];
    mitems['scedu']=['Select:','a4507','l4006','switches'];
    mitems['tc']=['Select:','switches'];
    mitems['uc']=['Select:','uc3550','uc4006','uc4200','uclabs4200','switches'];
    mitems['wa']=['Select:','a4507','switches'];
    mitems['governors']=['Select:','switches'];
    mitems['newman']=['Select:','switches'];
    mitems['obannon']=['Select:','switches'];
    mitems['ruston']=['Select:','switches'];
    mitems['jackson']=['Select:','3550','j4006','switches'];
    mitems['odan_south']=['Select:','ods4006','switches'];
    mitems['odan_north']=['Select:','odn4006','switches'];
    mitems['willard']=['Select:','3550','w4006','switches'];
    d.options.length=0;
    cur=mitems[o.options[o.selectedIndex].value];
    if(!cur){return;}
    d.options.length=cur.length;
    for(var i=0;i<cur.length;i++)
    {
        d.options[i].text=cur[i];
        d.options[i].value=cur[i];
    }
}

function pop_cat_5(o)
{
    d=document.getElementById('cat_5');
    if(!d){return;}			
    var mitems=new Array();
    mitems['bantest']=['Select:','inbx'];
    mitems['content']=['Select:','faculty','media','student'];
    mitems['services']=['Select:','clnaddr'];
    mitems['basket']=['Select:','vauction','vbanrftq','vbmx','vcredit','vlists','vpipelin','vutil'];
    mitems['citrix']=['Select:','lemon','lime','satsuma','tangelo'];
    mitems['tangelo']=['Select:','it'];
    mitems['e51101-1']=['Select:','e5510-173'];
    d.options.length=0;
    cur=mitems[o.options[o.selectedIndex].value];
    if(!cur){return;}
    d.options.length=cur.length;
    for(var i=0;i<cur.length;i++)
    {
        d.options[i].text=cur[i];
        d.options[i].value=cur[i];
    }
}
//----------------------------------------------------------------------------------//
function f_pop_cat_2(o)
{
    d=document.getElementById('f_cat_2');
    if(!d){return;}			
    var mitems=new Array();
    mitems['Desktop']=[''];
    mitems['Appliances']=['Select:','Struxureware','bomgar','campusmgr','copysense','dr4000','fish1','fish2','hr_ibm6400','kace','procerastat','wlc','wlse'];
    mitems['Network']=['Select:','MonitorLogs','Nexus1','Nexus2','admin','housing','ilight','labs','power','storage','unix'];
    mitems['Servers']=['Select:','novell','unix','VMservers','windows'];
    mitems['UPS']=[''];
    d.options.length=0;
    cur=mitems[o.options[o.selectedIndex].value];
    if(!cur){return;}
    d.options.length=cur.length;
    for(var i=0;i<cur.length;i++)
    {
        d.options[i].text=cur[i];
        d.options[i].value=cur[i];
    }
}

function f_pop_cat_3(o)
{
    d=document.getElementById('f_cat_3');
    if(!d){return;}			
    var mitems=new Array();
    mitems['novell']=['Select:','academic','archive','arcserver','garcon','gw2ucs','printing','pub','ucs'];
    mitems['unix']=['Select:','linux','sun'];
    mitems['VMservers']=['Select:','admin','labs'];
    mitems['windows']=['Select:','Best','FIM','PMIC','Sequoia','Titanium','WhatsUpVer16','acorde','act1000','ad','adc2k8v1','adc40','adc111','adc7','adfs','advtrac','amrtg','appworx','atlas','atlas2','authentica1','authentica2','avg8master','awhatsup','backdns','bberry','bbtsusi','bbtsusidb','beetlejuz','buelto4','buelto5','cecil','ceo','certserver','coblabssql','cognos','costcalc','counselsvr','darwin','dataone','delphi','dhcp110','docsa','docsb','docsc','docxfs','email','emailnew','ems2','emschedule','envision','epos','extend','extserv','fim','hbs1','helptrak','housingdns','hpdc','hpdentalclinic','hwhatsup','images','instrsh','ipswitch','johnson','la0118-media','labsprnone','labsupdate','labtape','ldhcp','license1','live','lmrtg','lwhatsup','mydocs','netmonitorpc','psserv','pub','quant','rmsdata','rmstest','rmsweb','sanjuan','scantron','scheduler','secserv','snhp7','snhpexam','sql2k8','student1','student2','swistem','t-serv','t-serve','testatlas','testprn','tranesql','updates','usiwebserver','vianet','web4alumni','win2k3a','win2kdns','winprn32','winprnone','work4usi','workforce','www','www2k8','wwwdev','wwwprod','xerox','yawin2k','yawin2k2'];
    mitems['storage']=['Select:','6140','brocade_LA','brocade_orr','equalogic'];
    mitems['labs']=['Select:','buseng','fa','hp','l-netmri','l6509','la','lab4507r','lcore_switches','lib','lmars','lpix','lpl7600','lps4500','orr','pac','rfc','sc','scedu','tc','uc','wireless'];
    mitems['admin']=['Select:','a-netmri','a6509','amars','apix','apl7600','aps6500','buseng','core_switches','fa','housing','hp','la','lib','orr','pac-rfc','pp-grounds','pub-child','sc','scedu','security','tc','uc','vpn','wa','wireless'];
    mitems['housing']=['Select:','core_switches','governors','h-netmri','h4507core','h6509','hmars','hpix','hpl7600','hps6500isp','jackson','newman','obannon','odan_north','odan_south','ruston','willard','wireless'];
    d.options.length=0;
    cur=mitems[o.options[o.selectedIndex].value];
    if(!cur){return;}
    d.options.length=cur.length;
    for(var i=0;i<cur.length;i++)
    {
        d.options[i].text=cur[i];
        d.options[i].value=cur[i];
    }
}

function f_pop_cat_4(o)
{
    d=document.getElementById('f_cat_4');
    if(!d){return;}			
    var mitems=new Array();
    mitems['FIM']=['Select:','usifimserv2'];
    mitems['adfs']=['Select:','adfsproxy15'];
    mitems['fim']=['Select:','usi-fimserv2'];
    mitems['admin']=['Select:','vm4pmic','vm4server0','vm4server1'];
    mitems['labs']=['Select:','vm4serverlabs'];
    mitems['linux']=['Select:','amrtg','auction','basket','blosxom','bmx','cacti','callusi','campuseai','centos1','citrix','collabra','extapps','faq','hmrtg','hsnort','intproc','ipaudit','learn','lime','linux','lists','lmrtg','login','lproxy','lsnort','misc','my','netreg','netsaint','netxen1','netxen2','netxen3','ninja','ninja2','ninja3','onemysql','pamela','pipeline','radA','radH','radL','reqtrack','resreg','sandbox','sandbox2','tangelo','scaccess','tgkarnac','tk20','tk20app','tk20db','usibr01','usibr02','util'];
    mitems['sun']=['Select:','6140','Blade','apocalypse','baninb','banprod','bantest','cognux','cogprod','content','coolstack','credit','drmlg','eai','eaimail','inb','jump','lamplite','library','lite','lnximag1','logjam','lum333','mail','myusi','newlibrary','newmail','ods','ods2','odsprod','oldmail','oraman','sctap1','sctap2','sctap3','sctdbms','sctdbms2','sctinb','sctssb','scttest','service2','services','testcp','testinb','testlum','testlum4','testmx','testods','testssb','thumper'];
    mitems['wireless']=['Select:','e51101-1','e5110-2','l-hipath','l-hipath2','h-hipath','h-hipath2','prime','wlc'];
    mitems['buseng']=['Select:','a4200','a4507r','switches','l4200'];
    mitems['core_switches']=['Select:','campus4507'];
    mitems['fa']=['Select:','switches'];
    mitems['housing']=['Select:','switches'];
    mitems['hp']=['Select:','a4507','l4006','switches'];
    mitems['la']=['Select:','a4507','l4006','switches'];
    mitems['lib']=['Select:','lib4507','l4507R','switches'];
    mitems['orr']=['Select:','a4507','l4006','switches'];
    mitems['pac']=['Select:','switches'];
    mitems['rfc']=['Select:','switches'];
    mitems['sc']=['Select:','a4507','switches'];
    mitems['scedu']=['Select:','a4507','l4006','switches'];
    mitems['tc']=['Select:','switches'];
    mitems['uc']=['Select:','uc3550','uc4006','uc4200','uclabs4200','switches'];
    mitems['wa']=['Select:','a4507','switches'];
    mitems['governors']=['Select:','switches'];
    mitems['newman']=['Select:','switches'];
    mitems['obannon']=['Select:','switches'];
    mitems['ruston']=['Select:','switches'];
    mitems['jackson']=['Select:','3550','j4006','switches'];
    mitems['odan_south']=['Select:','ods4006','switches'];
    mitems['odan_north']=['Select:','odn4006','switches'];
    mitems['willard']=['Select:','3550','w4006','switches'];
    d.options.length=0;
    cur=mitems[o.options[o.selectedIndex].value];
    if(!cur){return;}
    d.options.length=cur.length;
    for(var i=0;i<cur.length;i++)
    {
        d.options[i].text=cur[i];
        d.options[i].value=cur[i];
    }
}

function f_pop_cat_5(o)
{
    d=document.getElementById('f_cat_5');
    if(!d){return;}			
    var mitems=new Array();
    mitems['bantest']=['Select:','inbx'];
    mitems['content']=['Select:','faculty','media','student'];
    mitems['services']=['Select:','clnaddr'];
    mitems['basket']=['Select:','vauction','vbanrftq','vbmx','vcredit','vlists','vpipelin','vutil'];
    mitems['citrix']=['Select:','lemon','lime','satsuma','tangelo'];
    mitems['tangelo']=['Select:','it'];
    mitems['e51101-1']=['Select:','e5510-173'];
    d.options.length=0;
    cur=mitems[o.options[o.selectedIndex].value];
    if(!cur){return;}
    d.options.length=cur.length;
    for(var i=0;i<cur.length;i++)
    {
        d.options[i].text=cur[i];
        d.options[i].value=cur[i];
    }
}