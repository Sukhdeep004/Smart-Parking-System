#Parking Management System

A complete, modern, and interactive web-based Car Parking Management System built with PHP and MySQL. This system provides administrators with powerful tools to efficiently manage parking slots, monitor vehicle entries/exits, track payments, and generate insightful analytics.

![PHP](https://img.shields.io/badge/PHP-8.0%2B-blue)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)
![License](https://img.shields.io/badge/License-MIT-green)

## 🌟 Features

### Core Functionality
- **Modern Landing Page**: Attractive intro page with animated elements
- **Secure Authentication**: Admin login/registration with bcrypt password hashing
- **Real-time Dashboard**: Live statistics, charts, and activity feed
- **Slot Management**: Add, update, delete, and monitor parking slots
- **Vehicle Entry/Exit**: Quick vehicle entry recording with auto slot assignment
- **Fee Calculation**: Automatic parking fee calculation based on duration
- **Reports & Analytics**: Detailed reports with filtering and export options
- **System Settings**: Configure rates, company info, and preferences

### Technical Features
- **Responsive Design**: Works perfectly on all devices (mobile, tablet, desktop)
- **Interactive Charts**: Revenue trends, slot occupancy, and usage analytics
- **Live Updates**: Real-time activity feed with AJAX
- **Data Export**: CSV export functionality for reports
- **Print Support**: Print-optimized reports
- **Animations**: Smooth AOS animations and CountUp effects
- **Security**: SQL injection prevention, XSS protection, password hashing

## 📸 Screenshots

- Landing Page with hero section and features
- Admin login with animated card
- Dashboard with stats cards and charts
- Slot management with color-coded grid
- Vehicle entry/exit forms
- Interactive reports with filters

## 🛠️ Technology Stack

| Layer | Technology |
|-------|-----------|
| **Frontend** | HTML5, CSS3, Bootstrap 5, JavaScript |
| **Backend** | Core PHP (8.0+) |
| **Database** | MySQL (5.7+) |
| **Authentication** | PHP Sessions + bcrypt |
| **Charts** | Chart.js |
| **Animations** | AOS.js, CountUp.js |
| **Server** | Apache (XAMPP/LAMP/WAMP) |

## 📋 Requirements

- **PHP**: 8.0 or higher
- **MySQL**: 5.7 or higher
- **Apache**: 2.4 or higher
- **Web Browser**: Chrome, Firefox, Safari, Edge (latest versions)

## 🚀 Installation Guide

### Step 1: Clone or Download
```bash
git clone https://github.com/yourusername/parking-system.git
cd parking-system
```

Or download and extract the ZIP file to your web server directory.

### Step 2: Database Setup
1. Open **phpMyAdmin** (http://localhost/phpmyadmin)
2. Create a new database named `parking_system`
3. Import the `database.sql` file:
   - Click on the `parking_system` database
   - Go to **Import** tab
   - Choose file → Select `database.sql`
   - Click **Go**

### Step 3: Configure Database Connection
1. Open `includes/db.php`
2. Update database credentials if needed:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'parking_system');
```

### Step 4: Deploy to Web Server
- **XAMPP**: Place files in `C:\xampp\htdocs\parking-system\`
- **WAMP**: Place files in `C:\wamp64\www\parking-system\`
- **LAMP**: Place files in `/var/www/html/parking-system/`

### Step 5: Access the System
1. Start Apache and MySQL servers
2. Open browser and navigate to:
   ```
   http://localhost/parking-system/
   ```

### Step 6: Login
Use the default admin credentials:
- **Username**: `admin`
- **Password**: `admin123`

**Important**: Change the default password immediately after first login!

## 📁 Project Structure

```
parking-system/
├── index.php              # Landing page
├── login.php              # Admin login
├── register.php           # Admin registration
├── dashboard.php          # Main dashboard
├── slots.php              # Slot management
├── vehicle_entry.php      # Vehicle entry form
├── vehicle_exit.php       # Vehicle exit & payment
├── reports.php            # Reports & analytics
├── settings.php           # System settings
├── logout.php             # Logout script
├── database.sql           # MySQL schema + sample data
├── README.md              # This file
│
├── includes/              # Reusable PHP files
│   ├── db.php            # Database configuration
│   ├── header.php        # Header template
│   └── footer.php        # Footer template
│
└── assets/               # Static resources
    ├── css/
    │   └── style.css     # Custom CSS
    ├── js/
    │   └── main.js       # Custom JavaScript
    └── img/              # Images (if any)
```

## 💡 Usage Guide

### Admin Registration
1. Click **Register** on login page
2. Fill in full name, username, email, and password
3. Submit to create account
4. Login with new credentials

### Managing Parking Slots
1. Go to **Slots** page
2. View all slots organized by floor
3. Add new slots with name, type, and floor
4. Delete available slots (occupied slots cannot be deleted)
5. Slots are color-coded: Green (Available), Red (Occupied)

### Vehicle Entry
1. Go to **Entry** page
2. Fill in owner name, vehicle number, type, and contact
3. System automatically assigns nearest available slot
4. Entry time is recorded automatically

### Vehicle Exit
1. Go to **Exit** page
2. View all currently parked vehicles
3. Click **Exit** button for vehicle
4. System calculates fee based on duration
5. Confirm exit to free slot and record transaction

### Viewing Reports
1. Go to **Reports** page
2. Use filters: date range, vehicle number, slot
3. View statistics: total transactions, revenue, avg duration
4. Export to CSV or print report
5. View interactive charts

### System Settings
1. Go to **Settings** page
2. Update parking rates for car/bike/truck
3. Change company information
4. Update admin password
5. View system statistics

## 🔒 Security Features

- **Password Hashing**: All passwords stored using bcrypt
- **SQL Injection Prevention**: Prepared statements with PDO
- **XSS Protection**: Input sanitization and output escaping
- **Session Management**: Secure PHP sessions
- **CSRF Protection**: Form token validation (recommended to add)
- **Input Validation**: Client-side and server-side validation

## 🎨 Customization

### Change Color Scheme
Edit `assets/css/style.css`:
```css
:root {
    --primary-color: #0d6efd;    /* Change primary color */
    --success-color: #28a745;    /* Change success color */
    --danger-color: #dc3545;     /* Change danger color */
}
```

### Update Parking Rates
1. Login as admin
2. Go to **Settings**
3. Update rates in the form
4. Click **Save Settings**

### Add More Floors/Slots
1. Go to **Slots** page
2. Use the **Add New Slot** form
3. Specify floor number (can create new floors)

## 🐛 Troubleshooting

### Database Connection Error
- Check MySQL is running
- Verify database credentials in `includes/db.php`
- Ensure `parking_system` database exists

### Page Not Found (404)
- Check file permissions (755 for folders, 644 for files)
- Ensure `.htaccess` is present (if using mod_rewrite)
- Verify web server document root

### Charts Not Displaying
- Check browser console for JavaScript errors
- Ensure CDN links are accessible
- Clear browser cache

### Login Not Working
- Verify database contains user records
- Check password is hashed correctly
- Clear browser cookies/sessions

## 📊 Database Schema

### Tables:
- **users**: Admin accounts
- **parking_slots**: Slot information
- **vehicles**: Vehicle entry/exit records
- **transactions**: Payment history
- **settings**: System configuration
- **activity_logs**: System activity tracking

### Sample Data Included:
- 2 admin users
- 28 parking slots (cars, bikes, trucks)
- 7 currently parked vehicles
- 8 historical transactions

## 🤝 Contributing

Contributions are welcome! Please follow these steps:
1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👤 Author

**Sukhdeep Singh**
- GitHub: https://github.com/Sukhdeep004
- Email: sukhdeepsingh0221@gmail.com

## 🙏 Acknowledgments

- Bootstrap team for the excellent CSS framework
- Chart.js for beautiful charts
- AOS for smooth animations
- FontAwesome for icons


## 🔮 Future Enhancements

- [ ] Email notifications for vehicle entry/exit
- [ ] SMS alerts for payment reminders
- [ ] QR code generation for vehicles
- [ ] Mobile app integration
- [ ] Advanced analytics with AI predictions
- [ ] Multi-location support
- [ ] Online payment gateway integration
- [ ] REST API for third-party integrations

---

**Made with ❤️ for efficient parking management**
