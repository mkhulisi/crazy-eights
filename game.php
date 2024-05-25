<?php
    if($_SERVER['REQUEST_METHOD'] == "POST" || isset($_GET['game_id'])){
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            //if user is creating game
            $gameID = "init";
            $player_name  = $_POST['player_name'];
            $game_name= $_POST['game_name'];
        }
        else{
            //if user is joining game
            $gameID = $_GET['game_id'];
            $player_name  = "";
            $game_name= "";
        }
    }
    else{
        header("Location: home.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, proxy-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="Expires" content="Tue, 01 Jan 1980 1:00:00 GMT">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crazy8 game</title>
    <link href="bootstrap.min.css" rel="stylesheet">
    <link href="main.css" rel="stylesheet">
    <style type="text/css">
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(144, 192, 152);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        .player {
            margin-bottom: 20px;
        }

        .turn {
            font-size: 24px;
            margin-bottom: 10px;
            color: white;
            text-align: center;
        }

        .player-cards, .deck, .discard-pile {
            display: flex;
            justify-content: center;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .card {
            width: 70px;
            height: 90px;
            margin: 2px;
        }

        .actions {
            display: flex;
            justify-content: center;
            
        }

        button {
            font-size: 30px;
            padding: 10px 15px;
            margin: 2px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            min-width: 80px;
        }

        .table{
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        #play-button {
            background-color: #4CAF50;
            color: white;
        }


        /* Media Queries */
        @media (min-width: 600px) {
            .card {
                width: 80px;
                height: 105px;
            }

            button {
                font-size: 40px;
                padding: 10px 20px;
                margin: 2px;
                min-width: 100px;
            }
        }

         .card:hover {
            transform: translateY(-1px); /* Lift the card on hover */
            background-color: red;
        }

        /* CSS for the shaking animation */
        @keyframes shake {
            0% { transform: translate(0, 0); }
            10%, 30%, 50%, 70%, 90% { transform: translate(-5px, 0); }
            20%, 40%, 60%, 80%, 100% { transform: translate(5px, 0); }
        }
        .shake {
           
            animation: shake 0.5s ease infinite;
        }

        @keyframes heartbeat{
            0% { border-radius: 10%; }
            10%, 30%, 50%, 70%, 90% { border-radius: 30%; }
            20%, 40%, 60%, 80%, 100% { border-radius: 50%;; }
        }

        .heartbeat{
            animation: heartbeat 0.5s ease infinite;
        }
        #free-choice-requested{
            font-size: 40px;
            text-align: center;
        }

        .card-back {
          width: 63px; /* Width of the card */
          height: 87px; /* Height of the card */
          border-radius: 3px; /* Rounded corners like a real card */
          box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); /* Adds a shadow for depth */
          background-color: #ffffff; /* White background color */
          position: relative; /* Required for positioning the inner elements */
          overflow: hidden; /* Ensures stripes don't overflow out of the card */
          margin: 2px;
        }

        .card-back::before,
        .card-back::after {
          content: '';
          position: absolute;
          width: 100%;
          height: 100%;
          background: repeating-linear-gradient(
            45deg,
            #ff0000,
            #ff0000 5px,
            #ffffff 5px,
            #ffffff 10px
          );
        }

        .card-back::after {
          transform: rotate(180deg);
        }

    </style>
</head>
<body  state="<?php echo $state?>"gid="<?php echo $gameID ?>" player="<?php echo $player_name ?>" game="<?php echo $game_name ?>">
        <div class="player">
            <div class="turn"></div>
            <div class="player-cards" id="opponent">
               
            </div>
        </div>
        <div class="table">
            <div class="deck">
               <div class="card-back mdzay"></div>
            </div>
            <div class="discard-pile">
                <img class="card played" src="dist/images/cards/base.png" alt="Card">
            </div>
            <div id="free-choice-requested" class="card heartbeat" style="display: none;"></div>
        </div>
        <div class="player">
            <div class="player-cards" id="you">

            </div>
            <div class="actions" style="display: none">
                <button sute="heart" id="play-button" class="choice text-danger">&#9829;</button>
                <button sute="dice" id="play-button" class="choice text-danger">&#9830;</button>
                <button sute="club" id="play-button" class="choice text-dark">&#9827;</button>
                <button sute="spade" id="play-button" class="choice text-dark">&#9824;</button>
            </div>
        </div>
    <script src="jquery.min.js"></script>
    <script src="popper.min.js"></script>
    <script src="bootstrap.min.js"></script>
    <script src="cr8.js"></script>
</body>
</html>
