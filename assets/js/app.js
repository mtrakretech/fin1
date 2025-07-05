// Personal Finance & Todo Manager JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Tab navigation
    setupTabNavigation();
    
    // Form handlers
    setupForms();
    
    // Initialize app
    updateFinanceCategories();
    
    // Initialize charts if on charts tab
    if (document.getElementById('charts')) {
        setTimeout(initializeCharts, 500);
    }
});

// Tab Navigation
function setupTabNavigation() {
    const navLinks = document.querySelectorAll('.nav-link');
    const tabContents = document.querySelectorAll('.tab-content');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all nav links
            navLinks.forEach(nav => nav.classList.remove('active'));
            
            // Add active class to clicked nav link
            this.classList.add('active');
            
            // Hide all tab contents
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Show selected tab content
            const targetTab = this.getAttribute('href').substring(1);
            const targetContent = document.getElementById(targetTab);
            if (targetContent) {
                targetContent.classList.add('active');
                
                // Initialize charts if charts tab is selected
                if (targetTab === 'charts') {
                    setTimeout(initializeCharts, 100);
                }
            }
        });
    });
}

// Form Setup
function setupForms() {
    // Todo form
    const todoForm = document.getElementById('todoForm');
    if (todoForm) {
        todoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            addTodo();
        });
    }
    
    // Transaction form
    const transactionForm = document.getElementById('transactionForm');
    if (transactionForm) {
        transactionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            addTransaction();
        });
    }
}

