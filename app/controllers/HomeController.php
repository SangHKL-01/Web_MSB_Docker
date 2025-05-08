<?php
require_once 'app/controllers/BaseController.php';
require_once 'app/models/Product_Model.php';

class HomeController extends BaseController {
    private $productModel;
    
    public function __construct() {
        $this->productModel = new Product_Model(); // Sử dụng db_product database
    }
    
    // Hiển thị trang chủ
    public function index() {
        // Lấy sản phẩm nổi bật từ database products
        $featuredProducts = $this->productModel->getAll();
        
        // Lấy sản phẩm mới nhất (giả sử là 3 sản phẩm có ID cao nhất)
        $newestProducts = $this->getNewestProducts(3);
        
        // Lấy sản phẩm đã xem gần đây từ cookie không bảo mật
        $recentProducts = [];
        if (isset($_COOKIE['recent_products'])) {
            $recentIds = explode(',', $_COOKIE['recent_products']);
            foreach ($recentIds as $id) {
                $product = $this->productModel->Get_product($id);
                if ($product) {
                    $recentProducts[] = $product;
                }
            }
        }
        // Tính toán số lượng sản phẩm trong giỏ hàng
        $cartItemCount = $this->getCartItemCount();
        
        // Hiển thị trang chủ
        $this->view('trangchu', [
            'featuredProducts' => $featuredProducts,
            'newestProducts' => $newestProducts,
            'recentProducts' => $recentProducts,
            'cartItemCount' => $cartItemCount
        ]);
    }
    
    // Lấy sản phẩm mới nhất
    private function getNewestProducts($limit = 3) {
        // Sử dụng kết nối từ Database class
        $productDb = Database::getProductInstance();
        $conn = $productDb->getConnection();
        
        // Kiểm tra kết nối
        if (!$conn) {
            error_log("Database connection error in getNewestProducts");
            return [];
        }
        
        $sql = "SELECT * FROM products ORDER BY id DESC LIMIT $limit";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $products = $result->fetch_all(MYSQLI_ASSOC);
            return $products;
        } else {
            return [];
        }
    }
// Chức năng giới thiệu
public function about() {
    $queryData = $this->getQueryData();
    
    // Lọc dữ liệu đầu vào để ngăn chặn XSS
    $message = isset($queryData['message']) ? htmlspecialchars($queryData['message'], ENT_QUOTES, 'UTF-8') : '';
    $name = isset($queryData['name']) ? htmlspecialchars($queryData['name'], ENT_QUOTES, 'UTF-8') : '';
    
    // lấy số lương sản phẩm trong giỏ hàng
    $cartItemCount = $this->getCartItemCount();

    // Thông tin giới thiệu
    $aboutInfo = [
        'title' => 'Giới thiệu về chúng tôi',
        'content' => 'Chúng tôi là một công ty chuyên cung cấp các sản phẩm chất lượng cao với giá cả hợp lý.',
        'message' => $message,
        'name' => $name,
        'cartItemCount' => $cartItemCount
    ];
    
    $this->view('about', $aboutInfo);
}    

    // contact
    // Chức năng liên hệ đơn giản hóa - chỉ hiển thị thông tin
    public function contact() {
        // Lấy số lượng sản phẩm trong giỏ hàng
        $cartItemCount = $this->getCartItemCount();
        
        // Hiển thị trang liên hệ với thông tin cơ bản
        $this->view('contact', [
            'cartItemCount' => $cartItemCount // Thêm biến cartItemCount vào mảng
        ]);
    }
    // Lỗ hổng: Local File Inclusion
    public function page() {
        $queryData = $this->getQueryData();
        
        if (isset($queryData['name'])) {
            $page = $queryData['name'];
            // Lỗ hổng LFI: không kiểm tra tệp
            include 'app/views/pages/' . $page . '.php';
        } else {
            $this->redirect('index.php');
        }
    }
}
?>