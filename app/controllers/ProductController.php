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
                
                if ($result) {
                    // Đặt thông báo thành công vào session
                    $_SESSION['cart_message'] = "Đã thêm <strong>{$quantity}</strong> sản phẩm <strong>{$product['name']}</strong> vào giỏ hàng!";
                    
                    // Chuyển hướng trở lại trang trước đó
                    header("Location: $referer");
                    exit();
                } else {
                    $_SESSION['error'] = "Không thể thêm sản phẩm vào giỏ hàng";
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
            // Lọc đầu vào để tránh XSS
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
    
    // Thêm sản phẩm mới - yêu cầu đăng nhập
    public function add() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $this->getPostData();
            
            // Lỗ hổng XSS: không lọc đầu vào
            $productData = [
                'name' => $postData['name'],
                'description' => $postData['description'],
                'price' => $postData['price'],
                'stock' => $postData['stock'],
                'user_id' => $this->getLoggedInUser()['id']
            ];
            
            // Xử lý tải lên hình ảnh
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                // Lỗ hổng: Upload file mà không kiểm tra loại tệp
                $image_path = $this->productModel->uploadProductImage($_FILES['image']);
                
                if ($image_path) {
                    $productData['image'] = $image_path;
                    
                    // Lỗ hổng Command Injection: truyền dữ liệu người dùng vào lệnh shell
                    if (isset($postData['thumb_width']) && isset($postData['thumb_height'])) {
                        $this->productModel->generateProductThumbnail(
                            $image_path,
                            $postData['thumb_width'],
                            $postData['thumb_height']
                        );
                    }
                }
            }
            
            // Lỗ hổng SQL Injection: truyền dữ liệu không được lọc vào truy vấn
            $result = $this->productModel->insert($productData);
            
            if ($result) {
                $this->redirect('index.php?controller=product&action=index');
            } else {
                $this->view('product/add', ['error' => 'Không thể thêm sản phẩm']);
            }
        } else {
            $this->view('product/add');
        }
    }
    
    // Chỉnh sửa sản phẩm - yêu cầu đăng nhập
    public function edit() {
        $this->requireLogin();
        $queryData = $this->getQueryData();
        
        if (!isset($queryData['id'])) {
            $this->redirect('index.php?controller=product&action=index');
        }
        
        $product_id = $queryData['id'];
        $user = $this->getLoggedInUser();
        
        // Lỗ hổng IDOR: không kiểm tra xem sản phẩm có thuộc về người dùng hay không
        $product = $this->productModel->getUserProduct($product_id, $user['id']);
        
        if (!$product) {
            $this->redirect('index.php?controller=product&action=index');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $this->getPostData();
            
            // Lỗ hổng XSS: không lọc đầu vào
            $productData = [
                'name' => $postData['name'],
                'description' => $postData['description'],
                'price' => $postData['price'],
                'stock' => $postData['stock']
            ];
            
            // Lỗ hổng SQL Injection: truyền dữ liệu không được lọc vào truy vấn
            $result = $this->productModel->update($product_id, $productData);
            
            if ($result) {
                $this->redirect('index.php?controller=product&action=viewProduct&id=' . $product_id);
            } else {
                $this->view('product/edit', [
                    'product' => $product,
                    'error' => 'Không thể cập nhật sản phẩm'
                ]);
            }
        } else {
            $this->view('product/edit', ['product' => $product]);
        }
    }
    
    // Xóa sản phẩm - yêu cầu đăng nhập
    public function delete() {
        $this->requireLogin();
        $queryData = $this->getQueryData();
        
        if (!isset($queryData['id'])) {
            $this->redirect('index.php?controller=product&action=index');
        }
        
        $product_id = $queryData['id'];
        
        // Lỗ hổng CSRF: không xác thực token CSRF
        // Lỗ hổng IDOR: không kiểm tra quyền sở hữu sản phẩm
        $result = $this->productModel->delete($product_id);
        
        $this->redirect('index.php?controller=product&action=index');
    }
    
    // Lỗ hổng: Chức năng xuất dữ liệu sản phẩm vào CSV với Path Traversal
    public function export() {
        $this->requireLogin();
        $queryData = $this->getQueryData();
        
        if (isset($queryData['filename'])) {
            // Lỗ hổng Path Traversal: không lọc tên tệp
            $filename = $queryData['filename'];
            $filepath = "exports/" . $filename . ".csv";
            
            $products = $this->productModel->getAll();
            
            // Tạo file CSV
            $fp = fopen($filepath, 'w');
            
            // Thêm tiêu đề
            fputcsv($fp, ['ID', 'Tên', 'Mô tả', 'Giá', 'Tồn kho']);
            
            // Thêm dữ liệu
            foreach ($products as $product) {
                fputcsv($fp, [
                    $product['id'],
                    $product['name'],
                    $product['description'],
                    $product['price'],
                    $product['stock']
                ]);
            }
            
            fclose($fp);
            
            // Chuyển hướng để tải tệp
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
            header('Pragma: no-cache');
            readfile($filepath);
            exit;
        } else {
            $this->view('product/export');
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
            
            $this->redirect('index.php?controller=product&action=gio_hang');
        } else {
            $this->redirect('index.php?controller=product&action=gio_hang');
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
                $this->redirect('index.php?controller=product&action=checkout');
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
                }

                // Xóa giỏ hàng sau khi đặt hàng thành công
                $this->productModel->clearCart($user_id);

                // Lưu ID đơn hàng vừa tạo để hiển thị trong trang cảm ơn
                $_SESSION['last_order_id'] = $order_id;
                $_SESSION['success'] = "Đặt hàng thành công.";
                $this->redirect('index.php?controller=product&action=thank_you');
                exit;
            } else {
                $_SESSION['error'] = "Đã xảy ra lỗi khi xử lý đơn hàng. Vui lòng thử lại.";
                $this->redirect('index.php?controller=product&action=checkout');
                exit;
            }
        }

        $this->view('product/checkout', ['products' => $cart_items]);
    }
    
    // Phương thức này duy trì tương thích ngược với các liên kết cũ
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
                
                // Sử dụng view detail.php thay vì details_product.php
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