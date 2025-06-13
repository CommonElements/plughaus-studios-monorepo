#!/usr/bin/env node

/**
 * EquipRent Pro - Free Version Build Script
 * 
 * This script creates a WordPress.org compliant free version by:
 * - Removing pro directories and features
 * - Modifying the main plugin file to disable pro license checking
 * - Creating a clean distribution package
 * 
 * Usage: node build-free.js
 */

const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

// Configuration
const PLUGIN_DIR = path.resolve(__dirname, '..');
const DIST_DIR = path.join(PLUGIN_DIR, 'dist', 'free');
const PACKAGE_NAME = 'equiprent-pro';
const PACKAGE_DIR = path.join(DIST_DIR, PACKAGE_NAME);

console.log('🏗️  Building EquipRent Pro FREE version...\n');

// Clean previous build
if (fs.existsSync(DIST_DIR)) {
    console.log('🧹 Cleaning previous build...');
    fs.rmSync(DIST_DIR, { recursive: true, force: true });
}

// Create dist directory
fs.mkdirSync(DIST_DIR, { recursive: true });
fs.mkdirSync(PACKAGE_DIR, { recursive: true });

console.log('📁 Created build directories');

// Copy core files and directories
console.log('📋 Copying core files...');

const coreItems = [
    'core',
    'assets',
    'languages',
    'readme.txt',
    'uninstall.php'
];

coreItems.forEach(item => {
    const srcPath = path.join(PLUGIN_DIR, item);
    const destPath = path.join(PACKAGE_DIR, item);
    
    if (fs.existsSync(srcPath)) {
        if (fs.statSync(srcPath).isDirectory()) {
            copyDirectory(srcPath, destPath);
        } else {
            fs.copyFileSync(srcPath, destPath);
        }
        console.log(`  ✅ Copied ${item}`);
    }
});

// Create modified main plugin file (free version)
console.log('📝 Creating free version plugin file...');
createFreePluginFile();

// Create package.json for free version
console.log('📦 Creating package.json...');
createPackageJson();

// Create composer.json for free version
console.log('🎼 Creating composer.json...');
createComposerJson();

// Update readme.txt for free version
console.log('📄 Updating readme.txt...');
updateReadmeForFree();

// Create .gitignore
console.log('🔒 Creating .gitignore...');
createGitIgnore();

// Create ZIP package
console.log('📦 Creating ZIP package...');
createZipPackage();

console.log('\n✅ FREE version build completed successfully!');
console.log(`📍 Location: ${DIST_DIR}`);
console.log(`📦 Package: ${path.join(DIST_DIR, PACKAGE_NAME + '.zip')}`);
console.log('🚀 Ready for WordPress.org submission!');

/**
 * Copy directory recursively
 */
function copyDirectory(src, dest) {
    if (!fs.existsSync(dest)) {
        fs.mkdirSync(dest, { recursive: true });
    }
    
    const items = fs.readdirSync(src);
    
    items.forEach(item => {
        const srcPath = path.join(src, item);
        const destPath = path.join(dest, item);
        
        if (fs.statSync(srcPath).isDirectory()) {
            copyDirectory(srcPath, destPath);
        } else {
            fs.copyFileSync(srcPath, destPath);
        }
    });
}

/**
 * Create free version main plugin file
 */
function createFreePluginFile() {
    const originalFile = path.join(PLUGIN_DIR, 'equiprent-pro.php');
    const freeFile = path.join(PACKAGE_DIR, 'equiprent-pro.php');
    
    let content = fs.readFileSync(originalFile, 'utf8');
    
    // Modify plugin header for free version
    content = content.replace(
        'Plugin Name: EquipRent Pro - Equipment Rental Management',
        'Plugin Name: EquipRent Pro'
    );
    
    content = content.replace(
        'Description: Professional equipment rental management system for WordPress. Manage inventory, bookings, customers, and rentals with ease. Perfect for tool rental, party equipment, AV gear, and construction equipment businesses.',
        'Description: Professional equipment rental management system for WordPress. Manage equipment inventory, bookings, customers, and availability calendar. Perfect for tool rental, party equipment, AV gear, and construction equipment businesses.'
    );
    
    // Disable pro license checking for free version
    content = content.replace(
        /private function check_pro_license\(\) \{[\s\S]*?\n    \}/,
        `private function check_pro_license() {
        // Free version - always return false
        return false;
    }`
    );
    
    // Remove pro features loading
    content = content.replace(
        /\/\/ Load pro features if licensed\s*if \(\$this->is_pro\) \{[\s\S]*?\n        \}/,
        '// Pro features not available in free version'
    );
    
    // Add free version notice
    const freeVersionNotice = `
/**
 * FREE VERSION NOTICE
 * 
 * This is the free version of EquipRent Pro distributed via WordPress.org
 * Pro features are available separately at https://vireodesigns.com/plugins/equiprent-pro
 * 
 * Free Version Includes:
 * - Equipment inventory management
 * - Booking and reservation system  
 * - Customer database
 * - Availability calendar
 * - Basic invoicing
 * - Maintenance tracking
 */
`;
    
    content = content.replace(
        '// Exit if accessed directly',
        freeVersionNotice + '\n// Exit if accessed directly'
    );
    
    fs.writeFileSync(freeFile, content);
}

