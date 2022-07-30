<?php

class Bot extends \Basic\Basic {
	
	/**
	 * точка входа чат-бота
	 */
	public static function main() {
		$message = $_POST['message']['text'];

		if ($message == '/start') {
			$data = self::start();
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
	}

	/**
	 * стартовая команда
	 */
	private static function start() {
		$id = $_POST['message']['from']['id'];

		if (is_numeric($id)) {
			parent::checkUser($id);
			// TODO: вставить ифчик на проверку подписки группы. Ретернем другой объект

			$data = [
				'text' => 'Привет! Покажи максимум ловкости в игре от Банка Уралсиб и получи до 1000 бонусных рублей на дебетовую карту "Прибыль"',
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
}