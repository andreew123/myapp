<?php

class Conditions extends Model {

	protected $table = 'conditions';

	public function getConditions() {
		exit(json_encode(self::getAll()));
	}

}
