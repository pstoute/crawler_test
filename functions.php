<?php
//********************************
//
// Config File
//
// Description: This is the functions page. These functions were not all created by me, although I don't recall where I retrieved them when I originally built this page.
// 
// Notice: If you are the author or know who the author is for one or more of these scripts please let me know so I can add the appropriate credit.
//
// Author: Paul Stoute
// Version: 0.1
// Date Revised: 06/04/2014
//
//********************************


//********************************************************************//
//Domain Prefix Fix
//********************************************************************//
function prefix_protocol($url, $prefix = 'http://')
						{
							if (!preg_match("~^(?:f|ht)tps?://~i", $url))
								{
									$url = $prefix . $url;
								}
							return $url;
						}
//********************************************************************//
//Domain Age Checker
//********************************************************************//
class DomainAge{
  private $WHOIS_SERVERS=array(
  "com"               =>  array("whois.verisign-grs.com","/Creation Date:(.*)/"),
  "net"               =>  array("whois.verisign-grs.com","/Creation Date:(.*)/"),
  "org"               =>  array("whois.pir.org","/Created On:(.*)/"),
  "info"              =>  array("whois.afilias.info","/Created On:(.*)/"),
  "biz"               =>  array("whois.neulevel.biz","/Domain Registration Date:(.*)/"),
  "us"                =>  array("whois.nic.us","/Domain Registration Date:(.*)/"),
  "uk"                =>  array("whois.nic.uk","/Registered on:(.*)/"),
  "ca"                =>  array("whois.cira.ca","/Creation date:(.*)/"),
  "tel"               =>  array("whois.nic.tel","/Domain Registration Date:(.*)/"),
  "ie"                =>  array("whois.iedr.ie","/registration:(.*)/"),
  "it"                =>  array("whois.nic.it","/Created:(.*)/"),
  "cc"                =>  array("whois.nic.cc","/Creation Date:(.*)/"),
  "ws"                =>  array("whois.nic.ws","/Domain Created:(.*)/"),
  "sc"                =>  array("whois2.afilias-grs.net","/Created On:(.*)/"),
  "mobi"              =>  array("whois.dotmobiregistry.net","/Created On:(.*)/"),
  "pro"               =>  array("whois.registrypro.pro","/Created On:(.*)/"),
  "edu"               =>  array("whois.educause.net","/Domain record activated:(.*)/"),
  "tv"                =>  array("whois.nic.tv","/Creation Date:(.*)/"),
  "travel"            =>  array("whois.nic.travel","/Domain Registration Date:(.*)/"),
  "in"                =>  array("whois.inregistry.net","/Created On:(.*)/"),
  "me"                =>  array("whois.nic.me","/Domain Create Date:(.*)/"),
  "cn"                =>  array("whois.cnnic.cn","/Registration Date:(.*)/"),
  "asia"              =>  array("whois.nic.asia","/Domain Create Date:(.*)/"),
  "ro"                =>  array("whois.rotld.ro","/Registered On:(.*)/"),
  "aero"              =>  array("whois.aero","/Created On:(.*)/"),
  "nu"                =>  array("whois.nic.nu","/created:(.*)/")
  );
  public function age($domain)
  {
  $domain = trim($domain); //remove space from start and end of domain
  if(substr(strtolower($domain), 0, 7) == "http://") $domain = substr($domain, 7); // remove http:// if included
  if(substr(strtolower($domain), 0, 4) == "www.") $domain = substr($domain, 4);//remove www from domain
  if(preg_match("/^([-a-z0-9]{2,100})\.([a-z\.]{2,8})$/i",$domain))
  {
  $domain_parts = explode(".", $domain);
  $tld = strtolower(array_pop($domain_parts));
  if(!$server=$this->WHOIS_SERVERS[$tld][0]) {
  return false;
  }
  $res=$this->queryWhois($server,$domain);
  if(preg_match($this->WHOIS_SERVERS[$tld][1],$res,$match))
  {
  date_default_timezone_set('UTC');
  $time = time() - strtotime($match[1]);
  $years = floor($time / 31556926);
  $days = floor(($time % 31556926) / 86400);
  if($years == "1") {$y= "1 year";}
  else {$y = $years . " years";}
  if($days == "1") {$d = "1 day";}
  else {$d = $days . " days";}
  return "$y, $d";
  }
  else
  return false;
  }
  else
  return false;
  }
  private function queryWhois($server,$domain)
  {
  $fp = @fsockopen($server, 43, $errno, $errstr, 20) or die("Socket Error " . $errno . " - " . $errstr);
if($server=="whois.verisign-grs.com")
$domain="=".$domain;
  fputs($fp, $domain . "\r\n");
  $out = "";
  while(!feof($fp)){
  $out .= fgets($fp);
  }
  fclose($fp);
  return $out;
  }
}
//********************************************************************//
//Google Page Rank Tool
//********************************************************************//
class PR {
 public function get_google_pagerank($url) {
 $query="http://toolbarqueries.google.com/tbr?client=navclient-auto&ch=".$this->CheckHash($this->HashURL($url)). "&features=Rank&q=info:".$url."&num=100&filter=0";
 $data=file_get_contents($query);
 $pos = strpos($data, "Rank_");
 if($pos === false){} else{
 $pagerank = substr($data, $pos + 9);
 return $pagerank;
 }
 }
 
