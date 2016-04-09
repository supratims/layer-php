<?php
require 'vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Layer {
	
	// @TODO: replace your own keys 
	const APP_ID = 'layer:///apps/staging/<your app id here>';
	private $provider_id = '<your provider id>';
	private $key_id = 'your key id';
	// Returns a session token, required for all REST api requests to layer
	public function sessionToken($user_id){

		$client = new Client(['base_uri' => 'https://api.layer.com/sessions']);

		try {
			$options = [
				'headers' => [
					'Accept' => 'application/vnd.layer+json; version=1.0',
					'Content-Type' => 'application/json',
				],
				'body' => json_encode([
					'identity_token' => $this->identityToken($user_id),
					"app_id" => self::APP_ID,
				]),
			];
			$response = $client->request('POST', null, $options);

			$result = $response->getBody()->getContents();
			$json = json_decode($result, true);

			return $json['session_token'];
		}
		catch (GuzzleException $e) {
			throw $e;
		}
	}

	// Starts a conversation, returns a conversation id, a message can be created only under a conversation
	public function startConversation($token, $user_id){
		$result = $this->createConversation($token, $user_id);
		$json = json_decode($result, true);
		$chat_id = $this->parseConversationId($json['id']);

		return $chat_id;
	}

	// returns the conversation uuid from the conversation url
	public function parseConversationId($conversation_id_url){
		return str_replace('layer:///conversations/', '', $conversation_id_url);
	}

	/**
	 * response example
	 * {
	 * "id": "layer:///conversations/74a676c4-f697-45c4-b3bc-3e48bd2e372c",
	 * "url": "https://api.layer.com/conversations/74a676c4-f697-45c4-b3bc-3e48bd2e372c",
	 * "messages_url": "https://api.layer.com/conversations/74a676c4-f697-45c4-b3bc-3e48bd2e372c/messages",
	 * "created_at": "2015-10-10T22:51:12.010Z",
	 * "last_message": null,
	 * "participants": [ "1234", "5678" ],
	 * "distinct": false,
	 * "unread_message_count": 0,
	 * "metadata": {
	 * "background_color": "#3c3c3c"
	 * }
	 * }
	 */
	private function createConversation($token, $user_id){
		$client = new Client(['base_uri' => 'https://api.layer.com/conversations']);

		try {
			$options = [
				'headers' => [
					'Accept' => 'application/vnd.layer+json; version=1.0',
					'Authorization' => "Layer session-token='{$token}'",
					'Content-Type' => 'application/json',
				],
				'body' => json_encode([
					'participants' => [$user_id],
					"distinct" => false,
				]),
			];
			$response = $client->request('POST', null, $options);

			return $response->getBody()->getContents();
		}
		catch (GuzzleException $e) {
			throw $e;
		}
	}

	public function sendMessage($token, $conversation_id, $message){
		$client = new Client(['base_uri' => 'https://api.layer.com/conversations/' . $conversation_id . '/messages']);

		try {
			$options = [
				'headers' => [
					'Accept' => 'application/vnd.layer+json; version=1.0',
					'Authorization' => "Layer session-token='{$token}'",
					'Content-Type' => 'application/json',
				],
				'body' => json_encode([
					'parts' => [
						[
							'body' => $message,
							"mime_type" => "text/plain",
						],
					],
				]),
			];
			$response = $client->request('POST', null, $options);

			return $response->getBody()->getContents();
		}
		catch (GuzzleException $e) {
			throw $e;
		}
	}

	public function listConversations($token){
		$client = new Client(['base_uri' => 'https://api.layer.com/conversations']);
		try {
			$options = [
				'headers' => [
					'Accept' => 'application/vnd.layer+json; version=1.0',
					'Authorization' => "Layer session-token='{$token}'",
				],
			];
			$response = $client->request('GET', null, $options);
			$str = $response->getBody()->getContents();

			return json_decode($str, true);
		}
		catch (GuzzleException $e) {
			throw $e;
		}
	}

	public function listMessages($token, $conversation_id){
		$client = new Client(['base_uri' => 'https://api.layer.com/conversations/' . $conversation_id . '/messages']);

		try {
			$options = [
				'headers' => [
					'Accept' => 'application/vnd.layer+json; version=1.0',
					'Authorization' => "Layer session-token='{$token}'",
				],
			];
			$response = $client->request('GET', null, $options);
			$str = $response->getBody()->getContents();

			return json_decode($str, true);
		}
		catch (GuzzleException $e) {
			throw $e;
		}
	}

	private function nonce(){
		$client = new Client(['base_uri' => 'https://api.layer.com/nonces']);

		try {
			$options = [
				'headers' => [
					'Accept' => 'application/vnd.layer+json; version=1.0',
					'Content-Type' => 'application/json',
				],

			];
			$response = $client->request('POST', null, $options);

			return $response->getBody()->getContents();
		}
		catch (GuzzleException $e) {
			throw $e;
		}
	}

	private function identityToken($user_id = null){

		$json = json_decode($this->nonce(), true);
		$nonce = $json['nonce'];

		$user_id = $user_id ? $user_id : 'supratims';

		$layerIdentityTokenProvider = new \Layer\LayerIdentityTokenProvider();

		$layerIdentityTokenProvider->setProviderID("layer:///providers/".$this->provider_id);
		$layerIdentityTokenProvider->setKeyID("layer:///keys/".$this->key_id);
		$layerIdentityTokenProvider->setPrivateKey("-----BEGIN RSA PRIVATE KEY-----
	<paste the entire private key here>
-----END RSA PRIVATE KEY-----");

		$identityToken = $layerIdentityTokenProvider->generateIdentityToken($user_id, $nonce);

		return $identityToken;
	}

}
