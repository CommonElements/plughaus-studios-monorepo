#!/usr/bin/env node

const fs = require('fs-extra');
const path = require('path');
const { execSync } = require('child_process');

console.log('🔧 Building PlugHaus Property Management - FREE VERSION');

// Configuration
const ROOT_DIR = path.resolve(__dirname, '..');
const BUILD_DIR = path.join(ROOT_DIR, 'dist', 'free');
const PLUGIN_NAME = 'plughaus-property-management';

async function buildFreeVersion() {
    try {
        // Clean build directory
        console.log('🧹 Cleaning build directory...');
        await fs.remove(BUILD_DIR);
        await fs.ensureDir(BUILD_DIR);
        
        const pluginDir = path.join(BUILD_DIR, PLUGIN_NAME);
        await fs.ensureDir(pluginDir);
        
        // Copy core files only (no pro directory)
        console.log('📁 Copying core files...');
        
        // Copy main plugin file
        await fs.copy(
            path.join(ROOT_DIR, 'plughaus-property-management.php'),
            path.join(pluginDir, 'plughaus-property-management.php')
        );
        
        // Copy core directory
        await fs.copy(
            path.join(ROOT_DIR, 'core'),
            path.join(pluginDir, 'core')
        );
        
        // Copy package files
        await fs.copy(
            path.join(ROOT_DIR, 'package.json'),
            path.join(pluginDir, 'package.json')
        );
        
        await fs.copy(
            path.join(ROOT_DIR, 'composer.json'),
            path.join(pluginDir, 'composer.json')
        );
        
        // Copy readme for WordPress.org
        await fs.copy(
            path.join(ROOT_DIR, 'readme.txt'),
            path.join(pluginDir, 'readme.txt')
        );
        
        // Copy .gitignore if exists
        const gitignorePath = path.join(ROOT_DIR, '.gitignore');
        if (await fs.pathExists(gitignorePath)) {
            await fs.copy(gitignorePath, path.join(pluginDir, '.gitignore'));
        }
        
        // Modify main plugin file to remove pro features
        console.log('✂️  Removing pro features from main plugin file...');
        await modifyMainPluginFile(path.join(pluginDir, 'plughaus-property-management.php'));
        
        // Create WordPress.org compliant readme
        console.log('📝 Creating WordPress.org compliant readme...');
        await createWordPressOrgReadme(path.join(pluginDir, 'readme.txt'));
        
        // Build assets (if any)
        console.log('🔨 Building assets...');
        try {
            process.chdir(pluginDir);
            execSync('npm run build', { stdio: 'inherit' });
        } catch (error) {
            console.log('⚠️  Asset build failed, continuing without built assets');
        } finally {
            process.chdir(ROOT_DIR);
        }
        
        // Remove build dependencies from package.json
        console.log('🧹 Cleaning package.json for distribution...');
        await cleanPackageJsonForDistribution(path.join(pluginDir, 'package.json'));
        
        // Create ZIP file
        console.log('📦 Creating ZIP file...');
        const zipPath = path.join(BUILD_DIR, `${PLUGIN_NAME}-free-v1.0.0.zip`);
        await createZipFile(pluginDir, zipPath);
        
        console.log('✅ FREE version build complete!');
        console.log(`📦 ZIP file: ${zipPath}`);
        
    } catch (error) {
        console.error('❌ Build failed:', error);
        process.exit(1);
    }
}

async function modifyMainPluginFile(filePath) {
    let content = await fs.readFile(filePath, 'utf8');
    
    // Remove pro license checking
    content = content.replace(
        /\/\*\*\s*\n\s*\* Check if pro license is valid\s*\n\s*\*\/\s*\n\s*private function check_pro_license\(\)\s*\{[\s\S]*?\n\s*\}/,
        `/**
     * Check if pro license is valid
     */
    private function check_pro_license() {
        // Pro features not available in free version
        return false;
    }`
    );
    
    // Remove pro feature loading
    content = content.replace(
        /\/\*\*\s*\n\s*\* Load pro features[\s\S]*?\n\s*\}/,
        `/**
     * Load pro features (not available in free version)
     */
    private function load_pro_features() {
        // Pro features not available in free version
        return;
    }`
    );
    
    // Remove pro-specific constants and directories
    content = content.replace(/define\('PHPM_PRO_DIR'.*?\);/, '// Pro features not available in free version');
    
    await fs.writeFile(filePath, content);
}

