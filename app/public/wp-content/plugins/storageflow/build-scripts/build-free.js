#!/usr/bin/env node

/**
 * StorageFlow - WordPress.org Free Version Builder
 * Strips pro features and creates distribution-ready ZIP
 */

const fs = require('fs-extra');
const path = require('path');
const archiver = require('archiver');

const config = {
    sourceDir: path.resolve(__dirname, '../'),
    buildDir: path.resolve(__dirname, '../dist/free'),
    outputFile: 'storageflow-free.zip',
    pluginSlug: 'storageflow',
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
    console.log('🔨 Building StorageFlow Free Version for WordPress.org...');
    
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
    const mainFile = path.join(config.buildDir, 'storageflow.php');
    let content = await fs.readFile(mainFile, 'utf8');
    
    // Update plugin header for free version
    content = content.replace(
        /Plugin Name: StorageFlow.*/,
        'Plugin Name: StorageFlow - Self Storage Management'
    );
    
    content = content.replace(
        /Description: .*/,
        'Description: Complete self storage facility management system. Manage units, rentals, tenants, and payments. Perfect for storage facilities and operators.'
    );
    
    // Disable pro license checking
    content = content.replace(
        /private function check_pro_license\(\) \{[\s\S]*?\}/,
        'private function check_pro_license() {\n        return false; // Free version\n    }'
    );
    
    // Remove pro feature loading
    content = content.replace(
        /\/\/ Load pro features if licensed[\s\S]*?}/,
        '// Pro features available in StorageFlow Pro'
    );
    
    await fs.writeFile(mainFile, content);
}

async function updateReadmeFile() {
    const readmeContent = `=== StorageFlow - Self Storage Management ===
Contributors: vireodesigns
Tags: self storage, storage facility, unit rental, tenant management, storage management
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Complete self storage facility management system for WordPress. Manage units, rentals, tenants, and payments.

== Description ==

StorageFlow transforms your WordPress site into a complete self storage facility management system. Perfect for storage facility owners, operators, and property managers.

**Core Features:**

* **Unit Management** - Organize storage units by size, type, and features
* **Rental Applications** - Handle online rental applications and approvals
* **Tenant Database** - Track tenant information and rental history
* **Payment Tracking** - Generate invoices and monitor payments
* **Occupancy Dashboard** - Real-time facility occupancy and revenue overview
* **Online Rentals** - Let customers apply for units through your website

**Perfect For:**
* Self storage facilities
* Mini storage operators
* RV storage facilities
* Boat storage facilities
* Document storage services
* Container storage yards

**Pro Version Features:**
* Advanced payment processing
* Gate access control integration
* Multi-facility management
* Automated billing and late fees
* Customer portal and online payments
* Advanced reporting and analytics

== Installation ==

1. Upload the plugin files to the \`/wp-content/plugins/storageflow\` directory
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the StorageFlow menu to configure your facility settings
4. Add your unit inventory and start accepting rental applications

== Frequently Asked Questions ==

= Can I manage multiple storage facilities? =
The free version supports one facility. Multi-facility management is available in the Pro version.

= Does it handle different unit sizes and types? =
Yes! StorageFlow supports unlimited unit sizes, types, and features (climate controlled, drive-up, etc.).

= Can customers apply for units online? =
Yes, the plugin includes a comprehensive online rental application system.

= Is there automatic billing? =
Basic invoice generation is included. Automated billing and payment processing is available in the Pro version.

== Screenshots ==

1. Facility dashboard with occupancy overview
2. Unit inventory management
3. Rental application interface
4. Tenant management system
5. Billing and payment tracking

== Changelog ==

= 1.0.0 =
* Initial release
* Unit inventory management
* Rental application system
* Tenant database
* Basic billing and invoicing
* Public rental application form

== Upgrade Notice ==

= 1.0.0 =
Initial release of StorageFlow self storage facility management system.
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