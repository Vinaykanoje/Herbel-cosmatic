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

$user_id = $_SESSION["user_id"];


// ==========================================
// CHECK USER EXISTS
// ==========================================

$userStmt = $pdo->prepare("
    SELECT id, name, email
    FROM users
    WHERE id = :user_id
    LIMIT 1
");

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


// ==========================================
// DECODE CART
// ==========================================

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

    $total_amount = 0;

    $orderItems = [];


    // ======================================
    // CHECK EVERY PRODUCT
    // ======================================

    foreach ($cart as $item) {

        $product_id = (int)($item["id"] ?? 0);
        $quantity = (int)($item["qty"] ?? 0);

        if ($product_id <= 0 || $quantity <= 0) {

            throw new Exception(
                "Invalid product information in cart."
            );
        }


        $productStmt = $pdo->prepare("
            SELECT id, name, price, stock
            FROM products
            WHERE id = :product_id
            LIMIT 1
        ");

        $productStmt->execute([
            ":product_id" => $product_id
        ]);

        $product = $productStmt->fetch();


        if (!$product) {

            throw new Exception(
                "One of the products in your cart no longer exists."
            );
        }


        // Check stock

        if ($product["stock"] < $quantity) {

            throw new Exception(
                "Not enough stock for " . $product["name"]
            );
        }


        $price = (float)$product["price"];

        $total_amount += $price * $quantity;


        $orderItems[] = [
            "product_id" => $product_id,
            "quantity" => $quantity,
            "price" => $price
        ];
    }


    // ======================================
    // CREATE ORDER
    // ======================================

    $orderStmt = $pdo->prepare("
        INSERT INTO orders
        (
            user_id,
            total_amount,
            status,
            customer_name,
            email,
            phone,
            address
        )
        VALUES
        (
            :user_id,
            :total_amount,
            'Pending',
            :customer_name,
            :email,
            :phone,
            :address
        )
    ");

    $orderStmt->execute([

        ":user_id" => $user_id,

        ":total_amount" => $total_amount,

        ":customer_name" => $name,

        ":email" => $email,

        ":phone" => $phone,

        ":address" => $address
    ]);


    $order_id = $pdo->lastInsertId();


    // ======================================
    // INSERT ORDER ITEMS
    // ======================================

    $itemStmt = $pdo->prepare("
        INSERT INTO order_items
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
        )
    ");


    // Stock update

    $stockStmt = $pdo->prepare("
        UPDATE products
        SET stock = stock - :quantity
        WHERE id = :product_id
    ");


    foreach ($orderItems as $item) {

        $itemStmt->execute([

            ":order_id" => $order_id,

            ":product_id" => $item["product_id"],

            ":quantity" => $item["quantity"],

            ":price" => $item["price"]
        ]);


        $stockStmt->execute([

            ":quantity" => $item["quantity"],

            ":product_id" => $item["product_id"]
        ]);
    }


    // ======================================
    // COMMIT
    // ======================================

    $pdo->commit();


    echo json_encode([

        "success" => true,

        "message" => "Order placed successfully!",

        "order_id" => $order_id,

        "total" => $total_amount

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