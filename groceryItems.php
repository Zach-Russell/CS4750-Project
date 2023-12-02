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
    <title>Grocery Items</title>
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
    </style>
</head>
<body>
    <p>
        <a href="myLists.php" class="btn btn-primary" style="background-color: darkred; color: white;">Back to List</a>
    </p>

    <form method="get" action="search.php">
    <label for="search">Search Item Name:</label>
    <input type="text" name="search" id="search">
    <input type="submit" value="Submit">
</form>

<form method="get" action="searchCategory.php">
    <label for="search">Search Category Name:</label>
    <input type="text" name="search" id="search">
    <input type="submit" value="Submit">
</form>

    

    <?php 


    // $arr = array("item1", "item2", "item3");
    // foreach ($arr as $val){
    //     echo "<p>$val</p>";
    // }
    $servername = "mysql01.cs.virginia.edu"; 
    $username = "zhr8wex"; 
    $password = "Fall2023"; 
    $databasename = "zhr8wex"; 

    $conn = mysqli_connect($servername,  
    $username, $password, $databasename); 

    $query = "SELECT * FROM `grocery_items`;"; 

    $result = $conn->query($query); 
    if ($result->num_rows > 0)  
    { 
        

            
        
        // OUTPUT DATA OF EACH ROW 
        while($row = $result->fetch_assoc()) 
        { 
            
            echo
                "<b> Name: ". $row["item_name"]. "</b>".
                " | Category: ". $row["category"].
                " | Date Created: ". $row["date_created"]. "<br>"; 
        } 
    }  
    else { 
        echo "0 results"; 
    } 
    ?>
</body>
</html>