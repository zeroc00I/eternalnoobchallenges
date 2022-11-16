<?php
$search_string = $_GET['search'];

if(!isset($search_string)){
	echo 'Please provide search parameter';
	return true;
}

$tags_into_denylist = ["a","a2","abbr","acronym","address","animate","animatemotion","animatetransform","applet","area","article","aside","audio","audio2","b","bdi","bdo","big","blink","blockquote","body","br","button","canvas","caption","center","cite","code","col","colgroup","command","content","custom tags","data","datalist","dd","del","details","dfn","dialog","dir","div","dl","dt","element","em","embed","fieldset","figcaption","figure","font","footer","form","frame","frameset","h1","head","header","hgroup","hr","html","i","iframe","iframe2","image","image2","image3","img","img2","input","input2","input3","input4","ins","kbd","keygen","label","legend","li","link","listing","main","map","mark","marquee","menu","menuitem","meta","meter","multicol","nav","nextid","nobr","noembed","noframes","noscript","object","ol","optgroup","option","output","p","param","picture","plaintext","pre","progress","q","rb","rp","rt","rtc","ruby","s","samp","script","section","select","set","shadow","slot","small","source","spacer","span","strike","strong","style","sub","summary","sup","svg","table","tbody","td","template","textarea","tfoot","th","thead","time","title","tr","track","tt","u","ul","var","video","video2","wbr","xmp"];

foreach ($tags_into_denylist as $tag) {
	$result = stripos($search_string,"<".$tag);
	if($result!==false){
		print("DEBUG:BLOCKED!<br>");
		print("<br>[Sysadmin] hahaha i got you, hacker :)");
		print("<br>[Sysadmin] Its not allowed starting strings with '$tag'!");
		die();
	}
}

print("<img width='300px' height=100px src='https://www.looper.com/img/gallery/things-only-adults-notice-in-hackers/l-intro-1628777205.jpg'><br><br>");
print("<br><b>[Dumb_Zeroc00i] So you are again here.. damn hacker.</b>");
print("<br><b>[Dumb_Zeroc00i] Could u trigger a xss on this page using this reflected parameter?</b>");
print("<br><br>[Sysadmin] I did recently some updates on my WAF, and i havent detected any danger on your input");
print("<br>[Sysadmin] Suck it damn hacker, here it is your evil dark supreme payload LOL : ");
print($search_string);
