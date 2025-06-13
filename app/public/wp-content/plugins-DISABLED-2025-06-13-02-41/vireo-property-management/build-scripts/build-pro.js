#!/usr/bin/env node

const fs = require('fs-extra');
const path = require('path');
const { execSync } = require('child_process');

console.log('🔧 Building PlugHaus Property Management - PRO VERSION');

// Configuration
const ROOT_DIR = path.resolve(__dirname, '..');
const BUILD_DIR = path.join(ROOT_DIR, 'dist', 'pro');
const PLUGIN_NAME = 'plughaus-property-management-pro';

async function buildProVersion() {
    try {
        // Clean build directory
        console.log('🧹 Cleaning build directory...');
        await fs.remove(BUILD_DIR);
        await fs.ensureDir(BUILD_DIR);
        
        const pluginDir = path.join(BUILD_DIR, PLUGIN_NAME);
        await fs.ensureDir(pluginDir);
        
        // Copy all files (core + pro)
        console.log('📁 Copying all files (core + pro)...');
        
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
        
        // Copy pro directory
        await fs.copy(
            path.join(ROOT_DIR, 'pro'),
            path.join(pluginDir, 'pro')
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
        
        // Copy .gitignore if exists
        const gitignorePath = path.join(ROOT_DIR, '.gitignore');
        if (await fs.pathExists(gitignorePath)) {
            await fs.copy(gitignorePath, path.join(pluginDir, '.gitignore'));
        }
        
        // Modify main plugin file for pro version
        console.log('🔧 Configuring pro version...');
        await modifyMainPluginFileForPro(path.join(pluginDir, 'plughaus-property-management.php'));
        
        // Create pro readme
        console.log('📝 Creating pro readme...');
        await createProReadme(path.join(pluginDir, 'readme.txt'));
        
        // Create pro assets directory
        await fs.ensureDir(path.join(pluginDir, 'pro', 'assets', 'css'));
        await fs.ensureDir(path.join(pluginDir, 'pro', 'assets', 'js'));
        
        // Create placeholder pro assets
        await createProAssets(pluginDir);
        
        // Build assets
        console.log('🔨 Building assets...');
        try {
            process.chdir(pluginDir);
            execSync('npm run build', { stdio: 'inherit' });
        } catch (error) {
            console.log('⚠️  Asset build failed, continuing without built assets');
        } finally {
            process.chdir(ROOT_DIR);
        }
        
        // Clean package.json for distribution
        console.log('🧹 Cleaning package.json for distribution...');
        await cleanPackageJsonForDistribution(path.join(pluginDir, 'package.json'));
        
        // Create ZIP file
        console.log('📦 Creating ZIP file...');
        const zipPath = path.join(BUILD_DIR, `${PLUGIN_NAME}-v1.0.0.zip`);
        await createZipFile(pluginDir, zipPath);
        
        console.log('✅ PRO version build complete!');
        console.log(`📦 ZIP file: ${zipPath}`);
        
    } catch (error) {
        console.error('❌ Build failed:', error);
        process.exit(1);
    }
}

async function modifyMainPluginFileForPro(filePath) {
    let content = await fs.readFile(filePath, 'utf8');
    
    // Update plugin header for pro version
    content = content.replace(
        'Plugin Name: PlugHaus Property Management',
        'Plugin Name: PlugHaus Property Management Pro'
    );
    
    content = content.replace(
        'Description: A lightweight, powerful property management solution for WordPress. Manage properties, tenants, leases, and maintenance with ease.',
        'Description: Professional property management solution with advanced analytics, payment automation, and premium features.'
    );
    
    // Enable pro license checking by default for development
    content = content.replace(
        /private function check_pro_license\(\)\s*\{[\s\S]*?\n\s*\}/,
        `private function check_pro_license() {
        // In pro version, check actual license
        return PHPM_License_Manager::is_valid();
    }`
    );
    
    await fs.writeFile(filePath, content);
}

async function createProReadme(filePath) {
    const readmeContent = `=== PlugHaus Property Management Pro ===
Version: 1.0.0
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
License: Commercial

Professional property management solution with advanced analytics, payment automation, and premium features.

== Description ==

PlugHaus Property Management Pro is the premium version of our property management plugin, designed for professional property managers and real estate businesses.

**Pro Features:**

* **Advanced Analytics Dashboard** - Beautiful charts and metrics
* **Payment Automation** - Automated rent collection and reminders
* **Email Automation** - Customizable email templates and workflows
* **Advanced Reporting** - PDF/Excel exports with detailed analytics
* **White-label Options** - Customize branding for your business
* **Priority Support** - Get help when you need it
* **API Access** - Full REST API for custom integrations

**Plus all Free Features:**

* Property Management
* Unit Tracking
* Tenant Management
* Lease Management
* Maintenance Requests
* Basic Reporting
* Import/Export
* REST API

== Installation ==

1. Purchase a license from https://plughausstudios.com
2. Download the pro plugin
3. Upload and activate the plugin
4. Enter your license key in Property Mgmt > Settings > Pro License
5. Enjoy all pro features!

== Support ==

Pro customers receive priority support via:
* Email: support@plughausstudios.com
* Priority support portal
* Live chat (business hours)

== Changelog ==

= 1.0.0 =
* Initial pro release
* Advanced analytics dashboard
* Payment automation features
* Email automation system
* Advanced reporting with exports
* White-label options
`;

    await fs.writeFile(filePath, readmeContent);
}

async function createProAssets(pluginDir) {
    // Create pro CSS
    const proCss = `
/* Pro Dashboard Styles */
.phpm-pro-analytics-widget {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    padding: 20px;
}

.phpm-chart-container {
    position: relative;
    height: 200px;
}

.phpm-analytics-summary {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.phpm-metric {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.phpm-metric-value {
    display: block;
    font-size: 24px;
    font-weight: bold;
    color: #007cba;
}

.phpm-metric-label {
    display: block;
    font-size: 12px;
    color: #666;
    margin-top: 5px;
}

.phpm-metric-positive .phpm-metric-value {
    color: #28a745;
}

/* Pro Financial Widget */
.phpm-pro-financial-widget {
    padding: 20px;
}

.phpm-financial-metrics {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

/* Analytics Dashboard */
.phpm-analytics-dashboard {
    display: grid;
    gap: 20px;
}

.phpm-analytics-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.phpm-analytics-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
}

.phpm-analytics-card h3 {
    margin-top: 0;
    margin-bottom: 15px;
}

/* Status indicators */
.phpm-status-active {
    color: #28a745;
    font-weight: bold;
}

.phpm-status-inactive {
    color: #dc3545;
    font-weight: bold;
}

.phpm-status-success {
    color: #28a745;
}

.phpm-status-error {
    color: #dc3545;
}

/* Email Templates */
.phpm-email-template-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.phpm-email-automation-tabs .nav-tab-wrapper {
    margin-bottom: 20px;
}

.phpm-email-automation-tabs .tab-content {
    display: none;
}

.phpm-email-automation-tabs .tab-content.active {
    display: block;
}

/* Responsive design */
@media (max-width: 768px) {
    .phpm-analytics-row {
        grid-template-columns: 1fr;
    }
    
    .phpm-pro-analytics-widget {
        grid-template-columns: 1fr;
    }
    
    .phpm-financial-metrics {
        grid-template-columns: 1fr;
    }
}
`;
    
    await fs.writeFile(
        path.join(pluginDir, 'pro', 'assets', 'css', 'dashboard.css'),
        proCss
    );
    
    // Create pro JavaScript
    const proJs = `
/**
 * Pro Dashboard JavaScript
 */
(function($) {
    'use strict';
    
    // Initialize pro dashboard features
    $(document).ready(function() {
        initProDashboard();
        initEmailTemplates();
        initPaymentAutomation();
    });
    
    function initProDashboard() {
        // Initialize any chart libraries or interactive elements
        console.log('Pro dashboard initialized');
    }
    
    function initEmailTemplates() {
        // Email template preview functionality
        window.previewEmailTemplate = function(templateName) {
            // This would open a preview modal
            alert('Preview for ' + templateName + ' template');
        };
    }
    
    function initPaymentAutomation() {
        // Payment automation controls
        $('#phpm-validate-license').on('click', function() {
            var button = $(this);
            button.prop('disabled', true).text('Validating...');
            
            // Make AJAX call to validate license
            $.post(ajaxurl, {
                action: 'phpm_validate_license',
                license_key: $('#phpm_license_key').val(),
                nonce: phpm_admin.nonce
            }, function(response) {
                if (response.success) {
                    alert('License validated successfully!');
                    location.reload();
                } else {
                    alert('License validation failed: ' + response.data);
                }
            }).always(function() {
                button.prop('disabled', false).text('Validate License');
            });
        });
        
        $('#phpm-deactivate-license').on('click', function() {
            if (confirm('Are you sure you want to deactivate your license?')) {
                // Make AJAX call to deactivate license
                $.post(ajaxurl, {
                    action: 'phpm_deactivate_license',
                    nonce: phpm_admin.nonce
                }, function(response) {
                    if (response.success) {
                        alert('License deactivated successfully!');
                        location.reload();
                    }
                });
            }
        });
    }
    
})(jQuery);
`;
    
    await fs.writeFile(
        path.join(pluginDir, 'pro', 'assets', 'js', 'dashboard.js'),
        proJs
    );
}

async function cleanPackageJsonForDistribution(filePath) {
    const packageJson = await fs.readJson(filePath);
    
    // Remove build scripts not needed for distribution
    delete packageJson.scripts['build:free'];
    delete packageJson.scripts['build:pro'];
    delete packageJson.devDependencies;
    
    // Add archiver to dependencies since it's needed for builds
    if (!packageJson.dependencies) {
        packageJson.dependencies = {};
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
    await buildProVersion();
}

main();