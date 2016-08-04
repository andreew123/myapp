<?php

class CrudController {

	private $segments = array();

	public function start() {
		$actual_link = PROTOCOL."$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$local_url = str_replace('http://localhost:1112/myapp/api/', '', $actual_link);
		$this->segments = explode('/', $local_url);
		$this->callInstance();
	}

	public function callInstance() {
		if ($this->segments[0] === 'call') {
			call_user_func(array($this, $this->segments[1]));
		} else {
			call_user_func(array(Factory::getStaticName($this->segments[0]), $this->segments[1]));
		}
	}

}

?>
