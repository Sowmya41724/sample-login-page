<?php
session_start();

$is_logged_in = isset($_SESSION["id"]) || isset($_SESSION["is_admin"]);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon"
        href="https://image.similarpng.com/file/similarpng/very-thumbnail/2021/07/Logo-design-template-on-transparent-background-PNG.png">
    <link rel="stylesheet" href="../Stylesheet/stylesheet.css">
    <style>
        .header-right {
            float: right;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0a2463;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px;
        }

        .button:hover {
            background-color: #05153f;
        }
    </style>
</head>

<body>
    <header class="clearfix">
        <h1 style="display: inline-block;color: white;">Welcome to the Home Page</h1>
        <div class="header-right">
            <?php if ($is_logged_in): ?>
                <a href="../Login/logout.php" class="button">Logout</a>
            <?php else: ?>
                <a href="../Login/login_page.php" class="button">Login</a>
            <?php endif; ?>
        </div>
    </header>
    <div class="home-container">
        <?php if ($is_logged_in): ?>
            <?php
            if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] === true) {
                $greeting = "hello Admin, you are Admin";
            } else {
                $username = $_SESSION["username"] ?? "User";
                $greeting = "hello $username, you are $username";
            }
            ?>
            <p><?php echo $greeting; ?></p>
        <?php else: ?>
            <p>Welcome</p>
        <?php endif; ?>
    </div>
</body>

</html>