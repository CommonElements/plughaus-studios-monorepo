# PlugHaus Property Management

**A comprehensive, WordPress-native property management solution for small to medium property managers.**

Transform your WordPress site into a powerful property management platform with tenant portals, lease tracking, maintenance management, and more.

## 🚀 Features

### 🏢 **Property Management**
- **Multi-Property Support**: Manage unlimited properties with detailed information
- **Property Types**: Apartments, houses, condos, commercial, and more
- **Address Validation**: Ensure accurate property locations
- **Unit Management**: Track individual units within properties
- **Occupancy Tracking**: Real-time availability and occupancy rates

### 👥 **Tenant Management**
- **Tenant Profiles**: Complete contact information and emergency contacts
- **Tenant Portal**: Self-service portal for lease information and requests
- **Move-in/Move-out Tracking**: Complete tenant lifecycle management
- **Email Validation**: Prevent duplicate tenant records

### 📋 **Lease Management**
- **Comprehensive Leases**: Start/end dates, rent amounts, security deposits
- **Lease Validation**: Prevent overlapping leases and date conflicts
- **Automatic Calculations**: Lease terms, expiration tracking
- **Status Management**: Active, pending, expired, terminated leases

### 🔧 **Maintenance System**
- **Request Management**: Priority-based maintenance request system
- **Status Tracking**: Open, in progress, completed, closed
- **Property/Unit Assignment**: Link requests to specific locations
- **Tenant Submission**: Allow tenants to submit maintenance requests

### 💰 **Payment Tracking** (Logging Only)
- **Payment History**: Track rent payments and other charges
- **Payment Methods**: Log different payment types
- **Due Date Tracking**: Monitor payment schedules
- **Notes System**: Add payment-related notes

### 📊 **Reporting & Analytics**
- **Occupancy Reports**: Property and unit occupancy rates
- **Financial Overview**: Income tracking and summaries
- **Lease Expiration**: Upcoming renewals and expirations
- **Maintenance Summary**: Request status and trends

### 🔐 **Security & Permissions**
- **Role-Based Access**: Property Manager and Tenant roles
- **Data Validation**: Comprehensive form validation
- **Capability System**: WordPress-native permission management
- **Secure API**: RESTful endpoints with proper authentication

## 📦 Installation

### Automatic Installation
1. Go to **Plugins > Add New** in your WordPress admin
2. Search for "PlugHaus Property Management"
3. Click **Install Now** and then **Activate**

### Manual Installation
1. Download the plugin ZIP file
2. Go to **Plugins > Add New > Upload Plugin**
3. Choose the ZIP file and click **Install Now**
4. Activate the plugin

### Requirements
- **WordPress**: 5.8 or higher
- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher

## 🎯 Quick Start

### 1. Initial Setup
After activation, you'll see a new **Property Mgmt** menu in your WordPress admin.

1. **Configure Settings**: Go to **Property Mgmt > Settings**
   - Set your currency
   - Configure email notifications
   - Set default lease terms

2. **Add Your First Property**:
   - Go to **Property Mgmt > Properties > Add New**
   - Enter property details and address
   - Save the property

3. **Create Units**:
   - Go to **Property Mgmt > Units > Add New**
   - Assign to your property
   - Set rent amount and unit details

### 2. Add Tenants and Leases
1. **Add Tenants**: **Property Mgmt > Tenants > Add New**
2. **Create Leases**: **Property Mgmt > Leases > Add New**
   - Link tenant to unit
   - Set lease dates and rent amount

### 3. Setup Tenant Portal
The plugin automatically creates these pages:
- `/tenant-portal/` - Main tenant dashboard
- `/maintenance-request/` - Maintenance request form
- `/property-listings/` - Available properties

## 🔧 Configuration

### Settings Overview
Navigate to **Property Mgmt > Settings** to configure:

- **General Settings**
  - Currency format
  - Date format
  - Admin email notifications

- **Email Notifications**
  - New tenant registration
  - Lease expiry reminders
  - Maintenance requests
  - Payment received notifications

- **API Settings**
  - Enable/disable REST API access
  - API authentication

### Custom Post Types
The plugin creates these post types:
- `phpm_property` - Properties
- `phpm_unit` - Units
- `phpm_tenant` - Tenants  
- `phpm_lease` - Leases
- `phpm_maintenance` - Maintenance Requests

### Database Tables
Custom tables for enhanced functionality:
- `wp_phpm_property_views` - Property view tracking
- `wp_phpm_maintenance_log` - Maintenance activity log
- `wp_phpm_payments` - Payment history
- `wp_phpm_lease_history` - Lease change log
- `wp_phpm_documents` - Document attachments

## 🎨 Shortcodes

### Property Listings
```
[phpm_property_listings]
```
Display available properties with search and filtering.

**Attributes:**
- `limit` - Number of properties to show (default: 10)
- `type` - Filter by property type
- `location` - Filter by location
- `show_search` - Show search form (true/false)

### Tenant Portal
```
[phpm_tenant_portal]
```
Complete tenant dashboard with lease info and maintenance requests.

### Property Search
```
[phpm_property_search]
```
Search form for properties with filters.

### Maintenance Request Form
```
[phpm_maintenance_form]
```
Form for tenants to submit maintenance requests.

### Units List
```
[phpm_units property_id="123"]
```
Display units for a specific property.

## 🔌 REST API

