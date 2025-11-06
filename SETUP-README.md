# GST Invoice & Inventory SaaS - WordPress Theme + Plugin

A complete WordPress solution for GST-compliant invoice generation and inventory management.

## 📦 Package Contents

- **surajx-gii-theme.zip** - WordPress theme for frontend and dashboard
- **gst-invoice-inventory-saas.zip** - Backend plugin with REST API and Google OAuth

## 🚀 Installation

### Step 1: Install the Plugin

1. Log in to your WordPress admin dashboard
2. Navigate to **Plugins > Add New > Upload Plugin**
3. Choose `gst-invoice-inventory-saas.zip`
4. Click **Install Now**
5. After installation, click **Activate Plugin**

### Step 2: Install the Theme

1. Navigate to **Appearance > Themes > Add New > Upload Theme**
2. Choose `surajx-gii-theme.zip`
3. Click **Install Now**
4. After installation, click **Activate**

### Step 3: Configure Plugin Settings

1. Go to **GST Invoice > Settings** in the admin menu
2. Configure the following:
   - **Google Client ID** (optional, for Google Sign-In)
   - **Google Client Secret** (optional, for Google Sign-In)
   - **Default GST Rate** (default: 18%)
   - **Invoice Prefix** (default: INV)
3. Click **Save Settings**

### Step 4: Create Required Pages

Create the following pages in WordPress (Pages > Add New):

#### 1. Dashboard Page
- **Title:** Dashboard
- **Slug:** dashboard
- **Template:** Account Dashboard
- **Content:** `[gii_customer_dashboard]`

#### 2. Login Page
- **Title:** Login
- **Slug:** login
- **Template:** Login Page

#### 3. Register Page
- **Title:** Register
- **Slug:** register
- **Template:** Register Page

#### 4. Forgot Password Page
- **Title:** Forgot Password
- **Slug:** forgot-password
- **Template:** Forgot Password

#### 5. Pricing Page
- **Title:** Pricing
- **Slug:** pricing
- **Template:** Pricing Page

#### 6. Invoice Builder Page
- **Title:** Invoice Builder
- **Slug:** invoice-builder
- **Content:** `[gii_invoice_builder]`

### Step 5: Configure Homepage

1. Go to **Settings > Reading**
2. Select **A static page** for "Your homepage displays"
3. Choose **Home** or create a new page as Front page
4. Save changes

### Step 6: Setup Menu (Optional)

1. Go to **Appearance > Menus**
2. Create a new menu called "Primary Menu"
3. Add pages: Home, Pricing, Login, Register, Dashboard
4. Assign to **Primary Menu** location
5. Save menu

## 🔧 Google OAuth Setup (Optional)

If you want to enable Google Sign-In:

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing
3. Enable **Google+ API**
4. Go to **Credentials > Create Credentials > OAuth 2.0 Client ID**
5. Configure OAuth consent screen
6. Add authorized redirect URI:
   ```
   https://yourdomain.com/wp-json/gii-saas/v1/auth/google/callback
   ```
7. Copy Client ID and Client Secret
8. Add them to **GST Invoice > Settings** in WordPress admin

## 📚 REST API Endpoints

Base URL: `https://yourdomain.com/wp-json/gii-saas/v1/`

### Products
- `GET /products` - List all products
- `POST /products` - Create new product
- `GET /products/{id}` - Get single product
- `PUT /products/{id}` - Update product
- `DELETE /products/{id}` - Delete product

### Invoices
- `GET /invoices` - List all invoices
- `POST /invoices` - Create new invoice
- `GET /invoices/{id}` - Get single invoice
- `PUT /invoices/{id}` - Update invoice
- `DELETE /invoices/{id}` - Delete invoice

### Customers
- `GET /customers` - List all customers
- `POST /customers` - Create new customer

### Account Settings
- `GET /account/settings` - Get account settings
- `POST /account/settings` - Update account settings

### Authentication
All endpoints require authentication via WordPress cookies or nonce.

## 🌐 Multi-Language Support

The theme and plugin are translation-ready:

### Theme Text Domain
- `surajx-gii-theme`
- Translation files location: `/wp-content/themes/surajx-gii-theme/languages/`

