#!/usr/bin/env node

/**
 * GymFlow Free Version Build Script
 * 
 * Creates WordPress.org compliant free version by:
 * 1. Copying core functionality only
 * 2. Removing pro directories and references
 * 3. Modifying plugin file to disable pro features
 * 4. Creating distribution ZIP file
 * 
 * @package GymFlow
 * @version 1.0.0
 */

const fs = require('fs-extra');
const path = require('path');
const archiver = require('archiver');
const { execSync } = require('child_process');

class GymFlowFreeBuild {
    constructor() {
        this.sourceDir = path.resolve(__dirname, '..');
        this.buildDir = path.resolve(this.sourceDir, 'dist', 'free');
        this.zipPath = path.resolve(this.sourceDir, 'dist', 'gymflow-free.zip');
        
        console.log('🏋️ GymFlow Free Version Builder');
        console.log('Source:', this.sourceDir);
        console.log('Build:', this.buildDir);
        console.log('Zip:', this.zipPath);
    }

    async build() {
        try {
            await this.cleanBuild();
            await this.copyFiles();
            await this.modifyPluginFile();
            await this.removeProFeatures();
            await this.updateReadme();
            await this.compileAssets();
            await this.createZip();
            
            console.log('✅ GymFlow Free version built successfully!');
            console.log(`📦 ZIP created: ${this.zipPath}`);
            
        } catch (error) {
            console.error('❌ Build failed:', error.message);
            process.exit(1);
        }
    }

    async cleanBuild() {
        console.log('🧹 Cleaning previous build...');
        await fs.remove(this.buildDir);
        await fs.ensureDir(this.buildDir);
        
        if (await fs.pathExists(this.zipPath)) {
            await fs.remove(this.zipPath);
        }
    }

    async copyFiles() {
        console.log('📋 Copying files...');
        
        const filesToCopy = [
            'gymflow.php',
            'readme.txt',
            'uninstall.php',
            'composer.json',
            'package.json',
            'core/',
            'assets/',
            'languages/'
        ];

        for (const file of filesToCopy) {
            const src = path.join(this.sourceDir, file);
            const dest = path.join(this.buildDir, 'gymflow', file);
            
            if (await fs.pathExists(src)) {
                await fs.copy(src, dest, {
                    filter: (src) => {
                        // Skip pro-related files
                        return !src.includes('pro/') && 
                               !src.includes('-pro.') && 
                               !src.includes('.pro.');
                    }
                });
            }
        }
    }

    async modifyPluginFile() {
        console.log('✏️ Modifying main plugin file for free version...');
        
        const pluginFile = path.join(this.buildDir, 'gymflow', 'gymflow.php');
        let content = await fs.readFile(pluginFile, 'utf8');
        
        // Update plugin header for free version
        content = content.replace(
            /Plugin Name:\s*GymFlow/,
            'Plugin Name: GymFlow'
        );
        
        content = content.replace(
            /Description:\s*.*/,
            'Description: Professional fitness studio management for WordPress. Manage members, classes, trainers, and equipment booking. Free version with core features.'
        );
        
        // Modify check_pro_license method to always return false
        content = content.replace(
            /private function check_pro_license\(\)\s*\{[\s\S]*?\}/,
            `private function check_pro_license() {
        // Free version - pro features not available
        return false;
    }`
        );
        
        // Remove pro feature initialization
        content = content.replace(
            /\/\/ Initialize pro features.*?[\s\S]*?}\s*}/,
            '// Pro features not available in free version'
        );
        
        // Update version for free release
        content = content.replace(
            /Version:\s*[\d.]+/,
            'Version: 1.0.0'
        );
        
        await fs.writeFile(pluginFile, content);
    }

    async removeProFeatures() {
        console.log('🚫 Removing pro features...');
        
        // Remove pro directory if it was copied
        const proDir = path.join(this.buildDir, 'gymflow', 'pro');
        if (await fs.pathExists(proDir)) {
            await fs.remove(proDir);
        }
        
        // Remove build scripts
        const buildScriptsDir = path.join(this.buildDir, 'gymflow', 'build-scripts');
        if (await fs.pathExists(buildScriptsDir)) {
            await fs.remove(buildScriptsDir);
        }
        
        // Remove dist directory
        const distDir = path.join(this.buildDir, 'gymflow', 'dist');
        if (await fs.pathExists(distDir)) {
            await fs.remove(distDir);
        }
    }

    async updateReadme() {
        console.log('📝 Updating readme for free version...');
        
        const readmeFile = path.join(this.buildDir, 'gymflow', 'readme.txt');
        let content = await fs.readFile(readmeFile, 'utf8');
        
        // Remove pro features from description
        content = content.replace(
            /= Pro Features \(\$149\/year\) =[\s\S]*?= Why Choose GymFlow\? =/,
            '= Pro Features Available =\n\nUpgrade to GymFlow Pro for advanced features:\n* Automated billing and payment processing\n* Member mobile app with check-in\n* Advanced analytics and reporting\n* Trainer commission tracking\n* Equipment maintenance scheduling\n* Email automation\n* Priority support\n\nVisit [vireodesigns.com](https://vireodesigns.com) to learn more.\n\n= Why Choose GymFlow? ='
        );
        
        await fs.writeFile(readmeFile, content);
    }

    async compileAssets() {
        console.log('🔨 Compiling assets...');
        
        // Change to build directory
        const gymflowDir = path.join(this.buildDir, 'gymflow');
        
        try {
            // Install dependencies and build assets
            process.chdir(gymflowDir);
            
            if (await fs.pathExists('package.json')) {
                console.log('📦 Installing dependencies...');
                execSync('npm install --silent', { stdio: 'pipe' });
                
                console.log('🔨 Building assets...');
                execSync('npm run build', { stdio: 'pipe' });
                
                // Remove node_modules and dev files
                await fs.remove('node_modules');
                await fs.remove('package-lock.json');
                
                // Update package.json for production
                const packageJson = await fs.readJson('package.json');
                delete packageJson.devDependencies;
                delete packageJson.scripts.dev;
                delete packageJson.scripts.start;
                delete packageJson.scripts.watch;
                await fs.writeJson('package.json', packageJson, { spaces: 2 });
            }
        } catch (error) {
            console.warn('⚠️ Asset compilation skipped:', error.message);
        } finally {
            // Return to original directory
            process.chdir(this.sourceDir);
        }
    }

    async createZip() {
        console.log('📦 Creating ZIP file...');
        
        return new Promise((resolve, reject) => {
            const output = fs.createWriteStream(this.zipPath);
            const archive = archiver('zip', { zlib: { level: 9 } });
            
            output.on('close', () => {
                const sizeInMB = (archive.pointer() / 1024 / 1024).toFixed(2);
                console.log(`📊 ZIP size: ${sizeInMB} MB`);
                resolve();
            });
            
            archive.on('error', (err) => {
                reject(err);
            });
            
            archive.pipe(output);
            archive.directory(path.join(this.buildDir, 'gymflow'), 'gymflow');
            archive.finalize();
        });
    }
}

// Run the build
if (require.main === module) {
    const builder = new GymFlowFreeBuild();
    builder.build();
}

module.exports = GymFlowFreeBuild;