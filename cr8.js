// the game plays here
$(document).ready(function() {

  var gameId = $("body").attr("gid");
  var player_name = $("body").attr("player");
  var game_name = $("body").attr("game");
  var myCards = 0;
  var myPlayedCard = "";
  var baseCard = "";
  var played2Card = "";
  var oponentRemaining = 8;
  var myId = 0;
  var gameOverFlag = false;

  var socket = new WebSocket(`ws://192.168.88.196:8080?game_id=${gameId}&player_name=${player_name}&game_name=${game_name}`);

  // Event handler for when the WebSocket connection is open
  socket.onopen = function(event) {
    console.log('WebSocket connection opened');
    // Send a test message to the server
    socket.send('init');
  };

  // Event handler for incoming messages from the WebSocket server
  socket.onmessage = function(event) {
    //parse WebSocket server response message
    eventData = JSON.parse(event.data);
    console.log(eventData);

    if (eventData.remaining !== undefined) {
      oponentRemaining = eventData.remaining;
    }


    if (event.data.includes('gameStart')) {
      //clear waiting for client message
      $("#opponent").html("");
    
      const oponentCardDiv = `<div class="card-back"></div>`;
      for(var x = 0; x < 8; x++) {
       $("#opponent").append(oponentCardDiv);
      }
    }

    //alert player about disconnected player
    if (event.data.includes('opponentDisconnected') && gameOverFlag == false) {
      oponentLeftGame();
    }
    //handle card dishing
    if (event.data.includes("dish")) {
      handleDishing();
    }

    //handle free choice request
    if (event.data.includes('choice')) {
      handleFreeChoiceRequest();
    }

    //check if its not my turn to play
    else if (eventData.myturn == "no") {
      console.log("Not My Turn");
      //update played cards with card played by oponent
      $(".played").attr("src", "dist/images/cards/"+eventData.playedCard);
      $(".played").attr("card", eventData.playedCard);
      baseCard = eventData.playedCard;

      disablePlayingCards();
      if (oponentRemaining == 0) {
        gameOver(0);
      }
      //handle mdzayi depleted condition
      else if (oponentRemaining == 'md') {
        if (myCards < oponentRemaining) {
          gameOver(1);
        }
        else if (myCards == oponentRemaining) {
          gameOver(2);
        }
        else{
          gameOver(0);
        }
      }
    }
    //handle mddzayi response, receiving dzayed card and perfom relevent actions on the card
    else if (event.data.includes("mdzayi")) {
      handleMdzayiResponse();
    }
    else if (event.data.includes('doublepicked')) { //check if array contains index "doublepicked"
      console.log("ddd ", eventData);
      handleDoublePick(eventData[1][0], eventData[1][1]);
    }
    else if (eventData.playedCard) {
      //handle if received played card is 2
      if (eventData.playedCard.startsWith('2') && eventData.doublepick == "yes") {

        if (oponentRemaining == 0) {
          gameOver(0);
        }
        else{
          played2Card = eventData.playedCard;
          console.log("Event.data = ", eventData);
          $(".played").attr("src", "dist/images/cards/"+eventData.playedCard);
          $(".played").attr("card", eventData.playedCard);
          baseCard = "2_suite.png";
          $(".played").attr("card","2_suite.png");
          enablePlayingCards();
        }
      }
      else{
        //update played cards with card played by oponent
        $(".played").attr("src", "dist/images/cards/"+eventData.playedCard);
        $(".played").attr("card", eventData.playedCard);

        //handle if base was updated wth requested choice
        if (event.data.includes('choice')) {
          baseCard = "8_"+eventData.choice+".png";
        }
        else{
          baseCard = eventData.playedCard;
        }
        
        if (eventData.myturn != "yes" && eventData.myturn != "no") {
          console.log("eventData.myturn is not understood its ", eventData.myturn);
        }
        //dont enable playing board cards
        if (eventData.myturn == 'no') {
          //do nothing
          Console.log("Its not my turn played card is ", playedCard)
        }
        else{

          //handle win cases
          if (oponentRemaining == 0) {
            gameOver(0);
            //disable playing board cards
            disablePlayingCards();
          }
          else{
            //enable playing board cards
            enablePlayingCards();
          }
        }
      }
    }

    //code for showing oponent cards in real time
    if (!eventData.remaining) {
      //
    }
    else{
      oponentRemaining = eventData.remaining;

      $("#opponent").html("");

      var oponentCardDiv = `<div class="card-back"></div>`;
      for(var x = 0; x < oponentRemaining; x++) {
        $("#opponent").append(oponentCardDiv);
      }
    }
    console.log("Oponent cards ", oponentRemaining);
  };


  // Event handler for WebSocket connection errors
  socket.onerror = function(error) {
    console.error('WebSocket error:', error);
  };

  // Event handler for WebSocket connection closure
  socket.onclose = function(event) {
      console.log('WebSocket connection closed');
  };

  $(document).on('click', '.player-card', function() {

    //get clicked card file name
    myPlayedCard = $(this).attr("card");
    console.log("My Played card ", myPlayedCard);

    //free choice if card is 8
    console.log(eventData.doublepick);
    if (myPlayedCard.startsWith('8') && (eventData.doublepick == 'no' || eventData.doublepick === undefined)) {
      //disable cards
      disablePlayingCards();
      
      //hide requested free choice
      $("#free-choice-requested").hide();
      myCards--;

      animateCard(this, '.played', 500, myPlayedCard);
      

      $(this).fadeOut(500, function() {
        $(this).remove();
        //handle win cases
        if (myCards == 0) {
          socket.send(JSON.stringify({'playedCard': myPlayedCard, 'remaining': myCards, 'myturn': 'yes', 'gameId': gameId, 'doublepick': 'no'}));
          gameOver(1);
        }
        else{
          $(".actions").show();
        }
      });
    }
    else{
    
      if (matchingRule(myPlayedCard, baseCard)) {
        //hide requested free choice
        $("#free-choice-requested").hide();

        //handle jump and repeat play for 7 and j cards
        if (myPlayedCard.startsWith('7') || myPlayedCard.startsWith('j')) {

          myCards--;

          animateCard(this, '.played', 500, myPlayedCard);

          $(this).fadeOut(500, function() {
            $(this).remove();
            enablePlayingCards();
          });

          //handle win cases
            if (myCards == 0) {
              gameOver(1);
            }

          socket.send(JSON.stringify({'playedCard': myPlayedCard, 'remaining': myCards, 'myturn': 'no', 'gameId': gameId, 'doublepick': 'no'}));
          //update played cards
          baseCard = myPlayedCard;
          console.log("sent ", myPlayedCard);
        }
        else{

          //handle sigle play chance
          myCards--;
          disablePlayingCards()
          animateCard(this, '.played', 500, myPlayedCard);
          console.log(this);

          $(this).fadeOut(500, function() {
            $(this).remove();

            //handle win cases
            if (myCards == 0) {
              gameOver(1);
            }
            else{
              
            }
          });
          if (myPlayedCard.startsWith('2')) {
            socket.send(JSON.stringify({'playedCard': myPlayedCard, 'remaining': myCards, 'myturn': 'yes', 'gameId': gameId, 'doublepick': 'yes'}));
          }
          else{
            
            socket.send(JSON.stringify({'playedCard': myPlayedCard, 'remaining': myCards, 'myturn': 'yes', 'gameId': gameId, 'doublepick': 'no'}));
          }

          //update played cards
          baseCard = myPlayedCard;
        }
      }
      else{
        //animate invalid
        $(this).addClass("shake");

        setTimeout(() => {
          $(this).removeClass("shake");
        }, 500);
      }
    }
    console.log("My cards ", myCards);
  });

  //handel free choice chosen sute
  $(document).on('click', '.choice', function() {
    $(".actions").hide();
    var sute = $(this).attr("sute");
    socket.send(JSON.stringify({'playedCard': myPlayedCard, 'remaining': myCards, 'myturn': 'yes', 'gameId': gameId, 'choice': sute, 'doublepick': 'no'}));
  });


  //umdzayi
  $(document).on("click",".mdzay", function() {
    //disable cards
    disablePlayingCards();
    $("#free-choice-requested").hide();

    if (baseCard.startsWith('2') && eventData.doublepick == "yes") {
      socket.send(JSON.stringify({'playedCard': 'doublepick', 'remaining': myCards, 'myturn': 'yes', 'gameId': gameId}));
    }
    else{
      socket.send(JSON.stringify({'playedCard': 'mdzayi', 'remaining': myCards, 'myturn': 'yes', 'gameId': gameId}));
    }    
  });


  //game over
  function gameOver(state) {
    if (state === 1) {
      gameOverFlag = true;

      $("#you").html("<center><h3 class='text-center text-white'>Game Over!</h3><p>You Have Won</p><a class='btn btn-primary' href='home.php'>Create or Join a game</a></center>");
    }
    else if (state == 0) {
      gameOverFlag = true;
      $("#opponent").html("<center><h3 class='text-center text-white'>Game Over!</h3><p>You Have Lost</p><a class='btn btn-primary' href='home.php'>Create or Join a game</a></center>");
    }
    else{
      gameOverFlag = true;
      $(".game-over").html("<h3 class='text-center text-white'>Game Over!</h3><p>Its a tie</p><a class='btn btn-primary' href='home.php'>Create or Join a game</a>");
    } 
  }

  //handle oponent disconnection
  function oponentLeftGame() {
    $("#opponent").html("<center><h3 class='text-center text-white'>Player disconnected!</h3><p>The other player has left the game</p><a class='btn btn-danger' href='home.php'>Create or Join a game</a></center><br><br>")
     disablePlayingCards();
  }

  //hanlde double pick
  function handleDoublePick(card1, card2) {
    disablePlayingCards();
   
    animateCardDoublPick('.mdzay', '#you', 500, card1, "no", "no",);

   

    myCards = myCards + 2;

    console.log("My cards after double pick", myCards);

    baseCard = played2Card;

    $("#you").prepend(`<img card='${card1}' class='player-card card' src='dist/images/cards/${card1}' alt='Card image cap'>
      <img card='${card2}' class='player-card card' src='dist/images/cards/${card2}' alt='Card image cap'>`);

    socket.send(JSON.stringify({'playedCard': played2Card, 'remaining': myCards, 'myturn': 'yes', 'gameId': gameId, 'doublepick': 'no'}));
  }

  //handle mdzayi response
  function handleMdzayiResponse() {
    //store mdzayi card for use in updating played card
    var mdzayiCard = eventData[1];

    if (mdzayiCard == "depleted") {
      if (myCards < oponentRemaining) {
        gameOver(1);
        socket.send(JSON.stringify({'playedCard': myPlayedCard, 'remaining': 'md', 'myturn': 'no', 'gameId': gameId, 'doublepick': 'no'}));
      }
      else if (myCards == oponentRemaining) {
        gameOver(2);
        socket.send(JSON.stringify({'playedCard': myPlayedCard, 'remaining': 'md', 'myturn': 'no', 'gameId': gameId, 'doublepick': 'no'}));
      }
      else{
        gameOver(0);
        socket.send(JSON.stringify({'playedCard': myPlayedCard, 'remaining': 'md', 'myturn': 'no', 'gameId': gameId, 'doublepick': 'no'}));
      }
    }
    else{

      if (mdzayiCard.startsWith('8')) {
        myPlayedCard = mdzayiCard;

        animateCard('.mdzay', '.played', 500, myPlayedCard, "no");

        $(".played").attr("src", "dist/images/cards/"+mdzayiCard);
        disablePlayingCards();

        //handle win cases
        if (myCards == 0) {
          gameOver(1);
        }
        else{
          //free choice if card is 8
          $(".actions").show();
        }
      }
      else{ 

        //handle if mdzayi card is playable
        if (matchingRule(mdzayiCard, baseCard)) {

          console.log("Mdzayed card is ", mdzayiCard)

          //check reapeting rule for 7 and j
          if (mdzayiCard.startsWith('7') || mdzayiCard.startsWith('j')) {

            enablePlayingCards();

            socket.send(JSON.stringify({'playedCard': myPlayedCard, 'remaining': myCards, 'myturn': 'no', 'gameId': gameId, 'doublepick': 'no'}));
            
            $(".played").attr("src", "dist/images/cards/"+myPlayedCard);

           //handle win cases
            if (myCards == 0) {
              gameOver(1);
            }
          }
          else if (mdzayiCard.startsWith('2')) {
            console.log("Mdzayi is the rank 2 detected");
            //handle win cases
            if (myCards == 0) {
              gameOver(1);
            }
            animateCard('.mdzay', '.played', 500, mdzayiCard, "no");
            socket.send(JSON.stringify({'playedCard': mdzayiCard, 'remaining': myCards, 'myturn': 'yes', 'gameId': gameId, 'doublepick': 'yes'}));
          }
          else{
            animateCard('.mdzay', '.played', 500, mdzayiCard, "no");

            socket.send(JSON.stringify({'playedCard': mdzayiCard, 'remaining': myCards, 'myturn': 'yes', 'gameId': gameId, 'doublepick': 'yes'}));
            $(".played").attr("src", "dist/images/cards/"+mdzayiCard);
          }  
        }
        else{
          //if card does not match base card, put mdzayi card into plyer card stack
          animateCard('.mdzay', '#you', 500, mdzayiCard, "no", "no");


          myCards++;

          socket.send(JSON.stringify({'playedCard': baseCard, 'remaining': myCards, 'myturn': 'yes', 'gameId': gameId, 'doublepick': 'no'}));
        }
      }
    }
  }

  //handle free choice request
  function handleFreeChoiceRequest() {
    let sute = "";
    switch(eventData.choice) {
      case "heart":
        sute = "&#9829;";
        break;
      case "dice":
        sute = "&#9830;";
        break;
      case "club":
        sute = "&#9827;";
        break;
      case "spade":
        sute = "&#9824;";
        break;
      default:
      console.log("no valid selected sute");
    }
    //update played cards with card played by oponent
    $(".played").attr("src", "dist/images/cards/"+eventData.playedCard);
    $(".played").attr("card", "8_"+eventData.choice+".png");
    baseCard = "8_"+eventData.choice+".png";

    //enable board cards
    enablePlayingCards();

    $("#free-choice-requested").show();    
    $("#free-choice-requested").html(sute);
  }

  //handling initial card dishing
  function handleDishing() {

    //initialise variables
    gameId = eventData[9];
    myId = eventData[10];
    baseCard = eventData[11];

    //se base card
    $(".played").attr("src", `dist/images/cards/${baseCard}`);
    $(".played").attr("card", baseCard);

    //put dished card on the board div
    eventData.slice(0, 8).forEach(filename => {
      const cardDiv = `<img card='${filename}' class='player-card card' src="dist/images/cards/${filename}" alt="2 of Hearts">`;
      $("#you").append(cardDiv);
      console.log("my id == ", myId);
      console.log("dished for creater");

      if (myId != gameId) {
        $("#opponent").html("");
        const oponentCardDiv = `<div class="card-back"></div>`;
        for(var x = 0; x < 8; x++) {
         $("#opponent").append(oponentCardDiv);
        }
        console.log("dished for joiner");
      }
      else{
        $("#opponent").html('<h5 class="text-center text-danger">Waiting for guest player to join game...</h5>');
      }
    });
    console.log(eventData);
    //place base card
      myCards = 8;
  }

  //function for disabling playing cards
  function disablePlayingCards() {
    $(".turn").text("");
    $("#you").find("img").prop("disabled", true);
    $(".mdzay").prop("disabled", true);
  }

  //function for enabling playing cards
  function enablePlayingCards() {
    $(".turn").text("Your turn");
    $("#you").find("img").prop("disabled", false);
    $(".mdzay").prop("disabled", false);
  }

  // Function to check if there is a common substring before "_" or between "_" and "."
  function matchingRule(fileName1, fileName2) {
    // Extract substrings before "_" and between "_" and "." for both file names
    const parts1 = fileName1.split('_');
    const parts2 = fileName2.split('_');
    console.log("File1", fileName1);
    console.log("File2", fileName2);
    if (parts1.length < 2 || parts2.length < 2) {
      // If either file name doesn't have the expected format, return false
      return false;
    }

    const prefix1 = parts1[0]; // Substring before "_"
    const suffix1 = parts1[1].split('.')[0]; // Substring between "_" and "."

    const prefix2 = parts2[0]; // Substring before "_"
    const suffix2 = parts2[1].split('.')[0]; // Substring between "_" and "."

    // Check for common prefix or suffix
    if (prefix1 === prefix2 || suffix1 === suffix2) {
        return true;
    }

    return false;
  }

  function animateCard(cardSelector, destinationSelector, duration, myPlayedCard, move = "yes", match = "yes") {
    var destination = $(destinationSelector).offset();
    var card = $(cardSelector);
    var cardPosition = card.offset();
    
    var deltaX = destination.left - cardPosition.left;
    var deltaY = destination.top - cardPosition.top;

    if (move === "yes") {
      card.animate({
        top: `+=${deltaY}px`,
        left: `+=${deltaX}px`
      }, duration, function() {
        // Callback function to execute after animation completes
        $(".played").attr("src", "dist/images/cards/" + myPlayedCard);
      });
    }
    else {
      var clonedCard = card.clone().appendTo('body');
      clonedCard.css({
        position: 'absolute',
        top: cardPosition.top,
        left: cardPosition.left
      }).animate({
        top: `+=${deltaY}px`,
        left: `+=${deltaX}px`
      }, duration, function() {
        // Callback function to execute after animation completes
        if(match === "yes"){
          console.log("Matched mdzay");
          $(".played").attr("src", "dist/images/cards/" + myPlayedCard);
          clonedCard.remove(); // Remove the cloned card after animation
        }
        else{
          clonedCard.remove(); // Remove the cloned card after animation
          $("#you").prepend(`<img card='${myPlayedCard}' class='player-card card' src='dist/images/cards/${myPlayedCard}' alt='Card image cap'>`);
        }
      });
    }
  }

   function animateCardDoublPick(cardSelector, destinationSelector, duration, myPlayedCard, move = "yes", match = "yes") {
    var destination = $(destinationSelector).offset();
    var card = $(cardSelector);
    var cardPosition = card.offset();
    
    var deltaX = destination.left - cardPosition.left;
    var deltaY = destination.top - cardPosition.top;

    if (move === "yes") {
      card.animate({
        top: `+=${deltaY}px`,
        left: `+=${deltaX}px`
      }, duration, function() {
        // Callback function to execute after animation completes
        $(".played").attr("src", "dist/images/cards/" + myPlayedCard);
      });
    }
    else {
      var clonedCard = card.clone().appendTo('body');
      clonedCard.css({
        position: 'absolute',
        top: cardPosition.top,
        left: cardPosition.left
      }).animate({
        top: `+=${deltaY}px`,
        left: `+=${deltaX}px`
      }, duration, function() {
        // Callback function to execute after animation completes
        if(match === "yes"){
          console.log("Matched mdzay");
          $(".played").attr("src", "dist/images/cards/" + myPlayedCard);
          clonedCard.remove(); // Remove the cloned card after animation
        }
        else{
          console.log(" picked ", myPlayedCard);
          clonedCard.remove(); // Remove the cloned card after animation
        }
      });
    }
  }
});