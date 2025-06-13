#!/usr/bin/env node

const fs = require('fs-extra');
const path = require('path');
const { execSync } = require('child_process');

console.log('🔧 Building DealerEdge - FREE VERSION');

// Configuration
const ROOT_DIR = path.resolve(__dirname, '..');
const BUILD_DIR = path.join(ROOT_DIR, 'dist', 'free');
const PLUGIN_NAME = 'dealeredge';

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
            path.join(ROOT_DIR, 'dealeredge.php'),
            path.join(pluginDir, 'dealeredge.php')
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
        
        // Copy readme for WordPress.org
        await fs.copy(
            path.join(ROOT_DIR, 'readme.txt'),
            path.join(pluginDir, 'readme.txt')
        );
        
        // Copy uninstall.php
        await fs.copy(
            path.join(ROOT_DIR, 'uninstall.php'),
            path.join(pluginDir, 'uninstall.php')
        );
        
        // Create basic assets directories
        await fs.ensureDir(path.join(pluginDir, 'core', 'assets', 'css'));
        await fs.ensureDir(path.join(pluginDir, 'core', 'assets', 'js'));
        
        // Create basic CSS file
        await fs.writeFile(
            path.join(pluginDir, 'core', 'assets', 'css', 'admin.css'),
            '/* DealerEdge Admin Styles */\n.dealeredge-dashboard { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }\n'
        );
        
        // Create basic JS file
        await fs.writeFile(
            path.join(pluginDir, 'core', 'assets', 'js', 'admin.js'),
            '/* DealerEdge Admin Scripts */\njQuery(document).ready(function($) {\n    console.log("DealerEdge Admin Loaded");\n});\n'
        );
        
        // Create basic public CSS file
        await fs.writeFile(
            path.join(pluginDir, 'core', 'assets', 'css', 'public.css'),
            '/* DealerEdge Public Styles */\n.dealeredge-inventory { margin: 20px 0; }\n'
        );
        
        // Create basic public JS file
        await fs.writeFile(
            path.join(pluginDir, 'core', 'assets', 'js', 'public.js'),
            '/* DealerEdge Public Scripts */\njQuery(document).ready(function($) {\n    console.log("DealerEdge Public Loaded");\n});\n'
        );
        
        // Clean package.json for distribution
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

async function cleanPackageJsonForDistribution(filePath) {
    const packageJson = await fs.readJson(filePath);
    
    // Remove build scripts not needed for distribution
    delete packageJson.scripts['build:free'];
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