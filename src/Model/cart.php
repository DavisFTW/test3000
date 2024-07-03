namespace App\Model;

class Cart {
    private $conn;
    private $table_name = "cart";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addItem($productId, $quantity) {
        $query = "INSERT INTO " . $this->table_name . " (product_id, quantity) VALUES (:product_id, :quantity)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':quantity', $quantity);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function viewCart() {
        $query = "SELECT p.*, c.quantity FROM " . $this->table_name . " c JOIN products p ON c.product_id = p.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cartItems as &$item) {
            $item['gallery'] = json_decode($item['gallery'], true);
            $item['attributes'] = json_decode($item['attributes'], true);
            $item['prices'] = json_decode($item['prices'], true);
        }

        return $cartItems;
    }

    public function removeItem($productId) {
        $query = "DELETE FROM " . $this->table_name . " WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':product_id', $productId);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
