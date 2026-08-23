<?php

session_start();

require_once "config.php";

header("Content-Type: application/json");


// ==========================================
// CHECK LOGIN
// ==========================================

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Please login before placing an order."
    ]);

    exit;
}


$user_id = (int) $_SESSION["user_id"];


// ==========================================
// VERIFY USER EXISTS
// ==========================================

$userStmt = $pdo->prepare(
    "SELECT id, name, email
     FROM users
     WHERE id = :user_id
     LIMIT 1"
);

$userStmt->execute([
    ":user_id" => $user_id
]);

$user = $userStmt->fetch();


if (!$user) {

    session_destroy();

    echo json_encode([
        "success" => false,
        "message" => "Your login session is invalid. Please login again."
    ]);

    exit;
}


// ==========================================
// ONLY POST REQUEST
// ==========================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request."
    ]);

    exit;
}


// ==========================================
// GET FORM DATA
// ==========================================

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$address = trim($_POST["address"] ?? "");
$payment = trim($_POST["payment"] ?? "");
$cartJson = $_POST["cart"] ?? "";


// ==========================================
// VALIDATION
// ==========================================

if (
    $name === "" ||
    $email === "" ||
    $phone === "" ||
    $address === "" ||
    $payment === ""
) {

    echo json_encode([
        "success" => false,
        "message" => "Please fill in all checkout details."
    ]);

    exit;
}


$cart = json_decode($cartJson, true);


if (!is_array($cart) || count($cart) === 0) {

    echo json_encode([
        "success" => false,
        "message" => "Your cart is empty."
    ]);

    exit;
}


// ==========================================
// START TRANSACTION
// ==========================================

try {

    $pdo->beginTransaction();

    $total = 0;

    $orderItems = [];


// ==========================================
// CHECK PRODUCTS
// ==========================================

    foreach ($cart as $item) {

        $productId = (int) ($item["id"] ?? 0);
        $quantity = (int) ($item["qty"] ?? 0);

        if ($productId <= 0 || $quantity <= 0) {
            continue;
        }


        $productStmt = $pdo->prepare(
            "SELECT id, name, price, stock
             FROM products
             WHERE id = :id
             LIMIT 1"
        );

        $productStmt->execute([
            ":id" => $productId
        ]);

        $product = $productStmt->fetch();


        if (!$product) {

            throw new Exception(
                "Product ID " . $productId . " no longer exists."
            );
        }


        if ($product["stock"] < $quantity) {

            throw new Exception(
                $product["name"] . " does not have enough stock."
            );
        }


        $price = (float) $product["price"];

        $total += $price * $quantity;


        $orderItems[] = [
            "product_id" => $productId,
            "quantity" => $quantity,
            "price" => $price
        ];
    }


    if (count($orderItems) === 0) {

        throw new Exception("Your cart is empty.");
    }


// ==========================================
// CREATE ORDER
// ==========================================

    $orderStmt = $pdo->prepare(
        "INSERT INTO orders
        (
            user_id,
            total_amount,
            customer_name,
            email,
            phone,
            address
        )
        VALUES
        (
            :user_id,
            :total_amount,
            :customer_name,
            :email,
            :phone,
            :address
        )"
    );


    $orderStmt->execute([
        ":user_id" => $user_id,
        ":total_amount" => $total,
        ":customer_name" => $name,
        ":email" => $email,
        ":phone" => $phone,
        ":address" => $address
    ]);


    $orderId = $pdo->lastInsertId();


// ==========================================
// CREATE ORDER ITEMS
// ==========================================

    $itemStmt = $pdo->prepare(
        "INSERT INTO order_items
        (
            order_id,
            product_id,
            quantity,
            price
        )
        VALUES
        (
            :order_id,
            :product_id,
            :quantity,
            :price
        )"
    );


    $stockStmt = $pdo->prepare(
        "UPDATE products
         SET stock = stock - :quantity
         WHERE id = :product_id"
    );


    foreach ($orderItems as $item) {

        $itemStmt->execute([
            ":order_id" => $orderId,
            ":product_id" => $item["product_id"],
            ":quantity" => $item["quantity"],
            ":price" => $item["price"]
        ]);


        $stockStmt->execute([
            ":quantity" => $item["quantity"],
            ":product_id" => $item["product_id"]
        ]);
    }


// ==========================================
// COMPLETE TRANSACTION
// ==========================================

    $pdo->commit();


    echo json_encode([
        "success" => true,
        "message" => "Order placed successfully!",
        "order_id" => $orderId
    ]);

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }


    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}

?>