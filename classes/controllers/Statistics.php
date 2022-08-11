<?php

class Statistics extends \Basic\Basic {

	public static function markOpenApp($id) {
		$db = parent::getDb();
		$db->query("UPDATE users SET start_webapp = {?} WHERE id = {?}", array(1, $id));
	}

	public static function play() {
		$db = parent::getDb();
		$db->query("UPDATE main SET play = play + 1");
	}

	public static function prize() {
		$db = parent::getDb();
		$db->query("UPDATE main SET prize = prize + 1");
	}
	
	public static function save() {
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
		$db->query("INSERT IGNORE INTO stats SET chat = {?}, app = {?}, games = {?}, prize = {?}, time = {?}", [count($users), $webapp, $main['play'], $main['prize'], time()]);
	}
}