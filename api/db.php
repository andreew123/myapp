<?php

/* vim: set expandtab tabstop=4 shiftwidth=4: */

// +----------------------------------------------------------------------+
// | PHP version 5                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Original Author <j.cserko@gmail.com>                        |
// |          Your Name <Cserkó József>                                   |
// +----------------------------------------------------------------------+
//
// $Id:$
// $Description: Database wrapper class.
/**
 * Class and Function List:
 * Function list:
 * - __construct()
 * - _connect()
 * - _query()
 * - _all()
 * - _record_count()
 * - _truncate()
 * - _insert()
 * - table()
 * - select()
 * - where()
 * - andwhere()
 * - orwhere()
 * - wherein()
 * - limit()
 * - orderby()
 * - groupby()
 * - get_query_elements()
 * - get()
 * - batch_insert()
 * - __call()
 * - __get()
 * - __toString()
 * Classes list:
 * - DB
 */

class DB
{

	// Class variables.
	var $conn = null;
	var $server = 'localhost';
	var $user = '';
	var $pass = '';
	var $db_name = '';
	var $db_charset = 'utf8';
	var $mysqli = true;
	var $query_el = array();
	var $last_result = array();

	// Construct.
	function __construct($_server = null, $_user = null, $_pass = null, $_db_name = null, $_db_charset = null) {

		// Set variables.
		$this->server = !is_null($_server) ? $_server : $this->server;
		$this->user = $_user;
		$this->pass = $_pass;
		$this->db_name = $_db_name;
		$this->db_charset = !is_null($_db_charset) ? $_db_charset : $this->db_charset;

		$this->_connect();
	}

	// Connect to the database.
	private function _connect() {

		// Mysqli check.
		// if ( function_exists('mysqli_connect') )
		// 	$this->mysqli = true;

		if ($this->mysqli) {
			$this->conn = mysqli_connect($this->server, $this->user, $this->pass, $this->db_name) or die(mysqli_error($this->conn));
			mysqli_set_charset($this->conn, $this->db_charset);
		} else {
			$this->conn = mysql_connect($this->server, $this->user, $this->pass) or die(mysql_error());
			mysql_select_db($this->db_name, $this->conn);
			mysql_set_charset($this->db_charset, $this->conn);
		}
	}

	public function last_id() {
		$last_id = mysqli_insert_id($this->conn);
		return $last_id;
	}

	// Run simple query.
	// @params:
	// $q: string, query.
	public function _query($q) {
		$res = null;

		if ($this->mysqli) {
			$res = mysqli_query($this->conn, $q);
			if ($res === false) {
				echo mysqli_error($this->conn);
			} else {
				return $res;
			}
		} else {
			$res = mysql_query($q, $this->conn);
			if ($res === false) {
				echo mysql_error();
			} else {
				return $res;
			}
		}

		return $res;
	}

	// Run raw query.
	public function queryRaw($q, $to_array = false) {
		return $this->get($q, $to_array)->last_result;
	}

	// Get unid.
	public function get_unid( $table = '', $field = '', $size = 16 ) {

		$unid = substr( md5(microtime().rand(1000,9999) ), 0, $size );
		$row = $this->table( $table )
				->where( $field, '=', $unid )
				->get()
				->last_result;

		if ( isset($row->$field) ) {
			$unid = $this->get_unid( $table, $field, $size );
		} else {
			return $unid;
		}
	}

