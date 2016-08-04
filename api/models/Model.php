<?php

class Model {

	protected $db;
	protected $table;

	function __construct() {
		$this->db = new DB(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_CHARSET);
	}

	// Get one.
	public function getOne($data) {
		$select = isset($data['select']) ? $data['select'] : '*';
		$where = isset($data['where']) ? $data['where'] : '';
		$order_by = isset($data['order_by']) ? $data['order_by'] : 'id';
		$ret = $this->db->table($this->table)
					->select($select)
					->where($where[0], $where[1], $where[2])
					->limit(1)
					->orderby($order_by)
					->get()
					->last_result;
		return $ret;
	}

	// Get all.
	public function getAll($limit = false) {
		$limit = $limit !== false ? $limit : '';
		$ret = $this->db->_all( $this->table, $limit );
		return $ret;
	}

	// Save.
	public function create($record) {
		$ret = $this->db->_insert($this->table, $record);
		return $ret;
	}

	// Update.
	public function update($record, $where) {
		$ret = $this->db->update($this->table, $record, $where);
		return $ret;
	}

	// Delete.
	public function delete($where, $nolimit = false) {
		$query = "DELETE FROM " . $this->table;
		$wheres = $this->getWhere( $where );
		$query .= " WHERE " . implode("AND", $wheres);
		if ($nolimit === false) {
			$query .= " LIMIT 1";
		}
		$this->db->_query($query);
		return 'okay mate';
	}

	// Handle wheres.
	public function getWhere($where) {
		$wheres = array();
		foreach ($where as $key => $value) {
			$wheres[] = " ".$key." = '".$value."' ";
		}
		return $wheres;
	}

}
