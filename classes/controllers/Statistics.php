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
}