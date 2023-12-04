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
    renameList($_POST['listName']); 
}

function renameList($g_name){
    $servername = "mysql01.cs.virginia.edu"; 
    $username = "zhr8wex"; 
    $password = "Fall2023"; 
    $databasename = "zhr8wex"; 

    $conn = mysqli_connect($servername,  
    $username, $password, $databasename);

    // $escape_g_name = mysqli_escape_string($conn, $g_name); ;

    //update everything with g_name: grocery_lists, can_edit, grocery_lists_items
    $old_g_name = $_SESSION['g_name'];

    $query2 = "UPDATE `can_edit` SET g_name = '$g_name' WHERE g_name = '$old_g_name'";
    if ($conn->query($query2) === TRUE) {
        echo "Updated can_edit";
    } else {
        echo "Error when  can_edit: " . $conn->error;
    }

    $query1 = "SELECT * FROM `grocery_lists_items` WHERE g_name = '$old_g_name'";
    $result = $conn->query($query1);

    //empty list before we delete it
    if ($result->num_rows > 0) {
        while ($itemRow = $result->fetch_assoc()) {
            $itemName = $itemRow["item_name"];
            updateItems($itemName, $g_name, $old_g_name);
        }
    }

    $query3 = "UPDATE `grocery_lists` SET g_name = '$g_name' WHERE g_name = '$old_g_name'";
    if ($conn->query($query3) === TRUE) {
        echo "Updated grocery_lists";
    } else {
        echo "Error when grocery_lists: " . $conn->error;;
    }


    $conn->close();
    header("Location: myLists.php"); 
}

function updateItems($itemName, $gName, $old_g_name){
    $servername = "mysql01.cs.virginia.edu"; 
    $username = "zhr8wex"; 
    $password = "Fall2023"; 
    $databasename = "zhr8wex"; 

    $conn = mysqli_connect($servername,  
    $username, $password, $databasename);

    // $escape_gName = mysqli_escape_string($conn, $gName);
    // $escape_itemName = mysqli_escape_string($conn, $itemName);

    $query = "UPDATE `grocery_lists_items` SET g_name = '$gName' WHERE g_name = '$old_g_name' AND item_name = '$itemName'";
    if ($conn->query($query) === TRUE) {
        echo "Updated grocery_lists_items";
    } else {
        echo "Error when grocery_lists_items: " . $conn->error;
    }
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
    <div class="text-container"><h1> Choose a New Name for <?php echo $_SESSION['g_name']?>:</h1></div>
    <br>
    <form method="post" action="renameList.php">
        <input type="text" name="listName">
        <input type="submit" value="Rename" name="submitListName"> <!-- assign a name for the button -->
    </form>

   
</body>
</html>