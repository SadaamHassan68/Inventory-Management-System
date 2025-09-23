# Inventory Management System

A comprehensive Product Management and Customer Debt Tracking System built with PHP and MySQL.

## Features

### Core Functionality
- **Product Management**: Add, edit, delete, and view products with categories, suppliers, and stock levels
- **Customer Management**: Register and manage customer information
- **Debt Management**: Track customer debts with payment history and status updates
- **User Authentication**: Role-based access control (Admin/Staff)
- **Sales Management**: Record sales transactions and manage inventory automatically
- **Reports & Analytics**: Comprehensive dashboard with charts and reports

### Advanced Features
- **Low Stock Alerts**: Automatic notifications for products running low
- **Overdue Debt Tracking**: Monitor and manage overdue payments
- **Search & Filter**: Quick search across products, customers, and debts
- **CSV Import/Export**: Bulk operations for products and customers
- **Responsive Design**: Mobile-friendly interface using Tailwind CSS
- **Activity Logging**: Track all user actions for audit purposes
- **Real-time Dashboard**: Live statistics and charts

## System Requirements

- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Web Server**: Apache or Nginx
- **Extensions**: PDO, PDO_MySQL

## Installation

### Method 1: Automatic Setup (Recommended)

1. **Download/Clone the project** to your web server directory:
   ```bash
   git clone [repository-url] inventory-ms
   cd inventory-ms
   ```

2. **Configure Database Settings**:
   - Open `config/config.php`
   - Update database credentials:
     ```php
     'database' => [
         'host' => 'localhost',
         'dbname' => 'inventory_management',
         'username' => 'your_username',
         'password' => 'your_password',
     ]
     ```

3. **Run the Setup Script**:
   - Via Web Browser: Navigate to `http://localhost/inventory%20ms/database-setup.php`
   - Via Command Line: `php database-setup.php`

4. **Access the Application**:
   - URL: `http://localhost/inventory%20ms/public/index.php`
   - Default Login: `admin` / `admin123`

### Method 2: Manual Setup

1. **Create Database**:
   ```sql
   CREATE DATABASE inventory_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. **Import Schema**:
   ```bash
   mysql -u username -p inventory_management < database/schema.sql
   ```

3. **Configure Application**:
   - Update `config/config.php` with your database settings
   - Set proper file permissions for `storage/` directories

4. **Set Directory Permissions**:
   ```bash
   chmod 755 storage/
   chmod 755 storage/logs/
   chmod 755 storage/uploads/
   ```

## Default User Accounts

| Username | Password | Role  | Description |
|----------|----------|-------|-------------|
| admin    | admin123 | Admin | Full system access |

**Important**: Change the default password immediately after first login!

## Configuration

### Database Configuration
Edit `config/config.php` to configure:
- Database connection settings
- Application URL and timezone
- File upload limits
- Security settings

### Environment Setup
For production environments:
1. Set `'debug' => false` in config
2. Configure proper database credentials
3. Set up SSL/HTTPS
4. Configure backup procedures

## User Roles & Permissions

### Administrator
- Manage products, categories, suppliers
- Manage customers and debts
- Manage users and system settings
- View all reports and analytics
- Access system logs

### Staff
- Add sales and update stock
- Record customer debts
- View products and customers
- Limited reporting access

## File Structure

```
inventory-ms/
├── app/
│   ├── controllers/     # Application controllers
│   ├── models/         # Data models
│   ├── views/          # View templates
│   ├── middleware/     # Custom middleware
│   └── utils/          # Utility classes
├── bootstrap/          # Application bootstrap
├── config/            # Configuration files
├── database/          # Database schema and migrations
├── public/            # Public assets (CSS, JS, images)
├── storage/           # File storage (logs, uploads)
└── README.md
```

## Database Schema

### Core Tables
- `users` - System users with roles
- `products` - Product catalog with stock levels
- `categories` - Product categories
- `suppliers` - Supplier information
- `customers` - Customer database
- `sales` - Sales transactions
- `sale_items` - Individual sale line items
- `debts` - Customer debt records
- `debt_payments` - Debt payment history
- `stock_movements` - Inventory movement tracking

### System Tables
- `settings` - Application settings
- `activity_logs` - User activity tracking

## API Endpoints

The system includes AJAX endpoints for real-time functionality:

- `/api/products/search` - Product search
- `/api/customers/search` - Customer search
- `/api/dashboard/stats` - Dashboard statistics

## Security Features

- **Password Hashing**: Secure bcrypt password hashing
- **CSRF Protection**: Form token validation
- **SQL Injection Prevention**: Prepared statements
- **Session Management**: Secure session handling
- **Role-Based Access**: Granular permission system
- **Input Validation**: Server-side validation
- **Activity Logging**: Complete audit trail

## Backup & Maintenance

### Database Backup
```bash
mysqldump -u username -p inventory_management > backup_$(date +%Y%m%d).sql
```

### Log Management
- Application logs: `storage/logs/`
- Regular cleanup recommended
- Monitor disk space usage

### Updates
1. Backup database and files
2. Test updates in staging environment
3. Apply updates during maintenance window
4. Verify functionality post-update

## Troubleshooting

### Common Issues

1. **Database Connection Error**:
   - Check credentials in `config/config.php`
   - Verify MySQL service is running
   - Confirm database exists

2. **Permission Denied**:
   - Check file permissions on `storage/` directories
   - Ensure web server can write to storage folders

3. **Login Issues**:
   - Use default credentials: admin/admin123
   - Check if user account is active
   - Verify password hash in database

4. **Missing Dependencies**:
   - Ensure PHP PDO extension is installed
   - Check PHP version compatibility

### Error Logs
- Application errors: `storage/logs/`
- Web server errors: Check Apache/Nginx logs
- Database errors: Check MySQL error logs

## Support & Documentation

### Getting Help
1. Check this README for common solutions
2. Review error logs for specific issues
3. Verify system requirements are met
4. Check database connectivity

### Development
- Built with custom PHP MVC framework
- Uses Tailwind CSS for styling
- Chart.js for data visualization
- Font Awesome for icons

## License

This project is open source. Please check the license file for details.

## Security Notice

- Change default passwords immediately
- Keep PHP and MySQL updated
- Use HTTPS in production
- Regular security audits recommended
- Monitor access logs

---

**Version**: 1.0.0  
**Last Updated**: September 2025

For technical support or feature requests, please refer to the project documentation or contact your system administrator.