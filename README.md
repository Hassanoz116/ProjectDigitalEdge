# 🚀 ProjectDigitalEdge

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg?style=flat&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-blue.svg?style=flat&logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-orange.svg?style=flat&logo=mysql)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg?style=flat&logo=bootstrap)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/License-Proprietary-yellow.svg?style=flat)]()
[![Status](https://img.shields.io/badge/Status-Active-green.svg?style=flat)]()

A comprehensive Laravel-based digital platform featuring a modern admin dashboard and robust RESTful API. This enterprise-grade application provides complete user and product management solutions with real-time notifications, multilingual support, and advanced security features.

## 🌟 Overview

ProjectDigitalEdge is a full-featured web application built with Laravel 11 that combines powerful backend APIs with an intuitive admin interface. The platform is designed for scalability, security, and ease of use, making it perfect for businesses looking to manage users, products, and operations efficiently.

## 📋 Table of Contents

- [🌟 Overview](#-overview)
- [✨ Features](#-features)
- [💻 Tech Stack](#-tech-stack)
- [⚙️ Installation](#-installation)
- [🔑 Default Credentials](#-default-credentials)
- [📖 API Documentation](#api-documentation)
- [🏗️ Project Structure](#-project-structure)
- [📡 API Endpoints Overview](#api-endpoints-overview)
- [🗄️ Database Schema](#-database-schema)
- [📱 Key Features Demo](#-key-features-demo)
- [🧪 Testing](#-testing)
- [🚨 Troubleshooting](#-troubleshooting)
- [📈 Performance Features](#-performance-features)
- [🔒 Security Notes](#-security-notes)
- [🚀 Deployment](#-deployment)
- [📞 Support](#-support)

## ✨ Features

### 🔐 Authentication & Authorization
- **Laravel Sanctum** for API authentication
- **Spatie Permission** for role-based access control (Admin, User)
- Email/Phone verification with 4-digit codes
- Login attempt tracking with automatic blocking (3 attempts → 15-30 min block)
- Password management (Change, Forgot, Reset)
- Strong password validation

### 👥 User Management
- Complete CRUD operations
- User filtering (country, city, verification status, role)
- Admin can change user passwords
- Send custom emails to users
- Export users to Excel/CSV
- View products assigned to users

### 📦 Product Management
- Multilingual support (English & Arabic)
- Product CRUD with image upload
- Primary image + multiple additional images
- Assign/Unassign products to users
- Product filtering and search
- Export products to Excel/CSV
- Automatic slug generation

### 📊 Admin Dashboard
- Statistics overview
- Product charts (last 7 days)
- User management interface
- Product gallery
- Activity logs viewer
- Yajra DataTables integration

### 🔔 Real-Time Notifications
- **AJAX-based real-time notifications** (every 3 seconds polling)
- **Browser notifications** for immediate alerts
- **Database-stored notifications** with complete history
- **Mark as read/unread functionality** with instant UI updates
- **Unread count tracking** with animated badge updates
- **Notification dropdown** with beautiful UI and icons
- **Automatic notification categorization** by type (success, warning, error)
- **Multilingual notification support** (Arabic/English)

### 🖼️ Gallery Management
- **Image upload and management** with drag-and-drop interface
- **Multiple image formats** support (JPEG, PNG, GIF, WebP)
- **Image optimization** and compression
- **Gallery API endpoints** for programmatic access
- **Secure file storage** with proper permissions

### 📝 Activity Logging
- **Comprehensive activity tracking** for all user actions
- **Detailed logs** for user actions (login, logout, password changes)
- **Admin action tracking** for user/product CRUD operations
- **IP address and user agent tracking** for security monitoring
- **Filterable and searchable logs** with advanced search options
- **Real-time log updates** in admin dashboard

### 🌍 Internationalization
- English and Arabic support
- RTL layout for Arabic
- Multilingual product content

### 🔒 Security Features
- Rate limiting (20 requests/min per user)
- Bearer Token authentication
- Soft deletes for users and products
- CSRF protection
- Input validation and sanitization

## 💻 Tech Stack

### Backend
- **Framework:** Laravel 11.x (PHP 8.4)
- **Authentication:** Laravel Sanctum + JWT Tokens
- **Permissions:** Spatie Laravel Permission
- **Database:** MySQL 8.0+
- **File Storage:** Laravel Storage with Symbolic Links

### Frontend
- **Templating:** Blade Templates with Component Architecture
- **CSS Framework:** Bootstrap 5.3
- **JavaScript:** Modern ES6+ with jQuery
- **Icons:** FontAwesome 6.0
- **DataTables:** Yajra Laravel DataTables with AJAX
- **Charts:** Chart.js for analytics
- **Animations:** Animate.css for UI effects

### Additional Packages
- **Export:** Maatwebsite Excel for CSV/XLSX export
- **Notifications:** Custom AJAX-based real-time system
- **Image Handling:** Laravel's built-in file upload system
- **Localization:** Laravel's built-in multi-language support
- **Seeding:** Comprehensive database seeders for countries/cities

### Development Tools
- **API Testing:** Postman Collection included
- **Code Quality:** PSR-12 coding standards
- **Security:** CSRF protection, rate limiting, input validation

## Installation

### Prerequisites
- PHP >= 8.2
- Composer
- MySQL
- Node.js & NPM

### Steps

1. **Clone the repository**
```bash
git clone <repository-url>
cd ProjectDigitalEdge
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install NPM dependencies**
```bash
npm install
npm run build
```

4. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure database**
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=project_digital_edge
DB_USERNAME=root
DB_PASSWORD=
```

6. **Run migrations and seeders**
```bash
php artisan migrate
php artisan db:seed
```

7. **Create storage symlink**
```bash
php artisan storage:link
```

8. **Start the development server**
```bash
php artisan serve
```

Visit: `http://localhost:8000`

## 🔑 Default Credentials

### 👑 Admin Account
- **Email:** admin@digitaledge.com
- **Password:** 123

### 👤 Test User Account
- **Email:** user@digitaledge.com
- **Password:** 123

### Quick Start

1. **Login to get token**
```bash
POST /api/auth/login
{
  "email_or_phone": "admin@digitaledge.com",
  "password": "123"
}
```

2. **Use token in headers**
```bash
Authorization: Bearer {your_token}
```

3. **Import Postman Collection**
Import `Postman_Collection.json` into Postman for ready-to-use API requests.

## Project Structure

```
├── 📁 app/
│   ├── 📁 Exports/                    # Excel export classes
│   │   ├── UsersExport.php            # Users data export
│   │   └── ProductsExport.php         # Products data export
│   ├── 📁 Helpers/                    # Helper utilities
│   │   └── ActivityLogger.php         # Activity logging helper
│   ├── 📁 Http/
│   │   ├── 📁 Controllers/
│   │   │   ├── 📁 Admin/              # Admin dashboard controllers
│   │   │   │   ├── AdminController.php
│   │   │   │   ├── UserController.php
│   │   │   │   ├── ProductController.php
│   │   │   │   ├── ActivityLogController.php
│   │   │   │   └── NotificationController.php
│   │   │   └── 📁 Api/                # API controllers
│   │   │       ├── AuthController.php
│   │   │       ├── UserController.php
│   │   │       ├── ProductController.php
│   │   │       ├── NotificationController.php
│   │   │       ├── ActivityLogController.php
│   │   │       ├── CountryController.php
│   │   │       └── GalleryController.php
│   │   ├── 📁 Middleware/             # Custom middleware
│   │   └── 📁 Requests/               # Form request validation
│   ├── 📁 Models/                     # Eloquent models
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Country.php
│   │   ├── City.php
│   │   ├── ActivityLog.php
│   │   └── Gallery.php
│   └── 📁 Notifications/              # Notification classes
│       └── CustomNotification.php
├── 📁 bootstrap/                      # Bootstrap files
├── 📁 config/                         # Configuration files
│   ├── app.php
│   ├── database.php
│   ├── auth.php
│   ├── sanctum.php
│   ├── broadcasting.php
│   └── services.php
├── 📁 database/
│   ├── 📁 factories/                  # Model factories
│   ├── 📁 migrations/                 # Database migrations
│   │   ├── create_users_table.php
│   │   ├── create_products_table.php
│   │   ├── create_countries_table.php
│   │   ├── create_cities_table.php
│   │   ├── create_activity_logs_table.php
│   │   └── create_notifications_table.php
│   └── 📁 seeders/                    # Database seeders
│       ├── DatabaseSeeder.php
│       ├── RoleSeeder.php
│       ├── UserSeeder.php
│       ├── CountrySeeder.php
│       └── CitySeeder.php
├── 📁 public/                         # Public assets
│   ├── 📁 css/                       # Compiled CSS
│   ├── 📁 js/                        # Compiled JavaScript
│   ├── 📁 images/                    # Static images
│   └── index.php                     # Entry point
├── 📁 resources/
│   ├── 📁 css/                       # Source CSS files
│   ├── 📁 js/                        # Source JavaScript files
│   ├── 📁 lang/                      # Localization files
│   │   ├── 📁 ar/                    # Arabic translations
│   │   └── 📁 en/                    # English translations
│   └── 📁 views/                     # Blade templates
│       ├── 📁 admin/                 # Admin dashboard views
│       │   ├── 📁 layouts/           # Admin layouts
│       │   ├── 📁 users/             # User management views
│       │   ├── 📁 products/          # Product management views
│       │   ├── 📁 activity-logs/     # Activity logs views
│       │   └── dashboard.blade.php   # Admin dashboard
│       ├── 📁 auth/                  # Authentication views
│       │   ├── login.blade.php
│       │   ├── register.blade.php
│       │   └── verify.blade.php
│       ├── 📁 emails/                # Email templates
│       │   └── verification-code.blade.php
│       └── 📁 layouts/               # Main layouts
│           ├── app.blade.php
│           └── guest.blade.php
├── 📁 routes/
│   ├── api.php                       # API routes (Sanctum protected)
│   ├── web.php                       # Web routes (Session based)
│   ├── console.php                   # Artisan console routes
│   └── channels.php                  # Broadcasting channels
├── 📁 storage/
│   ├── 📁 app/
│   │   ├── 📁 public/               # Public storage (symlinked)
│   │   │   ├── 📁 products/         # Product images
│   │   │   └── 📁 gallery/          # Gallery images
│   │   └── 📁 private/              # Private storage
│   ├── 📁 framework/                # Framework storage
│   ├── 📁 logs/                     # Application logs
│   └── 📁 cache/                    # Cache files
├── 📁 tests/                        # Test files
├── 📁 vendor/                       # Composer dependencies
├── 📄 .env                          # Environment configuration
├── 📄 .env.example                  # Environment example
├── 📄 .gitignore                    # Git ignore rules
├── 📄 artisan                       # Artisan CLI
├── 📄 composer.json                 # Composer configuration
├── 📄 composer.lock                 # Composer lock file
├── 📄 package.json                  # NPM configuration
├── 📄 package-lock.json             # NPM lock file
├── 📄 vite.config.js                # Vite configuration
├── 📄 README.md                     # Project documentation
├── 📄 Postman_Collection.json       # API testing collection
└── 📄 phpunit.xml                   # PHPUnit configuration
```

```

## API Endpoints Overview

### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/verify` - Verify account
- `POST /api/auth/login` - Login
- `POST /api/logout` - Logout
- `GET /api/user-info` - Get user info
- `POST /api/auth/forgot-password` - Request password reset
- `POST /api/auth/reset-password` - Reset password
- `POST /api/change-password` - Change password

### Users (Admin Only)
- `GET /api/users` - List all users
- `GET /api/users/{id}` - Get user details
- `PUT /api/users/{id}` - Update user
- `DELETE /api/users/{id}` - Delete user
- `GET /api/users/{id}/products` - Get user products
- `GET /api/export-users` - Export users

### Products
- `GET /api/products` - List all products
- `GET /api/products/{id}` - Get product details
- `POST /api/products` - Create product (Admin)
- `PUT /api/products/{id}` - Update product (Admin)
- `DELETE /api/products/{id}` - Delete product (Admin)
- `POST /api/assign-product` - Assign to user (Admin)
- `POST /api/unassign-product` - Unassign from user (Admin)
- `GET /api/user-products` - Get current user products
- `GET /api/export-products` - Export products (Admin)

### Activity Logs (Admin Only)
- `GET /api/activity-logs` - Get activity logs

### Notifications
- `GET /api/notifications` - Get notifications
- `POST /api/notifications/{id}/mark-read` - Mark as read
- `POST /api/notifications/mark-all-read` - Mark all as read
- `GET /api/notifications/unread-count` - Get unread count

### Countries & Cities
- `GET /api/countries` - List all countries
- `GET /api/countries/{id}/cities` - Get cities by country

### Gallery Management
- `GET /api/gallery` - Get gallery images  
- `POST /api/gallery` - Upload new image
- `DELETE /api/gallery/{id}` - Delete image

### Real-Time Features (Web Routes)
- `GET /test-ajax-notification` - Test real-time notifications
- `GET /admin/notifications/api` - Admin notifications endpoint

## Database Schema

### Users Table
- id, name, email, phone, password
- country_id, city_id
- email_verified_at, is_active
- verification_code, verification_code_expires_at
- login_attempts, blocked_until
- timestamps, soft deletes

### Products Table
- id, title_en, title_ar
- description_en, description_ar
- price, slug
- primary_image, other_images (JSON)
- user_id (assigned user)
- timestamps, soft deletes

### Activity Logs Table
- id, user_id, action
- model_type, model_id
- description, properties (JSON)
- ip_address, user_agent
- timestamps

### Countries & Cities Tables
- Standard country/city relationship
- Seeded with UAE data

## 📱 Key Features Demo

### Real-Time Notifications
The application features a sophisticated real-time notification system:
- Notifications appear automatically without page refresh
- Visual animations for new notifications
- Browser notifications for immediate alerts
- Categorized icons and colors based on notification type

### Admin Dashboard
- Clean, responsive design with Bootstrap 5
- Interactive charts showing recent activity
- DataTables with advanced filtering and search
- Multilingual interface (Arabic RTL support)

### API Documentation
- Complete Postman collection with 30+ endpoints
- Automated token management
- Pre-configured testing scenarios

## 🧪 Testing

### Using Postman Collection
1. Import `Postman_Collection.json`
2. Set `base_url` variable to `http://localhost:8000/api`
3. Login with admin credentials to get token (automatically saved)
4. Test all endpoints with pre-configured requests
5. Switch between admin and user accounts seamlessly

### Real-Time Features Testing
1. Login to admin dashboard
2. Visit `/test-ajax-notification` in another tab
3. Watch notifications appear in real-time
4. Test browser notifications functionality

### Manual Testing
```bash
# Test registration
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@example.com","password":"Password@123","password_confirmation":"Password@123","country_id":1,"city_id":1}'

# Test login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email_or_phone":"admin@digitaledge.com","password":"123"}'
```

## Coding Conventions

- **Functions & Variables:** camelCase
- **URL Paths:** kebab-case (`/user-products`, `/change-password`)
- **Route Names:** snake_case (`get_user_info`, `assign_product`)
- **PSR-12** coding standards
- Clean, readable code with proper comments

## 🚨 Troubleshooting

### Common Issues

**Database Connection Error**
```bash
php artisan migrate:fresh --seed
php artisan config:clear
```

**Storage Link Issues**
```bash
php artisan storage:link
chmod -R 755 storage
```

**Real-Time Notifications Not Working**
- Check browser console for JavaScript errors
- Ensure CSRF token is properly set
- Verify admin/user authentication
- Test with `/test-ajax-notification` endpoint

**API Authentication Issues**
- Verify token is included in Authorization header
- Check token expiration
- Ensure correct user role permissions










