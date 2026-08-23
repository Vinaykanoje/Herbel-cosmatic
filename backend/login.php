<?php

session_start();

require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../Frontend/login.html");
    exit;
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($email === "" || $password === "") {
    die("Please enter email and password.");
}

try {

    $sql = "SELECT id, name, email, password
            FROM users
            WHERE email = :email
            LIMIT 1";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":email" => $email
    ]);

    $user = $stmt->fetch();

    if (!$user) {
        die("Invalid email or password.");
    }

    if (!password_verify($password, $user["password"])) {
        die("Invalid email or password.");
    }

    /*
     * Login successful
     */

    $_SESSION["user_id"] = $user["id"];
    $_SESSION["user_name"] = $user["name"];
    $_SESSION["user_email"] = $user["email"];

    header("Location: ../Frontend/index.html");
    exit;

} catch (PDOException $e) {

    die(
        "Login failed: " .
        $e->getMessage()
    );

}

?>