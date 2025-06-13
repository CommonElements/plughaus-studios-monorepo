#!/usr/bin/env node

const fs = require('fs-extra');
const path = require('path');
const { execSync } = require('child_process');
const archiver = require('archiver');

console.log('🔧 Building Vireo Property Management - FREE VERSION');

// Configuration
const ROOT_DIR = path.resolve(__dirname, '../');
const BUILD_DIR = path.resolve(ROOT_DIR, '../../dist/free');
const PLUGIN_NAME = 'vireo-property-management';

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
            path.join(ROOT_DIR, 'vireo-property-management.php'),
            path.join(pluginDir, 'vireo-property-management.php')
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
        await modifyMainPluginFile(path.join(pluginDir, 'vireo-property-management.php'));
        
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
    
    // Remove pro features
    content = content.replace(/\/\/ Pro Features[\s\S]*?\/\/ End Pro Features/g, '');
    
    // Update plugin header
    content = content.replace(/Plugin Name:.*$/m, 'Plugin Name: Vireo Property Management');
    content = content.replace(/Plugin URI:.*$/m, 'Plugin URI: https://vireodesigns.com/plugins/property-management');
    content = content.replace(/Author:.*$/m, 'Author: Vireo Designs');
    content = content.replace(/Author URI:.*$/m, 'Author URI: https://vireodesigns.com');
    
    await fs.writeFile(filePath, content);
}

async function createWordPressOrgReadme(filePath) {
    const readmeContent = `=== Vireo Property Management ===
Contributors: vireodesigns
Tags: property management, real estate, rental, landlord, tenant
Requires at least: 5.8
Tested up to: 6.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Professional property management solution for WordPress.

== Description ==

Vireo Property Management is a comprehensive yet lightweight property management plugin for WordPress. Perfect for small to medium-sized property managers, landlords, and real estate professionals.

== Installation ==

1. Upload the plugin files to the \`/wp-content/plugins/vireo-property-management\` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Vireo Property Management screen to configure the plugin
4. Make sure you save your settings

== Changelog ==

= 1.0.0 =
* Initial release of Vireo Property Management.
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