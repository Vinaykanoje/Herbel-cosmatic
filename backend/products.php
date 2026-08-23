<?php

require_once "config.php";

try {

    $sql = "SELECT 
                p.id,
                p.name,
                p.description,
                p.price,
                p.sku,
                p.image,
                p.stock,
                c.name AS category
            FROM products p
            LEFT JOIN categories c
            ON p.category_id = c.id
            ORDER BY p.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $products = $stmt->fetchAll();

    header("Content-Type: application/json");

    echo json_encode([
        "success" => true,
        "products" => $products
    ]);

} catch (PDOException $e) {

    header("Content-Type: application/json");

    echo json_encode([
        "success" => false,
        "message" => "Failed to fetch products",
        "error" => $e->getMessage()
    ]);
}

?>