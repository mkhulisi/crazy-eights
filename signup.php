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
        /* Custom styles for the sign-up form */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .signup-form {
            max-width: 350px;
            padding: 30px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body>

<div class="signup-form">
    <h2 class="mb-4">Sign Up</h2>
    <form>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" placeholder="Enter your username" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="text" class="form-control" id="phone" placeholder="Enter your phone number" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" placeholder="Enter your password" required>
        </div>
        <div class="form-group">
            <label for="verifyPassword">Verify Password</label>
            <input type="password" class="form-control" id="verifyPassword" placeholder="Re-enter your password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
        <br>
        <p class="text-center">Have an account? <a href="index.php">LogIn</a></p>
    </form>
</div>

    <!-- Bootstrap JS (optional, for certain Bootstrap components that require JS) -->
    <script src="jquery.min.js"></script>
    <script src="popper.min.js"></script>
    <script src="bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
      $(".player-cards").click(function() {
        var img = $(this).attr("src");

        $(this).parent().fadeOut("fast", function(){
          $(this).remove();
          $(".played").attr("src", img);
        });
      });
    });

    </script>

</body>

</html>
