<?php

class Finance {
    private $db;
    
    public function __construct($database) {
        $this->db = $database->getConnection();
    }
    
    public function addTransaction($type, $amount, $description, $category, $date) {
        $stmt = $this->db->prepare("INSERT INTO transactions (type, amount, description, category, date) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$type, $amount, $description, $category, $date]);
    }
    
    public function getTransactions($limit = 50) {
        $stmt = $this->db->prepare("SELECT * FROM transactions ORDER BY date DESC, created_at DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getBalance() {
        $stmt = $this->db->query("SELECT 
            SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
            SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expenses
            FROM transactions");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $income = $result['total_income'] ?? 0;
        $expenses = $result['total_expenses'] ?? 0;
        
        return [
            'income' => $income,
            'expenses' => $expenses,
            'balance' => $income - $expenses
        ];
    }
    
    public function getCategories($type = null) {
        if ($type) {
            $stmt = $this->db->prepare("SELECT * FROM categories WHERE type = ? ORDER BY name");
            $stmt->execute([$type]);
        } else {
            $stmt = $this->db->query("SELECT * FROM categories ORDER BY type, name");
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getCategoryStats() {
        $stmt = $this->db->query("SELECT 
            c.name, c.color, c.type,
            COALESCE(SUM(t.amount), 0) as total
            FROM categories c
            LEFT JOIN transactions t ON c.name = t.category
            GROUP BY c.id, c.name, c.color, c.type
            ORDER BY total DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function deleteTransaction($id) {
        $stmt = $this->db->prepare("DELETE FROM transactions WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function searchTransactions($search, $category = null, $type = null, $limit = 50) {
        $sql = "SELECT * FROM transactions WHERE (description LIKE ? OR category LIKE ?)";
        $params = ["%$search%", "%$search%"];
        
        if ($category) {
            $sql .= " AND category = ?";
            $params[] = $category;
        }
        
        if ($type) {
            $sql .= " AND type = ?";
            $params[] = $type;
        }
        
        $sql .= " ORDER BY date DESC, created_at DESC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getMonthlyReport($year = null, $month = null) {
        if (!$year) $year = date('Y');
        if (!$month) $month = date('m');
        
        $stmt = $this->db->prepare("SELECT 
            type, 
            category,
            SUM(amount) as total,
            COUNT(*) as count
            FROM transactions 
            WHERE strftime('%Y', date) = ? AND strftime('%m', date) = ?
            GROUP BY type, category
            ORDER BY type, total DESC");
        $stmt->execute([$year, $month]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}