<?php
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
            if (stripos($row["category"], $searchTerm) !== true) {
            echo
                "<b>Name: " . $row["item_name"] . "</b>" .
                " | Category: " . $row["category"] .
                " | Date Created: " . $row["date_created"] . "<br>";
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

