#!/usr/bin/env node

/**
 * GymFlow Pro Version Build Script
 * 
 * Creates Pro version distribution by:
 * 1. Copying all functionality (core + pro)
 * 2. Enabling pro features and license checking
 * 3. Modifying plugin file for pro functionality
 * 4. Creating distribution ZIP file
 * 
 * @package GymFlow
 * @version 1.0.0
 */

const fs = require('fs-extra');
const path = require('path');
const archiver = require('archiver');
const { execSync } = require('child_process');

class GymFlowProBuild {
    constructor() {
        this.sourceDir = path.resolve(__dirname, '..');
        this.buildDir = path.resolve(this.sourceDir, 'dist', 'pro');
        this.zipPath = path.resolve(this.sourceDir, 'dist', 'gymflow-pro.zip');
        
        console.log('🏋️‍♂️ GymFlow Pro Version Builder');
        console.log('Source:', this.sourceDir);
        console.log('Build:', this.buildDir);
        console.log('Zip:', this.zipPath);
    }

    async build() {
        try {
            await this.cleanBuild();
            await this.copyFiles();
            await this.modifyPluginFile();
            await this.updateReadme();
            await this.compileAssets();
            await this.createZip();
            
            console.log('✅ GymFlow Pro version built successfully!');
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
            'pro/',
            'assets/',
            'languages/'
        ];

        for (const file of filesToCopy) {
            const src = path.join(this.sourceDir, file);
            const dest = path.join(this.buildDir, 'gymflow-pro', file);
            
            if (await fs.pathExists(src)) {
                await fs.copy(src, dest, {
                    filter: (src) => {
                        // Skip build directories and dev files
                        return !src.includes('node_modules/') && 
                               !src.includes('dist/') &&
                               !src.includes('build-scripts/') &&
                               !src.includes('.git/') &&
                               !src.includes('.DS_Store');
                    }
                });
            }
        }
    }

    async modifyPluginFile() {
        console.log('✏️ Modifying main plugin file for pro version...');
        
        const pluginFile = path.join(this.buildDir, 'gymflow-pro', 'gymflow.php');
        let content = await fs.readFile(pluginFile, 'utf8');
        
        // Update plugin header for pro version
        content = content.replace(
            /Plugin Name:\s*GymFlow/,
            'Plugin Name: GymFlow Pro'
        );
        
        content = content.replace(
            /Description:\s*.*/,
            'Description: Professional fitness studio management for WordPress with advanced pro features. Complete solution for gym management, member billing, analytics, and automation.'
        );
        
        // Enable pro license checking
        content = content.replace(
            /private function check_pro_license\(\)\s*\{[\s\S]*?\}/,
            `private function check_pro_license() {
        // Pro version - check license status
        if (!class_exists('GF_License_Manager')) {
            require_once $this->plugin_path . 'pro/includes/licensing/class-gf-license-manager.php';
        }
        
        return GF_License_Manager::is_valid();
    }`
        );
        
        // Enable pro feature initialization
        content = content.replace(
            /\/\/ Initialize pro features.*?[\s\S]*?}\s*}/,
            `// Initialize pro features
        if ($this->check_pro_license()) {
            $this->load_pro_features();
        }`
        );
        
        // Update version for pro release
        content = content.replace(
            /Version:\s*[\d.]+/,
            'Version: 1.0.0'
        );
        
        // Add pro feature loading method if not exists
        if (!content.includes('load_pro_features')) {
            const loadProMethod = `
    /**
     * Load pro features
     */
    private function load_pro_features() {
        // Load pro includes
        $pro_includes = array(
            'licensing/class-gf-license-manager.php',
            'analytics/class-gf-analytics.php',
            'automation/class-gf-payment-automation.php',
            'automation/class-gf-email-automation.php',
            'reporting/class-gf-advanced-reports.php',
            'membership/class-gf-membership-management.php'
        );
        
        foreach ($pro_includes as $file) {
            $file_path = $this->plugin_path . 'pro/includes/' . $file;
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }
        
        // Initialize pro components
        if (class_exists('GF_Analytics')) {
            new GF_Analytics();
        }
        
        if (class_exists('GF_Payment_Automation')) {
            new GF_Payment_Automation();
        }
        
        if (class_exists('GF_Email_Automation')) {
            new GF_Email_Automation();
        }
        
        if (class_exists('GF_Advanced_Reports')) {
            new GF_Advanced_Reports();
        }
        
        if (class_exists('GF_Membership_Management')) {
            new GF_Membership_Management();
        }
    }`;
            
            // Insert before the last closing brace
            content = content.replace(/(\s+)}\s*$/, loadProMethod + '$1}');
        }
        
        await fs.writeFile(pluginFile, content);
    }

    async updateReadme() {
        console.log('📝 Updating readme for pro version...');
        
        const readmeFile = path.join(this.buildDir, 'gymflow-pro', 'readme.txt');
        let content = await fs.readFile(readmeFile, 'utf8');
        
        // Update plugin name in readme
        content = content.replace(
            /=== GymFlow - Fitness Studio Management ===/,
            '=== GymFlow Pro - Complete Fitness Studio Management ==='
        );
        
        // Update description
        content = content.replace(
            /Professional fitness studio and gym management plugin for WordPress\. Manage members, classes, trainers, equipment, and bookings with ease\./,
            'Complete professional fitness studio management solution for WordPress. Includes all pro features: automated billing, advanced analytics, member mobile app, trainer tools, and comprehensive reporting.'
        );
        
        // Move pro features to main features section
        content = content.replace(
            /= Core Features \(Free\) =/,
            '= Complete Pro Features ='
        );
        
        // Remove the separate Pro Features section
        content = content.replace(
            /= Pro Features \(\$149\/year\) =[\s\S]*?= Why Choose GymFlow\? =/,
            '= Why Choose GymFlow Pro? ='
        );
        
        // Update the pricing info
        content = content.replace(
            /Most fitness software charges \$100-300 per month\. GymFlow Pro is just \$149 per year - a fraction of the cost with no monthly fees\./,
            'GymFlow Pro includes all features for just $149 per year - no monthly fees, no per-member charges, unlimited usage.'
        );
        
        await fs.writeFile(readmeFile, content);
    }

    async compileAssets() {
        console.log('🔨 Compiling assets...');
        
        // Change to build directory
        const gymflowDir = path.join(this.buildDir, 'gymflow-pro');
        
        try {
            // Install dependencies and build assets
            process.chdir(gymflowDir);
            
            if (await fs.pathExists('package.json')) {
                console.log('📦 Installing dependencies...');
                execSync('npm install --silent', { stdio: 'pipe' });
                
                console.log('🔨 Building assets...');
                execSync('npm run build', { stdio: 'pipe' });
                
                // Remove dev dependencies and files for distribution
                await fs.remove('node_modules');
                await fs.remove('package-lock.json');
                
                // Update package.json for production
                const packageJson = await fs.readJson('package.json');
                packageJson.name = 'gymflow-pro';
                packageJson.description = 'Professional fitness studio management plugin for WordPress - Pro Version';
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
            archive.directory(path.join(this.buildDir, 'gymflow-pro'), 'gymflow-pro');
            archive.finalize();
        });
    }
}

// Run the build
if (require.main === module) {
    const builder = new GymFlowProBuild();
    builder.build();
}

module.exports = GymFlowProBuild;