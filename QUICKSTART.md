# Inventory Management System - Quick Start Guide

## 🚀 Getting Started

### 1. Access the Application
Open your web browser and navigate to: `http://localhost/inventory%20ms/public/index.php`

### 2. Database Setup
If you haven't set up the database yet:
1. Visit: `http://localhost/inventory%20ms/database-setup.php`
2. Click "Start Setup" to automatically create the database and tables
3. Wait for the setup to complete

### 3. Login
Use the default administrator credentials:
- **Username**: `admin`
- **Email**: `admin@inventory.com`
- **Password**: `admin123`

⚠️ **Important**: Change the default password immediately after first login!

### 4. First Steps After Login

#### Add Categories (Optional but Recommended)
1. Go to **Products** → **Categories**
2. Add your product categories (e.g., Electronics, Clothing, Food)

#### Add Suppliers (Optional but Recommended)
1. Go to **Products** → **Suppliers**
2. Add your suppliers with contact information

#### Add Your First Product
1. Go to **Products** → **Add Product**
2. Fill in the product details:
   - Name: Product name
   - SKU: Unique product code
   - Category: Select from dropdown
   - Price: Selling price
   - Stock Quantity: Current stock level
   - Min Stock Level: Low stock alert threshold

#### Add Your First Customer
1. Go to **Customers** → **Add Customer**
2. Fill in customer details:
   - Full Name: Customer's name
   - Phone: Contact number (required)
   - Email: Customer's email (optional)
   - Address: Customer address (optional)

#### Record a Sale
1. Go to **Sales** → **New Sale**
2. Select customer and products
3. Complete the transaction

#### Track Customer Debt
1. Go to **Debts** → **Record Debt**
2. Select customer and enter debt details
3. Set due date and amount

## 📊 Dashboard Overview

The main dashboard shows:
- **Total Products**: Number of active products
- **Low Stock Alerts**: Products running low
- **Total Customers**: Active customer count
- **Outstanding Debts**: Total amount owed
- **Today's Sales**: Sales for current day
- **Monthly Sales**: Current month performance

## 🔧 System Features

### Product Management
- ✅ Add/Edit/Delete products
- ✅ Category and supplier management
- ✅ Stock level tracking
- ✅ Low stock alerts
- ✅ Barcode support
- ✅ Image upload capability
- ✅ Bulk import/export via CSV

### Customer Management
- ✅ Customer registration
- ✅ Contact information management
- ✅ Credit limit tracking
- ✅ Purchase history
- ✅ Bulk import/export via CSV

### Debt Management
- ✅ Record customer debts
- ✅ Payment tracking
- ✅ Overdue notifications
- ✅ Payment history
- ✅ Automatic balance updates

### Sales Management
- ✅ Point of sale interface
- ✅ Automatic inventory updates
- ✅ Multiple payment methods
- ✅ Sales history and reports

### Reports & Analytics
- ✅ Dashboard with key metrics
- ✅ Sales reports
- ✅ Inventory reports
- ✅ Debt reports
- ✅ Low stock reports
- ✅ Customer reports

### User Management (Admin Only)
- ✅ Add/Edit users
- ✅ Role-based permissions
- ✅ Activity logging
- ✅ User access control

## 🛡️ Security Features

- ✅ Secure password hashing
- ✅ CSRF protection
- ✅ Session management
- ✅ Input validation
- ✅ SQL injection prevention
- ✅ Role-based access control

## 📱 Responsive Design

The system is fully responsive and works on:
- Desktop computers
- Tablets
- Mobile phones

## 🔄 Data Import/Export

### Import Products
1. Go to **Products** → **Import**
2. Download the sample CSV template
3. Fill in your product data
4. Upload and process

### Export Data
1. Go to any listing page
2. Click **Export** button
3. Download CSV file

## ⚠️ Important Notes

### Default Data
The system comes with:
- Sample categories
- Sample suppliers
- Default settings
- Admin user account

### Backup Recommendations
- Regular database backups
- File system backups
- Test restore procedures

### Security Recommendations
- Change default passwords
- Use HTTPS in production
- Regular security updates
- Monitor access logs

## 📞 Need Help?

### Common Issues
1. **Login Problems**: Use admin/admin123
2. **Database Errors**: Run database-setup.php
3. **Permission Errors**: Check file permissions on storage/ folder
4. **Missing Features**: Ensure database setup completed successfully

### System Requirements
- PHP 7.4+
- MySQL 5.7+
- Web server (Apache/Nginx)
- Modern web browser

### File Permissions
Ensure these directories are writable:
- `storage/logs/`
- `storage/uploads/`

## 🎯 Next Steps

1. **Customize Settings**: Go to Settings (Admin only)
2. **Add Users**: Create staff accounts with appropriate roles
3. **Configure Categories**: Set up your product categories
4. **Import Data**: Use CSV import for bulk data entry
5. **Set Up Backups**: Implement regular backup procedures
6. **Train Users**: Train staff on system usage

## 🔧 Advanced Configuration

### Database Configuration
Edit `config/config.php` for database settings

### Application Settings
Configure timezone, currency, and other settings in the config file

### Upload Limits
Adjust file upload limits in config for larger imports

---

**Version**: 1.0.0  
**Setup Date**: <?= date('Y-m-d H:i:s') ?>

Your Inventory Management System is ready to use! 🎉