<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect them to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    //Validate name
    if(empty(trim($_POST["name"]))){
        $name_err = "Please enter a name.";
    }else if(!preg_match('/[a-zA-Z]{2,}\s{1,}[a-zA-Z]{2,}/', trim($_POST["name"]))){
        $name_err = "You must type in a first and a last name";
    }else{
        $name = trim($_POST["name"]);
    }

    // Validate Email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter an email.";
    } elseif(!preg_match('/^[a-zA-Z0-9+.-]+@[a-zA-Z0-9.-]+$/', trim($_POST["email"]))){
        $email_err = "Email can only contain letters, numbers, and underscores.";
    } else{
        // Prepare a select statement
        $sql = "SELECT * FROM `users` WHERE email = :email";
        
        if($stmt = $db->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            
            // Set parameters
            $param_email = trim($_POST["email"]);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $email_err = "This email is already taken.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = htmlspecialchars(trim($_POST["password"]));
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = htmlspecialchars(trim($_POST["confirm_password"]));
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($name_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare an insert statement
        // $sqlA = "UPDATE grocery_shopper(email, name) VALUES (:email, :name)";
        // $sql = "UPDATE users (email, pwd) VALUES (:email, :password)";
        $sqlA = "UPDATE grocery_shopper(email, name) SET (:email, :name)";
         
        if($stmtA = $db->prepare($sqlA)){
            $stmtA->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmtA->bindParam(":name", $param_name, PDO::PARAM_STR);
            
            // Set parameters
            $param_email = $email;
            $param_name = $name;
        }
       
        if($stmt = $db->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            
            // Set parameters
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
        }
        // Attempt to execute the prepared statement
        try{
            $stmtA->execute();
            $stmt->execute();
            header("Location: login.php");
        }catch(error){ echo "Opps! Something went wrong. Please try again later.";}

        // Close statement
        unset($stmtA);
        unset($stmt);
    }
    
    // Close connection
    unset($db);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; text-align: center; }
        
        .btn:hover {
            /* font-size: 0.875rem; */
            line-height: 1;
            font-weight: 400;
            padding: .7rem 1.5rem;
            border-radius: 0.1275rem
        }
        body {
            background-image: url('picnic.jpeg');
            
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;

            height: 100vh;

            /* display: flex; */
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .text-container {
            background-color: white;
            padding: 1px;
            border-radius: 10px; 

            color: #e75480;
            border: 1px solid #e75480;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class = "text-container">
    <h1 class="my-5"> <b><?php echo htmlspecialchars($_SESSION["name"]); ?>'s Settings</b></h1></div>
    <br>
    <p>
        <a href="index.php" class="btn btn-primary" style="background-color: darkred; color: white;">Go Home</a>
    </p>
    <div class = "text-container">
        <h2>Current Information</h2>
        <b>Name:</b> <?php echo htmlspecialchars($_SESSION["name"]); ?>
        <b>Email:</b> <?php echo htmlspecialchars($_SESSION["email"]); ?>
        <!-- <b>Password:</b> <?php echo htmlspecialchars($_SESSION["confirm_password"]); ?> -->
        <!-- $email = $name = $password = $confirm_password = "";
$email_err = $name_err= $password_err = $confirm_password_err = ""; -->
<?php
$servername = "mysql01.cs.virginia.edu"; 
$username = "zhr8wex"; 
$password = "Fall2023"; 
$databasename = "zhr8wex"; 

$conn = mysqli_connect($servername,  
$username, $password, $databasename); 
$query = "SELECT * FROM `grocery_shopper`;"; 

$result = $conn->query($query); 

    // OUTPUT DATA OF EACH ROW 
    // while($row = $result->fetch_assoc()) 
    // { 
    //     if ($email = $row["email"]) {
    //     echo
    //         "<b> Name: ". $row["name"]. "</b>".
    //         " | Email: ". $row["email"]. "<br>"; 
    //     }
    // } 

?>


        <!-- <p>Please fill this form to edit your account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
        </form> -->
    </div>
</body>
</html>
