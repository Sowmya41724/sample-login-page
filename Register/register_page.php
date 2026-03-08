<?php
include "../config.php";
session_start();

$username = $mobile = $user_id = $password = $confirm_password = "";
$username_err = $mobile_err = $user_id_err = $password_err = $confirm_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $error = 0;

    if (empty($_POST["username"])) {
        $username_err = "Username is required.";
        $error = 1;
    } else {
        $username = test_input($_POST["username"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $username)) {
            $username_err = "Only letters and spaces allowed.";
            $error = 1;
        }
    }

    if (empty($_POST["mobile"])) {
        $mobile_err = "Mobile number is required.";
    } else {
        $mobile = test_input($_POST["mobile"]);
        if (!preg_match("/^(91)[6789]\d{9}$/", $mobile)) {
            $mobile_err = "Invaid format";
            $error = 1;
        } else {
            $stmt = $conn->prepare("SELECT id FROM users WHERE mobile = ?");
            $stmt->bind_param("s", $mobile);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $mobile_err = "Mobile number already registered.";
                $error = 1;
            }
            $stmt->close();
        }
    }

    if (empty($_POST["user_id"])) {
        $user_id_err = "User ID is required.";
        $error = 1;
    } else {
        $user_id = test_input($_POST["user_id"]);
        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $user_id)) {
            $user_id_err = "User ID must be 3-20 characters, using letters, numbers, or underscore.";
            $error = 1;
        } else {
            $stmt = $conn->prepare("SELECT id FROM users WHERE user_id = ?");
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $user_id_err = "User ID already registered.";
                $error = 1;
            }
            $stmt->close();
        }
    }

    if (empty($_POST["password"])) {
        $password_err = "Password is required.";
        $error = 1;
    } else {
        $password = test_input($_POST["password"]);
        if (strlen($password) < 6) {
            $password_err = "Password must be at least 6 characters.";
            $error = 1;
        }
    }

    if (empty($_POST["confirm_password"])) {
        $confirm_password_err = "Please confirm password.";
        $error = 1;
    } else {
        $confirm_password = test_input($_POST["confirm_password"]);
        if ($password != $confirm_password) {
            $confirm_password_err = "Passwords do not match.";
            $error = 1;
        }
    }

    if ($error === 0) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, mobile, user_id, password_hash) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $mobile, $user_id, $password_hash);

        if ($stmt->execute()) {
            header("Location: ../Login/login_page.php?registered=1");
            exit();
        } else {
            echo "Something went wrong. Please try again later.";
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
    <title>Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon"
        href="https://image.similarpng.com/file/similarpng/very-thumbnail/2021/07/Logo-design-template-on-transparent-background-PNG.png">
    <link rel="stylesheet" href="../Stylesheet/stylesheet.css">
</head>

<body>

    <div class="form-container form-reverse">
        <div class="left">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <a href="../Home/index.php"><i class="fas fa-home"></i></a>
                <h1><b>Register</b></h1><br>

                <label for="username">Username:</label>
                <span class="error"><?php echo $username_err; ?></span>
                <input type="text" id="username" name="username" placeholder="Enter your username"
                    value="<?php echo $username; ?>"><br><br>

                <label for="mobile">Mobile Number:</label>
                <span class="error"><?php echo $mobile_err; ?></span>
                <input type="text" id="mobile" maxlength="12" name="mobile" placeholder="Enter your mobile number"
                    value="<?php echo $mobile; ?>">
                <h5>(format: 91[6 or 7 or 8 or 9]xxxxxxxxx)</h5>

                <label for="User_ID">User ID:</label>
                <span class="error"><?php echo $user_id_err; ?></span>
                <input type="text" id="User_ID" name="user_id" placeholder="Enter your user id"
                    value="<?php echo $user_id; ?>"><br><br>

                <label for="password">Password:</label>
                <span class="error"><?php echo $password_err; ?></span>
                <input type="password" id="password" name="password" placeholder="Enter your password"><br><br>

                <label for="confirm_password">Confirm Password:</label>
                <span class="error"><?php echo $confirm_password_err; ?></span>
                <input type="password" id="confirm_password" name="confirm_password"
                    placeholder="Confirm your password"><br><br>

                <h4 style="text-align: center;">Already Registered? <a href="../Login/login_page.php">Login</a></h4>

                <button type="submit">Submit</button>
            </form>
        </div>
        <div class="right">
            <img src="https://st2.depositphotos.com/1001599/43046/v/450/depositphotos_430460192-stock-illustration-sign-page-abstract-concept-vector.jpg"
                alt="register">
        </div>
    </div>

</body>

</html>