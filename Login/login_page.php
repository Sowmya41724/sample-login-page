<?php
include "../config.php";
session_start();

if (isset($_SESSION["id"]) || isset($_SESSION["is_admin"])) {
    header("Location: ../Home/index.php");
    exit();
}

$username = $password = "";
$username_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $error = 0;

    if (empty($_POST["username"])) {
        $username_err = "Username is required.";
        $error = 1;
    } else {
        $username = test_input($_POST["username"]);
    }

    if (empty($_POST["password"])) {
        $password_err = "Password is required.";
        $error = 1;
    } else {
        $password = test_input($_POST["password"]);
    }

    if ($error === 0) {

        if ($username === "Admin" && $password === "Admin@123") {
            session_regenerate_id(true);
            $_SESSION["id"] = 0;
            $_SESSION["username"] = "Admin";
            $_SESSION["is_admin"] = true;
            header("Location: ../Home/index.php");
            exit();
        }

        $sql = "SELECT id, username, user_id, password_hash FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user["password_hash"])) {
                session_regenerate_id(true);
                $_SESSION["id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["is_admin"] = false;
                header("Location: ../Home/index.php");
                exit();
            } else {
                $password_err = "Invalid password.";
            }
        } else {
            $username_err = "No account found with that username/email.";
        }
        $stmt->close();
    }
}

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon"
        href="https://image.similarpng.com/file/similarpng/very-thumbnail/2021/07/Logo-design-template-on-transparent-background-PNG.png">
    <link rel="stylesheet" href="../Stylesheet/stylesheet.css">
</head>

<body>
    <div class="form-container">
        <div class="left">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <a href="../Home/index.php"><i class="fas fa-home"></i></a>
                <h1>Login</h1><br>

                <?php if (isset($_GET['registered'])): ?>
                    <p style="color:green;">Registration successful! Please log in.</p>
                <?php endif; ?>

                <label>User Name:</label>
                <span class="error"><?php echo $username_err; ?></span>
                <input type="text" name="username" placeholder="Enter your username"
                    value="<?php echo $username; ?>"><br><br>

                <label>Password:</label>
                <span class="error"><?php echo $password_err; ?></span>
                <input type="password" name="password" placeholder="Enter your password"><br><br>

                <p style="text-align:center;">Don't have an account? <a
                        href="../Register/register_page.php">Register</a></p>

                <button type="submit">Submit</button>
            </form>
        </div>
        <div class="right">
            <img src="https://account.asus.com/img/login_img02.png" alt="Login">
        </div>
    </div>
</body>

</html>