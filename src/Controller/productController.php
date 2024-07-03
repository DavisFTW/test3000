<?php
namespace App\Controller;
use App\Model\Product;
require_once '../Database/Database.php';
require_once '../Model/Product.php';
class ProductController {
    private $db;
    private $product;

    public function __construct() {
        $database = new \App\Database\Database();
        $this->db = $database->getConnection();
        $this->product = new Product($this->db);
    }

    public function getProducts() {
        $stmt = $this->product->readProduct();
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $products;
    }

    public function getProduct($id) {
        $stmt = $this->product->readProduct($id);
        $product = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $product;
    }
}
