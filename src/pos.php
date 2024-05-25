<?php
class Pos{
	public $dbc;

	public function __construct($dbc){
		$this->dbc = $dbc;
	}

	//transact
	public function transact($product_id, $quantity, $sub_total, $total, $vat, $key){
		if($key === 1){
			$statement = $this->dbc->prepare("INSERT INTO transactions(product_id, user_id, quantity, sub_total, total, vat, transaction_id, transaction_date) VALUES(?, ?, ?, ?, ?, ?, 0, NOW())");
			$statement->bind_param("iiiiss", $product_id, $_SESSION['uid'], $quantity, $sub_total, $total, $vat);
			$statement->execute();

			$transactionId = $this->dbc->insert_id;
			$statement->close();

			//update transaction id
			$statement = $this->dbc->prepare("UPDATE transactions SET transaction_id = ? WHERE id = ?");
			$statement->bind_param("ii", $transactionId, $transactionId);
			$statement->execute();
			$statement->close();

			//deduct from stock
			$statement = $this->dbc->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
			$statement->bind_param("ii", $quantity, $product_id);
			$statement->execute();
			$statement->close();

			$_SESSION['transaction_id'] = $transactionId;
		}
		else{
			$statement = $this->dbc->prepare("INSERT INTO transactions(product_id, user_id, quantity, sub_total, total, vat, transaction_id, transaction_date) VALUES(?, ?, ?, ?, ?, ?, ?, NOW())");
			$statement->bind_param("iiisssi", $product_id, $_SESSION['uid'], $quantity, $sub_total, $total, $vat, $_SESSION['transaction_id']);
			$statement->execute();
			$statement->close();
		}	
	}

	//transaction monies 
	public function transection_monies(){
		$statement = $this->dbc->prepare("INSERT INTO transactions_monies(transaction_id, cash, change_amount) VALUES(?, ?, ?)");
		$statement->bind_param("iss", $_SESSION['transaction_id'], $_POST['cash'], $_POST['change']);
		$statement->execute();
		$statement->close();
	}
}