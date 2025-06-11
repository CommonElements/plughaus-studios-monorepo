#!/usr/bin/env node

const fs = require('fs-extra');
const path = require('path');
const { execSync } = require('child_process');

console.log('🏆 Building Vireo Sports League Manager - FREE VERSION');

// Configuration
const ROOT_DIR = path.resolve(__dirname, '..');
const BUILD_DIR = path.join(ROOT_DIR, 'dist', 'free');
const PLUGIN_NAME = 'vireo-sports-league';

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
            path.join(ROOT_DIR, 'vireo-sports-league.php'),
            path.join(pluginDir, 'vireo-sports-league.php')
        );
        
        // Copy core directory
        await fs.copy(
            path.join(ROOT_DIR, 'core'),
            path.join(pluginDir, 'core')
        );
        
        // Copy package files if they exist
        const packageJsonPath = path.join(ROOT_DIR, 'package.json');
        if (await fs.pathExists(packageJsonPath)) {
            await fs.copy(packageJsonPath, path.join(pluginDir, 'package.json'));
        }
        
        const composerJsonPath = path.join(ROOT_DIR, 'composer.json');
        if (await fs.pathExists(composerJsonPath)) {
            await fs.copy(composerJsonPath, path.join(pluginDir, 'composer.json'));
        }
        
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
        
        // Modify main plugin file to remove pro features
        console.log('✂️  Removing pro features from main plugin file...');
        await modifyMainPluginFile(path.join(pluginDir, 'vireo-sports-league.php'));
        
        // Build assets (if any)
        console.log('🔨 Building assets...');
        try {
            if (await fs.pathExists(path.join(pluginDir, 'package.json'))) {
                process.chdir(pluginDir);
                execSync('npm run build', { stdio: 'inherit' });
            }
        } catch (error) {
            console.log('⚠️  Asset build failed or not configured, continuing...');
        } finally {
            process.chdir(ROOT_DIR);
        }
        
        // Remove build dependencies from package.json if it exists
        if (await fs.pathExists(path.join(pluginDir, 'package.json'))) {
            console.log('🧹 Cleaning package.json for distribution...');
            await cleanPackageJsonForDistribution(path.join(pluginDir, 'package.json'));
        }
        
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
        /private function check_pro_license\(\)\s*\{[\s\S]*?\n\s*\}/,
        `private function check_pro_license() {
        // Pro features not available in free version
        return false;
    }`
    );
    
    // Remove pro feature loading
    content = content.replace(
        /private function load_pro_features\(\)\s*\{[\s\S]*?\n\s*\}/,
        `private function load_pro_features() {
        // Pro features not available in free version
        return;
    }`
    );
    
    // Remove pro-specific constants and directories
    content = content.replace(/define\('VIREO_LEAGUE_PRO_DIR'.*?\);/, '// Pro features not available in free version');
    
    await fs.writeFile(filePath, content);
}

async function cleanPackageJsonForDistribution(filePath) {
    const packageJson = await fs.readJson(filePath);
    
    // Remove build scripts not needed for distribution
    if (packageJson.scripts) {
        delete packageJson.scripts['build:free'];
        delete packageJson.scripts['build:pro'];
    }
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