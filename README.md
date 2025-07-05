# Fin1 - Personal Finance & Todo Manager

A modern, responsive web application for managing personal finances and todo tasks, built with PHP, SQLite, Bootstrap, and vanilla JavaScript.

## Features

### 📊 Financial Management
- **Income & Expense Tracking**: Record and categorize all your financial transactions
- **Real-time Balance Calculation**: Automatically calculates your current financial position
- **Category Management**: Pre-defined categories for better organization
- **Transaction History**: View recent transactions with detailed information
- **Visual Dashboard**: Beautiful cards showing income, expenses, and balance

### ✅ Todo Management
- **Task Creation**: Add todos with titles and optional descriptions
- **Task Completion**: Mark tasks as completed with visual feedback
- **Task Statistics**: View total, completed, and pending task counts
- **Task Deletion**: Remove completed or unwanted tasks

### 🎨 Modern UI/UX
- **Responsive Design**: Works perfectly on desktop, tablet, and mobile devices
- **Beautiful Gradients**: Modern gradient-based color scheme
- **Smooth Animations**: Engaging hover effects and transitions
- **Intuitive Navigation**: Tab-based interface for easy access to features
- **Real-time Feedback**: Instant alerts for user actions

## Technology Stack

- **Backend**: PHP 8.4
- **Database**: SQLite (file-based, no server required)
- **Frontend**: Bootstrap 5.3, Font Awesome 6.0
- **JavaScript**: Vanilla JS with modern ES6+ features
- **Architecture**: MVC-inspired structure with clean separation of concerns

## Installation

### Prerequisites
- PHP 8.4 or higher
- SQLite extension (usually included with PHP)
- Web server (Apache/Nginx) or use PHP's built-in server

### Quick Setup

1. **Clone the repository**:
   ```bash
   git clone <repository-url>
   cd fin1
   ```

2. **Install dependencies** (if you haven't already):
   ```bash
   # Ubuntu/Debian
   sudo apt-get update
   sudo apt-get install -y php8.4 php8.4-cli php8.4-sqlite3 php8.4-curl php8.4-zip

   # Or use the setup script that was already run
   ```

3. **Set up permissions**:
   ```bash
   chmod 755 data/
   chmod 644 data/app.db  # This will be created automatically
   ```

4. **Start the development server**:
   ```bash
   cd public
   php -S localhost:8000
   ```

5. **Open your browser** and navigate to:
   ```
   http://localhost:8000
   ```

## Project Structure

```
fin1/
├── public/                 # Web-accessible files
│   ├── index.php          # Main application file
│   └── .htaccess          # URL rewriting and security
├── src/                   # PHP classes
│   ├── Todo.php           # Todo management logic
│   └── Finance.php        # Finance management logic
├── config/                # Configuration files
│   └── database.php       # Database connection and setup
├── assets/                # Static assets
│   ├── css/
│   │   └── style.css      # Custom styles
│   └── js/
│       └── app.js         # Application JavaScript
├── data/                  # SQLite database storage
│   └── app.db             # SQLite database file (auto-created)
├── composer.json          # PHP dependencies
└── README.md              # This file
```

## Database Schema

The application automatically creates the following tables:

### `todos`
- `id` (INTEGER PRIMARY KEY)
- `title` (TEXT NOT NULL)
- `description` (TEXT)
- `completed` (INTEGER DEFAULT 0)
- `created_at` (DATETIME DEFAULT CURRENT_TIMESTAMP)

### `transactions`
- `id` (INTEGER PRIMARY KEY)
- `type` (TEXT: 'income' or 'expense')
- `amount` (DECIMAL(10,2))
- `description` (TEXT NOT NULL)
- `category` (TEXT NOT NULL)
- `date` (DATE NOT NULL)
- `created_at` (DATETIME DEFAULT CURRENT_TIMESTAMP)

### `categories`
- `id` (INTEGER PRIMARY KEY)
- `name` (TEXT NOT NULL UNIQUE)
- `type` (TEXT: 'income' or 'expense')
- `color` (TEXT DEFAULT '#007bff')

## Usage

### Dashboard
- View your financial overview with income, expenses, and balance
- See todo statistics and recent transactions
- Quick access to all features

### Adding Transactions
1. Go to the "Finance" tab
2. Select transaction type (Income/Expense)
3. Enter amount, description, category, and date
4. Click "Add Transaction"

### Managing Todos
1. Go to the "Todos" tab
2. Enter task title and optional description
3. Click "Add Todo"
4. Check off completed tasks or delete unwanted ones

## API Endpoints

The application uses AJAX for dynamic updates:

- `POST /index.php` with `action=add_todo`
- `POST /index.php` with `action=toggle_todo`
- `POST /index.php` with `action=delete_todo`
- `POST /index.php` with `action=add_transaction`
- `POST /index.php` with `action=delete_transaction`

## Customization

### Adding Categories
Edit the database creation section in `config/database.php` to add more default categories:

```php
INSERT OR IGNORE INTO categories (name, type, color) VALUES 
('Your Category', 'expense', '#ff5722')
```

### Styling
Modify `assets/css/style.css` to customize the appearance. The design uses CSS custom properties for easy theming.

### Functionality
Extend the `Todo` and `Finance` classes in the `src/` directory to add new features.

## Security Features

- SQL injection prevention using prepared statements
- XSS protection with `htmlspecialchars()`
- CSRF protection ready for implementation
- Secure headers via .htaccess
- Input validation and sanitization

## Browser Support

- Chrome 70+
- Firefox 65+
- Safari 12+
- Edge 79+

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is open source and available under the MIT License.

## Support

For issues and questions, please create an issue in the repository or contact the development team.

---

**Fin1** - Making personal finance and task management simple and beautiful.