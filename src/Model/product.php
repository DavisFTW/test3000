<?php

namespace App\Model;

use PDO;

class Product {
    private $conn;
    private $table_name = "products";

    public $id;
    public $name;
    public $inStock;
    public $gallery;
    public $description;
    public $category;
    public $attributes;
    public $prices;
    public $brand;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readProduct($id = null) {
        if ($id === null) {
            $query = "SELECT * FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        } else {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
        }

        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Decode JSON fields
        foreach ($products as &$product) {
            $product['gallery'] = json_decode($product['gallery'], true);
            $product['attributes'] = json_decode($product['attributes'], true);
            $product['prices'] = json_decode($product['prices'], true);
        }

        return $products;
    }
}
