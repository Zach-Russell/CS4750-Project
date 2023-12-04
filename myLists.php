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
        deleteList($_POST['display']);

    }
    if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['removeBtn']))
    {
        removeFromList($_POST['delete_item_name'], $_POST['delete_g_name'], true);

    }
    if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['renameBtn']))
    {   
        $_SESSION['g_name'] = $_POST['display'];
        header("Location: renameList.php");
    }

    

    // function addToList()
    // {
    //     echo "<script>alert('Add to your ". $_POST['display']." List');</script>"; 
    // }
    function deleteList($val)
    {

        $servername = "mysql01.cs.virginia.edu"; 
        $username = "zhr8wex"; 
        $password = "Fall2023"; 
        $databasename = "zhr8wex"; 

        $conn = mysqli_connect($servername,  
        $username, $password, $databasename); 

        $g_name = mysqli_escape_string($conn, $val); 
        $email = mysqli_escape_string($conn, $_SESSION['email']);

        $query = "SELECT * FROM `grocery_lists_items` WHERE g_name = '$g_name'";
        $result = $conn->query($query);

        //empty list before we delete it
        if ($result->num_rows > 0) {
            while ($itemRow = $result->fetch_assoc()) {
                $itemName = $itemRow["item_name"];
                removeFromList($itemName, $val, false);
            }
        }

        //delete list
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

    function removeFromList($item_name, $g_name, $refresh){

        $servername = "mysql01.cs.virginia.edu"; 
        $username = "zhr8wex"; 
        $password = "Fall2023"; 
        $databasename = "zhr8wex"; 

        $conn = mysqli_connect($servername,  
        $username, $password, $databasename); 

        $escape_g_name = mysqli_escape_string($conn, $g_name); 
        $escape_item_name = mysqli_escape_string($conn, $item_name);

        $sql = "DELETE FROM `grocery_lists_items` WHERE item_name = '$escape_item_name' AND g_name = '$escape_g_name' ;"; 

        if ($conn->query($sql) === TRUE) {
            echo "Record1 was deleted";
        } else {
            echo "Encountered error when deleting record1: " . $conn->error;
        }

        $conn->close();

        if($refresh == true){
            header("Location: myLists.php");
        }
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
    <br>
    <p>
        <a href="createList.php" class="btn btn-primary">Create New List</a>
    </p>
    <!-- <p>
        <a href="" class="btn btn-primary">Delete List</a>
    </p> -->
    <p>
        <a href="index.php" class="btn btn-primary" style="background-color: darkred; color: white;">Go Home</a>
    </p>
    <div class="text-container"><h1> Active Lists: </h1></div>
    <br>
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
                            "<form method='post' action='myLists.php'><input type='submit' name='addBtn' value='+'></input><input type='hidden' name='display' value='". $row["g_name"]."'></input>".
                            "<u><b> List Name: ". $row["g_name"]. "</b></u> <input type='submit' name='renameBtn' value='Rename'> <input type='submit' name='deleteBtn' value='Delete'> </form>"; 
        
                $itemsQuery = "SELECT item_name FROM `grocery_lists_items` WHERE g_name = '$listName'";
                $itemsResult = $conn->query($itemsQuery);
        
                if ($itemsResult->num_rows > 0) {
                    while ($itemRow = $itemsResult->fetch_assoc()) {
                        $itemName = $itemRow["item_name"];
        
                        echo " <form method='post' action='myLists.php'> <input type='submit' name='removeBtn' value='-'> </input> <b>$itemName</b> ". 
                        "<input type='hidden' name='delete_g_name' value='". $row["g_name"]."'></input> <input type='hidden' name='delete_item_name' value='". $itemName."'></input> </form> <br />";
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