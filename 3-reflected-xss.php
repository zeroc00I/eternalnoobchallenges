<?php

function sanitize($lang){
if (preg_match('(<|>|!|`|&|%)', $lang) > 0) {
return 'en-US';
}
return $lang;
}

if(isset($_GET['lang'])){
	$language = sanitize($_GET['lang']);
}
else{
	$language='en-US';
}
?>
<script>
// Its not used anymore
function display_message(){
	var lang = "<?php echo $language?>";
	if(lang == "en-US"){
		var message = "Welcome!";
		return message	
	}
	var message = "Bem-vindo!";
	return message;
}

// just say hello, doesnt matter what language do you speak, english is universal LOL
document.write('<h1>Welcome hacker =)</h1>');
</script>
<br>
<img src="https://d4qkvw08lssf8.cloudfront.net/statics/img/drive/DOM-XSS-Zafiyeti.png">
