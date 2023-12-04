<?php
// Initialize the session
session_start();

$servername = "mysql01.cs.virginia.edu";
$username = "zhr8wex";
$password = "Fall2023";
$databasename = "zhr8wex";

try {
    // Establish a PDO database connection
    $db = new PDO("mysql:host=$servername;dbname=$databasename", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Check if the user is logged in, if not then redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$name_err = $email_err = $password_err = $confirm_password_err = $allergy_err = "";
$name = $email = $password = $confirm_password = $allergy = "";

// Processing form data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter a name.";
    } elseif (!preg_match('/[a-zA-Z]{2,}\s{1,}[a-zA-Z]{2,}/', trim($_POST["name"]))) {
        $name_err = "You must type in a first and a last name";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate Email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } elseif (!preg_match('/^[a-zA-Z0-9+.-]+@[a-zA-Z0-9.-]+$/', trim($_POST["email"]))) {
        $email_err = "Email can only contain letters, numbers, and underscores.";
    } else {
        // Prepare a select statement
        $sql = "SELECT * FROM `users` WHERE email = :email";

        if ($stmt = $db->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);

            // Set parameters
            $param_email = trim($_POST["email"]);

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    $email_err = "This email is already taken.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = htmlspecialchars(trim($_POST["password"]));
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = htmlspecialchars(trim($_POST["confirm_password"]));
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Validate allergy
    if (isset($_POST["add_allergy"])) {
        if (empty(trim($_POST["allergy"]))) {
            $allergy_err = "Please enter an allergy.";
        } else {
            $allergy = trim($_POST["allergy"]);
        }
    }

    // Check input errors before inserting into the database
    if (empty($name_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {

        // Prepare an update statement for user information
        $sqlA = "UPDATE grocery_shopper SET email = :email, name = :name WHERE email = :old_email";

        if ($stmtA = $db->prepare($sqlA)) {
            $stmtA->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmtA->bindParam(":name", $param_name, PDO::PARAM_STR);
            $stmtA->bindParam(":old_email", $param_old_email, PDO::PARAM_STR);

            // Set parameters
            $param_email = $email;
            $param_name = $name;
            $param_old_email = $_SESSION["email"];
        }

        // Prepare an update statement for user password
        $sql = "UPDATE users SET email = :email, pwd = :password WHERE email = :old_email";

        if ($stmt = $db->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);

            // Set parameters
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_old_email = $_SESSION["email"];
        }

        // Attempt to execute the prepared statement
        try {
            $stmtA->execute();
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Oops! Something went wrong. Please try again later.";
        }

        // Close statement
        unset($stmtA);
        unset($stmt);

        // Insert allergy
        if (empty($allergy_err)) {
            $sql_insert_allergy = "INSERT INTO allergies (email, allergy) VALUES (:email, :allergy)";
            $stmt_insert_allergy = $db->prepare($sql_insert_allergy);
            $stmt_insert_allergy->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt_insert_allergy->bindParam(":allergy", $param_allergy, PDO::PARAM_STR);

            // Set parameters
            $param_email = $email;
            $param_allergy = $allergy;

            try {
                $stmt_insert_allergy->execute();
            } catch (PDOException $e) {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt_insert_allergy);
        }

        header("Location: login.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; text-align: center; }

        .btn:hover {
            line-height: 1;
            font-weight: 400;
            padding: .7rem 1.5rem;
            border-radius: 0.1275rem;
        }

        body {
            background-image: url('picnic.jpeg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
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
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="text-container">
    <h1 class="my-5"> <b><?php echo htmlspecialchars($_SESSION["name"]); ?>'s Settings</b></h1>
</div>
<br>
<p>
    <a href="index.php" class="btn btn-primary" style="background-color: darkred; color: white;">Go Home</a>
</p>
<div class="text-container">
    <h2>Current Information</h2>
    <b>Name:</b> <?php echo htmlspecialchars($_SESSION["name"]); ?>
    <b>Email:</b> <?php echo htmlspecialchars($_SESSION["email"]); ?>
</div>
<br>
<div class="text-container">
    <h2>Allergies</h2>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <input type="text" name="allergy" id="allergy" class="form-control <?php echo (!empty($allergy_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $allergy ?? ''; ?>" placeholder="Add Allergy">
            <span class="invalid-feedback"><?php echo $allergy_err ?? ''; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" name="add_allergy" class="btn btn-primary" value="Add Allergy">
        </div>
    </form>
</div>
</body>
</html>
