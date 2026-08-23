<?php

require_once "config.php";

/*
|--------------------------------------------------------------------------
| Only allow POST requests
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: ../Frontend/register.html");
    exit;

}


/*
|--------------------------------------------------------------------------
| Get form data
|--------------------------------------------------------------------------
*/

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$confirm_password = $_POST["confirm_password"] ?? "";


/*
|--------------------------------------------------------------------------
| Validate input
|--------------------------------------------------------------------------
*/

if (
    $name === "" ||
    $email === "" ||
    $password === "" ||
    $confirm_password === ""
) {

    die("Please fill in all fields.");

}


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    die("Please enter a valid email address.");

}


if (strlen($password) < 6) {

    die("Password must be at least 6 characters long.");

}


if ($password !== $confirm_password) {

    die("Passwords do not match.");

}


/*
|--------------------------------------------------------------------------
| Check whether email already exists
|--------------------------------------------------------------------------
*/

try {

    $checkSql = "
        SELECT id
        FROM users
        WHERE email = :email
        LIMIT 1
    ";

    $checkStmt = $pdo->prepare($checkSql);

    $checkStmt->execute([
        ":email" => $email
    ]);

    if ($checkStmt->fetch()) {

        die("An account with this email already exists.");

    }


    /*
    |--------------------------------------------------------------------------
    | Securely hash password
    |--------------------------------------------------------------------------
    */

    $hashedPassword = password_hash(
        $password,
        PASSWORD_DEFAULT
    );


    /*
    |--------------------------------------------------------------------------
    | Insert new user
    |--------------------------------------------------------------------------
    */

    $sql = "
        INSERT INTO users
        (
            name,
            email,
            password
        )
        VALUES
        (
            :name,
            :email,
            :password
        )
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([

        ":name" => $name,

        ":email" => $email,

        ":password" => $hashedPassword

    ]);


    /*
    |--------------------------------------------------------------------------
    | Registration successful
    |--------------------------------------------------------------------------
    */

    echo "
        <script>
            alert('Account created successfully!');
            window.location.href = '../Frontend/login.html';
        </script>
    ";


} catch (PDOException $e) {

    die(
        "Registration failed: " .
        $e->getMessage()
    );

}

?>