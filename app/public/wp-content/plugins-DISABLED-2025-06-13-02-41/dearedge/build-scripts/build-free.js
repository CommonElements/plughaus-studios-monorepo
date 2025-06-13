#!/usr/bin/env node

/**
 * DealerEdge - WordPress.org Free Version Builder
 * Strips pro features and creates distribution-ready ZIP
 */

const fs = require('fs-extra');
const path = require('path');
const archiver = require('archiver');

const config = {
    sourceDir: path.resolve(__dirname, '../'),
    buildDir: path.resolve(__dirname, '../dist/free'),
    outputFile: 'dearedge-free.zip',
    pluginSlug: 'dearedge',
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
    console.log('🔨 Building DealerEdge Free Version for WordPress.org...');
    
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
    const mainFile = path.join(config.buildDir, 'dearedge.php');
    let content = await fs.readFile(mainFile, 'utf8');
    
    // Update plugin header for free version
    content = content.replace(
        /Plugin Name: DealerEdge.*/,
        'Plugin Name: DealerEdge - Auto Shop & Dealer Management'
    );
    
    content = content.replace(
        /Description: .*/,
        'Description: Complete auto shop and small car dealer management system. Manage work orders, inventory, customers, and sales. Perfect for auto repair shops and small dealerships.'
    );
    
    // Disable pro license checking
    content = content.replace(
        /private function check_pro_license\(\) \{[\s\S]*?\}/,
        'private function check_pro_license() {\n        return false; // Free version\n    }'
    );
    
    // Remove pro feature loading
    content = content.replace(
        /\/\/ Load pro features if licensed[\s\S]*?}/,
        '// Pro features available in DealerEdge Pro'
    );
    
    await fs.writeFile(mainFile, content);
}

async function updateReadmeFile() {
    const readmeContent = `=== DealerEdge - Auto Shop & Dealer Management ===
Contributors: vireodesigns
Tags: auto shop, car dealer, work orders, inventory, customer management
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Complete auto shop and small car dealer management system for WordPress. Manage work orders, inventory, customers, and sales.

== Description ==

DealerEdge transforms your WordPress site into a complete business management system for auto repair shops and small car dealerships. Perfect for independent mechanics, service centers, and small dealers.

**Core Features:**

* **Work Order Management** - Create, assign, and track repair orders
* **Customer Database** - Organize customer information and vehicle history
* **Inventory Management** - Track parts, supplies, and vehicle inventory
* **Service Scheduling** - Manage appointments and technician schedules
* **Invoice Generation** - Create professional invoices and track payments
* **Vehicle History** - Complete service records for each customer vehicle

**Perfect For:**
* Auto repair shops
* Oil change centers
* Tire shops
* Small car dealerships
* Mobile mechanics
* Fleet maintenance

**Pro Version Features:**
* Advanced scheduling and calendar management
* Payment processing integration
* Multi-location management
* Advanced inventory tracking with barcode scanning
* Customer portal and online booking
* Detailed analytics and reporting

== Installation ==

1. Upload the plugin files to the \`/wp-content/plugins/dearedge\` directory
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the DealerEdge menu to configure your shop settings
4. Start adding customers and creating work orders

== Frequently Asked Questions ==

= Can I track multiple vehicles per customer? =
Yes! DealerEdge allows unlimited vehicles per customer with complete service history.

= Does it work with any WordPress theme? =
Yes, DealerEdge is designed to work with any properly coded WordPress theme.

= Can I customize work order templates? =
Yes, work order templates are fully customizable to match your business needs.

= Is there mobile support? =
The admin interface is mobile-responsive, with dedicated mobile apps available in the Pro version.

== Screenshots ==

1. Shop dashboard with work order overview
2. Customer and vehicle management
3. Work order creation interface
4. Inventory tracking system
5. Invoice generation and payment tracking

== Changelog ==

= 1.0.0 =
* Initial release
* Work order management system
* Customer and vehicle database
* Basic inventory tracking
* Invoice generation
* Service scheduling

== Upgrade Notice ==

= 1.0.0 =
Initial release of DealerEdge auto shop and dealer management system.
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