#!/usr/bin/env node

/**
 * GymFlow Deployment Script
 * 
 * Handles deployment to WordPress.org SVN repository and pro distribution
 * 
 * Usage:
 * node deploy.js --type=free --version=1.0.0
 * node deploy.js --type=pro --version=1.0.0
 * 
 * @package GymFlow
 * @version 1.0.0
 */

const fs = require('fs-extra');
const path = require('path');
const { execSync } = require('child_process');

class GymFlowDeployer {
    constructor() {
        this.sourceDir = path.resolve(__dirname, '..');
        this.args = this.parseArguments();
        
        // Configuration
        this.config = {
            wpOrgSlug: 'gymflow',
            wpOrgSvnUrl: 'https://plugins.svn.wordpress.org/gymflow',
            proDistributionUrl: 'https://vireodesigns.com/api/releases',
            tempDir: path.resolve(this.sourceDir, 'temp-deploy'),
            
            // WordPress.org required files
            requiredFiles: [
                'readme.txt',
                'gymflow.php',
                'uninstall.php'
            ],
            
            // Files to exclude from WordPress.org
            excludeFromWpOrg: [
                'build-scripts/',
                'dist/',
                'pro/',
                'node_modules/',
                'tests/',
                '.git/',
                '.github/',
                'composer.json',
                'package.json',
                'package-lock.json',
                'webpack.config.js',
                'phpunit.xml.dist',
                '.gitignore',
                '.eslintrc',
                '.editorconfig'
            ]
        };
        
        console.log('🚀 GymFlow Deployment Script');
        console.log('Type:', this.args.type);
        console.log('Version:', this.args.version);
    }

    parseArguments() {
        const args = process.argv.slice(2);
        const parsed = {};
        
        args.forEach(arg => {
            if (arg.startsWith('--')) {
                const [key, value] = arg.substring(2).split('=');
                parsed[key] = value;
            }
        });
        
        // Validate required arguments
        if (!parsed.type || !['free', 'pro'].includes(parsed.type)) {
            console.error('❌ --type is required (free or pro)');
            process.exit(1);
        }
        
        if (!parsed.version || !/^\d+\.\d+\.\d+$/.test(parsed.version)) {
            console.error('❌ --version is required (format: x.y.z)');
            process.exit(1);
        }
        
        return parsed;
    }

    async deploy() {
        try {
            await this.validateEnvironment();
            await this.cleanTemp();
            
            if (this.args.type === 'free') {
                await this.deployFree();
            } else {
                await this.deployPro();
            }
            
            await this.cleanTemp();
            
            console.log('✅ Deployment completed successfully!');
            
        } catch (error) {
            console.error('❌ Deployment failed:', error.message);
            await this.cleanTemp();
            process.exit(1);
        }
    }

    async validateEnvironment() {
        console.log('🔍 Validating environment...');
        
        // Check if git is clean
        try {
            const gitStatus = execSync('git status --porcelain', { cwd: this.sourceDir, encoding: 'utf8' });
            if (gitStatus.trim()) {
                throw new Error('Git working directory is not clean. Please commit all changes before deploying.');
            }
        } catch (error) {
            console.warn('⚠️ Git status check failed:', error.message);
        }
        
        // Check if version exists in changelog
        const readmePath = path.join(this.sourceDir, 'readme.txt');
        if (await fs.pathExists(readmePath)) {
            const readmeContent = await fs.readFile(readmePath, 'utf8');
            if (!readmeContent.includes(`= ${this.args.version} =`)) {
                throw new Error(`Version ${this.args.version} not found in changelog. Please update readme.txt first.`);
            }
        }
        
        // Check required tools for WordPress.org deployment
        if (this.args.type === 'free') {
            try {
                execSync('svn --version', { stdio: 'pipe' });
            } catch (error) {
                throw new Error('SVN is required for WordPress.org deployment. Please install Subversion.');
            }
        }
    }

    async cleanTemp() {
        if (await fs.pathExists(this.config.tempDir)) {
            await fs.remove(this.config.tempDir);
        }
    }

    async deployFree() {
        console.log('🔄 Deploying free version to WordPress.org...');
        
        // Build free version first
        console.log('📦 Building free version...');
        const FreeBuild = require('./build-free.js');
        const freeBuild = new FreeBuild();
        await freeBuild.build();
        
        // Prepare SVN working directory
        await this.prepareSvnRepo();
        
        // Copy built free version to SVN
        await this.copyToSvn();
        
        // Update SVN and create tag
        await this.updateSvn();
        
        console.log('✅ Free version deployed to WordPress.org!');
    }

    async deployPro() {
        console.log('🔄 Deploying pro version...');
        
        // Build pro version first
        console.log('📦 Building pro version...');
        const ProBuild = require('./build-pro.js');
        const proBuild = new ProBuild();
        await proBuild.build();
        
        // Upload to pro distribution server
        await this.uploadProVersion();
        
        console.log('✅ Pro version deployed!');
    }

