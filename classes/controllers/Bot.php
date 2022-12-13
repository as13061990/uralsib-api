<?php

class Bot extends \Basic\Basic {
	
	/**
	 * точка входа чат-бота
	 */
	public static function main() {
		if ($_POST['message']['entities'][0]['type'] != 'bot_command' && $_POST['callback_query']['data'] != 'checkSubscribe' && $_POST['message']['from']['id'] != $_POST['message']['chat']['id']) {
			exit();
		}
		$message = $_POST['message'] ? $_POST['message']['text'] : $_POST['callback_query']['data'];

		if ($message == '/start' || $message == 'checkSubscribe') {
			$data = self::start();
		} else {
			$data = self::badCommand();
		}
		
		if ($data) {
			self::sendTelegram('sendMessage', $data);
		}
	}

	/**
	 * отправка сообщения
	 */
	private static function sendTelegram($method, $data, $headers = []) {
		global $config;
		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_POST => 1,
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => 'https://api.telegram.org/bot' . $config['token'] . '/' . $method,
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => array_merge(array("Content-Type: application/json"), $headers)
		]);
		$result = curl_exec($curl);
		curl_close($curl);
		return json_decode($result, true);
	}

	/**
	 * стартовая команда
	 */
	private static function start() {
		$id = $_POST['message'] ? $_POST['message']['from']['id'] : $_POST['callback_query']['from']['id'];
		$chat = $_POST['message'] ? $_POST['message']['chat']['id'] : $_POST['callback_query']['message']['chat']['id'];
		
		if (is_numeric($id)) {
			$user = parent::checkUser($id);
			self::checkUsername($user, $_POST['message']['from']['username']);

			if (!self::checkSubscribe($id)) {
				$data = [
					'text' => 'Чтобы начать игру, подпишись на канал <a href="https://t.me/bankuralsibnews">Банка Уралсиб</a> в Telegram',
					'chat_id' => $chat,
					'parse_mode' => 'HTML',
					'reply_markup' => [
						'inline_keyboard' => [
							[
								[
									'text' => 'Открыть канал',
									'url' => 'https://t.me/bankuralsibnews'
								],
								[
									'text' => 'Проверить подписку',
									"callback_data" => "checkSubscribe"
								],
							]
						]
					]
				];
				return $data;
			}

			self::sendVideo($chat);
			exit();

			$data = [
				'text' => "Покажите максимум ловкости в игре от Банка Уралсиб и заработайте до 1000 бонусных рублей на карту.\n\nПрибыль уже ждет вас!",
				'chat_id' => $chat,
				'reply_markup' => [
					'inline_keyboard' => [
						[
							[
								'text' => 'Играть',
								'web_app' => ['url' => 'https://uralsib.irsapp.ru']
							]
						]
					]
				]
			];
			return $data;
		}
		return false;
	}

	/**
	 * Любая другая команда
	 */
	private static function badCommand() {
		$id = $_POST['message'] ? $_POST['message']['from']['id'] : $_POST['callback_query']['from']['id'];
		$chat = $_POST['message'] ? $_POST['message']['chat']['id'] : $_POST['callback_query']['message']['chat']['id'];
		
		if (is_numeric($id)) {
			$user = parent::checkUser($id);
			self::checkUsername($user, $_POST['message']['from']['username']);

			if (!self::checkSubscribe($id)) {
				$data = [
					'text' => 'Чтобы начать игру, подпишись на канал <a href="https://t.me/bankuralsibnews">Банка Уралсиб</a> в Telegram',
					'chat_id' => $chat,
					'parse_mode' => 'HTML',
					'reply_markup' => [
						'inline_keyboard' => [
							[
								[
									'text' => 'Открыть канал',
									'url' => 'https://t.me/bankuralsibnews'
								],
								[
									'text' => 'Проверить подписку',
									"callback_data" => "checkSubscribe"
								],
							]
						]
					]
				];
				return $data;
			}

			$data = [
				'text' => 'Чтобы перейти к игре, нажми кнопку «Играть»',
				'chat_id' => $_POST['message']['chat']['id'],
				'reply_markup' => [
					'inline_keyboard' => [
						[
							[
								'text' => 'Играть',
								'web_app' => ['url' => 'https://uralsib.irsapp.ru']
							]
						]
					]
				]
			];
			return $data;
		}
		return false;
	}

	/**
	 * Проверка подписки на группу
	 */
	private static function checkSubscribe($id) {
		return true;
		$data = [
			'user_id' => $id,
			'chat_id' => -1001776797334
		];
		$result = self::sendTelegram('getChatMember', $data);
		
		if ($result['result']['status'] == 'member' ||
			$result['result']['status'] == 'creator' ||
			$result['result']['status'] == 'administrator' ||
			$result['result']['status'] == 'restricted') {
			return true;
		}
		return false;
	}

	private static function checkUsername($user, $name) {
		if ($name && $user['username'] != $name) {
			$db = parent::getDb();
			$db->query("UPDATE users SET username = {?} WHERE id = {?}", array($name, $user['id']));
		}
	}

	public static function sendVideo($id) {
		global $config;
		$path = realpath(__DIR__ . '/../../templates/video') . '/video.mp4';
		$data = [
			'caption' => "Покажите максимум ловкости в игре от Банка Уралсиб и заработайте до 1000 бонусных рублей на карту.\n\nПрибыль уже ждет вас!",
			'chat_id' => $id,
			'video' => new CurlFile($path),
			'parse_mode' => 'html',
			'reply_markup' => json_encode([
				'inline_keyboard' => [
					[
						[
							'text' => 'Играть',
							'web_app' => ['url' => 'https://uralsib.irsapp.ru']
						]
					]
				]
			])
		];
		$url = 'https://api.telegram.org/bot' . $config['token'] . '/sendVideo';
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Content-Type: multipart/form-data"
		));
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
		$result = curl_exec($ch);
	}
}