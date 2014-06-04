<?php
//********************************
//
// Crawling Script
//
// Description: This script pulls domains from your database and crawls the pages for new links. It then adds these links to the database to be searched again. This process is looped forever.
// Author: Paul Stoute
// Version: 0.1
// Date Revised: 06/04/2014
//
//********************************

// Include config file
require_once('config.php');

// Include functions
require_once('functions.php');
require_once('rel2abs.php');

// Query the crawlerlist table for domains
$result = mysqli_query($con,"SELECT domain FROM crawlerlist ORDER BY id");
	while($row = mysqli_fetch_array($result)) {
		$url = $row['domain'];
		
// Each domain is crawled to find href links
		$input = @file_get_contents($url);
		$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
		if(preg_match_all("/$regexp/siU", $input, $matches, PREG_SET_ORDER)) {

// Each link found is inserted into the crawllist table to be crawled
			foreach($matches as $match) { 
				$link = $match[2];
				if ($link != "") {
					echo $url."<br/>";
					echo "- ".$link."<br/>";
					// Convert Relative URLs to Absolute
					$link = url_to_absolute($url,$link);
					mysqli_query($con,"INSERT INTO crawlerlist (domain) VALUES ('$link')");
					echo "-- ". $link ." added to crawl DB.<br/>";
					// Convert url to host
					$linkConvert = parse_url($link, PHP_URL_HOST);
					$linkConvert = mysqli_real_escape_string($con, $linkConvert);
					if ($linkConvert != "") {
						// Get host IP from hostname
						$ip = gethostbyname($linkConvert);
						// Insert converted hostname and IP into new table
						mysqli_query($con,"INSERT INTO megalist (domain, ip) VALUES ('$linkConvert', '$ip')");
						echo "  ---  <br/>". $linkConvert ." converted successfully.<br/><br/>";
						mysqli_query($con,"DELETE FROM crawlerlist WHERE domain='$url'");
					}
				}
			}
		}
	}

// Include self to create forever loop
include('crawler.php');
//End of file
?>