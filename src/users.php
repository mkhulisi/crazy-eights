<?php
class Users{
	public $dbc;

	public function __construct($dbc){
		$this->dbc = $dbc;
	}

	//add user
	public function add_user(){
		$password = password_hash("@posPassword", PASSWORD_DEFAULT);
		$statement = $this->dbc->prepare("INSERT INTO users(name, phone, email, role, password, updated_date) VALUES(?, ?, ?, ?, ?, NOW())");
		$statement->bind_param("sisis", $_POST['name'], $_POST['phone'], $_POST['email'], $_POST['role'], $password);
		$statement->execute();
		$statement->close();
	}

	//check if product already exists
	public function is_user($option){

		if(filter_var($option, FILTER_VALIDATE_EMAIL)) {
		    $statement = $this->dbc->prepare("SELECT id FROM users WHERE email = ?");
		    $statement->bind_param("s", $option);
			$statement->execute();
			$result = $statement->get_result();
			$statement->close();
			
			if($result->num_rows > 0){
				
				return array("m" => "The provided email address is aready in use", "t" => "error");
			}
			else{
				return false;
			}
		}
		elseif(is_numeric($option)){
		    $statement = $this->dbc->prepare("SELECT id FROM users WHERE phone = ?");
		    $statement->bind_param("i", $option);
			    $statement->execute();
			$result = $statement->get_result();
			$statement->close();
			
			if($result->num_rows > 0){
				return array("m" => "The provided phone number is aready in use", "t" => "error");
			}
			else{
				return false;
			}
		}
		else{
			return array("m" => "Invalid email or phone number", "t" => "error");
		}
	}

	//get al users 
	public function get_all_users(){
		$statement = $this->dbc->prepare("SELECT id, name, phone, email, role, updated_date FROM users");
		$statement->execute();
		$result = $statement->get_result();
		$rows = $result->fetch_all(MYSQLI_ASSOC);
		return $rows;
	}

	//Authenticate user
	public function authenticate_user(){

	    // Prepare the statement
	    $statement = $this->dbc->prepare("SELECT id, password, name, phone, email, role FROM users WHERE email = ?");

	    // Bind parameters
	    $statement->bind_param("s", $_POST['email']);

	    // Execute the statement
	    $statement->execute();

	    // Get the result set
	    $result = $statement->get_result();

	    // Check if any rows were returned
	    if ($result->num_rows == 0){
	      return false;
	    }
	    else {
	      	// Fetch the first row
	      	$row = $result->fetch_assoc();

	      	// Get the password from the form
	      	$password = $_POST['password']; 
	      	// Verify the password
	      	$hash = $row['password'];
	      	if (password_verify($password, $hash)) {
	       	 	// Set session variables
		        $_SESSION['uid'] = $row['id'];
		        $_SESSION['name'] = $row['name'];
		        $_SESSION['email'] = $row['email'];
		        $_SESSION['phone'] = $row['phone'];
		        $_SESSION['role'] = $row['role'];
		        return true;
	      	}
	      	else 
	      	{
	        	return false;
	      	}
	    }
	}

	//get  user by id
	public function get_user_by_id($user_id){
		$statement = $this->dbc->prepare("SELECT name, phone, email, role FROM users WHERE id = ?");
		$statement->bind_param("i", $user_id);
		$statement->execute();
		$result = $statement->get_result();
		$row = $result->fetch_assoc();
		return $row;
	}
}