#!/usr/bin/env node

const fs = require('fs-extra');
const path = require('path');
const { execSync } = require('child_process');

console.log('🔧 Building EquipRent Pro - FREE VERSION');

// Configuration
const ROOT_DIR = path.resolve(__dirname, '..');
const BUILD_DIR = path.join(ROOT_DIR, 'dist', 'free');
const PLUGIN_NAME = 'equiprent-pro';

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
            path.join(ROOT_DIR, 'equiprent-pro.php'),
            path.join(pluginDir, 'equiprent-pro.php')
        );
        
        // Copy core directory
        await fs.copy(
            path.join(ROOT_DIR, 'core'),
            path.join(pluginDir, 'core')
        );
        
        // Copy assets directory if it exists
        const assetsPath = path.join(ROOT_DIR, 'assets');
        if (await fs.pathExists(assetsPath)) {
            await fs.copy(assetsPath, path.join(pluginDir, 'assets'));
        }
        
        // Copy package files
        await fs.copy(
            path.join(ROOT_DIR, 'package.json'),
            path.join(pluginDir, 'package.json')
        );
        
        // Copy composer.json if it exists
        const composerPath = path.join(ROOT_DIR, 'composer.json');
        if (await fs.pathExists(composerPath)) {
            await fs.copy(composerPath, path.join(pluginDir, 'composer.json'));
        }
        
        // Copy readme for WordPress.org
        await fs.copy(
            path.join(ROOT_DIR, 'readme.txt'),
            path.join(pluginDir, 'readme.txt')
        );
        
        // Copy uninstall.php if it exists
        const uninstallPath = path.join(ROOT_DIR, 'uninstall.php');
        if (await fs.pathExists(uninstallPath)) {
            await fs.copy(uninstallPath, path.join(pluginDir, 'uninstall.php'));
        }
        
        // Copy .gitignore if exists
        const gitignorePath = path.join(ROOT_DIR, '.gitignore');
        if (await fs.pathExists(gitignorePath)) {
            await fs.copy(gitignorePath, path.join(pluginDir, '.gitignore'));
        }
        
        // Modify main plugin file to remove pro features
        console.log('✂️  Removing pro features from main plugin file...');
        await modifyMainPluginFile(path.join(pluginDir, 'equiprent-pro.php'));
        
        // Create WordPress.org compliant readme
        console.log('📝 Creating WordPress.org compliant readme...');
        await createWordPressOrgReadme(path.join(pluginDir, 'readme.txt'));
        
        // Build assets (if any)
        console.log('🔨 Building assets...');
        try {
            process.chdir(pluginDir);
            if (await fs.pathExists(path.join(pluginDir, 'package.json'))) {
                execSync('npm install --production', { stdio: 'inherit' });
                execSync('npm run build', { stdio: 'inherit' });
            }
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
    content = content.replace(/define\('ERP_PRO_DIR'.*?\);/, '// Pro features not available in free version');
    
    await fs.writeFile(filePath, content);
}

async function createWordPressOrgReadme(filePath) {
    // The readme.txt has already been copied and updated, so we don't need to recreate it
    // This function is kept for backward compatibility but doesn't need to do anything
    console.log('✅ Using existing updated readme.txt');
    return;
}

async function cleanPackageJsonForDistribution(filePath) {
    if (!(await fs.pathExists(filePath))) {
        return;
    }
    
    const packageJson = await fs.readJson(filePath);
    
    // Remove build scripts not needed for distribution
    if (packageJson.scripts) {
        delete packageJson.scripts['build:free'];
        delete packageJson.scripts['build:pro'];
        delete packageJson.devDependencies;
    }
    
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