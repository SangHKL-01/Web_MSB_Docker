<?php
require_once 'app/controllers/BaseController.php';
require_once 'app/models/Product_Model.php';

class ProductController extends BaseController {
    private $productModel;
    
    public function __construct() {
        $this->productModel = new Product_Model(); // Sử dụng db_product database
    }
    
    // Liệt kê tất cả sản phẩm
    public function index() {
        $model = new Product_Model();
        
        // Hiển thị giỏ hàng
        $cartItemCount = $this->getCartItemCount();

        if (isset($_GET['sort_by']) && isset($_GET['sort_dir'])) {
            $sort_by = $_GET['sort_by'];
            $sort_dir = $_GET['sort_dir'];
            $products = $model->getProductsWithSorting($sort_by, $sort_dir);
        } else {
            $products = $model->getAll();
        }
        
        $this->view('product/shop', ['products' => $products, 'cartItemCount' => $cartItemCount]);
    }
    
    // Hiển thị giỏ hàng
    public function gio_hang() {
        $this->requireLogin();
        
        $user = $this->getLoggedInUser();
        $user_id = $user['id'];
        
        $products = $this->productModel->List_product($user_id);
        
        $this->view('product/gio_hang', ['products' => $products]);
    }
    
    // Thêm sản phẩm vào giỏ hàng
    public function insert_cart() {
        // Kiểm tra xem đã login chưa
        if (!$this->isLoggedIn()) {
            // Lưu URL hiện tại để chuyển hướng lại sau khi đăng nhập
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            $_SESSION['error'] = "Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng";
            $this->redirect('user/login');
            return;
        }
        
        $queryData = $this->getQueryData();
        $postData = $this->getPostData();
        
        $id = isset($queryData['id']) ? (int)$queryData['id'] : null;
        $quantity = isset($postData['quantity']) ? (int)$postData['quantity'] : 1;
        
        // Đảm bảo số lượng là số dương
        $quantity = max(1, $quantity);
        
        $user = $this->getLoggedInUser();
        $user_id = $user['id'];
        
        // Lấy trang trước đó từ HTTP_REFERER
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php?controller=product&action=index';
        
        if ($id) {
            $product = $this->productModel->Get_product($id);
            
            if ($product) {
                // Thêm sản phẩm vào giỏ hàng
                $result = $this->productModel->insert_cart(
                    $user_id, 
                    $product['name'], 
                    $quantity, 
                    $product['price'],
                    $product['id']
                );
                
                if (is_array($result) && isset($result['status']) && $result['status'] === true) {
                    // Đặt thông báo thành công vào session
                    $_SESSION['cart_message'] = $result['message'];
                    // Chuyển hướng trở lại trang trước đó
                    header("Location: $referer");
                    exit();
                } else {
                    // Thông báo lỗi chi tiết từ model
                    $_SESSION['error'] = is_array($result) && isset($result['message']) ? $result['message'] : "Không thể thêm sản phẩm vào giỏ hàng";
                    header("Location: $referer");
                    exit();
                }
            } else {
                $_SESSION['error'] = "Không tìm thấy sản phẩm";
                $this->redirect('product/index');
            }
        } else {
            $_SESSION['error'] = "Thiếu thông tin sản phẩm";
            $this->redirect('product/index');
        }
    }
    
    // Hiển thị lịch sử mua hàng
    public function history() {
        $user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
        
        if (!$user_id) {
            $_SESSION['error'] = "Vui lòng đăng nhập để xem lịch sử đơn hàng";
            $this->redirect('user/login');
            exit;
        }
        
        // Lấy danh sách đơn hàng
        $orders = $this->productModel->getOrdersByUser($user_id);
        
        // Lấy chi tiết đơn hàng
        foreach ($orders as &$order) {
            $order['details'] = $this->productModel->getOrderDetails($order['id']);
        }
        
        $this->view('product/history', ['orders' => $orders]);
    }
    public function viewProduct() {
        $queryData = $this->getQueryData();
        
        if (isset($queryData['id'])) {
            // Chống SQL Injection bằng cách ép kiểu và lọc ID
            $id = (int)$queryData['id'];
            $product = $this->productModel->Get_product($id);
            
            if ($product) {
                // Lưu sản phẩm đã xem
                $this->productModel->saveRecentlyViewedProduct($id);
                
                // Lấy danh sách sản phẩm đã xem gần đây
                $recentlyViewed = $this->productModel->getRecentlyViewedProducts();
                
                // Loại bỏ sản phẩm hiện tại khỏi danh sách đã xem
                foreach ($recentlyViewed as $key => $item) {
                    if ($item['id'] == $id) {
                        unset($recentlyViewed[$key]);
                        break;
                    }
                }
                
                // Lấy số lượng sản phẩm trong giỏ hàng
                $cartItemCount = $this->getCartItemCount();
                
                $this->view('product/detail', [
                    'product' => $product,
                    'recentlyViewed' => array_slice($recentlyViewed, 0, 4), // Giới hạn hiển thị 4 sản phẩm
                    'cartItemCount' => $cartItemCount
                ]);
            } else {
                $this->redirect('index.php?controller=product&action=index');
            }
        } else {
            $this->redirect('index.php?controller=product&action=index');
        }
    }
    
