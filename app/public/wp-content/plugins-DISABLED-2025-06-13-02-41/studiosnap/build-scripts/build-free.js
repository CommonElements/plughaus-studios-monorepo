#!/usr/bin/env node

/**
 * StudioSnap - WordPress.org Free Version Builder
 * Strips pro features and creates distribution-ready ZIP
 */

const fs = require('fs-extra');
const path = require('path');
const archiver = require('archiver');

const config = {
    sourceDir: path.resolve(__dirname, '../'),
    buildDir: path.resolve(__dirname, '../dist/free'),
    outputFile: 'studiosnap-free.zip',
    pluginSlug: 'studiosnap',
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
    console.log('🔨 Building StudioSnap Free Version for WordPress.org...');
    
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
    const mainFile = path.join(config.buildDir, 'studiosnap.php');
    let content = await fs.readFile(mainFile, 'utf8');
    
    // Update plugin header for free version
    content = content.replace(
        /Plugin Name: StudioSnap.*/,
        'Plugin Name: StudioSnap - Photography Studio Management'
    );
    
    content = content.replace(
        /Description: .*/,
        'Description: Complete photography studio management system. Manage bookings, clients, sessions, and galleries. Perfect for photographers and studios.'
    );
    
    // Disable pro license checking
    content = content.replace(
        /private function check_pro_license\(\) \{[\s\S]*?\}/,
        'private function check_pro_license() {\n        return false; // Free version\n    }'
    );
    
    // Remove pro feature loading
    content = content.replace(
        /\/\/ Load pro features if licensed[\s\S]*?}/,
        '// Pro features available in StudioSnap Pro'
    );
    
    await fs.writeFile(mainFile, content);
}

async function updateReadmeFile() {
    const readmeContent = `=== StudioSnap - Photography Studio Management ===
Contributors: vireodesigns
Tags: photography, studio management, booking, gallery, clients
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Complete photography studio management system for WordPress. Manage bookings, clients, sessions, and galleries.

== Description ==

StudioSnap transforms your WordPress site into a complete photography studio management system. Perfect for photographers, studios, and creative professionals.

**Core Features:**

* **Session Booking System** - Let clients book photography sessions online
* **Client Management** - Organize client information and session history  
* **Gallery Management** - Create beautiful client galleries
* **Session Packages** - Define different photography packages and pricing
* **Email Automation** - Automated booking confirmations and reminders
* **Client Portal** - Secure area for clients to view their sessions and photos

**Perfect For:**
* Portrait photographers
* Wedding photographers  
* Family photographers
* Commercial studios
* Event photographers

**Pro Version Features:**
* Advanced scheduling and calendar management
* Payment processing integration
* Contract management and digital signatures
* Advanced analytics and reporting
* Multi-photographer management
* Custom branding options

== Installation ==

1. Upload the plugin files to the \`/wp-content/plugins/studiosnap\` directory
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the StudioSnap menu to configure your studio settings
4. Add booking forms to your pages using shortcodes

== Frequently Asked Questions ==

= Can I accept online bookings? =
Yes! StudioSnap includes a complete booking system that integrates with your WordPress site.

= Does it work with any WordPress theme? =
Yes, StudioSnap is designed to work with any properly coded WordPress theme.

= Can I customize the booking form? =
Yes, the booking forms are fully customizable and can be styled to match your site.

== Screenshots ==

1. Studio dashboard with booking overview
2. Client booking form
3. Session management interface
4. Client gallery view
5. Studio settings panel

== Changelog ==

= 1.0.0 =
* Initial release
* Core booking system
* Client management
* Gallery functionality
* Email automation

== Upgrade Notice ==

= 1.0.0 =
Initial release of StudioSnap photography studio management system.
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