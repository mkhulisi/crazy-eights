<?php
class Products{
	public $dbc;

	public function __construct($dbc){
		$this->dbc = $dbc;
	}

	//add product function
	public function add_product(){
		if(isset($_POST['vat'])){
			$vat = 14;
		}
		else{
			$vat = 0;
		}
		$statement = $this->dbc->prepare("INSERT INTO products(product_name, quantity, price, user_id, category_id, description, vat, purchasing_price, bar_code, updated_date) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
		$statement->bind_param("sisiisiss", $_POST['product_name'], $_POST['quantity'], $_POST['price'], $_SESSION['uid'], $_POST['category'], $_POST['description'], $vat, $_POST['purchasing_price'], $_POST['code']);
		$statement->execute();
		$product_id = $this->dbc->insert_id;
		$statement->close();

		//add to inventory
		$statement = $this->dbc->prepare("INSERT INTO inventory_log(product_id, quantity, purchase_price, user_id,date_received) VALUES(?, ?, ?, ?, NOW())");
		$statement->bind_param("iisi", $product_id, $_POST['quantity'], $_POST['purchasing_price'], $_SESSION['uid']);
		$statement->execute();
		$statement->close();
	}

	//check if product already exists
	public function is_product(){
		$statement = $this->dbc->prepare("SELECT id FROM products WHERE product_name = ? AND description = ?");
		$statement->bind_param("ss", $_POST['product_name'], $_POST['description']);
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

	//get all products
	public function get_all_product(){
		$statement = $this->dbc->prepare("SELECT * FROM products");
		$statement->execute();
		$result = $statement->get_result();
		$rows = $result->fetch_all(MYSQLI_ASSOC);
		return $rows;
	}

	//get product category
	public function get_product_category($id){
		$statement = $this->dbc->prepare("SELECT category_name FROM categories WHERE id = ?");
		$statement->bind_param("i", $id);
		$statement->execute();

		$result = $statement->get_result();
		$row = $result->fetch_assoc();

		return $row['category_name'];
	}

	//get products for a category
	public function get_products_by_category_id($category_id){
		$statement = $this->dbc->prepare("SELECT * FROM products WHERE category_id = ?");
		$statement->bind_param("i", $category_id);
		$statement->execute();
		$result = $statement->get_result();
		$rows = $result->fetch_all(MYSQLI_ASSOC);
		return $rows;
	}

	//get product by bar code
	public function get_product_by_bar_code($bar_code){
		$statement = $this->dbc->prepare("SELECT * FROM products WHERE bar_code = ?");
		$statement->bind_param("s", $bar_code);
		$statement->execute();
		$result = $statement->get_result();
		if($result->num_rows == 0){
			$statement->close();
			return false;
		}
		else{
			$row = $result->fetch_assoc();
			$statement->close();
			return $row;
		}
	}
}