<?php
class Dashboard{
	public $dbc;

	public function __construct($dbc){
		$this->dbc = $dbc;
	}

	//get counts
	public function dash_counts(){

		//get products count
		$statement = $this->dbc->prepare("SELECT COUNT(id) AS total_products FROM products");
		$statement->execute();
		$result = $statement->get_result();
		$row = $result->fetch_assoc();
		$products_count = $row['total_products'];
		$statement->close();

		//get today transactions
		$statement = $this->dbc->prepare("SELECT COUNT(id) AS today_transactions FROM transactions WHERE DATE(transaction_date) = CURDATE()");
		$statement->execute();
		$result = $statement->get_result();
		$row = $result->fetch_assoc();
		$today_transactions = $row['today_transactions'];
		$statement->close();

		//get today's sales
		$statement = $this->dbc->prepare("SELECT SUM(total) AS sales FROM transactions WHERE DATE(transaction_date) = CURDATE()");
		$statement->execute();
		$result = $statement->get_result();
		$row = $result->fetch_assoc();
		$statement->close();
		$today_sales = $row['sales'];

		//get users count
		$statement = $this->dbc->prepare("SELECT COUNT(id) AS total_users FROM users");
		$statement->execute();
		$result = $statement->get_result();
		$row = $result->fetch_assoc();
		$total_users = $row['total_users'];
		$statement->close();

		$counts = array('products_count' => $products_count, 'today_transactions' => $today_transactions, 'today_sales' => $today_sales, 'total_users' => $total_users);

		return $counts;

	}
}