<?php
require_once 'Layer.php';

/**
 * Sample client to send message to Layer
 */

// We create a test user, but this could be a user in your application
$username = 'test_user';

$layer = new Layer();
// Create a session token first
$session_token = $layer->sessionToken($username);

// Start a conversation and create a chat id
$chat_id = $layer->startConversation($session_token, $username);

// Build your message
$message = '['.date(DATE_RFC2822).'] Hello World !'; // Or read this from standard input

// Send message to layer
$result = $layer->sendMessage($session_token, $chat_id, $message);

//echo print_r($result, true);
//data structure of result
/*
{"id":"layer:///messages/1c0af71e-a5a9-468c-8b9b-91023dd3e252",
"url":"https://api.layer.com/messages/1c0af71e-a5a9-468c-8b9b-91023dd3e252",
"receipts_url":"https://api.layer.com/messages/1c0af71e-a5a9-468c-8b9b-91023dd3e252/receipts",
"position":1345781760,
"conversation":
	{
		"id":"layer:///conversations/e6fcb323-8687-435e-beb0-08269930b1bc",
		"url":"https://api.layer.com/conversations/e6fcb323-8687-435e-beb0-08269930b1bc"
	},
	"parts":[{"id":"layer:///messages/1c0af71e-a5a9-468c-8b9b-91023dd3e252/parts/0","mime_type":"text/plain",
	"body":"[Sat, 07 May 2016 21:36:00 +0100] Hello World !","encoding":null,"content":null}],
	"sent_at":"2016-05-07T20:35:19.012Z",
	"received_at":"2016-05-07T20:35:19.010Z",
	"sender":{"user_id":"test_user","name":null,"display_name":null,"avatar_url":null},
	"is_unread":false,"recipient_status":{"test_user":"read"},
"notification":null}
*/

echo 'Message sent successfully !'. PHP_EOL;

