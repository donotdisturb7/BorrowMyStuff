<?php
namespace App\Config;

use PDO;
use PDOException;

require_once __DIR__ . '/../../bootstrap.php';

function initializeDatabase() {
    try {
        $host = $_ENV['DB_HOST'] ?? throw new \RuntimeException('DB_HOST not set in environment');
        $dbname = $_ENV['DB_NAME'] ?? throw new \RuntimeException('DB_NAME not set in environment');
        $username = $_ENV['DB_USER'] ?? throw new \RuntimeException('DB_USER not set in environment');
        $password = $_ENV['DB_PASS'] ?? throw new \RuntimeException('DB_PASS not set in environment');

        // First connect without database to create it if needed
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Create database if it doesn't exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
        
        // Now connect to the database and create tables
        $pdo = Database::getConnection();

        // Create users table
        $sql = "CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(255) NOT NULL,
            `email` VARCHAR(255) UNIQUE NOT NULL,
            `password_hash` VARCHAR(255) NOT NULL,
            `role` ENUM('user', 'admin') NOT NULL DEFAULT 'user',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $pdo->exec($sql);

        // Create items table
        $sql = "CREATE TABLE IF NOT EXISTS `items` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `description` TEXT,
            `category` VARCHAR(50),
            `image_url` TEXT,
            `owner_id` INT NOT NULL,
            `available` BOOLEAN NOT NULL DEFAULT TRUE,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (owner_id) REFERENCES users(id)
        )";
        $pdo->exec($sql);

        // Create demande_pret table
        $sql = "CREATE TABLE IF NOT EXISTS `demande_pret` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `item_id` INT NOT NULL,
            `requester_id` INT NOT NULL,
            `request_date` DATE NOT NULL,
            `start_date` DATE NOT NULL,
            `end_date` DATE NOT NULL,
            `status` ENUM('pending', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
            `admin_notes` TEXT,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (item_id) REFERENCES items(id),
            FOREIGN KEY (requester_id) REFERENCES users(id)
        )";
        $pdo->exec($sql);

        // Create prets table
        $sql = "CREATE TABLE IF NOT EXISTS `prets` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `item_id` INT NOT NULL,
            `borrower_id` INT NOT NULL,
            `start_date` DATE NOT NULL,
            `end_date` DATE NOT NULL,
            `status` ENUM('ongoing', 'returned') NOT NULL DEFAULT 'ongoing',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (item_id) REFERENCES items(id),
            FOREIGN KEY (borrower_id) REFERENCES users(id)
        )";
        $pdo->exec($sql);

        // Success is silent
        return true;
    } catch (PDOException $e) {
        throw new \RuntimeException("Database initialization failed: " . $e->getMessage());
    }
}

// Run the initialization
initializeDatabase();