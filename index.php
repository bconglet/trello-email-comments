<?php 

/**
 *
 * To Generate New Key:
 * https://trello.com/1/authorize?key=YOURTRELLOKEYHERE&name=My+Application&expiration=never&response_type=token&scope=read,write
 *
 */


$trello_key = 'YOURTRELLOKEYHERE';
$trello_token = 'YOURTRELLOTOKENHERE';

if (isset($_POST) && $_POST != '') {

	$to = $_POST['envelope']['to'];
  $from = $_POST['envelope']['from'];
	$card = null;
	$subject = $_POST['headers']['Subject'];
	$body = ($_POST['reply_plain'] != '') ? $_POST['reply_plain'] : $_POST['plain'];
	if (stripos($to, 'custom-domain')) {
		//Get card from custom domain (Change this to your custom domain)
		preg_match('/(.*)@custom-domain.*/', $to, $card);
	} else {
		//Get card from default cloudmailin.com email address
		preg_match('/.*\+(.*)@.*/', $to, $card);
	}
	$card = $card[1];
	$url = "https://trello.com/1/cards/$card/actions/comments?key=$trello_key&token=$trello_token";

	$body .= PHP_EOL.PHP_EOL.'-------'.PHP_EOL.$from;

	// Test Response
	// file_put_contents('test.txt', "$to\n$from\n$subject\n$body\n$card\n$url");
	// die();

	if ($card != null) {

		$curl = curl_init();
		curl_setopt($curl,CURLOPT_URL,$url);
		curl_setopt($curl,CURLOPT_POSTFIELDS,"text=$body");
		curl_setopt($curl,CURLOPT_POST,1);
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
		$ch = curl_exec($curl);
		curl_close($curl);

	}


	// ADD ATTACHMENTS (if you set up S3 in your cloudmailin account)

	if ($_POST['attachments'] != '') {

		$attach_url = "https://trello.com/1/cards/$card/attachments?key=$trello_key&token=$trello_token";

		foreach ($_POST['attachments'] as $key => $value) {
			$attach = $value['url'];
			$attach_name = $value['file_name'];
			$curl = curl_init();
			curl_setopt($curl,CURLOPT_URL,$attach_url);
			curl_setopt($curl,CURLOPT_POSTFIELDS,"url=$attach&name=$attach_name");
			curl_setopt($curl,CURLOPT_POST,1);
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
			$ch = curl_exec($curl);
			curl_close($curl); 
		}
	}

}

 ?>