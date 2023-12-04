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
    <p>
        <a href="myLists.php" class="btn btn-primary">Back My Lists</a>
    </p>
    <h1> Choose a name for your list:</h1>
    <form method="post" action="createList.php">
        <input type="text" name="listName">
        <input type="submit" value="Create" name="submitListName"> <!-- assign a name for the button -->
    </form>
</html>