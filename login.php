<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
}
 
// Include connection file
require_once "dbconnection.php";
 
// Define variables and initialize with empty values
$email = $password = "";
$email_err = $password_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if email is empty
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter email.";
    } else{
        $email = trim($_POST["email"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = htmlspecialchars(trim($_POST["password"]));
    }

if(empty($email_err) && empty($password_err)){
  // Prepare a select statement
  $sql = "SELECT email, pwd FROM users WHERE email = :email";
  $sqlN = "SELECT email, name FROM grocery_shopper WHERE email = :email";
  
  if($stmt = $db->prepare($sql)){
      // Bind variables to the prepared statement as parameters
      $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
      
      // Set parameters
      $param_email = trim($_POST["email"]);
      
      // Attempt to execute the prepared statement
      if($stmt->execute()){
          // Check if email exists, if yes then verify password
          if($stmt->rowCount() == 1){
              if($row = $stmt->fetch()){
                  $email = $row["email"];
                  $hashed_password = $row["pwd"];

                  if(password_verify($password, $hashed_password)){
                        //Password is correct, so fetch the name from grocery_shopper and send the name
                        if($stmtN = $db->prepare($sqlN)){
                            $stmtN->bindParam(":email", $param_email, PDO::PARAM_STR);
                            $param_email = trim($_POST["email"]);
                            if($stmtN->execute()){
                                $result = $stmtN->fetch();
                                $name = $result["name"];
                            }
                        }

                        // Password is correct, so start a new session
                        session_start();
                        
                        // Store data in session variables
                        $_SESSION["loggedin"] = true;
                        $_SESSION["email"] = $email;  
                        $_SESSION["name"] = $name;                          
                        
                        // Redirect user to welcome page
                        header("location: index.php");
                        exit;
                  } else{
                      // Password is not valid, display a generic error message
                      $login_err = "Invalid Email or password.";
                  }
              }
          } else{
              // email doesn't exist, display a generic error message
              $login_err = "Invalid email or password.";
          }
      } else{
          echo "Oops! Something went wrong. Please try again later.";
      }

      // Close statement
      unset($stmt);
      unset($stmtN);
  }
}

// Close connection
unset($db);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
  body{ font: 14px sans-serif; }
  .wrapper{ width: 360px; padding: 20px; }
</style>
</head>
<body>
<div class="wrapper">
  <h2>Login</h2>
  <p>Please fill in your credentials to login.</p>

  <?php 
  if(!empty($login_err)){
      echo '<div class="alert alert-danger">' . $login_err . '</div>';
  }        
  ?>

  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <div class="form-group">
          <label>Email</label>
          <input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
          <span class="invalid-feedback"><?php echo $email_err; ?></span>
      </div>    
      <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
          <span class="invalid-feedback"><?php echo $password_err; ?></span>
      </div>
      <div class="form-group">
          <input type="submit" class="btn btn-primary" value="Login">
      </div>
      <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
  </form>
</div>
</body>
</html>