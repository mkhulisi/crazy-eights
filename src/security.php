<?php
class Security{
	public $dbc;

	public function __construct($dbc){
		$this->dbc = $dbc;
	}

	//xsrf mitigation
	public function anti_xsrf(){
		$xsrf_token = md5(uniqid(rand(),true));
		$_SESSION['token'] = $xsrf_token;
		return $xsrf_token;
	}

	public function encrypter($string){

		  
		// Store the cipher method 

		$ciphering = "AES-128-CTR"; 

		  
		// Use OpenSSl Encryption method 

		$iv_length = openssl_cipher_iv_length($ciphering); 

		$options = 0; 

		  
		// Non-NULL Initialization Vector for encryption 

		$encryption_iv = '1234567891011121'; 

		  
		// Store the encryption key 

		$encryption_key = "@cipher3030"; 

		  
		// Use openssl_encrypt() function to encrypt the data 

		$encrypted_string = openssl_encrypt($string, $ciphering, 

		$encryption_key, $options, $encryption_iv); 

		return $encrypted_string;
	}

	public function decrypter($cipher){

		// Store the cipher method 

		$ciphering = "AES-128-CTR"; 

		  
		// Use OpenSSl Encryption method 

		$iv_length = openssl_cipher_iv_length($ciphering); 

		$options = 0; 

		// Non-NULL Initialization Vector for decryption 

		$decryption_iv = '1234567891011121'; 

		  
		// Store the decryption key 

		$decryption_key = "@cipher3030"; 

		  
		// Use openssl_decrypt() function to decrypt the data 

		$decrypted_string=openssl_decrypt ($cipher, $ciphering,  

		$decryption_key, $options, $decryption_iv); 

		return $decrypted_string; 
	}
}