The plugin provides RESTful API endpoints:

### Endpoints
- `GET /wp-json/phpm/v1/properties` - List properties
- `GET /wp-json/phpm/v1/properties/{id}` - Get property
- `POST /wp-json/phpm/v1/properties` - Create property
- `PUT /wp-json/phpm/v1/properties/{id}` - Update property
- `DELETE /wp-json/phpm/v1/properties/{id}` - Delete property

Similar endpoints exist for units, tenants, leases, and maintenance requests.

### Authentication
API endpoints use WordPress's built-in authentication system. Capabilities are checked for each request.

## 🎭 User Roles

### Property Manager
Full access to all property management features:
- Manage properties, units, tenants, leases
- View reports and analytics
- Access admin settings
- Manage maintenance requests

### Tenant
Limited access for tenant self-service:
- View their lease information
- Submit maintenance requests
- Update contact information
- View payment history

## 🔄 Data Import/Export

### Supported Formats
- **CSV**: Import/export properties, units, tenants
- **JSON**: Full data backup and restore

### Import Process
1. Go to **Property Mgmt > Import/Export**
2. Select import type (Properties, Units, Tenants)
3. Upload CSV file
4. Map columns to fields
5. Review and import

### CSV Format Examples

**Properties CSV:**
```csv
Name,Address,City,State,ZIP,Type,Units
"Sunset Apartments","123 Main St","San Francisco","CA","94102","apartment",12
```

**Units CSV:**
```csv
Property,Unit Number,Bedrooms,Bathrooms,Rent,Square Feet
"Sunset Apartments","101",1,1,2500,800
```

**Tenants CSV:**
```csv
First Name,Last Name,Email,Phone,Move-in Date
"John","Smith","john@example.com","555-1234","2024-01-01"
```

## 🎨 Customization

### Themes
The plugin works with any WordPress theme. For custom styling:

1. **Override Templates**: Copy templates from `/plugins/plughaus-property-management/templates/` to your theme
2. **Custom CSS**: Add styles to your theme's CSS file
3. **Hooks & Filters**: Use WordPress hooks for custom functionality

### Available Hooks

**Actions:**
- `phpm_property_created` - After property creation
- `phpm_lease_activated` - When lease becomes active
- `phpm_maintenance_submitted` - New maintenance request
- `phpm_tenant_moved_in` - Tenant move-in date

**Filters:**
- `phpm_property_types` - Customize property types
- `phpm_lease_statuses` - Customize lease statuses
- `phpm_maintenance_priorities` - Customize priorities

### Custom Fields
Add custom fields using WordPress meta boxes or ACF compatibility.

## 🔧 Troubleshooting

### Common Issues

**Plugin Won't Activate**
- Check PHP version (7.4+ required)
- Verify WordPress version (5.8+ required)
- Check for plugin conflicts

**Database Errors**
- Ensure proper MySQL permissions
- Check database character set (UTF-8)
- Verify table creation permissions

**Tenant Portal Not Working**
- Check page creation in Pages menu
- Verify shortcodes are present
- Check user capabilities

**Email Notifications Not Sending**
- Configure SMTP settings
- Check spam folders
- Verify email addresses

### Debug Mode
Enable debug mode in `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('PHPM_DEBUG', true);
```

### Support
- **Documentation**: [Plugin Documentation](#)
- **Support Forum**: [WordPress.org Support](#)
- **Bug Reports**: [GitHub Issues](#)

## 🔒 Security

### Data Protection
- All user inputs are sanitized and validated
- Database queries use prepared statements
- WordPress nonces prevent CSRF attacks
- Capability checks on all admin functions

### Privacy Compliance
- GDPR compliant data handling
- User data export/deletion tools
- Privacy policy integration
- Consent management

## 🚀 Performance

### Optimization Features
- Efficient database queries
- Transient caching
- Lazy loading for large datasets
- Optimized admin interfaces

### Recommended Hosting
- **Memory**: 256MB+ PHP memory limit
- **Storage**: SSD for database performance
- **Caching**: Redis or Memcached recommended
- **CDN**: For property images and assets

## 📈 Roadmap

### Version 1.1 (Planned)
- Advanced reporting dashboard
- Bulk import/export improvements
- Email template customization
- Mobile app integration

### Pro Features (Available Separately)
- Payment processing integration
- Advanced analytics with charts
- Document management system
- White-label customization
- Premium support

## 🤝 Contributing

We welcome contributions to improve the plugin:

1. **Fork** the repository
2. **Create** a feature branch
3. **Make** your changes
4. **Test** thoroughly
5. **Submit** a pull request

### Development Setup
```bash
# Clone repository
git clone https://github.com/plughausstudios/property-management

# Install dependencies
npm install
composer install

# Build assets
npm run build
```

## 📄 License

This plugin is licensed under the **GPL v2 or later**.

```
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
```

## 🙏 Credits

### Built With
- **WordPress** - Content management platform
- **Chart.js** - Analytics charts (Pro version)
- **WordPress Coding Standards** - Code quality
- **PHPUnit** - Testing framework

### Inspiration
Created to provide small property managers with an affordable, WordPress-native alternative to expensive SaaS platforms like Yardi and AppFolio.

---

**Developed with ❤️ by [PlugHaus Studios](https://plughausstudios.com)**

*Making professional WordPress plugins accessible to everyone.*