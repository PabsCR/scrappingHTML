<?php

// We will need the phpQuery library.
include 'phpQuery-onefile.php';

// We need to find and load the html doc from our computer.
$file = 'htmltest.html';
$document = phpQuery::newDocumentFileHTML($file);

// We look into the paragraphs.
$paragraphs = pq('p');


// We iterate each paragraph looking for the elements we want (reservation data).
foreach ($paragraphs as $item)
{
	$html = pq($item)->html();
	if(stripos($html, 'Date:'))
	{
		$date = pq($item)->find('strong')->html();
	}
	if(stripos($html, 'Price:'))
	{
		$price = pq($item)->find('strong')->html();
	}
	if(stripos($html, 'participants:'))
	{
		$participants = pq($item)->find('strong')->html();
	}
	if(stripos($html, 'Option:'))
	{
		$product = pq($item)->find('strong')->html();
	}
	if(stripos($html, 'language:'))
	{
		$lang = pq($item)->find('strong')->html();
	}
	if (stripos($html, 'Main customer:'))
	{
		$customer = explode('<br>', $html);
	}
}

// We only need the int for the product code.
$newproduct = substr($product, stripos($product, '('), 8);
$finalproduct = str_replace('(', '', $newproduct);

// Split client data in substrings for each data.
$string_customer = implode('', $customer);

// Get the money type.
$iframeCurrency = substr($price, stripos($price, '='), 9);

// We only need the int from the telephone numer.
$customer_str = str_replace('Main customer:', '', $string_customer);
$customer_rpl = str_replace('Phone:', '', $customer_str);
$finalcustomer_str = str_replace(' ', '', $customer_rpl);
$finalcustomer_rmv = trim(preg_replace('/\s+/', ',', $finalcustomer_str));

// Make an array for client data.
$finalcustomer_arr = explode(',', $finalcustomer_rmv);
unset($finalcustomer_arr[0], $finalcustomer_arr[6]);
$finalcustomer = array_values($finalcustomer_arr);

// We isolate the int price from the string.
$finalprice_str = substr($price, stripos($price, ' '));
$finalprice = str_replace(' ', '', $finalprice_str);

// We have to get rid of the 'x' for getting only the number of people.
$finalparticipants = str_replace('x', '', $participants);

// Formating the date in order to get the timestamp.
$dateformat = str_replace(',', '', $date);
$dateformat_dash = str_replace(' ', '-', $dateformat);

$dateformat_str = substr_replace($dateformat_dash, '', -16);
$timestamp = strtotime($dateformat_str);

// Checking what locale code is going to be.
if($lang == 'Spanish')
{
	$lang = 'es_ES';
}
else 
{
	$lang = 'en_EN';
}

// In order to get the type of money we have to check the character code received in the html.
if($iframeCurrency == '=E2=82=AC')
{
	$iframeCurrency = 'EUR';
}

// We can put all the data in an array for doing wathever we want.
$paramsAdd = array(
		"productID"=>$finalproduct,
		"startDate"=>$timestamp, 
		"adultsNumber"=>$finalparticipants, 
		"timestamp"=>$timestamp,
		"lang"=>$lang, 
		"rates"=>$finalprice,
		"client"=>$finalcustomer);
print_r($paramsAdd);