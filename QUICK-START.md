# Quick Start Guide - New Claude Code Instance

## 🚀 **IMMEDIATE ACTIONS**

### 1. **Get WordPress Admin Access**
```bash
# Visit this URL in your browser (replace with your Local site URL):
http://[your-local-site]/create-admin-fresh.php

# Login credentials will be created:
Username: admin
Password: password

# DELETE the create-admin-fresh.php file after login!
```

### 2. **Activate the Plugin**
1. Go to WordPress Admin → Plugins → Installed Plugins
2. Find "PlugHaus Property Management"
3. Click "Activate"
4. Look for "Property Mgmt" menu in admin sidebar

### 3. **Install Dependencies**
```bash
cd /path/to/plugin/directory
npm install          # Install Node.js dependencies
composer install     # Install PHP dependencies (WPCS)
```

## 📁 **KEY FILES TO KNOW**

### **Most Important Files**
- `plughaus-property-management.php` - Main plugin file (conditional loading logic)
- `CLAUDE.md` - Complete development guide
- `PROJECT-STATUS.md` - What's done vs. what needs work
- `package.json` - Build scripts and dependencies

### **Core Development Files**
- `core/includes/admin/class-phpm-admin.php` - Main admin interface
- `core/includes/core/class-phpm-property.php` - Property data model
- `pro/includes/licensing/class-phpm-license-manager.php` - License validation

### **Build System**
- `build-scripts/build-free.js` - Creates WordPress.org ZIP
- `build-scripts/build-pro.js` - Creates pro version ZIP

## ⚡ **DEVELOPMENT COMMANDS**

```bash
# Build free version (WordPress.org ready)
npm run build:free

# Build pro version  
npm run build:pro

# Development mode (watch files)
npm run dev

# Check WordPress coding standards
./vendor/bin/phpcs --standard=WordPress core/

# Compile assets
npm run build
```

## 🎯 **FIRST DEVELOPMENT TASKS**

### **Priority 1: Basic Testing**
1. ✅ Plugin activates without errors
2. ✅ Admin menu appears
3. ✅ Dashboard page loads
4. ❌ No JavaScript console errors

### **Priority 2: Database Implementation**
```php
// File: core/includes/class-phpm-activator.php
// Need to implement table creation:
- phpm_properties
- phpm_units  
- phpm_tenants
- phpm_leases
- phpm_maintenance
```

### **Priority 3: Core CRUD Operations**
- Test adding/editing properties
- Verify tenant management
- Check lease assignment

## 🔧 **ARCHITECTURE QUICK REFERENCE**

### **Freemium Logic**
```php
// Main plugin file line ~89:
private function check_pro_license() {
    return false; // Free version
    // return PHPM_License_Manager::is_valid(); // Pro version
}
```

### **Directory Structure**
```
core/     = Free features (WordPress.org compliant)
pro/      = Pro features (requires license)
addons/   = Future modules
assets/   = Shared CSS/JS
build-scripts/ = Distribution automation
```

### **Class Naming Convention**
- Core: `PHPM_*`
- Pro: `PHPM_Pro_*` 
- Admin: `PHPM_Admin_*`

## 🐛 **COMMON ISSUES & SOLUTIONS**

### **Plugin Won't Activate**
- Check PHP syntax errors
- Verify WordPress version compatibility
- Check error logs in Local

### **Assets Not Loading**
- Run `npm install` then `npm run build`
- Check file paths in admin classes
- Verify PHPM_PLUGIN_URL constant

### **Pro Features Not Working**
- Check license validation in pro/includes/licensing/
- Verify conditional loading in main plugin file
- Test license API endpoint: `/wp-json/phls/v1/validate`

## 📊 **CURRENT STATUS**

### ✅ **COMPLETED**
- Freemium architecture implemented
- Build system working
- License validation system ready
- WordPress.org compliance structure
- Fresh development environment ready

### ❌ **NEEDS WORK** 
- Database tables creation
- Core CRUD functionality testing
- Pro features completion (analytics, automation)
- Asset compilation setup
- WordPress.org submission prep

## 💡 **DEVELOPMENT TIPS**

### **WordPress Best Practices**
- Use WordPress hooks and filters
- Follow WordPress Coding Standards
- Sanitize inputs, escape outputs
- Use WordPress database functions

### **Testing Strategy**
- Test free version first
- Verify pro features toggle correctly
- Check license validation flow
- Test build scripts produce valid ZIPs

### **Debugging**
```php
// Add to wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('PHPM_DEBUG', true);
```

---

## 📞 **NEED HELP?**

1. **Read CLAUDE.md** - Comprehensive development guide
2. **Check PROJECT-STATUS.md** - What's implemented vs. needed
3. **Review code comments** - Architecture decisions documented
4. **Test incrementally** - Start with basic activation, build up

**Ready to code!** 🚀