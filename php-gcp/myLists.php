<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect them to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>myLists</title>
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
<body><div class="text-container">
    <h1 class="my-5"> <b><?php echo htmlspecialchars($_SESSION["name"]); ?>'s Lists</b></h1></div>
    
    <p>
        <a href="groceryItems.php" class="btn btn-primary">Add Item</a>
    </p>
    <p>
        <a href="" class="btn btn-primary">Delete List</a>
    </p>
    <p>
        <a href="index.php" class="btn btn-primary" style="background-color: darkred; color: white;">Go Home</a>
    </p>
    <div class="text-container"><h1> Current List: </h1></div>
    <?php 
        $servername = "mysql01.cs.virginia.edu"; 
        $username = "zhr8wex"; 
        $password = "Fall2023"; 
        $databasename = "zhr8wex"; 
    
        $conn = mysqli_connect($servername,  
        $username, $password, $databasename); 
    
        $query = "SELECT * FROM `can_edit` WHERE `email` =". $_SESSION["email"] .";";  //Error with this SQL syntax 
    
        $result = $conn->query($query); 
        if ($result->num_rows > 0)  
        { 
            
            // OUTPUT DATA OF EACH ROW 
            while($row = $result->fetch_assoc()) 
            { 
                
                echo
                    "<form method='post' action='myLists.php'><input type='submit' name='addBtn' value='+'></input><input type='submit' name='deleteBtn' value='-'></input><input type='hidden' name='display' value='". $row["item_name"]."'></input>".
                    "<b> Name: ". $row["g_name"]. "</b>"; 
            } 
        }  
        else { 
            echo "No Lists"; 
        } 

    ?>
</body>
</html>