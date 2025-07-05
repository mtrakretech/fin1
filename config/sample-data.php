<?php

class SampleDataLoader {
    private $db;
    
    public function __construct($database) {
        $this->db = $database->getConnection();
    }
    
    public function loadSampleData() {
        // Check if sample data already exists
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM transactions");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            return false; // Data already exists
        }
        
        // Sample transactions for the last 30 days
        $sampleTransactions = [
            ['income', 3500.00, 'Monthly Salary', 'Salary', date('Y-m-01')],
            ['expense', 1200.00, 'Rent Payment', 'Utilities', date('Y-m-01')],
            ['expense', 45.50, 'Grocery Shopping', 'Food', date('Y-m-02')],
            ['expense', 25.00, 'Coffee Shop', 'Food', date('Y-m-03')],
            ['expense', 80.00, 'Gas Station', 'Transportation', date('Y-m-03')],
            ['expense', 120.00, 'Electric Bill', 'Utilities', date('Y-m-05')],
            ['expense', 15.99, 'Netflix Subscription', 'Entertainment', date('Y-m-05')],
            ['expense', 67.89, 'Grocery Shopping', 'Food', date('Y-m-07')],
            ['income', 200.00, 'Freelance Project', 'Salary', date('Y-m-08')],
            ['expense', 89.99, 'New Shoes', 'Shopping', date('Y-m-10')],
            ['expense', 32.50, 'Restaurant Dinner', 'Food', date('Y-m-12')],
            ['expense', 50.00, 'Movie Tickets', 'Entertainment', date('Y-m-14')],
            ['expense', 95.00, 'Internet Bill', 'Utilities', date('Y-m-15')],
            ['expense', 78.43, 'Grocery Shopping', 'Food', date('Y-m-16')],
            ['expense', 40.00, 'Uber Rides', 'Transportation', date('Y-m-18')],
            ['income', 150.00, 'Sold Old Electronics', 'Salary', date('Y-m-20')],
            ['expense', 29.99, 'Book Purchase', 'Shopping', date('Y-m-22')],
            ['expense', 55.67, 'Grocery Shopping', 'Food', date('Y-m-24')],
            ['expense', 35.00, 'Gym Membership', 'Entertainment', date('Y-m-25')],
            ['expense', 18.50, 'Coffee Shop', 'Food', date('Y-m-26')],
            ['expense', 125.00, 'Phone Bill', 'Utilities', date('Y-m-28')],
            ['expense', 42.30, 'Gas Station', 'Transportation', date('Y-m-29')]
        ];
        
        // Insert sample transactions
        $stmt = $this->db->prepare("INSERT INTO transactions (type, amount, description, category, date) VALUES (?, ?, ?, ?, ?)");
        foreach ($sampleTransactions as $transaction) {
            $stmt->execute($transaction);
        }
        
        // Sample todos
        $sampleTodos = [
            ['Complete monthly budget review', 'Review all expenses and plan for next month', 0],
            ['Pay credit card bill', 'Due date is coming up', 0],
            ['Set up emergency fund', 'Save $1000 for emergencies', 0],
            ['Research investment options', 'Look into index funds and ETFs', 0],
            ['Update insurance policies', 'Review and update car and health insurance', 0],
            ['File tax documents', 'Organize receipts and financial documents', 1],
            ['Create shopping list', 'Plan groceries for the week', 1],
            ['Schedule financial advisor meeting', 'Discuss retirement planning', 0],
            ['Track daily expenses', 'Monitor spending for better budgeting', 0],
            ['Read financial literacy book', 'Improve personal finance knowledge', 0]
        ];
        
        // Insert sample todos
        $stmt = $this->db->prepare("INSERT INTO todos (title, description, completed) VALUES (?, ?, ?)");
        foreach ($sampleTodos as $todo) {
            $stmt->execute($todo);
        }
        
        return true;
    }
    
    public function clearSampleData() {
        $this->db->exec("DELETE FROM transactions");
        $this->db->exec("DELETE FROM todos");
        return true;
    }
}