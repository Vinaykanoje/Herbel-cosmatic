<?php

session_start();

header("Content-Type: application/json");

require_once "config.php";

try {

    // ==========================================
    // CHECK IF USER IS LOGGED IN
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
    // VERIFY USER EXISTS IN DATABASE
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
    // ONLY ACCEPT POST REQUESTS
    // ==========================================

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        echo json_encode([
            "success" => false,
            "message" => "Invalid request method."
        ]);
        exit;
    }

    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid request data."
        ]);
        exit;
    }

    /*
    =========================================
    CUSTOMER INFORMATION
    =========================================
    */

    $name = trim($input["name"] ?? "");
    $email = trim($input["email"] ?? "");
    $phone = trim($input["phone"] ?? "");
    $address = trim($input["address"] ?? "");
    $payment = trim($input["payment"] ?? "");

    $cart = $input["cart"] ?? [];

    /*
    =========================================
    VALIDATION
    =========================================
    */

    if (
        $name === "" ||
        $email === "" ||
        $phone === "" ||
        $address === "" ||
        $payment === ""
    ) {
        echo json_encode([
            "success" => false,
            "message" => "Please fill in all required fields."
        ]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            "success" => false,
            "message" => "Please enter a valid email address."
        ]);
        exit;
    }

    $allowedPayments = ["cod", "upi", "card"];

    if (!in_array($payment, $allowedPayments, true)) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid payment method."
        ]);
        exit;
    }

    if (!is_array($cart) || count($cart) === 0) {
        echo json_encode([
            "success" => false,
            "message" => "Your cart is empty."
        ]);
        exit;
    }

    /*
    =========================================
    START DATABASE TRANSACTION
    =========================================
    */

    $pdo->beginTransaction();

    $total = 0;
    $validatedItems = [];

    /*
    =========================================
    CHECK PRODUCTS FROM MYSQL
    =========================================
    */

    $productStmt = $pdo->prepare("
        SELECT id, name, price, stock
        FROM products
        WHERE id = ?
        FOR UPDATE
    ");

    foreach ($cart as $item) {

        $productId = (int)($item["id"] ?? 0);
        $quantity = (int)($item["qty"] ?? 0);

        if ($productId <= 0 || $quantity <= 0) {
            throw new Exception("Invalid cart item.");
        }

        $productStmt->execute([$productId]);

        $product = $productStmt->fetch();

        if (!$product) {
            throw new Exception(
                "One of the products in your cart no longer exists."
            );
        }

        /*
        =====================================
        STOCK CHECK
        =====================================
        */

        if ($quantity > (int)$product["stock"]) {
            throw new Exception(
                $product["name"] .
                " has only " .
                $product["stock"] .
                " item(s) available."
            );
        }

        /*
        =====================================
        CALCULATE PRICE FROM DATABASE
        =====================================
        */

        $price = (float)$product["price"];
        $subtotal = $price * $quantity;

        $total += $subtotal;

        $validatedItems[] = [
            "product_id" => $productId,
            "quantity" => $quantity,
            "price" => $price,
            "subtotal" => $subtotal
        ];
    }

    /*
    =========================================
    CREATE ORDER (INCLUDES user_id)
    =========================================
    */

    $orderStmt = $pdo->prepare("
        INSERT INTO orders
        (
            user_id,
            customer_name,
            email,
            phone,
            address,
            total_amount,
            status
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
        )
    ");

    $orderStmt->execute([
        $user_id,
        $name,
        $email,
        $phone,
        $address,
        $total,
        "Pending"
    ]);

    $orderId = $pdo->lastInsertId();

    /*
    =========================================
    INSERT ORDER ITEMS
    =========================================
    */

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
            ?,
            ?,
            ?,
            ?
        )
    ");

    /*
    =========================================
    UPDATE PRODUCT STOCK
    =========================================
    */

    $stockStmt = $pdo->prepare("
        UPDATE products
        SET stock = stock - ?
        WHERE id = ?
    ");

    foreach ($validatedItems as $item) {

        $itemStmt->execute([
            $orderId,
            $item["product_id"],
            $item["quantity"],
            $item["price"]
        ]);

        $stockStmt->execute([
            $item["quantity"],
            $item["product_id"]
        ]);
    }

    /*
    =========================================
    COMMIT
    =========================================
    */

    $pdo->commit();

    echo json_encode([
        "success" => true,
        "message" => "Your order has been placed successfully.",
        "order_id" => $orderId,
        "total" => $total
    ]);

} catch (Exception $e) {

    /*
    =========================================
    ROLLBACK
    =========================================
    */

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>