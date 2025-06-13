#!/usr/bin/env node

/**
 * Build StudioSnap Free Version for WordPress.org
 */

const fs = require('fs-extra');
const path = require('path');
const archiver = require('archiver');

const pluginDir = path.resolve(__dirname, '..');
const distDir = path.join(pluginDir, 'dist');
const freeDir = path.join(distDir, 'free');
const buildDir = path.join(freeDir, 'studiosnap');

console.log('🔨 Building StudioSnap Free Version for WordPress.org...');

// Clean and create directories
fs.removeSync(distDir);
fs.ensureDirSync(buildDir);

console.log('📁 Copying core files...');

// Files to include in free version
const filesToCopy = [
    'studiosnap.php',
    'readme.txt',
    'uninstall.php'
];

// Directories to copy
const dirsToCopy = [
    'core',
    'assets'
];

// Copy main files
filesToCopy.forEach(file => {
    const srcPath = path.join(pluginDir, file);
    const destPath = path.join(buildDir, file);
    
    if (fs.existsSync(srcPath)) {
        fs.copySync(srcPath, destPath);
        console.log(`✅ Copied ${file}`);
    } else {
        console.log(`⚠️ Missing ${file}`);
    }
});

// Copy directories
dirsToCopy.forEach(dir => {
    const srcPath = path.join(pluginDir, dir);
    const destPath = path.join(buildDir, dir);
    
    if (fs.existsSync(srcPath)) {
        fs.copySync(srcPath, destPath);
        console.log(`✅ Copied ${dir}/`);
    } else {
        console.log(`⚠️ Missing ${dir}/`);
    }
});

console.log('📝 Modifying plugin header...');

// Modify main plugin file for free version
const mainPluginFile = path.join(buildDir, 'studiosnap.php');
if (fs.existsSync(mainPluginFile)) {
    let content = fs.readFileSync(mainPluginFile, 'utf8');
    
    // Update plugin header for WordPress.org
    content = content.replace(
        /Plugin Name: StudioSnap - Photography Studio Management/,
        'Plugin Name: StudioSnap - Photography Studio Management (Free)'
    );
    
    content = content.replace(
        /Description: Complete photography studio management system for WordPress\./,
        'Description: Free photography studio management system with booking calendar, client management, and session tracking for WordPress.'
    );
    
    // Ensure pro features are disabled in free version
    content = content.replace(
        /\/\/ TODO: For free version, this should return false/,
        'return false; // Free version'
    );
    
    fs.writeFileSync(mainPluginFile, content);
    console.log('✅ Updated plugin header for free version');
}

console.log('🗜️ Creating ZIP package...');

// Create ZIP file
const zipPath = path.join(freeDir, 'studiosnap-free.zip');
const output = fs.createWriteStream(zipPath);
const archive = archiver('zip', { zlib: { level: 9 } });

output.on('close', () => {
    const size = (archive.pointer() / 1024 / 1024).toFixed(2);
    console.log(`✅ ZIP created: ${zipPath} (${size} MB)`);
    console.log('');
    console.log('🎯 WordPress.org Submission Ready!');
    console.log('📦 Package: ' + zipPath);
    console.log('📋 Next: Upload to WordPress.org plugin repository');
    console.log('🚀 StudioSnap is ready for marketplace submission!');
});

archive.on('error', (err) => {
    console.error('❌ ZIP creation failed:', err);
});

archive.pipe(output);
archive.directory(buildDir, 'studiosnap');
archive.finalize();