	// Get all records from the table.
	public function _all($table, $limit = '') {
		$q = "SELECT * FROM {$table}";
		if ($limit != '') $q.= " LIMIT " . $limit;

		// echo $q."\n";

		if ($this->mysqli) {
			$r = mysqli_query($this->conn, $q);
			if (!$r) return array();

			$ret = array();
			while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
				$ret[] = $row;
			}

			return $ret;
		} else {
			$r = mysql_query($q, $this->conn);
			if (!$r) return array();

			$ret = array();
			while ($row = mysql_fetch_array($r, MYSQL_ASSOC)) {
				$ret[] = $row;
			}

			return $ret;
		}
	}

	// Count of records.
	public function _record_count($table) {
		$q = "SELECT COUNT(id) as bnum FROM {$table}";
		if ($this->mysqli) {
			$r = mysqli_query($this->conn, $q);
			$obj = mysqli_fetch_object($r);
			return $obj->bnum;
		} else {
			$r = mysql_query($q, $this->conn);
			$obj = mysql_fetch_object($r);
			return $obj->bnum;
		}
	}

	// Trucate table.
	public function _truncate($table) {
		$q = "TRUNCATE TABLE {$table}";
		$this->_query($q);
	}

	// Insert new record.
	// @params:
	// $table: string, table name.
	// $record: array, associative.
	public function _insert($table, $record) {

		$q = "INSERT INTO %s ( %s ) VALUES ( %s )";
		$q = sprintf($q, $table, implode(',', array_keys($record)), "'" . implode("','", $record) . "'");

		$this->_query($q);
		// echo '<pre>';
		// print_r( $q );
		// echo '</pre>';
	}

	// Query elements.
	public function table($table) {
		$this->query_el['table'] = $table;
		return $this;
	}
	public function select($select) {
		$this->query_el['select'] = $select;
		return $this;
	}
	public function where() {
		$args = func_get_args();
		$this->query_el['where'] = ' ' . $args[0] . $args[1] . "'" . $args[2] . "'";
		return $this;
	}
	public function andwhere() {
		$args = func_get_args();
		if (empty($this->query_el['where'])) {
			return $this->where($args[0], $args[1], $args[2]);
		}

		$this->query_el['where'].= ' AND ' . $args[0] . ' ' . $args[1] . " '" . $args[2] . "'";
		return $this;
	}
	public function orwhere() {
		$args = func_get_args();
		if (empty($this->query_el['where'])) {
			return $this->where($args[0], $args[1], $args[2]);
		}

		$this->query_el['where'].= ' OR ' . $args[0] . ' ' . $args[1] . " '" . $args[2] . "'";
		return $this;
	}
	public function wherein() {
		$args = func_get_args();
		if (empty($this->query_el['where'])) {
			return $this->where($args[0], 'IN', "('" . implode("', '", $args[1]) . "')");
		}

		$this->query_el['where'].= ' AND ' . $args[0] . ' IN ' . "('" . implode("', '", $args[1]) . "')";
		return $this;
	}
	public function limit() {
		$args = func_get_args();
		$this->query_el['limit'] = $args[0];
		if (isset($args[1])) $this->query_el['limit'].= ',' . $args[0] . ' ';
		return $this;
	}
	public function orderby($orderby) {
		$this->query_el['orderby'] = $orderby;
		return $this;
	}
	public function groupby($groupby) {
		$this->query_el['groupby'] = $groupby;
		return $this;
	}
	public function get_query_elements() {
		echo '<pre>';
		print_r($this->query_el);
		echo '</pre>';
	}

	// Get first record.
	public function get( $raw = null, $to_array = false ) {

		if ( is_null($raw) ) {

			// Base query.
			$q = sprintf("SELECT * FROM %s", $this->query_el['table']);

			// If select specified.
			if (isset($this->query_el['select'])) $q = sprintf("SELECT %s FROM %s", $this->query_el['select'], $this->query_el['table']);

			// Add where clause.
			if (isset($this->query_el['where'])) $q.= sprintf(" WHERE %s", $this->query_el['where']);

			// Add group by statement.
			if (isset($this->query_el['groupby'])) $q.= sprintf(" GROUP BY %s", $this->query_el['groupby']);

			// Add order by statement.
			if (isset($this->query_el['orderby'])) $q.= sprintf(" ORDER BY %s", $this->query_el['orderby']);

			// Add limit statement.
			if (isset($this->query_el['limit'])) $q.= sprintf(" LIMIT %s", $this->query_el['limit']);

		} else {
			$q = $raw;
		}

		// Save last query.
		$this->last_query = $q;
		// echo '<pre>';
		// print_r( $q );
		// echo '</pre>';

		// Start query.
		$res = $this->_query($q);

		// Is mysqli.
		if ($this->mysqli) {
			$nr = mysqli_num_rows($res);
			switch ($nr) {
				case 0:
					$this->last_result = null;
					break;

				case 1:
					if ( $to_array ) {
						$this->last_result = array();
						while ($row = mysqli_fetch_object($res)) {
							$this->last_result[] = $row;
						}
					} else {
						$this->last_result = mysqli_fetch_object($res);
					}
					break;

				default:
					$this->last_result = array();
					while ($row = mysqli_fetch_object($res)) {
						$this->last_result[] = $row;
					}
					break;
			}
		} else {
			 // Is plain mysql.
			$nr = mysql_num_rows($res);
			switch ($nr) {
				case 0:
					$this->last_result = null;
					break;

				case 1:
					if ( $to_array ) {
						$this->last_result = array();
						while ($row = mysql_fetch_object($res)) {
							$this->last_result[] = $row;
						}
					}
					$this->last_result = mysql_fetch_object($res);
					break;

				default:
					$this->last_result = array();
					while ($row = mysql_fetch_object($res)) {
						$this->last_result[] = $row;
					}
					break;
			}
		}

		// This.
		$this->query_el = array();
		return $this;
	}

	// Batch insert to database table.
	// @params:
	// $records: array, two dimensional array with records.
	public function batch_insert($table, $records) {

		// Base query.
		$q = "INSERT INTO %s ( %s ) VALUES";
		$q = sprintf($q, $table, implode(',', array_keys($records[0])));

		// Recordok hozzáadása a limit erejéig.
		$lgt = count($records);
		$limit = 50;
		$reqs = array();
		for ($i = 0; $i < $lgt; ++$i) {
			if ($i < $limit) $reqs[] = "('" . implode("','", $records[$i]) . "')";
		}
		$records = array_slice($records, 50);

		if (count($reqs) > 0) {
			$q.= implode(', ', $reqs);

			// echo 		$q."\n";
			$this->_query($q);
		}

		if (count($records) > 0) $this->batch_insert($records);
	}

	// Call watcher.
	public function __call($method, $args) {

		//
		var_dump($args);
	}

	//
	public function __get($name) {

		// Data set.
		if (empty($this->last_result)) {
			return null;
		} else if (gettype($this->last_result) == 'object') {
			return isset($this->last_result->{$name}) ? $this->last_result->{$name} : null;
		} else if (count($this->last_result) == 1) {
			return isset($this->last_result[0]->{$name}) ? $this->last_result[0]->{$name} : null;
		} else {
			$ret = array();
			foreach ($this->last_result as $lr) {
				$ret[] = isset($lr->{$name}) ? $lr->{$name} : null;
			}
			return $ret;
		}
	}

	// String invocation.
	public function __toString() {
		ob_start();
		echo '<pre>';
		print_r($this->last_result);
		echo '</pre>';
		$cont = ob_get_contents();
		ob_end_clean();
		return $cont;
	}

	// Update.
	public function update($table, $data, $where, $format = null, $where_format = null) {
		if (!is_array($data) || !is_array($where) ) {
		        return false;
		}
		$data = $this->process_fields($table, $data, $format);
		if (false === $data) {
		        return false;
		}
		$where = $this->process_fields($table, $where, $where_format);
		if (false === $where) {
		        return false;
		}
		$fields = $conditions = $values = array();
		foreach ($data as $field => $value) {
		        if (is_null($value['value'])) {
		                $fields[] = "`$field` = NULL";
		                continue;
		        }
		        $fields[] = "`$field` = " . $value['format'];
		        $values[] = $value['value'];
		}
		foreach ($where as $field => $value) {
		        if (is_null($value['value'])) {
		                $conditions[] = "`$field` IS NULL";
		                continue;
		        }
		        $conditions[] = "`$field` = " . $value['format'];
		        $values[] = $value['value'];
		}
		$fields = implode(', ', $fields);
		$conditions = implode(' AND ', $conditions);
		$q = "UPDATE `$table` SET $fields WHERE $conditions";
		$prep = $this->prepare($q, $values);
		// exit($prep);
		$this->_query($prep);
	}

	protected function process_fields($table, $data, $format) {
		$data = $this->process_field_formats($data, $format);
		if (false === $data) {
			return false;
		}
		return $data;
	}

	protected function process_field_formats($data, $format) {
		$formats = $original_formats = (array)$format;
		foreach ($data as $field => $value) {
			$value = array(
				'value'  => $value,
				'format' => '%s',
			);
			if (!empty($format)) {
				$value['format'] = array_shift($formats);
				if (!$value['format']) {
					$value['format'] = reset($original_formats);
				}
			} elseif (isset($this->field_types[ $field ])) {
				$value['format'] = $this->field_types[$field];
			}
			$data[$field] = $value;
		}
		return $data;
	}

	public function prepare($query, $args) {
		if (is_null($query))
			return;
		$args = func_get_args();
		array_shift($args);
		if ( isset($args[0]) && is_array($args[0]))
			$args = $args[0];
		$query = str_replace("'%s'", '%s', $query); // in case someone mistakenly already singlequoted it
		$query = str_replace('"%s"', '%s', $query); // doublequote unquoting
		$query = preg_replace('|(?<!%)%f|' , '%F', $query); // Force floats to be locale unaware
		$query = preg_replace('|(?<!%)%s|', "'%s'", $query); // quote the strings, avoiding escaped strings like %%s
		// array_walk($args, array($this, 'escape_by_ref'));
		return @vsprintf($query, $args);
    }

}
