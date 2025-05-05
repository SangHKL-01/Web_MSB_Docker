<?php
/**
 * Fix payment_method column in orders table
 * 
 * This script will check if the payment_method column exists in the orders table
 * and add it if it doesn't exist.
 * 
 * To run this script:
 * 1. Place it in your WEB_MSB directory
 * 2. Access it from your browser: http://localhost/WEB_MSB/fix_payment_method.php
 * or via command line: php fix_payment_method.php
 */

echo "Starting fix for payment_method column in orders table...\n";

// Database connection parameters
$host = "localhost";
$username = "root";
$password = "";
$database = "db_product";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

echo "Connected to database successfully.\n";

// Check if the orders table exists
$check_table = $conn->query("SHOW TABLES LIKE 'orders'");

if ($check_table->num_rows == 0) {
    echo "Orders table does not exist. Creating orders table...\n";
    
    // Create the orders table
    $create_orders = "CREATE TABLE orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        total_amount DECIMAL(10, 2) NOT NULL,
        status VARCHAR(50) DEFAULT 'đang xử lý',
        payment_method VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($create_orders)) {
        echo "Orders table created successfully.\n";
    } else {
        echo "Error creating orders table: " . $conn->error . "\n";
    }
} else {
    echo "Orders table already exists. Checking for payment_method column...\n";
    
    // Check if payment_method column exists
    $check_column = $conn->query("SHOW COLUMNS FROM orders LIKE 'payment_method'");
    
    if ($check_column->num_rows == 0) {
        echo "payment_method column does not exist. Adding column...\n";
        
        // Add payment_method column
        $add_column = "ALTER TABLE orders ADD COLUMN payment_method VARCHAR(100) AFTER status";
        
        if ($conn->query($add_column)) {
            echo "payment_method column added successfully.\n";
        } else {
            echo "Error adding payment_method column: " . $conn->error . "\n";
        }
    } else {
        echo "payment_method column already exists.\n";
    }
}

// Check if the order_details table exists
$check_details = $conn->query("SHOW TABLES LIKE 'order_details'");

if ($check_details->num_rows == 0) {
    echo "order_details table does not exist. Creating table...\n";
    
    // Create the order_details table
    $create_details = "CREATE TABLE order_details (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT,
        product_name VARCHAR(255) NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        price DECIMAL(10, 2) NOT NULL
    )";
    
    if ($conn->query($create_details)) {
        echo "order_details table created successfully.\n";
    } else {
        echo "Error creating order_details table: " . $conn->error . "\n";
    }
} else {
    echo "order_details table already exists.\n";
}

echo "Fix completed! You can now use the checkout functionality.\n";

$conn->close();
?> 