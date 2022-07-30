<?php

class API extends \Basic\Basic {
	
	/**
	 * Тестовый маршрут
	 */
	public static function test() {
		parent::success('test');
	}

	/**
	 * Проверка пользователя
	 */
	public static function getData() {
		if (is_numeric($_POST['id'])) {
			$user = parent::checkUser($_POST['id']);

			if (!(boolean) $user['start_webapp']) {
				Statistics::markOpenApp($_POST['id']);
			}

			if ($user) {
				parent::success([
					'record' => (int) $user['best_result'],
					'rules' => (boolean) $user['rules']
				]);
			} else {
				parent::success([
					'record' => 0,
					'rules' => false
				]);
			}
		} else {
			parent::error(2, 'bad user id');
		}
	}

	/**
	 * Прочитанные правила игры
	 */
	public static function markRules() {
		if (is_numeric($_POST['id'])) {
			$db = parent::getDb();
			$db->query("UPDATE users SET rules = {?} WHERE id = {?}", array(1, $_POST['id']));
		} else {
			parent::error(2, 'bad user id');
		}
	}

	/**
	 * Финальный экран
	 */
	public static function sendData() {
		if (is_numeric($_POST['id']) && is_numeric($_POST['record'])) {
			$db = parent::getDb();
			$db->query("UPDATE users SET best_result = {?}, time = {?} WHERE id = {?}", array($_POST['record'], time(), $_POST['id']));
			Statistics::play();
		} else {
			parent::error(3, 'bad data');
		}
	}

	/**
	 * Маршрут не найден
	 */
	public static function notFound() {
		parent::error(1);
	}
}