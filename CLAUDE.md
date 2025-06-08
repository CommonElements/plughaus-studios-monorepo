# PlugHaus Property Management Plugin - Development Guide

## 🎯 Project Overview

This is the **PlugHaus Property Management Plugin** - a freemium WordPress plugin targeting small property managers as an alternative to Yardi and AppFolio. The plugin uses an "Accelerated Freemium with Pro Upgrade Path" architecture.

### Core Mission
- **Free Version**: Basic residential property management (WordPress.org marketplace)
- **Pro Version**: Advanced features with licensing through PlugHausStudios.com
- **Future Modules**: Community association, commercial rental, STR, accounting, payments, work orders

## 🏗️ Architecture Overview

### Single Repository Structure
```
plughaus-property-management/
├── plughaus-property-management.php    # Main plugin file with conditional loading
├── core/                               # Free features (WordPress.org compliant)
│   ├── includes/
│   │   ├── admin/                      # Admin interface classes
│   │   ├── api/                        # REST API endpoints
│   │   ├── core/                       # Core functionality
│   │   └── public/                     # Frontend classes
│   ├── assets/                         # CSS/JS for core features
│   └── property-management-core.php    # Core bootstrap
├── pro/                                # Pro-only features
│   ├── includes/
│   │   ├── analytics/                  # Advanced analytics & charts
│   │   ├── automation/                 # Payment & email automation
│   │   ├── licensing/                  # License validation system
│   │   └── reporting/                  # Advanced reporting & exports
│   └── assets/                         # Pro-specific CSS/JS
├── addons/                             # Future bolt-on modules
├── build-scripts/                      # Distribution automation
│   ├── build-free.js                   # WordPress.org ZIP builder
│   └── build-pro.js                    # Pro version ZIP builder
└── assets/                             # Shared assets
```

### Key Design Principles
1. **Conditional Loading**: Pro features only load with valid license
2. **WordPress.org Compliance**: Free version passes all marketplace checks
3. **Single Codebase**: Maintain both versions from one repository
4. **Modular Architecture**: Easy to add future modules/addons

## 🚀 Development Environment

### Local Setup (Local by Flywheel)
- **Site Name**: "The Beginning is Finished"
- **Database**: MySQL (local/root/root)
- **WordPress**: Latest version
- **PHP**: 7.4+

### WordPress Admin Access
1. Run `/create-admin-fresh.php` (delete after use!)
2. Login: `admin` / `password`
3. Go to Plugins → Activate "PlugHaus Property Management"

### Development Commands
```bash
# Install dependencies
npm install

# Development mode
npm run dev

# Build free version (WordPress.org ready)
npm run build:free

# Build pro version
npm run build:pro

# Code standards
composer install
./vendor/bin/phpcs --standard=WordPress core/

# Asset compilation
npm run build
npm run watch
```

## 🔑 Licensing System

### Architecture
- **Free Version**: `check_pro_license()` returns `false`
- **Pro Version**: Validates against WooCommerce License Manager
- **API Endpoint**: `/wp-json/phls/v1/validate`
- **License Storage**: WordPress options table

### Key Files
- `pro/includes/licensing/class-phpm-license-manager.php` - Core license validation
- `plughaus-property-management.php:89-93` - License checking logic
- Build scripts automatically configure license checking per version

### License Validation Flow
1. User enters license key in Settings → Pro License
2. AJAX call to license validation endpoint
3. Server validates against PlugHausStudios.com API
4. License status cached locally
5. Pro features enabled/disabled accordingly

## 📁 Key Files & Functions

### Main Plugin File
`plughaus-property-management.php` - Entry point with conditional loading:
```php
private function check_pro_license() {
    // Free version: return false;
    // Pro version: return PHPM_License_Manager::is_valid();
}
```

### Core Admin Classes
- `core/includes/admin/class-phpm-admin.php` - Main admin interface
- `core/includes/admin/class-phpm-admin-menu.php` - Menu structure
- `core/includes/admin/class-phpm-dashboard.php` - Dashboard widgets

### Pro Features
- `pro/includes/analytics/class-phpm-analytics.php` - Charts & metrics
- `pro/includes/automation/class-phpm-payment-automation.php` - Payment processing
- `pro/includes/licensing/class-phpm-license-manager.php` - License validation

### Data Models
- `core/includes/core/class-phpm-property.php` - Property management
- `core/includes/core/class-phpm-tenant.php` - Tenant management
- `core/includes/core/class-phpm-lease.php` - Lease management

## 🛠️ Build System

### Free Version Build (`npm run build:free`)
- Strips all pro directories and files
- Modifies main plugin file to disable pro features
- Creates WordPress.org-compliant ZIP
- Output: `dist/free/plughaus-property-management.zip`

### Pro Version Build (`npm run build:pro`)
- Includes all features (core + pro)
- Enables license validation in main plugin file
- Creates pro distribution ZIP
- Output: `dist/pro/plughaus-property-management-pro.zip`

### Build Automation
Both build scripts:
1. Clean previous builds
2. Copy appropriate files
3. Modify plugin headers and functionality
4. Compile assets
5. Create distribution ZIP

## 📊 Current Implementation Status

