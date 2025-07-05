<?php

class Todo {
    private $db;
    
    public function __construct($database) {
        $this->db = $database->getConnection();
    }
    
    public function create($title, $description = '') {
        $stmt = $this->db->prepare("INSERT INTO todos (title, description) VALUES (?, ?)");
        return $stmt->execute([$title, $description]);
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM todos ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function toggle($id) {
        $stmt = $this->db->prepare("UPDATE todos SET completed = 1 - completed WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM todos WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getStats() {
        $stmt = $this->db->query("SELECT 
            COUNT(*) as total,
            SUM(completed) as completed,
            COUNT(*) - SUM(completed) as pending
            FROM todos");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}