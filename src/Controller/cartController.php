namespace App\Controller;

use App\Model\Cart;
use App\Database\Database;

class CartController {
    private $db;
    private $cart;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->cart = new Cart($this->db);
    }

    public function addItem($productId, $quantity) {
        if ($this->cart->addItem($productId, $quantity)) {
            return json_encode(['message' => 'Item added to cart.']);
        }
        return json_encode(['message' => 'Failed to add item to cart.']);
    }

    public function viewCart() {
        $cartItems = $this->cart->viewCart();
        return json_encode($cartItems);
    }

    public function removeItem($productId) {
        if ($this->cart->removeItem($productId)) {
            return json_encode(['message' => 'Item removed from cart.']);
        }
        return json_encode(['message' => 'Failed to remove item from cart.']);
    }
}
