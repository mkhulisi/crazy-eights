<?php
class Categories{
	public $dbc;

	public function __construct($dbc){
		$this->dbc = $dbc;
	}

	//add category
	public function add_category(){
		$statement = $this->dbc->prepare("INSERT INTO categories(category_name, user_id, updated_date) VALUES(?, ?, NOW())");
		$statement->bind_param("si", $_POST['name'], $_SESSION['uid']);
		$statement->execute();
		$statement->close();

	}

	//check if category exists
	public function is_category(){
		$statement = $this->dbc->prepare("SELECT id FROM categories WHERE category_name = ?");
		$statement->bind_param("s", $_POST['name']);
		$statement->execute();
		$result = $statement->get_result();
		$statement->close();
		
		if($result->num_rows == 0){
			return false;
		}
		else{
			return true;
		}
	}

	//get all categories
	public function get_all_catrgories(){
		$statement = $this->dbc->prepare("SELECT * FROM categories");
		$statement->execute();
		$result = $statement->get_result();
		$rows = $result->fetch_all(MYSQLI_ASSOC);
		return $rows;
	}

	//get category stock price by id
	public function get_category_stock_price_by_id($id){
		$statement = $this->dbc->prepare("SELECT SUM(price) AS stock_price FROM products WHERE category_id = ?");
		$statement->bind_param("i", $id);
		$statement->execute();
		$result = $statement->get_result();

		if($result->num_rows == 0){
			return 0;
		}
		else{
			$row = $result->fetch_assoc();
			return $row['stock_price']; 
		}
	}

	//get quantity of products for a category
	public function get_category_products_quantity($id){
		$statement = $this->dbc->prepare("SELECT COUNT(id) AS quantities FROM products WHERE category_id = ?");
		$statement->bind_param("i", $id);
		$statement->execute();
		$result = $statement->get_result();
		$row = $result->fetch_assoc();

		return $row['quantities'];
	}
}