// Search Functions for Fin1 Application

// Initialize search functionality
function initializeSearch() {
    // Transaction search
    const transactionSearch = document.getElementById('transactionSearch');
    if (transactionSearch) {
        transactionSearch.addEventListener('input', function(e) {
            filterTransactions(e.target.value);
        });
    }
    
    // Todo search
    const todoSearch = document.getElementById('todoSearch');
    if (todoSearch) {
        todoSearch.addEventListener('input', function(e) {
            filterTodos(e.target.value);
        });
    }
}

function filterTransactions(searchTerm) {
    const tableRows = document.querySelectorAll('#transactionsList tr');
    
    tableRows.forEach(row => {
        const description = row.cells[1]?.textContent.toLowerCase() || '';
        const category = row.cells[2]?.textContent.toLowerCase() || '';
        const searchText = searchTerm.toLowerCase();
        
        if (description.includes(searchText) || category.includes(searchText) || searchTerm === '') {
            row.style.display = '';
            row.style.opacity = '1';
            row.style.transform = 'translateX(0)';
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show "no results" message if no transactions are visible
    const visibleRows = document.querySelectorAll('#transactionsList tr[style*="display: table-row"], #transactionsList tr:not([style*="display: none"])');
    const tbody = document.getElementById('transactionsList');
    
    // Remove existing no-results row
    const existingNoResults = tbody.querySelector('.no-results-row');
    if (existingNoResults) {
        existingNoResults.remove();
    }
    
    if (visibleRows.length === 0 && searchTerm.trim() !== '') {
        const noResultsRow = document.createElement('tr');
        noResultsRow.className = 'no-results-row';
        noResultsRow.innerHTML = `
            <td colspan="5" class="text-center text-muted py-4">
                <i class="fas fa-search"></i><br>
                No transactions found matching "${searchTerm}"
            </td>
        `;
        tbody.appendChild(noResultsRow);
    }
}

function filterTodos(searchTerm) {
    const todoItems = document.querySelectorAll('.todo-item');
    let visibleCount = 0;
    
    todoItems.forEach(item => {
        const title = item.querySelector('h6')?.textContent.toLowerCase() || '';
        const description = item.querySelector('p')?.textContent.toLowerCase() || '';
        const searchText = searchTerm.toLowerCase();
        
        if (title.includes(searchText) || description.includes(searchText) || searchTerm === '') {
            item.style.display = '';
            item.style.opacity = '1';
            item.style.transform = 'translateX(0)';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });
    
    // Show "no results" message if no todos are visible
    const todosList = document.getElementById('todosList');
    
    // Remove existing no-results message
    const existingNoResults = todosList.querySelector('.no-results-message');
    if (existingNoResults) {
        existingNoResults.remove();
    }
    
    if (visibleCount === 0 && searchTerm.trim() !== '') {
        const noResultsDiv = document.createElement('div');
        noResultsDiv.className = 'no-results-message text-center text-muted py-4';
        noResultsDiv.innerHTML = `
            <i class="fas fa-search fa-2x mb-2"></i><br>
            <h6>No todos found</h6>
            <p class="mb-0">No todos match "${searchTerm}"</p>
        `;
        todosList.appendChild(noResultsDiv);
    }
}

// Initialize search when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeSearch();
});