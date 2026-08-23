<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

// Get logged-in user's information from the session
$userName = $_SESSION["user_name"] ?? "";
$userEmail = $_SESSION["user_email"] ?? "";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Account — VANA</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
    font-family: Arial, sans-serif;

    background-color: #f5f0e5;

    background-image: url("images/ingredients/tree-bg.png");

    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    background-attachment: fixed;

    color: #12382d;

    min-height: 100vh;
}
        /* HEADER */

        header {
            background: #12382d;
            padding: 20px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: white;
            text-decoration: none;
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 4px;
        }

        nav {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-size: 15px;
        }

        nav a:hover {
            opacity: 0.8;
        }

        .logout-btn {
            background: #c89b3c;
            padding: 10px 18px;
            border-radius: 6px;
        }

        /* MAIN */

        .container {
            width: 90%;
            max-width: 850px;
            margin: 60px auto;
        }

        .welcome {
            text-align: center;
            margin-bottom: 35px;
        }

        .profile-icon {
            width: 95px;
            height: 95px;
            margin: 0 auto 20px;

            border-radius: 50%;

            background: #b8c9b6;

            display: flex;
            justify-content: center;
            align-items: center;

            font-size: 42px;
        }

        .welcome h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }

        .welcome p {
            color: #6b776f;
        }

        /* ACCOUNT CARD */

        .account-card {
            background: white;
            padding: 40px;

            border-radius: 18px;

            box-shadow:
                0 10px 35px rgba(18, 56, 45, 0.10);
        }

        .account-card h2 {
            margin-bottom: 25px;
            font-size: 25px;
        }

        .info-row {
            padding: 20px 0;
            border-bottom: 1px solid #e5e5e5;
        }

        .label {
            display: block;
            font-size: 13px;
            color: #777;
            margin-bottom: 7px;
        }

        .value {
            font-size: 17px;
            font-weight: 600;
        }

        /* BUTTONS */

        .actions {
            margin-top: 30px;

            display: flex;
            gap: 15px;

            flex-wrap: wrap;
        }

        .btn {
            text-decoration: none;
            padding: 13px 22px;
            border-radius: 7px;
            font-weight: 600;
        }

        .shop-btn {
            background: #12382d;
            color: white;
        }

        .logout-action {
            background: #c89b3c;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }

        /* MOBILE */

        @media (max-width: 600px) {

            header {
                padding: 18px 5%;
            }

            nav {
                gap: 10px;
            }

            nav a:not(.logout-btn) {
                display: none;
            }

            .container {
                width: 92%;
                margin: 40px auto;
            }

            .account-card {
                padding: 25px;
            }

            .welcome h1 {
                font-size: 28px;
            }

        }

    </style>

</head>


<body>


<!-- HEADER -->

<header>

    <a href="index.html" class="logo">
        VANA
    </a>

    <nav>

        <a href="index.html">
            Home
        </a>

        <a href="shop.html">
            Shop
        </a>

        <a href="cart.html">
            Cart
        </a>

       <a
    href="../backend/logout.php"
    class="logout-btn"
    >
    Logout
    </a>

    </nav>

</header>


<!-- MAIN CONTENT -->

<main class="container">


    <!-- WELCOME -->

    <section class="welcome">

        <div class="profile-icon">
            👤
        </div>

        <h1>
            Welcome,
            <?php echo htmlspecialchars($userName); ?>!
        </h1>

        <p>
            Welcome to your VANA account.
        </p>

    </section>


    <!-- ACCOUNT INFORMATION -->

    <section class="account-card">

        <h2>
            My Account
        </h2>


        <div class="info-row">

            <span class="label">
                Full Name
            </span>

            <span class="value">
                <?php
                echo htmlspecialchars($userName);
                ?>
            </span>

        </div>


        <div class="info-row">

            <span class="label">
                Email Address
            </span>

            <span class="value">
                <?php
                echo htmlspecialchars($userEmail);
                ?>
            </span>

        </div>


        <div class="info-row">

            <span class="label">
                Account Status
            </span>

            <span class="value">
                Active ✓
            </span>

        </div>


        <!-- ACTIONS -->

        <div class="actions">

            <a
                href="shop.html"
                class="btn shop-btn"
            >
                Continue Shopping
            </a>

            <a
         href="../backend/logout.php"
         class="btn logout-action"
          >
         Logout
          </a>

        </div>

    </section>


</main>


</body>

</html>