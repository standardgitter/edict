<?php
set_time_limit(0);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
//https://github.com/skywind3000/ECDICT/blob/master/stardict.7z?raw=true

/*
wc -l  conver2unix-stardict.csv 3402565
*/



@unlink('stardict.sqlite3');
$db = new SQLite3('stardict.sqlite3');
$query = $db->exec('create table stardict (word text collate nocase ,phonetic text,definition text,translation text,pos text,collins text,oxford text,tag text,bnc text ,frq text,exchange text,detail text,audio text)');
$db->exec('BEGIN;');
$statement = $db->prepare("insert into stardict (word,phonetic,definition,translation,pos,collins,oxford,tag,bnc,frq,exchange,detail,audio) values(:word,:phonetic,:definition,:translation,:pos,:collins,:oxford,:tag,:bnc,:frq,:exchange,:detail,:audio)");
$fp = fopen("conver2unix-stardict.csv","r");
$ffix=fopen("fix_stardict.csv","w");
$n=0;
while(!feof($fp)){
	$line = fgets($fp,10000);
	//print_r($arr);
	//die("====<br/>")	;
	$n++;
	if($n==1)
		continue;
	$arr=explode(',',str_replace('""',' ',$line));
	$count=count($arr);
	if($count<13){
  		//echo "=====".$line.'<br/>';
  		fprintf($ffix,"%s",$count,$line);
  		continue;
  }
  if($count>13){
  		//echo $line.'<br/>';
  		$arrB=array();
  		$j=0;
  		$left=false;
  		for($i=0; $i < $count; $i++){
  				if(strstr($arr[$i],'"') && $left==false){
  						$left = true;
  						$arrB[$j] =  ltrim($arr[$i],'"');
  						continue;
  				}
  				if(!strstr($arr[$i],'"') && $left==true){
  						$arrB[$j] .=  ','.ltrim($arr[$i],'"');
  						continue;
  				}
  				if(strstr($arr[$i],'"') && $left==true){
  						$arrB[$j] .=  ','.rtrim($arr[$i],'"');
  						$j++;
  						$left=false;
  						continue;
  				}
  				if(!strstr($arr[$i],'"') && $left==false){
  						$arrB[$j]=$arr[$i];
  						$j++;
  				}
  				
  		}
  		if(count($arrB) != 13){
  				//echo ">>>>>".$line.'<br/>';
  				fprintf($ffix,"%s",$line);
  				//echo ">>>>>".str_replace('""',' ',$line).'<br/>';
  		}else{
  			$count=13;
  			for($k=0; $k < 13; $k++){
  				$arr[$k]=$arrB[$k];
  				//echo ">>>>>".$arr[$i];
  			}
  		}
  		
  }
  	
	if($count!=13)
		continue;
  //echo $word.'<br/>'.$phone.'<br/>'.$tran.'<hr/>';
	$statement->bindValue(':word', ltrim($arr[0],"'"), SQLITE3_TEXT);
	$statement->bindValue(':phonetic', $arr[1], SQLITE3_TEXT);
	$statement->bindValue(':definition', $arr[2], SQLITE3_TEXT);
	$statement->bindValue(':translation', $arr[3], SQLITE3_TEXT);
	$statement->bindValue(':pos', $arr[4], SQLITE3_TEXT);
	$statement->bindValue(':collins', $arr[5], SQLITE3_TEXT);
	$statement->bindValue(':oxford', $arr[6], SQLITE3_TEXT);
	$statement->bindValue(':tag', $arr[7], SQLITE3_TEXT);
	$statement->bindValue(':bnc', $arr[8], SQLITE3_TEXT);
	$statement->bindValue(':frq', $arr[9], SQLITE3_TEXT);
	$statement->bindValue(':exchange', $arr[10], SQLITE3_TEXT);
	$statement->bindValue(':detail', $arr[11], SQLITE3_TEXT);
	$statement->bindValue(':audio', '', SQLITE3_TEXT);
	$results = $statement->execute();
	if($n%50000==0){
		$db->exec('COMMIT;');
		$db->exec('BEGIN;');
	}

}
$db->exec('COMMIT;');
$query = $db->exec('create index idx_word on stardict (word)');

$results = $db->query("select count(*) from stardict");
while ($result = $results->fetchArray(SQLITE3_ASSOC)) {
		echo 'total ==== '.$result['count(*)']." / $n<br/>";
}

$db->close();



?>