/**
 * Create package.json for free version
 */
function createPackageJson() {
    const packageJson = {
        "name": "equiprent-pro",
        "version": "1.0.0",
        "description": "Equipment rental management system for WordPress",
        "main": "equiprent-pro.php",
        "scripts": {
            "build": "npm run build:css && npm run build:js",
            "build:css": "echo 'CSS compilation would go here'",
            "build:js": "echo 'JavaScript compilation would go here'",
            "watch": "npm run watch:css && npm run watch:js",
            "watch:css": "echo 'CSS watching would go here'",
            "watch:js": "echo 'JavaScript watching would go here'"
        },
        "keywords": [
            "wordpress",
            "plugin", 
            "equipment",
            "rental",
            "booking",
            "inventory"
        ],
        "author": "Vireo Designs",
        "license": "GPL-2.0-or-later",
        "homepage": "https://vireodesigns.com/plugins/equiprent-pro",
        "repository": {
            "type": "git",
            "url": "https://github.com/vireodesigns/equiprent-pro"
        },
        "devDependencies": {
            "@wordpress/scripts": "^26.0.0"
        }
    };
    
    fs.writeFileSync(
        path.join(PACKAGE_DIR, 'package.json'),
        JSON.stringify(packageJson, null, 2)
    );
}

/**
 * Create composer.json for free version
 */
function createComposerJson() {
    const composerJson = {
        "name": "vireodesigns/equiprent-pro",
        "description": "Equipment rental management system for WordPress",
        "type": "wordpress-plugin",
        "license": "GPL-2.0-or-later",
        "authors": [
            {
                "name": "Vireo Designs",
                "homepage": "https://vireodesigns.com"
            }
        ],
        "require": {
            "php": ">=7.4"
        },
        "require-dev": {
            "wp-coding-standards/wpcs": "^2.3",
            "phpunit/phpunit": "^9.0",
            "dealerdirect/phpcodesniffer-composer-installer": "^0.7"
        },
        "scripts": {
            "phpcs": "phpcs --standard=WordPress core/",
            "phpcbf": "phpcbf --standard=WordPress core/",
            "test": "phpunit"
        },
        "config": {
            "allow-plugins": {
                "dealerdirect/phpcodesniffer-composer-installer": true
            }
        }
    };
    
    fs.writeFileSync(
        path.join(PACKAGE_DIR, 'composer.json'),
        JSON.stringify(composerJson, null, 2)
    );
}

/**
 * Update readme.txt for free version
 */
function updateReadmeForFree() {
    const readmePath = path.join(PACKAGE_DIR, 'readme.txt');
    let content = fs.readFileSync(readmePath, 'utf8');
    
    // Add free version banner
    const freeBanner = `
== Free Version ==

This is the free version of EquipRent Pro, providing comprehensive equipment rental management capabilities at no cost. Upgrade to Pro for advanced features like payment processing, delivery management, and analytics.

`;
    
    content = content.replace(
        '== Description ==',
        '== Description ==' + freeBanner
    );
    
    fs.writeFileSync(readmePath, content);
}

/**
 * Create .gitignore for distribution
 */
function createGitIgnore() {
    const gitignore = `# Dependencies
node_modules/
vendor/

# Build outputs
dist/
build/

# OS generated files
.DS_Store
.DS_Store?
._*
.Spotlight-V100
.Trashes
ehthumbs.db
Thumbs.db

# IDE files
.vscode/
.idea/
*.swp
*.swo

# Logs
*.log
npm-debug.log*

# Runtime data
pids
*.pid
*.seed

# Optional npm cache directory
.npm

# Optional eslint cache
.eslintcache
`;
    
    fs.writeFileSync(path.join(PACKAGE_DIR, '.gitignore'), gitignore);
}

/**
 * Create ZIP package
 */
function createZipPackage() {
    const zipPath = path.join(DIST_DIR, PACKAGE_NAME + '.zip');
    
    try {
        // Change to dist directory to avoid full path in zip
        process.chdir(DIST_DIR);
        
        // Create zip using system zip command
        execSync(`zip -r "${PACKAGE_NAME}.zip" "${PACKAGE_NAME}"/`, { stdio: 'inherit' });
        
        console.log(`  ✅ Created ${PACKAGE_NAME}.zip`);
    } catch (error) {
        console.error('  ❌ Failed to create ZIP package:', error.message);
        console.log('  💡 You can manually create the ZIP from the package directory');
    }
}