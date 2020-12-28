<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Method:POST,GET");


if(!isset($_GET['w'])){
	die('bad request');
	
}



if(isset($_GET['t']) && $_GET['t']=='1')
	$word=trim($_GET['w']);
else
	$word=ltrim($_GET['w']).'%';

$db = new SQLite3('ecdict.sqlite3',SQLITE3_OPEN_READONLY);
$statement = $db->prepare("select word,phonetic,definition,translation,pos,collins,oxford,tag,bnc,frq,exchange,detail,audio from ecdict where word like :word");
$statement->bindValue(':word', $word, SQLITE3_TEXT);
$results = $statement->execute();
//die($sqlstr);
$content='[';
$i=0;
while ($result = $results->fetchArray(SQLITE3_ASSOC)) {
//   var_dump($result);rawurlencode
	$content.='{"w":"'.$result['word'].'","p":"'.$result['phonetic'].'","t":"'.str_replace('"','\"',$result['definition']).'\t'.str_replace('"','\"',$result['translation']).'","s":""},';

	if(++$i>=100)
		break;
}

$db->close();
$content=rtrim($content,',');
$content.=']';

die($content);

?>
