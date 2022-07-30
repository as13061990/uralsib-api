<?php

namespace Basic;

class Basic extends \DB\Db {

	/**
	 * Шаблонизатор
	 */
	protected static function loadView($strViewPath, $arrayOfData) {
		extract($arrayOfData);
		ob_start();
		require($strViewPath);
		$strView = ob_get_contents();
		ob_end_clean();
		return $strView;
	}

	/**
	 * Лог функция для тестов
	 */
	protected static function log($text) {
		file_put_contents('logs.txt', $text."\n", FILE_APPEND);
	}
	
	/**
	 * Функция вывода ошибки
	 */
	protected static function error($type = 0, $data = null) {
		echo json_encode(array(
			'error' => true,
			'error_type' => $type,
			'data' => $data,
		));
		exit();
	}

	/**
	 * Функция вывода ответа
	 */
	protected static function success($data = null) {
		if (is_array($data)) {

			foreach ($data as $key => $value) {
				if (is_numeric($value)) {
					$data[$key] = (int) $value;
				}

				if (is_array($value)) {

					foreach ($value as $key2 => $value2) {
						if (is_numeric($value2)) {
							$data[$key][$key2] = (int) $value2;
						} else if (is_array($value2)) {
							foreach ($value2 as $key3 => $value3) {
								if (is_numeric($value3)) {
									$data[$key][$key2][$key3] = (int) $value3;
								}
							}
						}
					}
				}
			}
		}
		
		echo json_encode(array(
			'data' => $data,
			'error' => false,
			'error_type' => null,
		));
		exit();
	}

  /**
   * Проверка пользователя в базе
   */
  protected static function checkUser($id) {
		$db = parent::getDb();
		$user = $db->select("SELECT * FROM users WHERE id = {?}", array($id))[0];

		if (!$user) {
			$db->query("INSERT IGNORE INTO users SET id = {?}", array($id));
		}
		return $user;
  }
}

?>