    // Hiển thị danh sách sản phẩm đã xem gần đây
    public function recentlyViewed() {
        // Lấy danh sách sản phẩm đã xem
        $recentlyViewed = $this->productModel->getRecentlyViewedProducts();
        
        // Hiển thị danh sách
        $this->view('product/recently_viewed', [
            'products' => $recentlyViewed,
            'title' => 'Sản phẩm đã xem gần đây'
        ]);
    }
    
    // Tìm kiếm sản phẩm
    public function search() {
        $queryData = $this->getQueryData();
        
        if (isset($queryData['keyword'])) {
            $keyword = $queryData['keyword'];
            
            // Thực hiện tìm kiếm sản phẩm với từ khóa đã được lọc
            $products = $this->productModel->searchProducts($keyword);
            
            $this->view('product/search_results', [
                'products' => $products,
                'keyword' => $keyword
            ]);
        } else {
            $this->view('product/search_form');
        }
    }
    // Xóa sản phẩm khỏi giỏ hàng
    public function remove_from_cart() {
        $this->requireLogin();
        
        $queryData = $this->getQueryData();
        
        if (isset($queryData['id'])) {
            $cart_id = $queryData['id'];
            $user = $this->getLoggedInUser();
            
            // Cần thêm phương thức này vào model
            $result = $this->productModel->remove_from_cart($cart_id, $user['id']);
            
            $this->redirect('product/gio_hang');
        } else {
            $this->redirect('product/gio_hang');
        }
    }
    
    // Trang thanh toán
    public function checkout() {
        $this->requireLogin();
        
        if (!$this->isLoggedIn()) {
            $_SESSION['error'] = "Vui lòng đăng nhập để tiếp tục thanh toán";
            $this->redirect('user/login');
            exit;
        }

        $user = $this->getLoggedInUser();
        $user_id = $user['id'];
        $cart_items = $this->productModel->List_product($user_id);
        $total_amount = 0;

        if (empty($cart_items)) {
            $_SESSION['error'] = "Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm trước khi thanh toán.";
            $this->redirect('product/gio_hang');
            exit;
        }

        // Trong phương thức checkout()
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy thông tin của người dùng từ form checkout
            $customer_name = isset($_POST['fullname']) ? $_POST['fullname'] : '';
            $email = isset($_POST['email']) ? $_POST['email'] : '';
            $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
            $address = isset($_POST['address']) ? $_POST['address'] : '';
            $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'cod';
            $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
        
            // Kiểm tra thông tin người dùng
            if (empty($customer_name) || empty($phone) || empty($address)) {
                $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin bắt buộc.";
                $this->redirect('product/checkout');
                exit;
            }
            
            // Kiểm tra số điện thoại chỉ chứa số
            if (!preg_match('/^\d+$/', $phone)) {
                $_SESSION['error'] = "Số điện thoại chỉ được chứa chữ số.";
                $this->redirect('product/checkout');
                exit;
            }
            
            // Kiểm tra độ dài số điện thoại
            if (strlen($phone) < 10 || strlen($phone) > 11) {
                $_SESSION['error'] = "Số điện thoại phải có 10-11 chữ số.";
                $this->redirect('product/checkout');
                exit;
            }
            
            // Tính tổng tiền từ giỏ hàng
            foreach ($cart_items as $item) {
                $total_amount += $item['price'] * $item['quantity'];
            }

            // Tạo đơn hàng mới
            $order_data = [
                'user_id' => $user_id,
                'customer_name' => $customer_name,
                'customer_phone' => $phone,
                'customer_address' => $address,
                'email' => $email,
                'total_amount' => $total_amount,
                'payment_method' => $payment_method,
                'notes' => $notes
            ];

            $order_id = $this->productModel->createOrder($order_data);

            if ($order_id) {
                // Tạo chi tiết đơn hàng cho mỗi sản phẩm trong giỏ hàng
                foreach ($cart_items as $item) {
                    $order_detail_data = [
                        'order_id' => $order_id,
                        'product_name' => isset($item['name_product']) ? $item['name_product'] : 
                                          (isset($item['product_name']) ? $item['product_name'] : 
                                          (isset($item['name']) ? $item['name'] : 'Sản phẩm không tên')),
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'product_id' => isset($item['product_id']) ? $item['product_id'] : null
                    ];

                    $this->productModel->createOrderDetail($order_detail_data);
                    
                    // Cập nhật số lượng sản phẩm trong kho sau khi đặt hàng
                    if (isset($item['product_id']) && $item['product_id']) {
                        $this->productModel->updateProductQuantity($item['product_id'], $item['quantity']);
                    }
                }

                // Xóa giỏ hàng sau khi đặt hàng thành công
                $this->productModel->clearCart($user_id);
                

                // Lưu ID đơn hàng vừa tạo để hiển thị trong trang cảm ơn
                $_SESSION['last_order_id'] = $order_id;
                $_SESSION['success'] = "Đặt hàng thành công.";
                $this->redirect('product/thank_you');
                exit;
            } else {
                $_SESSION['error'] = "Đã xảy ra lỗi khi xử lý đơn hàng. Vui lòng thử lại.";
                $this->redirect('product/checkout');
                exit;
            }
        }

