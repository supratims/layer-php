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
$message = '['.new date(DATE_RFC2822).'] Hello World !'; // Or read this from standard input

// Send message to layer
$layer->sendMessage($session_token, $chat_id, $message);


