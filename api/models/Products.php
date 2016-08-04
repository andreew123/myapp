<?php

class Products extends Model {

	protected $table = 'products';

	public function getPages() {
		$query = "SELECT COUNT(*) AS count FROM ".$this->table;
		$ret = $this->db->queryRaw($query, true);
		$ret = $ret[0]->count;
		$ret = ceil((float)$ret/10);
		exit(json_encode($ret));
	}

	public function getProductsWithCategories() {
		$data = Input::data();
		$query = "SELECT p.*, c.id AS category"
		." FROM ".$this->table." p INNER JOIN products_to_categories pc"
		." ON p.id = pc.product_id INNER JOIN categories c"
		." ON pc.category_id = c.id WHERE pc.category_id = c.id";
		$query .= " LIMIT ".$data->page.",10";
		$ret = $this->db->queryRaw($query, true);
		$ret !== null ? $ret : [];
		exit(json_encode($ret));
	}

	public function saveNewProduct() {
		$data = Input::data();
		$query = "INSERT INTO ".$this->table."(`name`, `description`, `price`, `condition_id`) VALUES ('%s', '%s', %d, %d)";
		$query = sprintf($query, $data->name, $data->description, $data->price,
		$data->conditions);
		$ret = $this->db->_query($query);
		$last_id = $this->db->last_id();
		$query = "INSERT INTO products_to_categories (`product_id`, `category_id`)
			VALUES (%d, %d)";
		$query = sprintf($query, $last_id, $data->category);
		$ret = $this->db->_query($query);
		exit(json_encode($data));
	}

	public function deleteProduct() {
		$data = Input::data();
		$where = array('id'=>$data->id);
		self::delete($where);
		$where = array(
			'product_id'=>$data->id
		);
		ProductsToCategories::delete($where, true);
	}

	public function updateProduct() {
		$data = Input::data();
		$query = "UPDATE ".$this->table." SET `name`='%s', `description`='%s',
			`price`=%d, `condition_id`=%d";
		$query = sprintf($query, $data->name, $data->description, $data->price,
			$data->condition_id);
		$ret = $this->db->_query($query);
		// TODO: delete and save product to categories
		exit(json_encode($data));
	}

	public function searchProduct() {
		$data = Input::data();
		$query = "SELECT p.*, c.id AS category"
		." FROM ".$this->table." p INNER JOIN products_to_categories pc"
		." ON p.id = pc.product_id INNER JOIN categories c"
		." ON pc.category_id = c.id WHERE pc.category_id = c.id";
		if (!empty($data->name)) {
			$query .= " AND p.name LIKE '%".$data->name."%'";
		}
		if (!empty($data->description)) {
			$query .= " AND p.description LIKE '%".$data->description."%'";
		}
		if (!empty($data->category)) {
			$query .= " AND pc.category_id = ".$data->category;
		}
		if (!empty($data->price)) {
			$query .= " AND p.price LIKE '%".$data->price."%'";
		}
		if (!empty($data->condition_id)) {
			$query .= " AND p.condition_id = ".$data->condition_id;
		}
		$ret = $this->db->queryRaw($query, true);
		$ret !== null ? $ret : [];
		exit(json_encode($ret));
	}

}
