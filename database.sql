
CREATE DATABASE IF NOT EXISTS food_donation;
USE food_donation;

CREATE TABLE IF NOT EXISTS donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    food_type VARCHAR(50),
    quantity VARCHAR(50),
    best_before VARCHAR(50),
    instructions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