async function createWordPressOrgReadme(filePath) {
    const readmeContent = `=== PlugHaus Property Management ===
Contributors: plughausstudios
Tags: property management, real estate, rental, landlord, tenant
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight, powerful property management solution for WordPress. Manage properties, tenants, leases, and maintenance with ease.

== Description ==

PlugHaus Property Management is a comprehensive yet lightweight property management plugin for WordPress. Perfect for small to medium-sized property managers, landlords, and real estate professionals.

**Features:**

* **Property Management** - Add and manage unlimited properties with detailed information
* **Unit Tracking** - Track individual units within properties
* **Tenant Management** - Maintain tenant records and contact information
* **Lease Management** - Track lease terms, dates, and rental amounts
* **Maintenance Requests** - Handle maintenance requests from tenants
* **Basic Reporting** - Generate simple reports on occupancy and finances
* **Import/Export** - Import property data via CSV and export for backup
* **REST API** - Full REST API for integrations and mobile apps

**Pro Features Available:**

Upgrade to Pro for advanced features including:
* Advanced Analytics Dashboard
* Automated Payment Processing
* Email Automation
* Advanced Reporting with Charts
* White-label Options
* Priority Support

== Installation ==

1. Upload the plugin files to the \`/wp-content/plugins/plughaus-property-management\` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Property Mgmt menu item to configure the plugin and start adding properties

== Frequently Asked Questions ==

= Is this plugin free? =

Yes! The core plugin is completely free and includes all basic property management features.

= What's included in the Pro version? =

Pro includes advanced analytics, payment automation, email automation, advanced reporting, and priority support.

= Can I import existing property data? =

Yes! The plugin includes CSV import functionality to help you migrate from other systems.

= Does it work with any theme? =

Yes! The plugin is designed to work with any properly coded WordPress theme.

== Screenshots ==

1. Property Management Dashboard
2. Add/Edit Property Screen
3. Tenant Management
4. Lease Tracking
5. Maintenance Requests

== Changelog ==

= 1.0.0 =
* Initial release
* Property, unit, tenant, and lease management
* Basic reporting and dashboard
* Import/export functionality
* REST API endpoints

== Upgrade Notice ==

= 1.0.0 =
Initial release of PlugHaus Property Management.
`;

    await fs.writeFile(filePath, readmeContent);
}

async function cleanPackageJsonForDistribution(filePath) {
    const packageJson = await fs.readJson(filePath);
    
    // Remove build scripts not needed for distribution
    delete packageJson.scripts['build:free'];
    delete packageJson.scripts['build:pro'];
    delete packageJson.devDependencies;
    
    await fs.writeJson(filePath, packageJson, { spaces: 2 });
}

async function createZipFile(sourceDir, zipPath) {
    const archiver = require('archiver');
    const output = fs.createWriteStream(zipPath);
    const archive = archiver('zip', { zlib: { level: 9 } });
    
    return new Promise((resolve, reject) => {
        output.on('close', resolve);
        archive.on('error', reject);
        
        archive.pipe(output);
        archive.directory(sourceDir, PLUGIN_NAME);
        archive.finalize();
    });
}

// Add archiver dependency check
async function checkDependencies() {
    try {
        require('archiver');
    } catch (error) {
        console.log('Installing required dependency: archiver');
        execSync('npm install archiver --save-dev', { stdio: 'inherit' });
    }
}

// Main execution
async function main() {
    await checkDependencies();
    await buildFreeVersion();
}

main();