        // Hiển thị trang thanh toán nếu không phải là POST request
        $this->view('product/checkout', ['products' => $cart_items]);
    }
    
    // Hiển thị chi tiết sản phẩm
    public function details_product() {
        $queryData = $this->getQueryData();
        
        if (isset($queryData['id'])) {
            // Chống SQL Injection bằng cách ép kiểu và lọc ID
            $id = (int)$queryData['id'];
            $product = $this->productModel->Get_product($id);
            
            if ($product) {
                // Lưu sản phẩm đã xem
                $this->productModel->saveRecentlyViewedProduct($id);
                
                // Lấy danh sách sản phẩm đã xem gần đây
                $recentlyViewed = $this->productModel->getRecentlyViewedProducts();
                
                // Loại bỏ sản phẩm hiện tại khỏi danh sách đã xem
                foreach ($recentlyViewed as $key => $item) {
                    if ($item['id'] == $id) {
                        unset($recentlyViewed[$key]);
                        break;
                    }
                }
                
                // Lấy số lượng sản phẩm trong giỏ hàng
                $cartItemCount = $this->getCartItemCount();
                
                $this->view('product/detail', [
                    'product' => $product,
                    'recentlyViewed' => array_slice($recentlyViewed, 0, 4), // Giới hạn hiển thị 4 sản phẩm
                    'cartItemCount' => $cartItemCount
                ]);
            } else {
                $this->redirect('product/index');
            }
        } else {
            $this->redirect('product/index');
        }
    } 
    // Chức năng mua ngay - mua sản phẩm trực tiếp không qua giỏ hàng
    public function buy_now() {
        $this->requireLogin();
        
        if (!$this->isLoggedIn()) {
            $_SESSION['error'] = "Vui lòng đăng nhập để tiếp tục thanh toán";
            $this->redirect('user/login');
            exit;
        }

        $queryData = $this->getQueryData();
        $product_id = isset($queryData['id']) ? $queryData['id'] : null;
        $quantity = isset($queryData['quantity']) ? (int)$queryData['quantity'] : 1;
        
        if (!$product_id) {
            $_SESSION['error'] = "Không tìm thấy sản phẩm";
            $this->redirect('product/index');
            exit;
        }
        
        // Lấy thông tin sản phẩm
        $product = $this->productModel->Get_product($product_id);
        
        if (!$product) {
            $_SESSION['error'] = "Không tìm thấy sản phẩm";
            $this->redirect('product/index');
            exit;
        }
        
        // Kiểm tra số lượng tồn kho
        if ($product['stock'] < $quantity) {
            $_SESSION['error'] = "Số lượng sản phẩm trong kho không đủ. Hiện chỉ còn {$product['stock']} sản phẩm.";
            $this->redirect("index.php?controller=product&action=detail&id={$product_id}");
            exit;
        }
        
        $user = $this->getLoggedInUser();
        $user_id = $user['id'];
        
        // Tạo mảng sản phẩm để hiển thị trong trang thanh toán
        $buy_now_product = [
            'id' => $product['id'],
            'product_id' => $product['id'],
            'name' => $product['name'],
            'product_name' => $product['name'],
            'quantity' => $quantity,
            'price' => $product['price'],
            'image' => $product['image']
        ];
        
        // Lưu thông tin sản phẩm mua ngay vào session
        $_SESSION['buy_now_product'] = $buy_now_product;
        
        // Trong phương thức buy_now()
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
            // Xử lý thanh toán tương tự như phương thức checkout
            $customer_name = isset($_POST['fullname']) ? $_POST['fullname'] : '';
            $email = isset($_POST['email']) ? $_POST['email'] : '';
            $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
            $address = isset($_POST['address']) ? $_POST['address'] : '';
            $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'cod';
            $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
            
            // Kiểm tra thông tin người dùng
            if (empty($customer_name) || empty($phone) || empty($address)) {
                $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin bắt buộc.";
                $this->redirect('index.php?controller=product&action=buy_now&id=' . $product_id);
                exit;
            }
            
            // Kiểm tra số điện thoại chỉ chứa số
            if (!preg_match('/^\d+$/', $phone)) {
                $_SESSION['error'] = "Số điện thoại chỉ được chứa chữ số.";
                $this->redirect('index.php?controller=product&action=buy_now&id=' . $product_id);
                exit;
            }
            
            // Kiểm tra độ dài số điện thoại
            if (strlen($phone) < 10 || strlen($phone) > 11) {
                $_SESSION['error'] = "Số điện thoại phải có 10-11 chữ số.";
                $this->redirect('index.php?controller=product&action=buy_now&id=' . $product_id);
                exit;
            }
            
            // Tính tổng tiền
            $total_amount = $buy_now_product['price'] * $buy_now_product['quantity'];

            // Tạo đơn hàng mới
            $order_data = [
                'user_id' => $user_id,
                'customer_name' => $customer_name,
                'customer_phone' => $phone,
                'customer_address' => $address,
                'email' => $email,
                'total_amount' => $total_amount,
                'payment_method' => $payment_method,
                'notes' => $notes
            ];

            $order_id = $this->productModel->createOrder($order_data);

            if ($order_id) {
                // Tạo chi tiết đơn hàng cho sản phẩm mua ngay
                $order_detail_data = [
                    'order_id' => $order_id,
                    'product_name' => $buy_now_product['name'],
                    'quantity' => $buy_now_product['quantity'],
                    'price' => $buy_now_product['price'],
                    'product_id' => $buy_now_product['product_id']
                ];

                $this->productModel->createOrderDetail($order_detail_data);
                
                // Cập nhật số lượng sản phẩm trong kho sau khi đặt hàng
                $this->productModel->updateProductQuantity($buy_now_product['product_id'], $buy_now_product['quantity']);

                // Xóa thông tin sản phẩm mua ngay khỏi session
                unset($_SESSION['buy_now_product']);
                
                // Lưu ID đơn hàng vừa tạo để hiển thị trong trang cảm ơn
                $_SESSION['last_order_id'] = $order_id;
                $_SESSION['success'] = "Đặt hàng thành công.";
                $this->redirect('product/thank_you');
                exit;
            } else {
                $_SESSION['error'] = "Đã xảy ra lỗi khi xử lý đơn hàng. Vui lòng thử lại.";
                $this->redirect('index.php?controller=product&action=buy_now&id=' . $product_id);
                exit;
            }
        }
        
        // Hiển thị trang thanh toán với sản phẩm mua ngay
        $this->view('product/buy_now', ['product' => $buy_now_product]);
    }
    
    // Trang cảm ơn sau khi đặt hàng thành công
    public function thank_you() {
        $this->requireLogin();
        
        // Bạn có thể truyền thêm dữ liệu về đơn hàng vừa tạo nếu cần
        $this->view('product/thank_you', [
            'message' => 'Cảm ơn bạn đã đặt hàng. Đơn hàng của bạn đang được xử lý.',
            'order_number' => isset($_SESSION['last_order_id']) ? $_SESSION['last_order_id'] : 'N/A'
        ]);
        
        // Xóa ID đơn hàng khỏi session sau khi hiển thị
        if (isset($_SESSION['last_order_id'])) {
            unset($_SESSION['last_order_id']);
        }
    }
}