 public function StrToNum($Str, $Check, $Magic)
 {
 $Int32Unit = 4294967296; // 2^32
 
 $length = strlen($Str);
 for ($i = 0; $i < $length; $i++) {
 $Check *= $Magic;
 
 if ($Check >= $Int32Unit) {
 $Check = ($Check - $Int32Unit * (int) ($Check / $Int32Unit));
 $Check = ($Check < -2147483648) ? ($Check + $Int32Unit) : $Check;
 }
 $Check += ord($Str{$i});
 }
 return $Check;
 }
 
 public function HashURL($String)
 {
 $Check1 = $this->StrToNum($String, 0x1505, 0x21);
 $Check2 = $this->StrToNum($String, 0, 0x1003F);
 
 $Check1 >>= 2;
 $Check1 = (($Check1 >> 4) & 0x3FFFFC0 ) | ($Check1 & 0x3F);
 $Check1 = (($Check1 >> 4) & 0x3FFC00 ) | ($Check1 & 0x3FF);
 $Check1 = (($Check1 >> 4) & 0x3C000 ) | ($Check1 & 0x3FFF);
 
 $T1 = (((($Check1 & 0x3C0) << 4) | ($Check1 & 0x3C)) <<2 ) | ($Check2 & 0xF0F );
 $T2 = (((($Check1 & 0xFFFFC000) << 4) | ($Check1 & 0x3C00)) << 0xA) | ($Check2 & 0xF0F0000 );
 
 return ($T1 | $T2);
 }
 
 public function CheckHash($Hashnum)
 {
 $CheckByte = 0;
 $Flag = 0;
 
 $HashStr = sprintf('%u', $Hashnum) ;
 $length = strlen($HashStr);
 
 for ($i = $length - 1; $i >= 0; $i --) {
 $Re = $HashStr{$i};
 if (1 === ($Flag % 2)) {
 $Re += $Re;
 $Re = (int)($Re / 10) + ($Re % 10);
 }
 $CheckByte += $Re;
 $Flag ++;
 }
 
 $CheckByte %= 10;
 if (0 !== $CheckByte) {
 $CheckByte = 10 - $CheckByte;
 if (1 === ($Flag % 2) ) {
 if (1 === ($CheckByte % 2)) {
 $CheckByte += 9;
 }
 $CheckByte >>= 1;
 }
 }
 
 return '7'.$CheckByte.$HashStr;
 }
}
//********************************************************************//
//Alexa Rank Checker
//********************************************************************//
class Get_Alexa_Ranking{
		
