<?php
class Games{
	public $dbc;

	public function __construct($dbc){
		$this->dbc = $dbc;
	}

	public function createGame($game_id, $game_name, $player_name, $player_id){
		$statement = $this->dbc->prepare("INSERT INTO games(game_id, game_name, player_name, player1_id) VALUES(?, ?, ?, ?)");
		$statement->bind_param("issi", $game_id, $game_name, $player_name, $player_id);
		$statement->execute();
		$statement->close();
	}

	public function getGames(){
		$statement = $this->dbc->prepare("SELECT * FROM games WHERE state = 1");
		$statement->execute();
		$result = $statement->get_result();
		$rows = $result->fetch_all(MYSQLI_ASSOC);
		return $rows;
	}

	public function attend_game($game_id, $player_id){
		$statement = $this->dbc->prepare("UPDATE games SET state = 0, player2_id = ? WHERE game_id = ?");
		$statement->bind_param("ii", $player_id, $game_id,);
		$statement->execute();
		$statement->close();
	}

}