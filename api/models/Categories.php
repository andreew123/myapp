<?php

class Categories extends Model {

	protected $table = 'categories';

	public function getCategories() {
		exit(json_encode(self::getAll()));
	}

}