### ✅ Completed Features
- [x] Freemium architecture with conditional loading
- [x] Core property management (CRUD operations)
- [x] Tenant and lease management
- [x] Basic maintenance request system
- [x] WordPress admin interface
- [x] REST API endpoints
- [x] License validation system
- [x] Build scripts for free/pro distributions
- [x] WordPress.org compliance structure

### 🚧 In Development
- [ ] Advanced analytics dashboard (Chart.js integration)
- [ ] Payment automation workflows
- [ ] Email template system
- [ ] Advanced reporting & exports
- [ ] Import/export functionality
- [ ] Mobile-responsive frontend

### 📋 Immediate Next Steps
1. **Test Plugin Activation**: Verify plugin activates without errors
2. **Core Feature Testing**: Test property/tenant/lease CRUD operations
3. **Admin Interface**: Ensure admin menu and pages load correctly
4. **Pro Feature Development**: Complete analytics dashboard
5. **WordPress.org Preparation**: Final compliance checks

## 🔧 Technical Specifications

### WordPress Requirements
- **Minimum WordPress**: 5.8
- **Minimum PHP**: 7.4
- **Tested up to**: 6.4
- **License**: GPL v2+ (free), Commercial (pro)

### Dependencies
- **Chart.js**: Analytics charts in pro version
- **WooCommerce License Manager**: Pro licensing
- **@wordpress/scripts**: Asset compilation
- **Composer**: PHP dependencies and WPCS

### Database Schema
```sql
-- Properties table
phpm_properties (id, name, address, type, status, created_at)

-- Units table  
phpm_units (id, property_id, unit_number, bedrooms, bathrooms, rent_amount)

-- Tenants table
phpm_tenants (id, first_name, last_name, email, phone, status)

-- Leases table
phpm_leases (id, property_id, unit_id, tenant_id, start_date, end_date, rent_amount)

-- Maintenance table
phpm_maintenance (id, property_id, unit_id, title, description, status, priority)
```

## 🎨 UI/UX Guidelines

### WordPress Admin Standards
- Follow WordPress admin design patterns
- Use WordPress core CSS classes
- Implement responsive design
- Maintain accessibility standards

### Color Scheme
- Primary: `#007cba` (WordPress blue)
- Success: `#28a745`
- Warning: `#ffc107`
- Danger: `#dc3545`

### Pro Feature Styling
- Use subtle badges/indicators for pro features
- Graceful degradation when pro disabled
- Clear upgrade prompts without being pushy

## 🔒 Security Considerations

### Data Validation
- Sanitize all user inputs
- Validate and escape output
- Use WordPress nonces for forms
- Capability checks for admin functions

### Pro License Security
- Validate licenses server-side
- Cache validation results securely
- Handle license API failures gracefully
- Don't expose sensitive license data

## 📚 Development Patterns

### WordPress Standards
- Follow WordPress Coding Standards (WPCS)
- Use WordPress hooks and filters
- Implement proper error handling
- Cache expensive operations

### Class Naming
- Core classes: `PHPM_*`
- Pro classes: `PHPM_Pro_*`
- Admin classes: `PHPM_Admin_*`

### File Organization
- One class per file
- Use PSR-4 autoloading patterns
- Group related functionality
- Clear separation of concerns

## 🐛 Debugging & Testing

### Debug Mode
```php
// Add to wp-config.php for development
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('PHPM_DEBUG', true);
```

### Common Issues
1. **Plugin Won't Activate**: Check PHP syntax and WordPress version
2. **Pro Features Not Loading**: Verify license validation
3. **Asset Issues**: Run `npm run build` to compile assets
4. **Database Errors**: Check table creation in activator

### Testing Checklist
- [ ] Plugin activates without errors
- [ ] Admin menu appears
- [ ] Core CRUD operations work
- [ ] Pro features respect license status
- [ ] Free build passes WordPress.org checks
- [ ] No JavaScript console errors

## 🚀 Deployment Strategy

### WordPress.org Submission
1. Run `npm run build:free`
2. Test free version thoroughly
3. Submit to WordPress.org repository
4. Maintain separate SVN repository

### Pro Version Distribution
1. Run `npm run build:pro`
2. Upload to PlugHausStudios.com
3. Configure WooCommerce licensing
4. Set up automated delivery system

## 📞 Support & Resources

### Documentation
- WordPress Plugin Handbook
- WooCommerce License Manager docs
- Chart.js documentation
- Local by Flywheel guides

### Key Contacts
- **Primary Development**: Contact through Claude Code sessions
- **Business Requirements**: PlugHaus Studios team
- **Technical Support**: WordPress.org forums (free version)

---

## 🎯 Current Session Goals

**You are taking over active development of this freemium property management plugin.** The architecture is complete and the plugin has been transferred to a fresh Local by Flywheel site.

### Immediate Priorities:
1. **Verify Setup**: Test plugin activation and core functionality
2. **Complete Pro Features**: Finish analytics dashboard and payment automation
3. **Test License System**: Ensure pro features toggle correctly
4. **WordPress.org Prep**: Final compliance checks for free version
5. **Feature Development**: Add remaining core functionality

### Context Notes:
- Previous session transferred complete codebase from locked site
- Architecture implements accelerated freemium strategy
- Build system ready for dual distribution (free/pro)
- License validation system implemented but needs testing
- All core files are in place and ready for development

**Ready to continue development!** 🚀