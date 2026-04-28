<?php
require 'api/config.php';

$sql = "
CREATE TABLE admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50),
  password VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO admin (username, password) VALUES 
('admin', '$2a$11$7DaGYLxIfRtoJzrermqESOteB5ikHvlo9MJjHiUS..oKLKgeilHbG');

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100) UNIQUE,
  password VARCHAR(255),
  balance DECIMAL(10,2) DEFAULT 0,
  referral_code VARCHAR(20),
  referred_by VARCHAR(20),
  referral_earn DECIMAL(10,2) DEFAULT 0,
  referral_count INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  amount DECIMAL(10,2),
  type ENUM('add','deduct','referral'),
  status ENUM('success','failed','pending') DEFAULT 'success',
  description VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE withdraw_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  amount DECIMAL(10,2),
  upi_id VARCHAR(100),
  status ENUM('pending','approved','rejected') DEFAULT 'pending',
  admin_note VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
";

$pdo->exec($sql);

echo "Database Imported Successfully ✅";
?>
