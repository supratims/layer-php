<?php
require_once 'Layer.php';

/**
 * Script to fetch messages from Layer
 */

// here we try to list all messages for a particular user
$username = 'test_user';

$layer = new Layer();

$token = $layer->sessionToken($username);

$list = $layer->listConversations($token);

foreach($list as $item){
	if (!empty($item['last_message']) && is_array($item['last_message'])){
		$messages = $item['last_message'];
		foreach($messages as $message){
			//debug : echo print_r($message, true);
			if (!is_array($message)){
				continue;
			}
			echo $message[0]['body'].PHP_EOL;
		}
	}
}