	/**
	 * Get the rank from alexa for the given domain
	 * 
	 * @param $domain
	 * The domain to search on
	 */
	public function get_rank($domain){
			
		$url = "http://data.alexa.com/data?cli=10&dat=snbamz&url=".$domain;
	  
		//Initialize the Curl  
		$ch = curl_init();  
		  
		//Set curl to return the data instead of printing it to the browser.  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,2); 
		  
		//Set the URL  
		curl_setopt($ch, CURLOPT_URL, $url);  
		  
		//Execute the fetch  
		$dataA = curl_exec($ch);  
		  
		//Close the connection  
		curl_close($ch);  
		
		$xml = new SimpleXMLElement($dataA);  

                //Get popularity node
		$popularity = $xml->xpath("//POPULARITY");

                //Get the Rank attribute
		$rank = (string)$popularity[0]['TEXT']; 
		
		return $rank;
	}

}
//********************************************************************//
//Google BackLinks
//********************************************************************//
function GoogleBL($domain){
$url="http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=link:".$domain."&filter=0";
$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt ($ch, CURLOPT_HEADER, 0);
curl_setopt ($ch, CURLOPT_NOBODY, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$json = curl_exec($ch);
curl_close($ch);
$data=json_decode($json,true);
if($data['responseStatus']==200)
return $data['responseData']['cursor']['resultCount'];
else
return false;
}
//********************************************************************//
//Google Indexed Pages
//********************************************************************//
function GoogleIP($domain){
$url="http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=site:".$domain."&filter=0";
$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt ($ch, CURLOPT_HEADER, 0);
curl_setopt ($ch, CURLOPT_NOBODY, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$json = curl_exec($ch);
curl_close($ch);
$data=json_decode($json,true);
if($data['responseStatus']==200)
return $data['responseData']['cursor']['resultCount'];
else
return false;
}
//********************************************************************//
//Get Share Count
//********************************************************************//
class shareCount {
private $url,$timeout;
function __construct($url,$timeout=20) {
$this->url=rawurlencode($url);
$this->timeout=$timeout;
}
function get_tweets() {
$json_string = $this->file_get_contents_curl('http://urls.api.twitter.com/1/urls/count.json?url=' . $this->url);
$json = json_decode($json_string, true);
return isset($json['count'])?intval($json['count']):0;
}
function get_linkedin() {
$json_string = $this->file_get_contents_curl("http://www.linkedin.com/countserv/count/share?url=$this->url&format=json");
$json = json_decode($json_string, true);
return isset($json['count'])?intval($json['count']):0;
}
function get_fb() {
$json_string = $this->file_get_contents_curl('http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls='.$this->url);
$json = json_decode($json_string, true);
return isset($json[0]['total_count'])?intval($json[0]['total_count']):0;
}
function get_plusones()  {
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"'.rawurldecode($this->url).'","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
$curl_results = curl_exec ($curl);
curl_close ($curl);
$json = json_decode($curl_results, true);
return isset($json[0]['result']['metadata']['globalCounts']['count'])?intval( $json[0]['result']['metadata']['globalCounts']['count'] ):0;
}
function get_stumble() {
$json_string = $this->file_get_contents_curl('http://www.stumbleupon.com/services/1.01/badge.getinfo?url='.$this->url);
$json = json_decode($json_string, true);
return isset($json['result']['views'])?intval($json['result']['views']):0;
}
function get_delicious() {
$json_string = $this->file_get_contents_curl('http://feeds.delicious.com/v2/json/urlinfo/data?url='.$this->url);
$json = json_decode($json_string, true);
return isset($json[0]['total_posts'])?intval($json[0]['total_posts']):0;
}
function get_pinterest() {
$return_data = $this->file_get_contents_curl('http://api.pinterest.com/v1/urls/count.json?url='.$this->url);
$json_string = preg_replace('/^receiveCount\((.*)\)$/', "\\1", $return_data);
$json = json_decode($json_string, true);
return isset($json['count'])?intval($json['count']):0;
}
private function file_get_contents_curl($url){
$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_FAILONERROR, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
$cont = curl_exec($ch);
if(curl_error($ch))
{
die(curl_error($ch));
}
return $cont;
}
}
//********************************************************************//
//Strong Password Generator
//********************************************************************//
function generate_password( $length = 8 ) {
$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
$password = substr( str_shuffle( $chars ), 0, $length );
return $password;
}
//********************************************************************//
//Password Strenght Calculator
//********************************************************************//
class PasswordStrength {
const STRENGTH_VERY_WEAK = 0;
const STRENGTH_WEAK = 1;
const STRENGTH_FAIR = 2;
const STRENGTH_STRONG = 3;
const STRENGTH_VERY_STRONG = 4;
 
public function classifyScore($score) {
if ($score < 0) return self::STRENGTH_VERY_WEAK;
if ($score < 60) return self::STRENGTH_WEAK;
if ($score < 70) return self::STRENGTH_FAIR;
if ($score < 90) return self::STRENGTH_STRONG;
 
return self::STRENGTH_VERY_STRONG;
}
 
public function classify($pw) {
return $this->classifyScore($this->calculate($pw));
}
 
/**
* Calculate score for a password
*
* @param string $pw the password to work on
* @return int score
*/
public function calculate($pw) {
$length = strlen($pw);
$score = $length * 4;
$nUpper = 0;
$nLower = 0;
$nNum = 0;
$nSymbol = 0;
$locUpper = array();
$locLower = array();
$locNum = array();
$locSymbol = array();
$charDict = array();
 
// count character classes
for ($i = 0; $i < $length; ++$i) {
$ch = $pw[$i];
$code = ord($ch);
 
/* [0-9] */ if ($code >= 48 && $code <= 57) { $nNum++; $locNum[] = $i; }
/* [A-Z] */ elseif ($code >= 65 && $code <= 90) { $nUpper++; $locUpper[] = $i; }
/* [a-z] */ elseif ($code >= 97 && $code <= 122) { $nLower++; $locLower[] = $i; }
/* . */ else { $nSymbol++; $locSymbol[] = $i; }
 
if (!isset($charDict[$ch])) {
$charDict[$ch] = 1;
}
else {
$charDict[$ch]++;
}
}
 
// reward upper/lower characters if pw is not made up of only either one
if ($nUpper !== $length && $nLower !== $length) {
if ($nUpper !== 0) {
$score += ($length - $nUpper) * 2;
}
 
if ($nLower !== 0) {
$score += ($length - $nLower) * 2;
}
}
 
// reward numbers if pw is not made up of only numbers
if ($nNum !== $length) {
$score += $nNum * 4;
}
 
// reward symbols
$score += $nSymbol * 6;
 
// middle number or symbol
foreach (array($locNum, $locSymbol) as $list) {
$reward = 0;
 
foreach ($list as $i) {
$reward += ($i !== 0 && $i !== $length -1) ? 1 : 0;
}
 
$score += $reward * 2;
}
 
// chars only
if ($nUpper + $nLower === $length) {
$score -= $length;
}
 
// numbers only
if ($nNum === $length) {
$score -= $length;
}
 
// repeating chars
$repeats = 0;
 
foreach ($charDict as $count) {
if ($count > 1) {
$repeats += $count - 1;
}
}
 
if ($repeats > 0) {
$score -= (int) (floor($repeats / ($length-$repeats)) + 1);
}
 
if ($length > 2) {
// consecutive letters and numbers
foreach (array('/[a-z]{2,}/', '/[A-Z]{2,}/', '/[0-9]{2,}/') as $re) {
preg_match_all($re, $pw, $matches, PREG_SET_ORDER);
 
if (!empty($matches)) {
foreach ($matches as $match) {
$score -= (strlen($match[0]) - 1) * 2;
}
}
}
 
// sequential letters
$locLetters = array_merge($locUpper, $locLower);
sort($locLetters);
 
foreach ($this->findSequence($locLetters, mb_strtolower($pw)) as $seq) {
if (count($seq) > 2) {
$score -= (count($seq) - 2) * 2;
}
}
 
// sequential numbers
foreach ($this->findSequence($locNum, mb_strtolower($pw)) as $seq) {
if (count($seq) > 2) {
$score -= (count($seq) - 2) * 2;
}
}
}
 
return $score;
}
 
/**
* Find all sequential chars in string $src
*
* Only chars in $charLocs are considered. $charLocs is a list of numbers.
* For example if $charLocs is [0,2,3], then only $src[2:3] is a possible
* substring with sequential chars.
*
* @param array $charLocs
* @param string $src
* @return array [[c,c,c,c], [a,a,a], ...]
*/
private function findSequence($charLocs, $src) {
$sequences = array();
$sequence = array();
 
for ($i = 0; $i < count($charLocs)-1; ++$i) {
$here = $charLocs[$i];
$next = $charLocs[$i+1];
$charHere = $src[$charLocs[$i]];
$charNext = $src[$charLocs[$i+1]];
$distance = $next - $here;
$charDistance = ord($charNext) - ord($charHere);
 
if ($distance === 1 && $charDistance === 1) {
// We find a pair of sequential chars!
if (empty($sequence)) {
$sequence = array($charHere, $charNext);
}
else {
$sequence[] = $charNext;
}
}
elseif (!empty($sequence)) {
$sequences[] = $sequence;
$sequence = array();
}
}
 
if (!empty($sequence)) {
$sequences[] = $sequence;
}
 
return $sequences;
}
}
?>