// Todo Functions
function addTodo() {
    const form = document.getElementById('todoForm');
    const formData = new FormData(form);
    formData.append('action', 'add_todo');
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.innerHTML = '<span class="loading"></span> Adding...';
    submitBtn.disabled = true;
    
    fetch('index.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Todo added successfully!', 'success');
            form.reset();
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showAlert('Failed to add todo. Please try again.', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred. Please try again.', 'danger');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

function toggleTodo(id) {
    const formData = new FormData();
    formData.append('action', 'toggle_todo');
    formData.append('id', id);
    
    fetch('index.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const todoItem = document.querySelector(`[data-id="${id}"]`);
            if (todoItem) {
                todoItem.classList.toggle('completed');
            }
            // Refresh after a short delay to update stats
            setTimeout(() => {
                location.reload();
            }, 500);
        } else {
            showAlert('Failed to update todo. Please try again.', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred. Please try again.', 'danger');
    });
}

function deleteTodo(id) {
    if (!confirm('Are you sure you want to delete this todo?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'delete_todo');
    formData.append('id', id);
    
    fetch('index.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Todo deleted successfully!', 'success');
            const todoItem = document.querySelector(`[data-id="${id}"]`);
            if (todoItem) {
                todoItem.style.transform = 'translateX(-100%)';
                todoItem.style.opacity = '0';
                setTimeout(() => {
                    location.reload();
                }, 300);
            }
        } else {
            showAlert('Failed to delete todo. Please try again.', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred. Please try again.', 'danger');
    });
}

// Finance Functions
function addTransaction() {
    const form = document.getElementById('transactionForm');
    const formData = new FormData(form);
    formData.append('action', 'add_transaction');
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.innerHTML = '<span class="loading"></span> Adding...';
    submitBtn.disabled = true;
    
    fetch('index.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Transaction added successfully!', 'success');
            form.reset();
            // Reset date to today
            form.querySelector('input[name="date"]').value = new Date().toISOString().split('T')[0];
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showAlert('Failed to add transaction. Please try again.', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred. Please try again.', 'danger');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

function deleteTransaction(id) {
    if (!confirm('Are you sure you want to delete this transaction?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'delete_transaction');
    formData.append('id', id);
    
    fetch('index.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Transaction deleted successfully!', 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showAlert('Failed to delete transaction. Please try again.', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred. Please try again.', 'danger');
    });
}

// Update category options based on transaction type
function updateFinanceCategories() {
    const typeSelect = document.querySelector('select[name="type"]');
    const categorySelect = document.querySelector('select[name="category"]');
    
    if (typeSelect && categorySelect) {
        typeSelect.addEventListener('change', function() {
            const selectedType = this.value;
            const options = categorySelect.querySelectorAll('option');
            
            options.forEach(option => {
                if (option.value === '') {
                    option.style.display = 'block';
                    return;
                }
                
                // This is a simple approach - in a real app, you'd filter based on actual category types
                if (selectedType === 'income') {
                    option.style.display = option.textContent === 'Salary' ? 'block' : 'none';
                } else if (selectedType === 'expense') {
                    option.style.display = option.textContent !== 'Salary' ? 'block' : 'none';
                } else {
                    option.style.display = 'block';
                }
            });
            
            // Reset category selection
            categorySelect.value = '';
        });
    }
}

// Utility Functions
function showAlert(message, type) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create new alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at the top of the container
    const container = document.querySelector('.container');
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        if (alertDiv && alertDiv.parentNode) {
            alertDiv.classList.remove('show');
            setTimeout(() => {
                if (alertDiv && alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 150);
        }
    }, 5000);
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

// Format date
function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Add loading states to all buttons
document.querySelectorAll('button').forEach(button => {
    button.addEventListener('click', function() {
        if (this.type !== 'submit' && !this.onclick) {
            return;
        }
        
        // Add a subtle loading effect
        this.style.transform = 'scale(0.98)';
        setTimeout(() => {
            this.style.transform = 'scale(1)';
        }, 100);
    });
});

// Mobile menu improvements
const navbarToggler = document.querySelector('.navbar-toggler');
const navbarCollapse = document.querySelector('.navbar-collapse');

if (navbarToggler && navbarCollapse) {
    navbarToggler.addEventListener('click', function() {
        navbarCollapse.classList.toggle('show');
    });
    
    // Close mobile menu when clicking nav links
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                navbarCollapse.classList.remove('show');
            }
        });
    });
}

// Sample Data Functions
function loadSampleData() {
    if (!confirm('This will load sample financial transactions and todos. Continue?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'load_sample_data');
    
    fetch('index.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Sample data loaded successfully!', 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showAlert('Sample data already exists or failed to load.', 'warning');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while loading sample data.', 'danger');
    });
}

function clearSampleData() {
    if (!confirm('This will delete ALL transactions and todos. This action cannot be undone. Continue?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'clear_sample_data');
    
    fetch('index.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('All data cleared successfully!', 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showAlert('Failed to clear data.', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while clearing data.', 'danger');
    });
}

// Chart Functions
function initializeCharts() {
    // Only initialize if Chart.js is loaded and we're on the charts tab
    if (typeof Chart === 'undefined') {
        console.warn('Chart.js not loaded');
        return;
    }
    
    // Get chart canvases
    const expenseChartCanvas = document.getElementById('expenseChart');
    const balanceChartCanvas = document.getElementById('balanceChart');
    const incomeExpenseChartCanvas = document.getElementById('incomeExpenseChart');
    
    if (!expenseChartCanvas || !balanceChartCanvas || !incomeExpenseChartCanvas) {
        return;
    }
    
    // Fetch data for charts (this would ideally be passed from PHP or fetched via AJAX)
    createExpenseChart(expenseChartCanvas);
    createBalanceChart(balanceChartCanvas);
    createIncomeExpenseChart(incomeExpenseChartCanvas);
}

function createExpenseChart(canvas) {
    const ctx = canvas.getContext('2d');
    
    // Sample data - in a real app, this would come from the server
    const data = {
        labels: ['Food', 'Transportation', 'Utilities', 'Entertainment', 'Shopping'],
        datasets: [{
            data: [300, 120, 340, 100, 160],
            backgroundColor: [
                '#dc3545',
                '#ffc107', 
                '#6c757d',
                '#17a2b8',
                '#e83e8c'
            ],
            borderWidth: 0
        }]
    };
    
    new Chart(ctx, {
        type: 'doughnut',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function createBalanceChart(canvas) {
    const ctx = canvas.getContext('2d');
    
    // Sample data for the last 6 months
    const data = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Balance',
            data: [1000, 1200, 800, 1500, 1800, 2100],
            borderColor: '#667eea',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    };
    
    new Chart(ctx, {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                }
            }
        }
    });
}

function createIncomeExpenseChart(canvas) {
    const ctx = canvas.getContext('2d');
    
    const data = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [
            {
                label: 'Income',
                data: [3500, 3700, 3500, 3800, 3600, 3900],
                backgroundColor: '#28a745',
                borderColor: '#28a745',
                borderWidth: 1
            },
            {
                label: 'Expenses',
                data: [2500, 2500, 2700, 2300, 1800, 1800],
                backgroundColor: '#dc3545',
                borderColor: '#dc3545',
                borderWidth: 1
            }
        ]
    };
    
    new Chart(ctx, {
        type: 'bar',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                }
            }
        }
    });
}