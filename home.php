<?php
session_start();
if(isset($_POST['c8guid'])){
    $data = array("m" => "expired", "balance" => 0 );
    exit(json_encode($data));
}
else{
    spl_autoload_register(function($class_name){
        require_once("src/".strtolower($class_name.".php"));
    });
    $dbc = (new Dbc())->dbc();
    $game_obj = new Games($dbc);

    $games = $game_obj->getGames();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CraZy8</title>
    <!-- Bootstrap CSS -->
    <link href="bootstrap.min.css" rel="stylesheet">
    <link href="main.css" rel="stylesheet">
    <!-- Custom CSS for full-screen layout -->
     <style>
        body {
            padding: 20px;
        }

        .game-card {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="content">

        <div class="container">
            <h1 class="my-4">Game Lobby</h1>

            <!-- List of Created Games -->
            <div class="row">
                <div class="col-md-6 col-12">
                    <h2>Available Games</h2>
                    <?php foreach ($games as $row) {
                        // code...
                    ?>
                    <div class="list-group">
                        <!-- Sample Game Card (Repeat for each game) -->
                        <div class="list-group-item game-card text-center" >
                            <h4 class="mb-1"><?php echo $row['game_name'] ?>!</h4>
                            <p class="mb-1">Created by: <?php echo $row['player_name'] ?></p>
                            <a class="btn btn-primary" href="game.php?game_id=<?php echo $row['game_id'] ?>">Join Game</a>
                        </div>
                    </div>
                <?php } ?>
                </div>
            </div>

            <hr>

            <!-- Create New Game Form -->
            <div class="row">
                <div class="col-md-6">
                    <h2>Create New Game</h2>
                    <form action="game.php" method="POST">
                        <div class="form-group">
                            <label for="gameName">Game Name</label>
                            <input type="text" class="form-control" name="game_name" id="gameName" placeholder="Enter Game Name" required>
                        </div>
                        <div class="form-group">
                            <label for="playerName">Your Name</label>
                            <input type="text" name="player_name" class="form-control" id="playerName" placeholder="Enter Your Name" required>
                        </div>
                        <button type="submit" class="btn btn-success">Create Game</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS (optional, for certain Bootstrap components that require JS) -->
    <script src="jquery.min.js"></script>
    <script src="popper.min.js"></script>
    <script src="bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
      $(".join").click(function(){
        document.location="game.php";
      })
    });

    </script>

</body>

</html>
