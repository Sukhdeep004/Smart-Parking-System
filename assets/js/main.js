// Main JavaScript for Parking Management System

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    
    // Update current time every second
    updateTime();
    setInterval(updateTime, 1000);
});

// Update current time display
function updateTime() {
    var timeElements = document.querySelectorAll('.current-time');
    if (timeElements.length > 0) {
        var now = new Date();
        var timeString = now.toLocaleTimeString('en-IN', { 
            hour: '2-digit', 
            minute: '2-digit',
            second: '2-digit'
        });
        timeElements.forEach(function(el) {
            el.textContent = timeString;
        });
    }
}

// Format currency
function formatCurrency(amount) {
    return '₹' + parseFloat(amount).toFixed(2);
}

// Calculate duration in hours
function calculateDuration(entryTime, exitTime) {
    var entry = new Date(entryTime);
    var exit = exitTime ? new Date(exitTime) : new Date();
    var diff = exit - entry;
    var hours = Math.ceil(diff / (1000 * 60 * 60));
    return hours;
}

// Confirm delete action
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this item?');
}

// Show loading spinner
function showLoading() {
    var loadingHtml = '<div class="spinner-wrapper"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    document.body.insertAdjacentHTML('beforeend', loadingHtml);
}

// Hide loading spinner
function hideLoading() {
    var spinner = document.querySelector('.spinner-wrapper');
    if (spinner) {
        spinner.remove();
    }
}

// Toast notification
function showToast(message, type = 'success') {
    var toastHtml = `
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', toastHtml);
    var toastElement = document.querySelector('.toast:last-child');
    var toast = new bootstrap.Toast(toastElement);
    toast.show();
    
    // Remove toast after hidden
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.closest('.toast-container').remove();
    });
}

// Export table to CSV
function exportTableToCSV(tableId, filename) {
    var csv = [];
    var rows = document.querySelectorAll('#' + tableId + ' tr');
    
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll('td, th');
        
        for (var j = 0; j < cols.length; j++) {
            row.push(cols[j].innerText);
        }
        
        csv.push(row.join(','));
    }
    
    // Download CSV file
    downloadCSV(csv.join('\n'), filename);
}

// Download CSV
function downloadCSV(csv, filename) {
    var csvFile;
    var downloadLink;
    
    csvFile = new Blob([csv], {type: 'text/csv'});
    downloadLink = document.createElement('a');
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

// Print page
function printPage() {
    window.print();
}

// Validate form
function validateForm(formId) {
    var form = document.getElementById(formId);
    if (form.checkValidity() === false) {
        event.preventDefault();
        event.stopPropagation();
    }
    form.classList.add('was-validated');
}

// Search table
function searchTable(inputId, tableId) {
    var input = document.getElementById(inputId);
    var filter = input.value.toUpperCase();
    var table = document.getElementById(tableId);
    var tr = table.getElementsByTagName('tr');
    
    for (var i = 1; i < tr.length; i++) {
        var td = tr[i].getElementsByTagName('td');
        var found = false;
        
        for (var j = 0; j < td.length; j++) {
            if (td[j]) {
                if (td[j].innerHTML.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        if (found) {
            tr[i].style.display = '';
        } else {
            tr[i].style.display = 'none';
        }
    }
}

// Dark mode toggle
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    var isDark = document.body.classList.contains('dark-mode');
    localStorage.setItem('darkMode', isDark);
}

// Check dark mode preference on load
if (localStorage.getItem('darkMode') === 'true') {
    document.body.classList.add('dark-mode');
}

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        var target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});

// Auto-refresh data (for dashboard)
var autoRefreshInterval;
function startAutoRefresh(callback, interval = 30000) {
    autoRefreshInterval = setInterval(callback, interval);
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
}

// AJAX helper function
function ajaxRequest(url, method = 'GET', data = null, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open(method, url, true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    
    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            callback(null, JSON.parse(xhr.responseText));
        } else {
            callback(new Error('Request failed'), null);
        }
    };
    
    xhr.onerror = function() {
        callback(new Error('Network error'), null);
    };
    
    if (data) {
        xhr.send(JSON.stringify(data));
    } else {
        xhr.send();
    }
}

// Number animation
function animateValue(id, start, end, duration) {
    var obj = document.getElementById(id);
    if (!obj) return;
    
    var range = end - start;
    var increment = end > start ? 1 : -1;
    var stepTime = Math.abs(Math.floor(duration / range));
    var current = start;
    
    var timer = setInterval(function() {
        current += increment;
        obj.textContent = current;
        if (current == end) {
            clearInterval(timer);
        }
    }, stepTime);
}