<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect them to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>

<?php
    if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['addBtn']))
    {
        // addToList();
        $_SESSION['g_name'] = $_POST['display'];
        header("Location: groceryItems.php");
    }
    if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['deleteBtn']))
    {
        deleteFromList($_POST['display']);

    }

    // function addToList()
    // {
    //     echo "<script>alert('Add to your ". $_POST['display']." List');</script>"; 
    // }
    function deleteFromList($val)
    {
        // require_once "dbconnection.php";
        // $email = $_SESSION['email'];
        // $g_name = $_POST['listName'];

        // $sqlB = "DELETE FROM can_edit WHERE g_name = $g_name AND email = $email";
    
        // if($stmtB = $db->prepare($sqlB)){
        //     // Bind variables to the prepared statement as parameters
        //     $stmtB->bindParam(":g_name", $param_g_name, PDO::PARAM_STR);
        //     $stmtA->bindParam(":email", $param_email, PDO::PARAM_STR);
            
        //     // Set parameters
        //     $param_g_name = $g_name;
        //     $param_email = $email;
            
        // }
        // // Attempt to execute the prepared statement
        // try{
        //     $stmtB->execute();
        // }catch(error){ echo "Opps! Something went wrong with stmtB. Please try again later.";}

        // // Close statement

        // $sqlA = "DELETE FROM can_edit WHERE g_name = $g_name AND email = $email";
        // if($stmtA = $db->prepare($sqlA)){
        //     $stmtA->bindParam(":g_name", $param_g_name, PDO::PARAM_STR);
            
            
        //     // Set parameters
        //     $param_g_name = $g_name;
        // }
        // // Attempt to execute the prepared statement
        // try{
        //     $stmtA->execute();
        //     header("Location: myLists.php");
        // }catch(error){echo "Opps! Something went wrong with stmtA. Please try again later.";}

        // unset($stmtB);
        // unset($stmtA);

        // // Close connection
        // unset($db);

        $servername = "mysql01.cs.virginia.edu"; 
        $username = "zhr8wex"; 
        $password = "Fall2023"; 
        $databasename = "zhr8wex"; 

        $conn = mysqli_connect($servername,  
        $username, $password, $databasename); 

        $g_name = mysqli_escape_string($conn, $val); 
        $email = mysqli_escape_string($conn, $_SESSION['email']);

        $sql = "DELETE FROM `can_edit` WHERE email = '$email' AND g_name = '$g_name' ;"; 

        if ($conn->query($sql) === TRUE) {
            echo "Record1 was deleted";
        } else {
            echo "Encountered error when deleting record1: " . $conn->error;
        }

        $sql2 = "DELETE FROM `grocery_lists` WHERE g_name = '$g_name' ;"; 

        if ($conn->query($sql2) === TRUE) {
            echo "Record2 was deleted";
        } else {
            echo "Encountered error when deleting record2: " . $conn->error;
        }

        $conn->close();
        header("Location: myLists.php");
        // echo "<script>alert('Delete from your ". $_POST['display']." List');</script>"; 
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
        <a href="createList.php" class="btn btn-primary">Create New List</a>
    </p>
    <p>
        <a href="" class="btn btn-primary">Delete List</a>
    </p>
    <p>
        <a href="index.php" class="btn btn-primary" style="background-color: darkred; color: white;">Go Home</a>
    </p>
    <div class="text-container"><h1> Active Lists: </h1></div>
    <?php 
        $servername = "mysql01.cs.virginia.edu";
        $username = "zhr8wex";
        $password = "Fall2023";
        $databasename = "zhr8wex";
        
        $conn = mysqli_connect($servername, $username, $password, $databasename);
        
        $currentEmail = mysqli_escape_string($conn, $_SESSION['email']);
        
        $query = "SELECT g_name FROM `can_edit` WHERE email = '$currentEmail'";
        $result = $conn->query($query);
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $listName = $row["g_name"];
        
                // echo "<h2>List Name: $listName</h2>";
        
                echo
                            "<form method='post' action='myLists.php'><input type='submit' name='addBtn' value='+'></input><input type='submit' name='removeBtn' value='-'></input><input type='hidden' name='display' value='". $row["g_name"]."'></input>".
                            "<b> List Name: ". $row["g_name"]. "</b> <input type='submit' name='renameBtn' value='Rename'> <input type='submit' name='deleteBtn' value='Delete'> </form>"; 
        
                $itemsQuery = "SELECT item_name FROM `grocery_lists_items` WHERE g_name = '$listName'";
                $itemsResult = $conn->query($itemsQuery);
        
                if ($itemsResult->num_rows > 0) {
                    while ($itemRow = $itemsResult->fetch_assoc()) {
                        $itemName = $itemRow["item_name"];
        
                        echo "<p>$itemName</p>";
                    }
                } else {
                    echo "<p>No items in this list</p>";
                }
            }
        } else {
            echo "<p>No Lists</p>";
        }
        
        $conn->close();
    ?>
</body>
</html>