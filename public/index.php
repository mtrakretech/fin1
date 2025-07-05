<?php
require_once '../config/database.php';
require_once '../src/Todo.php';
require_once '../src/Finance.php';

// Initialize database and classes
$database = new Database();
$todo = new Todo($database);
$finance = new Finance($database);

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_todo':
            $result = $todo->create($_POST['title'], $_POST['description'] ?? '');
            echo json_encode(['success' => $result]);
            exit;
            
        case 'toggle_todo':
            $result = $todo->toggle($_POST['id']);
            echo json_encode(['success' => $result]);
            exit;
            
        case 'delete_todo':
            $result = $todo->delete($_POST['id']);
            echo json_encode(['success' => $result]);
            exit;
            
        case 'add_transaction':
            $result = $finance->addTransaction(
                $_POST['type'],
                $_POST['amount'],
                $_POST['description'],
                $_POST['category'],
                $_POST['date']
            );
            echo json_encode(['success' => $result]);
            exit;
            
        case 'delete_transaction':
            $result = $finance->deleteTransaction($_POST['id']);
            echo json_encode(['success' => $result]);
            exit;
    }
}

// Get data for display
$todos = $todo->getAll();
$todoStats = $todo->getStats();
$transactions = $finance->getTransactions(20);
$balance = $finance->getBalance();
$categories = $finance->getCategories();
$categoryStats = $finance->getCategoryStats();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Finance & Todo Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-chart-line"></i> Fin1 Manager</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#finance">Finance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#todos">Todos</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Dashboard Tab -->
        <div id="dashboard" class="tab-content active">
            <div class="row">
                <!-- Balance Cards -->
                <div class="col-md-4 mb-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-arrow-up"></i> Income</h5>
                            <h3>$<?= number_format($balance['income'], 2) ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-arrow-down"></i> Expenses</h5>
                            <h3>$<?= number_format($balance['expenses'], 2) ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card <?= $balance['balance'] >= 0 ? 'bg-primary' : 'bg-warning' ?> text-white">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-wallet"></i> Balance</h5>
                            <h3>$<?= number_format($balance['balance'], 2) ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Todo Stats -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-tasks"></i> Todo Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h4 class="text-primary"><?= $todoStats['total'] ?></h4>
                                    <small>Total</small>
                                </div>
                                <div class="col-4">
                                    <h4 class="text-success"><?= $todoStats['completed'] ?></h4>
                                    <small>Completed</small>
                                </div>
                                <div class="col-4">
                                    <h4 class="text-warning"><?= $todoStats['pending'] ?></h4>
                                    <small>Pending</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Transactions -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-history"></i> Recent Transactions</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach (array_slice($transactions, 0, 5) as $transaction): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong><?= htmlspecialchars($transaction['description']) ?></strong><br>
                                        <small class="text-muted"><?= $transaction['category'] ?></small>
                                    </div>
                                    <span class="badge <?= $transaction['type'] === 'income' ? 'bg-success' : 'bg-danger' ?>">
                                        <?= $transaction['type'] === 'income' ? '+' : '-' ?>$<?= number_format($transaction['amount'], 2) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Finance Tab -->
        <div id="finance" class="tab-content">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-plus"></i> Add Transaction</h5>
                        </div>
                        <div class="card-body">
                            <form id="transactionForm">
                                <div class="mb-3">
                                    <select class="form-select" name="type" required>
                                        <option value="">Select Type</option>
                                        <option value="income">Income</option>
                                        <option value="expense">Expense</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <input type="number" class="form-control" name="amount" step="0.01" placeholder="Amount" required>
                                </div>
                                <div class="mb-3">
                                    <input type="text" class="form-control" name="description" placeholder="Description" required>
                                </div>
                                <div class="mb-3">
                                    <select class="form-select" name="category" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= htmlspecialchars($category['name']) ?>"><?= htmlspecialchars($category['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <input type="date" class="form-control" name="date" value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Add Transaction</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-list"></i> Recent Transactions</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Category</th>
                                            <th>Amount</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="transactionsList">
                                        <?php foreach ($transactions as $transaction): ?>
                                            <tr>
                                                <td><?= date('M d, Y', strtotime($transaction['date'])) ?></td>
                                                <td><?= htmlspecialchars($transaction['description']) ?></td>
                                                <td><span class="badge bg-secondary"><?= htmlspecialchars($transaction['category']) ?></span></td>
                                                <td>
                                                    <span class="<?= $transaction['type'] === 'income' ? 'text-success' : 'text-danger' ?>">
                                                        <?= $transaction['type'] === 'income' ? '+' : '-' ?>$<?= number_format($transaction['amount'], 2) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteTransaction(<?= $transaction['id'] ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Todos Tab -->
        <div id="todos" class="tab-content">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-plus"></i> Add Todo</h5>
                        </div>
                        <div class="card-body">
                            <form id="todoForm">
                                <div class="mb-3">
                                    <input type="text" class="form-control" name="title" placeholder="Todo title" required>
                                </div>
                                <div class="mb-3">
                                    <textarea class="form-control" name="description" placeholder="Description (optional)" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Add Todo</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-tasks"></i> Your Todos</h5>
                        </div>
                        <div class="card-body">
                            <div id="todosList">
                                <?php foreach ($todos as $todoItem): ?>
                                    <div class="todo-item <?= $todoItem['completed'] ? 'completed' : '' ?>" data-id="<?= $todoItem['id'] ?>">
                                        <div class="d-flex align-items-center">
                                            <input type="checkbox" class="form-check-input me-3" <?= $todoItem['completed'] ? 'checked' : '' ?> onchange="toggleTodo(<?= $todoItem['id'] ?>)">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1"><?= htmlspecialchars($todoItem['title']) ?></h6>
                                                <?php if ($todoItem['description']): ?>
                                                    <p class="mb-0 text-muted small"><?= htmlspecialchars($todoItem['description']) ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <button class="btn btn-sm btn-danger" onclick="deleteTodo(<?= $todoItem['id'] ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/app.js"></script>
</body>
</html>