### Plugin Text Domain
- `gii-saas`
- Translation files location: `/wp-content/plugins/gst-invoice-inventory-saas/languages/`

### Supported Languages
- English (default)
- Hindi (ready for translation)

To add translations:
1. Install **Loco Translate** plugin
2. Navigate to **Loco Translate > Themes** or **Plugins**
3. Select the theme/plugin
4. Add new language and translate strings

## 🎨 Theme Features

### Templates
- **front-page.php** - Homepage with hero section and features
- **page-pricing.php** - Pricing plans page
- **page-account.php** - Customer dashboard
- **page-login.php** - Login form with Google Sign-In
- **page-register.php** - Registration form
- **page-forgot-password.php** - Password reset

### Shortcodes
- `[gii_customer_dashboard]` - Display customer dashboard with tabs
- `[gii_invoice_builder]` - Invoice creation form
- `[gii_google_signin]` - Google Sign-In button

### Dashboard Tabs
1. **Products** - Manage inventory
2. **Invoices** - View and create invoices
3. **Account** - Company settings (GSTIN, address)

## 🔌 Plugin Features

### Database Tables
- `wp_gii_products` - Products inventory
- `wp_gii_invoices` - Invoice records
- `wp_gii_invoice_items` - Invoice line items
- `wp_gii_customers` - Customer database
- `wp_gii_user_settings` - User preferences

### Security Features
- Nonce verification on all forms
- Data sanitization and escaping
- User ownership verification
- SQL injection protection
- XSS protection

### OOP Architecture
- Namespace: `GII_SaaS`
- PSR-4 autoloading structure
- Singleton pattern for main classes

## 📊 Admin Features

Navigate to **GST Invoice** in WordPress admin to see:
- Total users, invoices, products, customers
- Total revenue statistics
- Quick access to settings
- REST API documentation

## 🛠 Troubleshooting

### Issue: Google Sign-In not working
- Verify Client ID and Secret in settings
- Check redirect URI matches exactly
- Ensure Google+ API is enabled

### Issue: 404 on pages
- Go to **Settings > Permalinks**
- Click **Save Changes** (flush rewrite rules)

### Issue: REST API not accessible
- Check permalink structure (must be pretty permalinks)
- Verify user is logged in for protected endpoints
- Check for conflicting plugins

### Issue: Theme not loading correctly
- Clear browser cache
- Check if jQuery is loaded
- Verify `functions.php` loaded without errors

## 📝 Database Schema

### Products Table
```sql
- id (bigint)
- user_id (bigint)
- name (varchar)
- sku (varchar)
- description (text)
- price (decimal)
- cost (decimal)
- stock (int)
- hsn_code (varchar)
- gst_rate (decimal)
- unit (varchar)
- created_at (datetime)
- updated_at (datetime)
```

### Invoices Table
```sql
- id (bigint)
- user_id (bigint)
- invoice_number (varchar)
- customer_id (bigint)
- customer_name (varchar)
- customer_gstin (varchar)
- customer_address (text)
- company_name (varchar)
- company_gstin (varchar)
- company_address (text)
- invoice_date (date)
- due_date (date)
- subtotal (decimal)
- gst_amount (decimal)
- total (decimal)
- status (varchar)
- notes (text)
- created_at (datetime)
- updated_at (datetime)
```

## 🔐 Security Best Practices

1. Keep WordPress, theme, and plugin updated
2. Use strong passwords
3. Enable SSL certificate (HTTPS)
4. Regular database backups
5. Limit login attempts
6. Use 2FA for admin accounts
7. Keep Google OAuth credentials secure

## 📞 Support

For issues or questions:
1. Check this README
2. Review WordPress debug.log
3. Check browser console for JavaScript errors
4. Verify database tables were created

## 📄 License

- Theme: GPL v2 or later
- Plugin: GPL v2 or later

## ✅ Requirements

- WordPress 6.0+
- PHP 8.0+
- MySQL 5.7+ or MariaDB 10.2+
- Pretty permalinks enabled
- HTTPS recommended for Google OAuth

## 🎯 Credits

Developed by Surajx for GST Invoice & Inventory SaaS platform.

---

**Version:** 1.0.0
**Last Updated:** November 2024
