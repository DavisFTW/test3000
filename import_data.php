<?php
require_once 'src/Database/Database.php';

$database = new Database();
$db = $database->getConnection();

$data = file_get_contents('data.json');
$json_data = json_decode($data, true);

$products = $json_data['data']['products'];

foreach ($products as $product) {
    $query = "INSERT INTO products (id, name, inStock, gallery, description, category, attributes, prices, brand)
              VALUES (:id, :name, :inStock, :gallery, :description, :category, :attributes, :prices, :brand)";
              
    $stmt = $db->prepare($query);

    $gallery = json_encode($product['gallery']);
    $attributes = json_encode($product['attributes']);
    $prices = json_encode($product['prices']);

    $stmt->bindParam(':id', $product['id']);
    $stmt->bindParam(':name', $product['name']);
    $stmt->bindParam(':inStock', $product['inStock'], PDO::PARAM_BOOL);
    $stmt->bindParam(':gallery', $gallery);
    $stmt->bindParam(':description', $product['description']);
    $stmt->bindParam(':category', $product['category']);
    $stmt->bindParam(':attributes', $attributes);
    $stmt->bindParam(':prices', $prices);
    $stmt->bindParam(':brand', $product['brand']);

    if ($stmt->execute()) {
        echo "Product " . $product['name'] . " inserted successfully.\n";
    } else {
        echo "Failed to insert product " . $product['name'] . ".\n";
    }
}

