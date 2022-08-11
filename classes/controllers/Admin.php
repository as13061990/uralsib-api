<?php

class Admin extends \Basic\Basic {
	
	/**
	 * Главная страница админки
	 */
	public static function main() {
		$db = parent::getDb();
		$main = $db->select("SELECT * FROM main")[0];
		$users = $db->select("SELECT * FROM users");

		$webapp = 0;
		$count = count($users);

		for ($i = 0; $i < $count; $i++) {
			
			if ($users[$i]['start_webapp'] == 1) {
				$webapp++;
			}
		}

		echo parent::loadView('templates/statistics.php', array(
			'main' => $main,
			'webapp' => $webapp,
			'users' => $users
		));
	}

	/**
	 * Часовая статистика
	 */
	public static function stats() {
		$db = parent::getDb();
		$stats = $db->select("SELECT * FROM stats");

		echo parent::loadView('templates/stats.php', array(
			'stats' => $stats
		));
	}
}