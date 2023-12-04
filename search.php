<?php
session_start();
 
// Check if the user is logged in, if not then redirect them to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: /login.php");
    exit;
}
?>

<?php
    if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['addBtn']))
    {

        $servername = "mysql01.cs.virginia.edu"; 
        $username = "zhr8wex"; 
        $password = "Fall2023"; 
        $databasename = "zhr8wex"; 
    
        $conn = mysqli_connect($servername,  
        $username, $password, $databasename); 
        
        $item_name = $_POST['display'];
        $g_name = $_SESSION['g_name'];


        $escape_g_name = mysqli_escape_string($conn, $g_name); 
        $escape_item = mysqli_escape_string($conn, $item_name);

        $query = "SELECT * FROM `grocery_lists_items` WHERE item_name = '$item_name' AND g_name = '$g_name' ;";
        try{
            $result = $conn->query($query);
        }catch (mysqli_sql_exception $e) { 
            var_dump($e);
        } 

        //Duplicates are in list
        if($result->num_rows >= 1){
            echo "<script>alert('". $item_name." is already in your ". $g_name ." list!');</script>";

        }

        //Not in List Yet
        else{
            require_once "dbconnection.php";

            $sqlB = "INSERT INTO grocery_lists_items(g_name , item_name) VALUES (:g_name , :item_name)";
        
            if($stmtB = $db->prepare($sqlB)){
                // Bind variables to the prepared statement as parameters
                $stmtB->bindParam(":g_name", $param_g_name, PDO::PARAM_STR);
                $stmtB->bindParam(":item_name", $param_item_name, PDO::PARAM_STR);

                // Set parameters
                $param_g_name = $g_name;
                $param_item_name = $item_name;
                
            }
            // Attempt to execute the prepared statement
            try{
                $stmtB->execute();
            }catch(error){ echo "Opps! Something went wrong with stmtB. Please try again later.";}


            $sqlU = "UPDATE current_grocery_list SET checkboxes = 1 WHERE g_name = :g_name";
        
            if($stmtU = $db->prepare($sqlU)){
                // Bind variables to the prepared statement as parameters
                $stmtU->bindParam(":g_name", $param_g_name, PDO::PARAM_STR);
                // Set parameters
                $param_g_name = $g_name;
            }
            // Attempt to execute the prepared statement
            try{
                $stmtU->execute();

            }catch(error){ echo "Opps! Something went wrong with stmtB. Please try again later.";}

            unset($stmtB);
            unset($stmtU);
            unset($db);

            // addItem();
            echo "<script>alert('Added". $item_name." to your ". $g_name ." list!');</script>";
            header('Location: groceryItems.php');
        }

    }
    // function addItem()
    // {
    //     echo "<script>alert('Added". $_POST['display']." to Current List');</script>"; 
    // }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
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
    <h1 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["name"]); ?></b>. Here are your filtered results!</h1>
    <p>
        <a href="groceryItems.php" class="btn btn-primary" style="background-color: darkred; color: white;">Back to Search</a>
    </p>
</body>
</html>

<?php
session_start();
 
// Check if the user is logged in, if not then redirect them to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

if (isset($_GET['search'])) {
    $servername = "mysql01.cs.virginia.edu"; 
    $username = "zhr8wex"; 
    $password = "Fall2023"; 
    $databasename = "zhr8wex"; 

    $conn = mysqli_connect($servername,  
    $username, $password, $databasename); 

    // $query = "SELECT * FROM `grocery_items`;"; 

    // $result = $conn->query($query); 


    $searchTerm = $_GET['search'];

    // $stmt = $mysqli->prepare("SELECT item_name, category, date_created FROM grocery_items WHERE item_name LIKE ?");
    $stmt = $conn->prepare("SELECT item_name, category, date_created FROM grocery_items WHERE LOWER(item_name) LIKE LOWER(?)");

    $searchTerm = '%' . $searchTerm . '%';

    $stmt->bind_param('s', $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if (stripos($row["item_name"], $searchTerm) !== true) {
            echo
                "<form method='post' action='search.php'><input type='submit' name='addBtn' value='+'></input><input type='hidden' name='display' value='". $row["item_name"]."'></input>".
                "<b>Name: " . $row["item_name"] . "</b>" .
                " | Category: " . $row["category"] .
                " | Date Created: " . $row["date_created"] . "<br></form>";
        }
    }
    } else {
        echo "0 results";
    }

    $stmt->close();
} else {
    echo "No search term provided.";
}

?>