    async prepareSvnRepo() {
        console.log('📥 Preparing SVN repository...');
        
        await fs.ensureDir(this.config.tempDir);
        
        // Checkout or update SVN repo
        const svnDir = path.join(this.config.tempDir, 'svn');
        
        if (await fs.pathExists(svnDir)) {
            console.log('🔄 Updating existing SVN checkout...');
            execSync('svn update', { cwd: svnDir, stdio: 'inherit' });
        } else {
            console.log('📥 Checking out SVN repository...');
            execSync(`svn checkout ${this.config.wpOrgSvnUrl} svn`, { 
                cwd: this.config.tempDir, 
                stdio: 'inherit' 
            });
        }
        
        return svnDir;
    }

    async copyToSvn() {
        console.log('📋 Copying files to SVN...');
        
        const svnDir = path.join(this.config.tempDir, 'svn');
        const freeBuildDir = path.join(this.sourceDir, 'dist', 'free', 'gymflow');
        const trunkDir = path.join(svnDir, 'trunk');
        
        // Clear trunk
        if (await fs.pathExists(trunkDir)) {
            await fs.emptyDir(trunkDir);
        } else {
            await fs.ensureDir(trunkDir);
        }
        
        // Copy built free version to trunk
        await fs.copy(freeBuildDir, trunkDir, {
            filter: (src) => {
                // Skip excluded files
                const relativePath = path.relative(freeBuildDir, src);
                return !this.config.excludeFromWpOrg.some(exclude => 
                    relativePath.startsWith(exclude)
                );
            }
        });
        
        console.log('✅ Files copied to SVN trunk');
    }

    async updateSvn() {
        console.log('📤 Updating SVN repository...');
        
        const svnDir = path.join(this.config.tempDir, 'svn');
        const tagDir = path.join(svnDir, 'tags', this.args.version);
        
        // Add any new files
        try {
            execSync('svn add --force .', { cwd: svnDir, stdio: 'pipe' });
        } catch (error) {
            // Ignore errors for files that are already added
        }
        
        // Remove any deleted files
        try {
            const deletedFiles = execSync('svn status | grep "^!" | awk "{print $2}"', { 
                cwd: svnDir, 
                encoding: 'utf8' 
            });
            
            if (deletedFiles.trim()) {
                execSync(`svn remove ${deletedFiles.trim().split('\n').join(' ')}`, { 
                    cwd: svnDir, 
                    stdio: 'inherit' 
                });
            }
        } catch (error) {
            // Ignore if no deleted files
        }
        
        // Commit to trunk
        const commitMessage = `Update to version ${this.args.version}`;
        execSync(`svn commit -m "${commitMessage}"`, { 
            cwd: svnDir, 
            stdio: 'inherit' 
        });
        
        // Create tag
        if (await fs.pathExists(tagDir)) {
            console.log(`⚠️ Tag ${this.args.version} already exists, skipping tag creation`);
        } else {
            await fs.ensureDir(path.dirname(tagDir));
            await fs.copy(path.join(svnDir, 'trunk'), tagDir);
            
            execSync('svn add --force .', { cwd: svnDir, stdio: 'pipe' });
            execSync(`svn commit -m "Tag version ${this.args.version}"`, { 
                cwd: svnDir, 
                stdio: 'inherit' 
            });
            
            console.log(`✅ Created tag ${this.args.version}`);
        }
    }

    async uploadProVersion() {
        console.log('📤 Uploading pro version...');
        
        const proZipPath = path.join(this.sourceDir, 'dist', 'gymflow-pro.zip');
        
        if (!(await fs.pathExists(proZipPath))) {
            throw new Error('Pro version ZIP not found. Run build-pro.js first.');
        }
        
        // Here you would implement the actual upload to your distribution server
        // This is a placeholder for the actual implementation
        console.log('📦 Pro version ZIP ready for upload:', proZipPath);
        
        // Example upload implementation (adjust for your server):
        /*
        const FormData = require('form-data');
        const fetch = require('node-fetch');
        
        const form = new FormData();
        form.append('file', fs.createReadStream(proZipPath));
        form.append('version', this.args.version);
        form.append('product', 'gymflow-pro');
        
        const response = await fetch(this.config.proDistributionUrl, {
            method: 'POST',
            body: form,
            headers: {
                'Authorization': `Bearer ${process.env.VIREO_API_TOKEN}`
            }
        });
        
        if (!response.ok) {
            throw new Error(`Upload failed: ${response.statusText}`);
        }
        */
        
        console.log('✅ Pro version ready for distribution');
    }
}

// Run the deployer
if (require.main === module) {
    const deployer = new GymFlowDeployer();
    deployer.deploy();
}

module.exports = GymFlowDeployer;