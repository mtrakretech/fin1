<?php

class Database {
    private $connection;
    
    public function __construct() {
        $this->connect();
        $this->createTables();
    }
    
    private function connect() {
        try {
            $this->connection = new PDO('sqlite:' . __DIR__ . '/../data/app.db');
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }
    
    private function createTables() {
        // Create todos table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS todos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                description TEXT,
                completed INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Create transactions table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS transactions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                type TEXT NOT NULL CHECK(type IN ('income', 'expense')),
                amount DECIMAL(10,2) NOT NULL,
                description TEXT NOT NULL,
                category TEXT NOT NULL,
                date DATE NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Create categories table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL UNIQUE,
                type TEXT NOT NULL CHECK(type IN ('income', 'expense')),
                color TEXT DEFAULT '#007bff'
            )
        ");
        
        // Insert default categories
        $this->connection->exec("
            INSERT OR IGNORE INTO categories (name, type, color) VALUES 
            ('Salary', 'income', '#28a745'),
            ('Food', 'expense', '#dc3545'),
            ('Transportation', 'expense', '#ffc107'),
            ('Entertainment', 'expense', '#17a2b8'),
            ('Utilities', 'expense', '#6c757d'),
            ('Shopping', 'expense', '#e83e8c')
        ");
    }
    
    public function getConnection() {
        return $this->connection;
    }
}