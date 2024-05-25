<?php
require __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

// Include any required classes and autoloading logic here
spl_autoload_register(function($class_name){
    require_once("src/".strtolower($class_name.".php"));
});

// Database connection and game object initialization
$dbc = (new Dbc())->dbc();
$game_obj = new Games($dbc);

class WebSocketApp implements MessageComponentInterface
{
    protected $clients;
    protected $gameClients; // Associative array to store game-specific client connections
    public $game_obj;
    public $dbc;
    protected $baseCard;

    public function __construct($dbc, $game_obj)
    {
        $this->clients = new \SplObjectStorage();
        $this->gameClients = [];
        $this->dbc = $dbc;
        $this->game_obj = $game_obj;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Retrieve query parameters from the handshake request
        $queryParams = $conn->httpRequest->getUri()->getQuery();
        parse_str($queryParams, $queryData); // Parse query string into array

        // Add client connection to clients list
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";

        // Handle if client is joining an existing game or starting a new game
        if ($queryData['game_id'] != "init") {
            // Client is joining an existing game
            $gameID = $queryData['game_id'];
            $this->game_obj->attend_game($gameID, $conn->resourceId);

            // Respond to client with initial game state
            $cards = $this->dishPlayer_2_Cards($gameID);
            $cards[] = $conn->resourceId;
            $cards[] = $this->baseCard;
            $conn->send(json_encode($cards));

            // Store client connection in the game-specific clients array
            if (!isset($this->gameClients[$gameID])) {
                $this->gameClients[$gameID] = [];
            }
            $this->gameClients[$gameID][] = $conn;

            // Check if two players are ready to start the game
            if (count($this->gameClients[$gameID]) === 2) {

                // Notify both players that the game is ready to start

                if (isset($this->gameClients[$gameID])) {
                    foreach ($this->gameClients[$gameID] as $client) {
                        if ($client !== $conn) {
                            $client->send(json_encode(['gameStart' => true]));
                            echo "Game joining confirmation sent to client: {$client->resourceId}\n";
                        }
                    }
                }
            }
            echo "Player {$conn->resourceId} joined game $gameID\n";

        } else {
            // Client is creating a new game
            $gameID = $conn->resourceId; // Use resource ID as game ID for simplicity
            $game_name = $queryData['game_name'];
            $player_name = $queryData['player_name'];

            // Create game in the database
            $this->game_obj->createGame($gameID, $game_name, $player_name, $gameID);

            $this->gameClients[$gameID][] = $conn; // Associate client with game ID

            // Respond to client with initial game state
            $cards = $this->dishPlayer_1_Cards($gameID);
            $cards[] = $conn->resourceId;
            $cards[] = $this->baseCard;
            $conn->send(json_encode($cards));
            echo "Created a new game (ID: $gameID) for player {$conn->resourceId}\n";
        }
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        // Handle incoming messages
        $data = json_decode($msg, true);

        if (isset($data['playedCard'])) {
            $gameId = $data['gameId'];
            $playedCard = $data['playedCard'];

            switch ($playedCard) {
                case "doublepick":
                    // Handle double pick request
                    echo "Picking double request\n";
                    $doublePickedCards = $this->doublePick($gameId);

                    // Send double pick cards to requester
                    $from->send(json_encode(["doublepicked", $doublePickedCards]));
                    break;

                case "mdzayi":
                    // Handle mdzayi request
                    echo "Mdzayi request\n";
                    $mdzayiCard = $this->getUmdzayi($gameId);

                    // Send mdzayi card to requester
                    $from->send(json_encode(["mdzayi", $mdzayiCard]));
                    echo "Mdzayi card = $mdzayiCard\n";
                    break;

                default:
                    // Handle played card messages
                    if (strpos($playedCard, ".png") !== false) {
                        echo "Received played card: $playedCard\n";

                        // Send played card message to game participants (clients associated with the same game)
                        if (isset($this->gameClients[$gameId])) {
                            foreach ($this->gameClients[$gameId] as $client) {
                                if ($client !== $from) {
                                    $client->send($msg);
                                    echo "Sent to client: {$client->resourceId}\n";
                                }
                            }
                        }
                    }
                break;
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);

        // Remove client from game-specific clients array
        foreach ($this->gameClients as $gameID => &$clients) {
            if (in_array($conn, $clients, true)) {
                $index = array_search($conn, $clients, true);
                if ($index !== false) {
                    unset($clients[$index]);

                    // Notify remaining player about opponent's disconnection
                    foreach ($clients as $client) {
                        $client->send(json_encode(['opponentDisconnected' => true]));
                    }

                    echo "Player {$conn->resourceId} disconnected from game $gameID\n";
                }
                break;
            }
        }

        if(count($this->gameClients[$gameID]) == 0){
            //remove game from db
            $statement = $this->dbc->prepare("DELETE FROM game_cards WHERE game_id = ?");
            $statement->bind_param("i", $gameID);
            $statement->execute();
            $statement->close();
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
        echo "Error: {$e->getMessage()}\n";
    }

    protected function dishPlayer_1_Cards($gameID)
    {
        // Retrieve all cards from the 'cards' table
        $statement = $this->dbc->prepare("SELECT card_name FROM cards");
        $statement->execute();
        $result = $statement->get_result();
        $allCards = $result->fetch_all();
        $statement->close();

        // Shuffle allCards array randomly
        shuffle($allCards);
        
        // Select 8 random cards for the player
        $playerCards = array_splice($allCards, 0, 8);

        // Insert remaining cards into the 'game_cards' table
        foreach ($allCards as $card) {
            $statement = $this->dbc->prepare("INSERT INTO game_cards(game_id, card) VALUES(?, ?)");
            $statement->bind_param("is", $gameID, $card[0]);
            $statement->execute();
            $statement->close();
        }

        //get base card 
        $statement = $this->dbc->prepare("SELECT card FROM game_cards WHERE card NOT REGEXP '[7j82]' AND game_id = ? ORDER BY RAND() LIMIT 1");
        $statement->bind_param("i", $gameID);
        $statement->execute();
        $result = $statement->get_result();
        $row = $result->fetch_assoc();
        $this->baseCard = $row['card'];

        //delete base card from game cards
        $statement = $this->dbc->prepare("DELETE FROM game_cards WHERE card = ?");
        $statement->bind_param("s", $this->baseCard);
        $statement->execute();
        $statement->close();
        echo "Base card = $this->baseCard\n";


        // Prepare player's initial card set and include game ID
        $playerCards = array_merge(...array_map('array_values', $playerCards));
        $playerCards[] = "dish";
        $playerCards[] = $gameID;
        return $playerCards;
    }

    protected function dishPlayer_2_Cards($gameID)
    {
        // Retrieve all cards from the 'cards' table
        $statement = $this->dbc->prepare("SELECT card FROM game_cards WHERE game_id = ?");
        $statement->bind_param("i", $gameID);
        $statement->execute();
        $result = $statement->get_result();
        $allCards = $result->fetch_all();
        $statement->close();

        // Shuffle allCards array randomly
        shuffle($allCards);
        
        // Select 8 random cards for the player
        $playerCards = array_splice($allCards, 0, 8);

        // Remove selected cards from 'game_cards' table
        foreach ($playerCards as $card) {
            $statement = $this->dbc->prepare("DELETE FROM game_cards WHERE card = ?");
            $statement->bind_param("s", $card[0]);
            $statement->execute();
            $statement->close();
            echo "Deleted: ".$card[0]."\n";
        }
        echo "Base card = $this->baseCard\n";
        $playerCards = array_merge(...array_map('array_values', $playerCards));
        $playerCards[] = "dish";
        $playerCards[] = $gameID;
        return $playerCards;
    }

    protected function getUmdzayi($gameID)
    {
        //select random card from game_cards
        $statement = $this->dbc->prepare("SELECT card FROM game_cards WHERE game_id = ? ORDER BY RAND() LIMIT 1");
        $statement->bind_param("i", $gameID);
        $statement->execute();
        $result = $statement->get_result();

        if($result->num_rows == 0){
            // handle if umdzayi is depleted
            return 'depleted';
        }
        else{
            //continue rocessing retrieved card
            $card = $result->fetch_assoc();
            $statement->close();

            $card = $card['card'];
            //delte mdzayed card from mdzayi
            $statement = $this->dbc->prepare("DELETE FROM game_cards WHERE card = ? AND game_id = ?");
            $statement->bind_param("si",$card, $gameID);
            $statement->execute();

            return $card;
        }
    }

    public function doublePick($gameID)
    {
        //select random card from game_cards
        $statement = $this->dbc->prepare("SELECT card FROM game_cards WHERE game_id = ? ORDER BY RAND() LIMIT 2");
        $statement->bind_param("i", $gameID);
        $statement->execute();
        $result = $statement->get_result();
        

        if($result->num_rows == 0){
            // handle if umdzayi is depleted
            return 'depleted';
        }
        else{
            //continue processing selected cards
            $cards = $result->fetch_all();
            $statement->close();

            //delte selected cards from mdzayi
            foreach($cards as $row){
                $statement = $this->dbc->prepare("DELETE FROM game_cards WHERE card = ? AND game_id = ?");
                $statement->bind_param("si",$row['card'], $gameID);
                $statement->execute();
                $statement->close();
            }
            $cards = array_merge(...array_map('array_values', $cards));
            return $cards;
        }
    }
}

// Create a new WebSocket server
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new WebSocketApp($dbc, $game_obj)
        )
    ),
    8080  // Port number for the WebSocket server (adjust as needed)
);

echo "WebSocket server running at 127.0.0.1:8080\n";

// Run the WebSocket server
$server->run();
?>
