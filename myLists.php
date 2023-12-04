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
    if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['FavoriteBtn']))
    {
        favoriteList($_POST['favorite'], $_POST['g_name'], true);

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

    $conn = mysqli_connect($servername, $username, $password, $databasename);

    $escape_g_name = mysqli_escape_string($conn, $val);
    $email = mysqli_escape_string($conn, $_SESSION['email']);

    // Check if there are items in the list
    $itemsQuery = "SELECT * FROM `grocery_lists_items` WHERE g_name = '$escape_g_name'";
    $itemsResult = $conn->query($itemsQuery);

    // Empty the list before deleting it
    if ($itemsResult->num_rows > 0) {
        while ($itemRow = $itemsResult->fetch_assoc()) {
            $itemName = $itemRow["item_name"];
            removeFromList($itemName, $val, false);
        }
    }

    // Delete the list
    $sql = "DELETE FROM `can_edit` WHERE email = '$email' AND g_name = '$escape_g_name';";

    if ($conn->query($sql) === TRUE) {
        echo "Record1 was deleted";
    } else {
        echo "Encountered error when deleting record1: " . $conn->error;
    }

    $sql2 = "DELETE FROM `grocery_lists` WHERE g_name = '$escape_g_name';";

    if ($conn->query($sql2) === TRUE) {
        echo "Record2 was deleted";
    } else {
        echo "Encountered error when deleting record2: " . $conn->error;
    }

    $conn->close();
    header("Location: myLists.php");
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
    function favoriteList($favorite, $g_name, $refresh) {
        $servername = "mysql01.cs.virginia.edu";
        $username = "zhr8wex";
        $password = "Fall2023";
        $databasename = "zhr8wex";
    
        $conn = mysqli_connect($servername, $username, $password, $databasename);
    
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
    
        $escape_g_name = mysqli_escape_string($conn, $_POST['display']);  // Update this line
    
        // Check if g_name exists
        $check_sql = "SELECT * FROM `favorite_lists` WHERE g_name = '$escape_g_name'";
        $result = $conn->query($check_sql);
    
        if ($result->num_rows == 0) {
            // If g_name doesn't exist, insert a new record
            $insert_sql = "INSERT INTO `favorite_lists` (favorite, g_name) VALUES (TRUE, '$escape_g_name')";
            if ($conn->query($insert_sql) === FALSE) {
                echo "Error inserting record: " . $conn->error;
                $conn->close();
                return;
            }
        }
    
        // Update the favorite column
        $update_sql = "UPDATE `favorite_lists` SET favorite = TRUE WHERE g_name = '$escape_g_name'";
        if ($conn->query($update_sql) === TRUE) {
            // echo "Record was updated successfully";  // Commented out to avoid interference with redirection
        } else {
            echo "Error updating record: " . $conn->error;
        }
    
        $conn->close();
    
        if ($refresh) {
            header("Location: myLists.php");
            exit(); // Ensure that no further code is executed after redirection
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
    <div class="text-container"><h1> Grocery Lists: </h1></div>
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

                $favoriteQuery = "SELECT favorite FROM `favorite_lists` WHERE g_name = '$listName'";
                $favoriteResult = $conn->query($favoriteQuery);
        
                $isFavorite = $favoriteResult->num_rows > 0 && $favoriteResult->fetch_assoc()['favorite'];

                // echo "<h2>List Name: $listName</h2>";
        
                // echo
                //             "<form method='post' action='myLists.php'><input type='submit' name='addBtn' value='+'></input><input type='hidden' name='display' value='". $row["g_name"]."'></input>"."</form>";
                //             // "<u><b> List Name: ". $row["g_name"]. "</b></u></form>"; 
                            //  <input type='submit' name='renameBtn' value='Rename'> <input type='submit' name='deleteBtn' value='Delete'>
                            // <input type='submit' name='FavoriteBtn' value='Favorite'></form>"; 
        
                            if ($isFavorite) {
                                echo "<u><b> List Name: *Favorite* - ". $listName. "</b></u>";
                            } else {
                                echo "<u><b> List Name: ". $listName. "</b></u>";
                            }
                            echo "<form method='post' action='myLists.php'><input type='submit' name='addBtn' value='+'></input><input type='hidden' name='display' value='". $row["g_name"]."'></input>".
                            "<input type='submit' name='renameBtn' value='Rename'>
            <input type='submit' name='deleteBtn' value='Delete'>
            <input type='submit' name='FavoriteBtn' value='Favorite'>
          </form>";



                $itemsQuery = "SELECT item_name FROM `grocery_lists_items` WHERE g_name = '$listName'";
                $itemsResult = $conn->query($itemsQuery);
        
                if ($itemsResult->num_rows > 0) {
                    while ($itemRow = $itemsResult->fetch_assoc()) {
                        $itemName = $itemRow["item_name"];
        
                        echo " <form method='post' action='myLists.php'> <input type='submit' name='removeBtn' value='-'> </input> <b style='color: #e75480;'>$itemName</b> ". 
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