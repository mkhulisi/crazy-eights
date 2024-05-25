<?php
session_start();
if($_SERVER['REQUEST_METHOD'] != "POST"){
	$data = array("m" => "expired", "balance" => 0 );
	exit(json_encode($data));
}
else{
	spl_autoload_register(function($class_name){
    	require_once("src/".strtolower($class_name.".php"));
	});
	$dbc = (new Dbc())->dbc();
	$game_obj = new Games($dbc);
}