<?php

// - BEGIN transaction list -------------------------------------------------------

/*
Shows the last transactions of an account_name.

V.1.0 (15.aug.2018) - sven.pohl@zen-systems.de

based on:
curl --request POST  --url https://eos.greymass.com/v1/history/get_actions  --data '{"account_name":"cryptobeings","pos":0,"offset":200, "json":true }'

*/

// CONFIGURATION ----------------------------------------

$account_name = "eosnewyorkio";
$max_limit = 4;

/*
Important! Not all BP's activated the nodeos- history module. In case of inactive history-
module the get_actions call fails.
*/
$url = "https://eos.greymass.com/v1/history/get_actions";

$DEBUG = 0;

// END CONFIGURATION ------------------------------------

print("<hr>");
print("<h2>Thank you 💐 for last donations:  (to <a href='https://eosflare.io/account/$account_name' target='_blank'>$account_name</a>) </h1>");

/*
Step 1:
Get Number of transactions
*/
$data = array("account_name" => $account_name,  "json" => true);

$data_string = json_encode($data);

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");    			
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 105);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 105);

$file_contents = curl_exec($ch);
curl_close($ch);    
 
$json_array = json_decode($file_contents, true);

$cnt = count($json_array['actions']);
$account_action_seq = $json_array['actions'][($cnt-1)]['account_action_seq'];

if ($DEBUG)
   {
   print("Count: $cnt <br>");
   print("account_action_seq: $account_action_seq <br>");
   }


/*
Step 1:
Get the last transactions
*/

$data = array("account_name" => $account_name, "pos" => ($account_action_seq-($max_limit-1)), "offset" => ($max_limit-1),  "json" => true);
$data_string = json_encode($data);

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");    			
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 105);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 105);

$file_contents = curl_exec($ch);
curl_close($ch);    
 
$json_array = json_decode($file_contents, true);

$cnt = count($json_array['actions']);


if ($DEBUG)
{
print("<pre>");
print_r($json_array);
print("</pre>");
}

for ($i=0; $i<$max_limit; $i++)
{
$name     = $json_array['actions'][$i]['action_trace']['act']['name'];
$from     = $json_array['actions'][$i]['action_trace']['act']['data']['from'];
$to       = $json_array['actions'][$i]['action_trace']['act']['data']['to'];
$quantity = $json_array['actions'][$i]['action_trace']['act']['data']['quantity'];

$memo = $json_array['actions'][$i]['action_trace']['act']['data']['memo'];

if ($name == 'transfer' && $to == $account_name)
   {
   print("<strong>".$quantity . "</strong> | memo: <em>[".$memo."]</em> <br>"  );
   }
} // for i..max_limit


print("<hr>");

// - END transaction list -------------------------------------------------------

?>