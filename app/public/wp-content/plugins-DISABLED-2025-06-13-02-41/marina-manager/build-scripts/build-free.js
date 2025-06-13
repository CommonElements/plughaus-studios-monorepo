#!/usr/bin/env node

/**
 * Marina Manager - WordPress.org Free Version Builder
 * Strips pro features and creates distribution-ready ZIP
 */

const fs = require('fs-extra');
const path = require('path');
const archiver = require('archiver');

const config = {
    sourceDir: path.resolve(__dirname, '../'),
    buildDir: path.resolve(__dirname, '../dist/free'),
    outputFile: 'marina-manager-free.zip',
    pluginSlug: 'marina-manager',
    excludePatterns: [
        'pro/',
        'build-scripts/',
        'dist/',
        'node_modules/',
        '.git/',
        '.gitignore',
        'package.json',
        'package-lock.json',
        'composer.json',
        'composer.lock',
        '*.log',
        '.DS_Store'
    ]
};

async function buildFreeVersion() {
    console.log('🔨 Building Marina Manager Free Version for WordPress.org...');
    
    try {
        // Clean build directory
        await fs.remove(config.buildDir);
        await fs.ensureDir(config.buildDir);
        
        // Copy core files
        console.log('📁 Copying core files...');
        await copyFilteredFiles(config.sourceDir, config.buildDir);
        
        // Modify main plugin file for free version
        console.log('✂️ Modifying plugin for free version...');
        await modifyMainPluginFile();
        
        // Update readme.txt for WordPress.org
        console.log('📝 Updating readme.txt...');
        await updateReadmeFile();
        
        // Create ZIP archive
        console.log('📦 Creating distribution ZIP...');
        await createZipArchive();
        
        console.log('✅ Free version build complete!');
        console.log(`📍 Output: ${path.join(config.buildDir, config.outputFile)}`);
        
    } catch (error) {
        console.error('❌ Build failed:', error);
        process.exit(1);
    }
}

async function copyFilteredFiles(source, dest) {
    const items = await fs.readdir(source);
    
    for (const item of items) {
        const sourcePath = path.join(source, item);
        const destPath = path.join(dest, item);
        
        // Skip excluded patterns
        if (config.excludePatterns.some(pattern => item.includes(pattern))) {
            continue;
        }
        
        const stat = await fs.stat(sourcePath);
        
        if (stat.isDirectory()) {
            await fs.ensureDir(destPath);
            await copyFilteredFiles(sourcePath, destPath);
        } else {
            await fs.copy(sourcePath, destPath);
        }
    }
}

async function modifyMainPluginFile() {
    const mainFile = path.join(config.buildDir, 'marina-manager.php');
    let content = await fs.readFile(mainFile, 'utf8');
    
    // Update plugin header for free version
    content = content.replace(
        /Plugin Name: Marina Manager.*/,
        'Plugin Name: Marina Manager - Marina & Boat Slip Management'
    );
    
    content = content.replace(
        /Description: .*/,
        'Description: Complete marina and boat slip management system. Manage slip rentals, reservations, tenants, and facility operations. Perfect for marinas and boat harbors.'
    );
    
    // Disable pro license checking
    content = content.replace(
        /private function check_pro_license\(\) \{[\s\S]*?\}/,
        'private function check_pro_license() {\n        return false; // Free version\n    }'
    );
    
    // Remove pro feature loading
    content = content.replace(
        /\/\/ Load pro features if licensed[\s\S]*?}/,
        '// Pro features available in Marina Manager Pro'
    );
    
    await fs.writeFile(mainFile, content);
}

async function updateReadmeFile() {
    const readmeContent = `=== Marina Manager - Marina & Boat Slip Management ===
Contributors: vireodesigns
Tags: marina, boat slip, reservations, marina management, slip rental
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Complete marina and boat slip management system for WordPress. Manage slip rentals, reservations, tenants, and facility operations.

== Description ==

Marina Manager transforms your WordPress site into a complete marina management system. Perfect for marinas, yacht clubs, boat harbors, and waterfront facilities.

**Core Features:**

* **Slip Management** - Organize boat slips by size, location, and amenities
* **Reservation System** - Handle slip reservations and availability checking
* **Tenant Management** - Track boat owners and their vessel information
* **Billing System** - Generate invoices and track payments
* **Facility Dashboard** - Overview of occupancy and revenue
* **Online Booking** - Let customers reserve slips through your website

**Perfect For:**
* Marinas and yacht clubs
* Boat harbors and docks
* RV parks with water access
* Waterfront facilities
* Boat storage facilities

**Pro Version Features:**
* Advanced reservation management
* Payment processing integration
* Multi-facility management
* Weather integration and alerts
* Maintenance scheduling
* Advanced reporting and analytics

== Installation ==

1. Upload the plugin files to the \`/wp-content/plugins/marina-manager\` directory
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Marina Manager menu to configure your facility settings
4. Add your slip inventory and start taking reservations

== Frequently Asked Questions ==

= Can I manage multiple marinas? =
The free version supports one marina facility. Multi-facility management is available in the Pro version.

= Does it handle seasonal and transient slips? =
Yes! Marina Manager supports both long-term slip rentals and short-term transient reservations.

= Can customers book slips online? =
Yes, the plugin includes a public booking system for your website.

= Is there boat size compatibility checking? =
Yes, the system automatically checks boat size against slip dimensions when making reservations.

== Screenshots ==

1. Marina dashboard with occupancy overview
2. Slip management and availability grid
3. Reservation booking interface
4. Tenant and boat information management
5. Billing and payment tracking

== Changelog ==

= 1.0.0 =
* Initial release
* Slip inventory management
* Reservation booking system
* Tenant and boat database
* Basic billing and invoicing
* Public booking interface

== Upgrade Notice ==

= 1.0.0 =
Initial release of Marina Manager slip and facility management system.
`;

    const readmePath = path.join(config.buildDir, 'readme.txt');
    await fs.writeFile(readmePath, readmeContent);
}

async function createZipArchive() {
    const output = fs.createWriteStream(path.join(config.buildDir, config.outputFile));
    const archive = archiver('zip', { zlib: { level: 9 } });
    
    return new Promise((resolve, reject) => {
        output.on('close', resolve);
        archive.on('error', reject);
        
        archive.pipe(output);
        archive.directory(config.buildDir, config.pluginSlug, {
            ignore: [config.outputFile]
        });
        archive.finalize();
    });
}

// Run the build
buildFreeVersion();