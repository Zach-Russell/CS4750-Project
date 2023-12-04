<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect them to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['submitListName']))
    {
    
        require_once "dbconnection.php";
        $email = $_SESSION['email'];
        $g_name = $_POST['listName'];

            
        $sqlB = "INSERT INTO grocery_lists(g_name) VALUES (:g_name)";
    
        if($stmtB = $db->prepare($sqlB)){
            // Bind variables to the prepared statement as parameters
            $stmtB->bindParam(":g_name", $param_g_name, PDO::PARAM_STR);
            
            // Set parameters
            $param_g_name = $g_name;
            
        }
        // Attempt to execute the prepared statement
        try{
            $stmtB->execute();
        }catch(error){ echo "Opps! Something went wrong with stmtB. Please try again later.";}

        // Close statement
        
        


        $sqlA = "INSERT INTO can_edit(g_name, email) VALUES (:g_name, :email)";
        if($stmtA = $db->prepare($sqlA)){
            $stmtA->bindParam(":g_name", $param_g_name, PDO::PARAM_STR);
            $stmtA->bindParam(":email", $param_email, PDO::PARAM_STR);
            
            // Set parameters
            $param_g_name = $g_name;
            $param_email = $email;
        }
        // Attempt to execute the prepared statement
        try{
            $stmtA->execute();
            header("Location: myLists.php");
        }catch(error){echo "Opps! Something went wrong with stmtA. Please try again later.";}

        unset($stmtB);
        unset($stmtA);


        
        // Close connection
        unset($db);


        // $servername = "mysql01.cs.virginia.edu"; 
        // $username = "zhr8wex"; 
        // $password = "Fall2023"; 
        // $databasename = "zhr8wex"; 

        // $conn = mysqli_connect($servername,  
        // $username, $password, $databasename); 
        // // Check connection
        // if ($conn->connect_error) {
        // die("Connection failed: " . $conn->connect_error);
        // }
        // $email = mysqli_escape_string($conn, $_SESSION['email']);
        // $g_name = mysqli_escape_string($conn, $_POST['listName']);

        // // $email = $_SESSION['email'];
        // // $g_name = $_POST['listName'];

        // $sql = "INSERT INTO `can_edit`(`email` , `g_name`) SET `email` = $email , `g_name` = $g_name";

        // $sql2 = "INSERT INTO `grocery_lists`(`g_name`) SET `g_name` = $g_name";

        // if ($conn->query($sql) === TRUE) {
        //     echo "New record created successfully";
        // } else {
        //     echo "Error: " . $sql . "<br>" . $conn->error;
        // }

        // if ($conn->query($sql2) === TRUE) {
        //     echo "New record created successfully";
        // } else {
        //     echo "Error: " . $sql2 . "<br>" . $conn->error;
        // }

        // $conn->close();
        // header("Location: myLists.php");
    }
?>

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    
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
<p>
        <a href="myLists.php" class="btn btn-primary">Back My Lists</a>
    </p>
    <div class="text-container"><h1> Choose a name for your list:</h1></div>
    <br>
    <form method="post" action="createList.php">
        <input type="text" name="listName">
        <input type="submit" value="Create" name="submitListName"> <!-- assign a name for the button -->
    </form>

   
</body>
</html>