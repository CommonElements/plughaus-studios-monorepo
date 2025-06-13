#!/usr/bin/env node

/**
 * Vireo Ecosystem Package Builder
 * Creates self-contained downloadable packages for each plugin ecosystem
 */

const fs = require('fs-extra');
const path = require('path');
const { execSync } = require('child_process');
const archiver = require('archiver');

console.log('🎯 Building Vireo Plugin Ecosystem Packages');

// Configuration
const ROOT_DIR = path.resolve(__dirname, '..');
const BUILD_DIR = path.join(ROOT_DIR, 'dist', 'ecosystem-packages');
const VERSION = '1.0.0';

// Plugin configurations
const PLUGIN_CONFIGS = {
    'property-management': {
        name: 'Vireo Property Management',
        slug: 'vireo-property-management',
        description: 'Complete property management solution for small landlords and property managers.',
        coreFeatures: [
            'Property & Unit Management',
            'Tenant Database',
            'Lease Tracking',
            'Maintenance Requests',
            'Basic Reporting',
            'CSV Import/Export'
        ],
        proFeatures: [
            'Advanced Analytics Dashboard',
            'Payment Automation',
            'Email Automation',
            'Advanced Reporting',
            'White-label Options',
            'API Integrations'
        ],
        addons: [
            'Financial Reporting Add-on',
            'Tenant Portal Add-on',
            'Mobile App Add-on',
            'Document Management Add-on'
        ],
        price: { core: 'Free', pro: '$99/year' },
        category: 'Real Estate & Property Management'
    },
    
    'sports-league': {
        name: 'Vireo Sports League Manager',
        slug: 'vireo-sports-league',
        description: 'Comprehensive sports league management for local leagues and tournaments.',
        coreFeatures: [
            'League & Team Management',
            'Game Scheduling',
            'Player Registration',
            'Basic Statistics',
            'Season Management',
            'Contact Management'
        ],
        proFeatures: [
            'Advanced Statistics',
            'Payment Processing',
            'Tournament Brackets',
            'Live Scoring',
            'Mobile App',
            'Custom Reports'
        ],
        addons: [
            'Referee Management Add-on',
            'Live Streaming Add-on',
            'Equipment Rental Add-on'
        ],
        price: { core: 'Free', pro: '$79/year' },
        category: 'Sports & Recreation'
    },
    
    'equipment-rental': {
        name: 'Vireo Equipment Rental System',
        slug: 'vireo-equipment-rental',
        description: 'Complete rental management system for equipment businesses.',
        coreFeatures: [
            'Equipment Inventory',
            'Basic Booking System',
            'Customer Database',
            'Rental Calendar',
            'Simple Invoicing',
            'Availability Tracking'
        ],
        proFeatures: [
            'Advanced Scheduling',
            'Payment Processing',
            'Automated Billing',
            'Maintenance Tracking',
            'GPS Tracking',
            'Advanced Reports'
        ],
        addons: [
            'Delivery Management Add-on',
            'Insurance Tracking Add-on',
            'Multi-location Add-on'
        ],
        price: { core: 'Free', pro: '$89/year' },
        category: 'Equipment & Rental Management'
    }
};

async function buildEcosystemPackages() {
    try {
        // Clean build directory
        console.log('🧹 Cleaning build directory...');
        await fs.remove(BUILD_DIR);
        await fs.ensureDir(BUILD_DIR);
        
        // Build each plugin ecosystem
        for (const [pluginKey, config] of Object.entries(PLUGIN_CONFIGS)) {
            console.log(`\n📦 Building ${config.name} ecosystem...`);
            await buildPluginEcosystem(pluginKey, config);
        }
        
        // Create ecosystem overview
        console.log('\n📊 Creating ecosystem overview...');
        await createEcosystemOverview();
        
        console.log('\n✅ All ecosystem packages built successfully!');
        console.log(`📁 Packages available in: ${BUILD_DIR}`);
        
    } catch (error) {
        console.error('❌ Build failed:', error);
        process.exit(1);
    }
}

async function buildPluginEcosystem(pluginKey, config) {
    const pluginBuildDir = path.join(BUILD_DIR, config.slug);
    await fs.ensureDir(pluginBuildDir);
    
    // Build core (free) version
    console.log(`  🔧 Building core version...`);
    await buildCoreVersion(pluginKey, config, pluginBuildDir);
    
    // Build pro version
    console.log(`  💎 Building pro version...`);
    await buildProVersion(pluginKey, config, pluginBuildDir);
    
    // Build addon packages
    console.log(`  🔌 Building addon packages...`);
    await buildAddonPackages(pluginKey, config, pluginBuildDir);
    
    // Create ecosystem documentation
    console.log(`  📚 Creating documentation...`);
    await createEcosystemDocumentation(pluginKey, config, pluginBuildDir);
    
    // Create marketing materials
    console.log(`  📢 Creating marketing materials...`);
    await createMarketingMaterials(pluginKey, config, pluginBuildDir);
}

async function buildCoreVersion(pluginKey, config, buildDir) {
    const coreDir = path.join(buildDir, 'core');
    await fs.ensureDir(coreDir);
    
    // Create main plugin file
    const mainPluginContent = createMainPluginFile(config, 'core');
    await fs.writeFile(
        path.join(coreDir, `${config.slug}.php`),
        mainPluginContent
    );
    
    // Create core functionality
    await createCoreStructure(config, coreDir);
    
    // Create WordPress.org readme
    const readmeContent = createWordPressOrgReadme(config);
    await fs.writeFile(path.join(coreDir, 'readme.txt'), readmeContent);
    
    // Create package.json
    const packageJson = createPackageJson(config, 'core');
    await fs.writeFile(
        path.join(coreDir, 'package.json'),
        JSON.stringify(packageJson, null, 2)
    );
    
    // Create ZIP package
    const zipPath = path.join(buildDir, `${config.slug}-core-v${VERSION}.zip`);
    await createZipPackage(coreDir, zipPath, config.slug);
}

async function buildProVersion(pluginKey, config, buildDir) {
    const proDir = path.join(buildDir, 'pro');
    await fs.ensureDir(proDir);
    
    // Create main plugin file with pro features
    const mainPluginContent = createMainPluginFile(config, 'pro');
    await fs.writeFile(
        path.join(proDir, `${config.slug}-pro.php`),
        mainPluginContent
    );
    
    // Create complete structure (core + pro)
    await createCoreStructure(config, proDir);
    await createProStructure(config, proDir);
    
    // Create pro readme
    const readmeContent = createProReadme(config);
    await fs.writeFile(path.join(proDir, 'readme.txt'), readmeContent);
    
    // Create package.json
    const packageJson = createPackageJson(config, 'pro');
    await fs.writeFile(
        path.join(proDir, 'package.json'),
        JSON.stringify(packageJson, null, 2)
    );
    
    // Create ZIP package
    const zipPath = path.join(buildDir, `${config.slug}-pro-v${VERSION}.zip`);
    await createZipPackage(proDir, zipPath, `${config.slug}-pro`);
}

async function buildAddonPackages(pluginKey, config, buildDir) {
    const addonsDir = path.join(buildDir, 'addons');
    await fs.ensureDir(addonsDir);
    
    for (const addonName of config.addons) {
        const addonSlug = addonName.toLowerCase().replace(/[^a-z0-9]+/g, '-');
        const addonDir = path.join(addonsDir, addonSlug);
        await fs.ensureDir(addonDir);
        
        // Create addon structure
        await createAddonStructure(config, addonName, addonDir);
        
        // Create ZIP package
        const zipPath = path.join(buildDir, `${config.slug}-${addonSlug}-v${VERSION}.zip`);
        await createZipPackage(addonDir, zipPath, `${config.slug}-${addonSlug}`);
    }
}

function createMainPluginFile(config, type) {
    const isCore = type === 'core';
    const pluginName = isCore ? config.name : `${config.name} Pro`;
    const fileName = isCore ? config.slug : `${config.slug}-pro`;
    
    return `<?php
/**
 * Plugin Name: ${pluginName}
 * Plugin URI: https://vireodesigns.com/plugins/${config.slug.replace('vireo-', '')}
 * Description: ${config.description}
 * Version: ${VERSION}
 * Author: Vireo Designs
 * Author URI: https://vireodesigns.com
 * License: ${isCore ? 'GPL v2 or later' : 'Commercial'}
 * License URI: ${isCore ? 'https://www.gnu.org/licenses/gpl-2.0.html' : 'https://vireodesigns.com/license'}
 * Text Domain: ${config.slug}
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Network: false
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('${config.slug.toUpperCase().replace(/-/g, '_')}_VERSION', '${VERSION}');
define('${config.slug.toUpperCase().replace(/-/g, '_')}_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('${config.slug.toUpperCase().replace(/-/g, '_')}_PLUGIN_URL', plugin_dir_url(__FILE__));
define('${config.slug.toUpperCase().replace(/-/g, '_')}_PLUGIN_BASE', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
class ${config.name.replace(/[^a-zA-Z0-9]/g, '_')} {
    
    private static $instance = null;
    private $is_pro = ${!isCore};
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
        $this->load_dependencies();
    }
    
    private function init_hooks() {
        add_action('plugins_loaded', array($this, 'init'), 0);
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        // Load text domain
        load_plugin_textdomain('${config.slug}', false, dirname(plugin_basename(__FILE__)) . '/languages');
        
        // Initialize plugin features
        $this->init_core_features();
        ${!isCore ? '$this->init_pro_features();' : ''}
    }
    
    private function load_dependencies() {
        // Load core files
        require_once plugin_dir_path(__FILE__) . 'includes/class-${config.slug}-core.php';
        ${!isCore ? `require_once plugin_dir_path(__FILE__) . 'includes/class-${config.slug}-pro.php';` : ''}
    }
    
    private function init_core_features() {
        // Initialize core functionality
        ${config.slug.replace(/-/g, '_')}_Core::init();
    }
    
    ${!isCore ? `private function init_pro_features() {
        if ($this->check_license()) {
            ${config.slug.replace(/-/g, '_')}_Pro::init();
        }
    }
    
    private function check_license() {
        // License validation logic
        return apply_filters('${config.slug}_license_valid', false);
    }` : ''}
    
    public function activate() {
        // Activation logic
        flush_rewrite_rules();
    }
    
    public function deactivate() {
        // Deactivation logic
        flush_rewrite_rules();
    }
}

// Initialize plugin
${config.name.replace(/[^a-zA-Z0-9]/g, '_')}::get_instance();
`;
}

async function createCoreStructure(config, dir) {
    // Create includes directory structure
    const includesDir = path.join(dir, 'includes');
    await fs.ensureDir(includesDir);
    
    // Create core class file
    const coreClassContent = `<?php
/**
 * Core functionality for ${config.name}
 */
class ${config.slug.replace(/-/g, '_')}_Core {
    
    public static function init() {
        // Initialize core features
        self::init_hooks();
        self::load_assets();
    }
    
    private static function init_hooks() {
        add_action('init', array(__CLASS__, 'register_post_types'));
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_public_assets'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_assets'));
    }
    
    public static function register_post_types() {
        // Register custom post types for this plugin
    }
    
    public static function enqueue_public_assets() {
        // Enqueue public CSS/JS
    }
    
    public static function enqueue_admin_assets() {
        // Enqueue admin CSS/JS
    }
    
    private static function load_assets() {
        // Load required asset files
    }
}
`;
    
    await fs.writeFile(
        path.join(includesDir, `class-${config.slug}-core.php`),
        coreClassContent
    );
    
    // Create assets directory
    const assetsDir = path.join(dir, 'assets');
    await fs.ensureDir(path.join(assetsDir, 'css'));
    await fs.ensureDir(path.join(assetsDir, 'js'));
    await fs.ensureDir(path.join(assetsDir, 'images'));
    
    // Create languages directory
    await fs.ensureDir(path.join(dir, 'languages'));
}

async function createProStructure(config, dir) {
    const includesDir = path.join(dir, 'includes');
    
    // Create pro class file
    const proClassContent = `<?php
/**
 * Pro functionality for ${config.name}
 */
class ${config.slug.replace(/-/g, '_')}_Pro {
    
    public static function init() {
        // Initialize pro features
        self::init_hooks();
        self::load_pro_modules();
    }
    
    private static function init_hooks() {
        add_action('init', array(__CLASS__, 'register_pro_features'));
        add_filter('${config.slug}_features', array(__CLASS__, 'add_pro_features'));
    }
    
    public static function register_pro_features() {
        // Register pro-only features
    }
    
    public static function add_pro_features($features) {
        // Add pro features to the features list
        return array_merge($features, array(
            'advanced_analytics',
            'automation',
            'api_access',
            'priority_support'
        ));
    }
    
    private static function load_pro_modules() {
        // Load pro-specific modules
    }
}
`;
    
    await fs.writeFile(
        path.join(includesDir, `class-${config.slug}-pro.php`),
        proClassContent
    );
    
    // Create pro assets
    const proAssetsDir = path.join(dir, 'assets', 'pro');
    await fs.ensureDir(path.join(proAssetsDir, 'css'));
    await fs.ensureDir(path.join(proAssetsDir, 'js'));
}

async function createAddonStructure(config, addonName, dir) {
    const addonSlug = addonName.toLowerCase().replace(/[^a-z0-9]+/g, '-');
    
    // Create main addon file
    const addonContent = `<?php
/**
 * Plugin Name: ${config.name} - ${addonName}
 * Plugin URI: https://vireodesigns.com/plugins/${config.slug.replace('vireo-', '')}/addons/${addonSlug}
 * Description: ${addonName} for ${config.name}
 * Version: ${VERSION}
 * Author: Vireo Designs
 * Author URI: https://vireodesigns.com
 * License: Commercial
 * Text Domain: ${config.slug}-${addonSlug}
 * Requires Plugins: ${config.slug}
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if parent plugin is active
if (!class_exists('${config.name.replace(/[^a-zA-Z0-9]/g, '_')}')) {
    add_action('admin_notices', function() {
        echo '<div class="notice notice-error"><p>';
        echo sprintf(__('${addonName} requires %s to be installed and activated.', '${config.slug}-${addonSlug}'), '${config.name}');
        echo '</p></div>';
    });
    return;
}

/**
 * ${addonName} Class
 */
class ${config.name.replace(/[^a-zA-Z0-9]/g, '_')}_${addonName.replace(/[^a-zA-Z0-9]/g, '_')} {
    
    public static function init() {
        // Initialize addon
        add_action('plugins_loaded', array(__CLASS__, 'load'));
    }
    
    public static function load() {
        // Load addon functionality
    }
}

${config.name.replace(/[^a-zA-Z0-9]/g, '_')}_${addonName.replace(/[^a-zA-Z0-9]/g, '_')}::init();
`;
    
    await fs.writeFile(path.join(dir, `${config.slug}-${addonSlug}.php`), addonContent);
    
    // Create addon structure
    await fs.ensureDir(path.join(dir, 'includes'));
    await fs.ensureDir(path.join(dir, 'assets'));
}

function createWordPressOrgReadme(config) {
    return `=== ${config.name} ===
Contributors: vireodesigns
Donate link: https://vireodesigns.com/donate
Tags: ${config.category.toLowerCase().split(' & ').join(', ')}, management, business
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: ${VERSION}
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

${config.description}

== Description ==

${config.name} is a powerful yet lightweight WordPress plugin designed specifically for small businesses. 

**Core Features (Free):**

${config.coreFeatures.map(feature => `* ${feature}`).join('\n')}

**Pro Features Available:**

${config.proFeatures.map(feature => `* ${feature}`).join('\n')}

**Add-ons Available:**

${config.addons.map(addon => `* ${addon}`).join('\n')}

== Installation ==

1. Upload the plugin files to \`/wp-content/plugins/${config.slug}/\`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Configure the plugin through the admin menu

== Frequently Asked Questions ==

= Is this plugin free? =

Yes! The core features are completely free. Pro features are available with a license.

= What's included in the Pro version? =

Pro includes: ${config.proFeatures.slice(0, 3).join(', ')}, and more.

= Do you offer support? =

Yes! Free users get community support, Pro users get priority support.

== Screenshots ==

1. Main dashboard
2. Management interface
3. Reporting features

== Changelog ==

= ${VERSION} =
* Initial release
* Core functionality implemented
* ${config.coreFeatures.length} core features included

== Upgrade Notice ==

= ${VERSION} =
Initial release of ${config.name}.
`;
}

function createProReadme(config) {
    return `# ${config.name} Pro

Professional-grade ${config.category.toLowerCase()} solution for WordPress.

## Features

### Core Features
${config.coreFeatures.map(feature => `- ${feature}`).join('\n')}

### Pro Features
${config.proFeatures.map(feature => `- ${feature}`).join('\n')}

### Available Add-ons
${config.addons.map(addon => `- ${addon}`).join('\n')}

## Installation

1. Upload and activate the plugin
2. Enter your license key in the settings
3. Configure your preferences

## License

This is commercial software. A valid license is required for updates and support.

## Support

Pro users receive priority support at https://vireodesigns.com/support
`;
}

function createPackageJson(config, type) {
    return {
        name: type === 'core' ? config.slug : `${config.slug}-pro`,
        version: VERSION,
        description: config.description,
        author: 'Vireo Designs',
        license: type === 'core' ? 'GPL-2.0-or-later' : 'Commercial',
        homepage: `https://vireodesigns.com/plugins/${config.slug.replace('vireo-', '')}`,
        repository: {
            type: 'git',
            url: 'https://github.com/vireodesigns/plugins.git'
        },
        keywords: [
            'wordpress',
            'plugin',
            config.category.toLowerCase(),
            'business',
            'management'
        ],
        engines: {
            node: '>=14.0.0'
        },
        scripts: {
            test: 'echo "No tests specified"',
            build: 'echo "No build process needed"'
        }
    };
}

async function createEcosystemDocumentation(pluginKey, config, buildDir) {
    const docContent = `# ${config.name} Ecosystem

## Overview
${config.description}

## Package Contents

### Core Package (Free)
- **Price**: ${config.price.core}
- **Features**: ${config.coreFeatures.length} core features
- **Support**: Community support
- **Updates**: Automatic via WordPress.org

### Pro Package 
- **Price**: ${config.price.pro}
- **Features**: All core features + ${config.proFeatures.length} pro features
- **Support**: Priority support
- **Updates**: Automatic with license key

### Add-on Packages
${config.addons.map(addon => `- **${addon}**: Extends functionality with specialized features`).join('\n')}

## Installation Guide

### Core Version
1. Download \`${config.slug}-core-v${VERSION}.zip\`
2. Install via WordPress admin or upload to plugins directory
3. Activate and configure

### Pro Version
1. Download \`${config.slug}-pro-v${VERSION}.zip\`
2. Install and activate
3. Enter license key in settings
4. Access pro features

### Add-ons
1. Ensure Pro version is installed and licensed
2. Download desired addon package
3. Install and activate addon
4. Configure addon settings

## Feature Comparison

| Feature | Core | Pro | Add-ons |
|---------|------|-----|---------|
${config.coreFeatures.map(feature => `| ${feature} | ✅ | ✅ | ✅ |`).join('\n')}
${config.proFeatures.map(feature => `| ${feature} | ❌ | ✅ | ✅ |`).join('\n')}

## Support & Documentation

- **Documentation**: https://vireodesigns.com/docs/${config.slug.replace('vireo-', '')}
- **Support**: https://vireodesigns.com/support
- **Community**: https://vireodesigns.com/community

## License Information

- **Core**: GPL v2 or later (Free forever)
- **Pro**: Commercial license (Annual subscription)
- **Add-ons**: Commercial license (One-time purchase)
`;

    await fs.writeFile(path.join(buildDir, 'README.md'), docContent);
}

async function createMarketingMaterials(pluginKey, config, buildDir) {
    const marketingDir = path.join(buildDir, 'marketing');
    await fs.ensureDir(marketingDir);
    
    // Create product overview
    const productOverview = {
        name: config.name,
        description: config.description,
        category: config.category,
        pricing: config.price,
        features: {
            core: config.coreFeatures,
            pro: config.proFeatures,
            addons: config.addons
        },
        targetAudience: getTargetAudience(pluginKey),
        competitiveAdvantages: getCompetitiveAdvantages(pluginKey),
        use_cases: getUseCases(pluginKey)
    };
    
    await fs.writeFile(
        path.join(marketingDir, 'product-overview.json'),
        JSON.stringify(productOverview, null, 2)
    );
    
    // Create feature matrix
    const featureMatrix = createFeatureMatrix(config);
    await fs.writeFile(
        path.join(marketingDir, 'feature-matrix.md'),
        featureMatrix
    );
}

function getTargetAudience(pluginKey) {
    const audiences = {
        'property-management': [
            'Small landlords with 1-50 properties',
            'Independent property managers',
            'Real estate investors',
            'Property management startups'
        ],
        'sports-league': [
            'Local sports league organizers',
            'Youth sports coordinators',
            'Community recreation centers',
            'Tournament organizers'
        ],
        'equipment-rental': [
            'Small equipment rental businesses',
            'Tool rental shops',
            'Party rental companies',
            'Construction equipment rental'
        ]
    };
    
    return audiences[pluginKey] || [];
}

function getCompetitiveAdvantages(pluginKey) {
    const advantages = {
        'property-management': [
            'WordPress-native (no external dependencies)',
            'One-time fee vs monthly subscriptions',
            'No per-unit pricing',
            'Full data ownership',
            'Customizable with WordPress themes/plugins'
        ],
        'sports-league': [
            'Designed for volunteers, not professionals',
            'Simple setup and maintenance',
            'No technical expertise required',
            'Affordable for community organizations',
            'Integrates with existing WordPress websites'
        ],
        'equipment-rental': [
            'Built for small businesses',
            'No transaction fees',
            'Offline capability',
            'Simple inventory management',
            'WordPress ecosystem compatibility'
        ]
    };
    
    return advantages[pluginKey] || [];
}

function getUseCases(pluginKey) {
    const useCases = {
        'property-management': [
            'Landlord managing 5-20 rental properties',
            'Property manager handling maintenance requests',
            'Real estate investor tracking ROI',
            'Small PM company replacing expensive software'
        ],
        'sports-league': [
            'Youth baseball league scheduling games',
            'Adult softball league tracking standings',
            'Basketball tournament organization',
            'Soccer club managing multiple teams'
        ],
        'equipment-rental': [
            'Construction tool rental business',
            'Party equipment rental for events',
            'Camping gear rental shop',
            'Photography equipment rental'
        ]
    };
    
    return useCases[pluginKey] || [];
}

function createFeatureMatrix(config) {
    return `# ${config.name} Feature Matrix

## Core vs Pro Comparison

| Category | Feature | Core | Pro | Notes |
|----------|---------|------|-----|-------|
${config.coreFeatures.map(feature => `| Core | ${feature} | ✅ | ✅ | Available in free version |`).join('\n')}
${config.proFeatures.map(feature => `| Pro | ${feature} | ❌ | ✅ | Pro license required |`).join('\n')}

## Add-on Extensions

${config.addons.map(addon => `### ${addon}
- **Compatibility**: Requires Pro version
- **Type**: Extension module
- **Installation**: Separate plugin installation`).join('\n\n')}

## Technical Requirements

- **WordPress**: 5.8 or higher
- **PHP**: 7.4 or higher
- **MySQL**: 5.6 or higher
- **Server**: Any WordPress-compatible hosting

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
`;
}

async function createEcosystemOverview() {
    const overviewContent = `# Vireo Plugin Ecosystem - Complete Package Collection

## Available Ecosystems

${Object.entries(PLUGIN_CONFIGS).map(([key, config]) => `
### ${config.name}
- **Category**: ${config.category}
- **Description**: ${config.description}
- **Pricing**: Core (${config.price.core}) | Pro (${config.price.pro})
- **Features**: ${config.coreFeatures.length} core + ${config.proFeatures.length} pro
- **Add-ons**: ${config.addons.length} available extensions
`).join('\n')}

## Package Structure

Each ecosystem includes:
- \`{plugin-slug}-core-v${VERSION}.zip\` - Free WordPress.org version
- \`{plugin-slug}-pro-v${VERSION}.zip\` - Commercial pro version  
- \`{plugin-slug}-{addon}-v${VERSION}.zip\` - Individual addon packages
- \`README.md\` - Complete documentation
- \`marketing/\` - Product information and feature matrices

## Installation Priority

1. **Property Management** - Most comprehensive, ready for production
2. **Sports League** - Community-focused, simple setup
3. **Equipment Rental** - Business-focused, moderate complexity

## Development Status

- ✅ **Property Management**: Production ready
- 🚧 **Sports League**: Core complete, pro in development  
- 📋 **Equipment Rental**: Planned for Q2 2025

## License Information

- **Core packages**: GPL v2+ (Free forever)
- **Pro packages**: Commercial (Annual subscription)
- **Add-on packages**: Commercial (One-time purchase)

---
Generated on ${new Date().toISOString()}
Built with Vireo Ecosystem Package Builder v${VERSION}
`;

    await fs.writeFile(path.join(BUILD_DIR, 'ECOSYSTEM-OVERVIEW.md'), overviewContent);
    
    // Create package index
    const packageIndex = {
        version: VERSION,
        generated: new Date().toISOString(),
        ecosystems: Object.entries(PLUGIN_CONFIGS).map(([key, config]) => ({
            key,
            name: config.name,
            slug: config.slug,
            category: config.category,
            pricing: config.price,
            packages: {
                core: `${config.slug}-core-v${VERSION}.zip`,
                pro: `${config.slug}-pro-v${VERSION}.zip`,
                addons: config.addons.map(addon => {
                    const addonSlug = addon.toLowerCase().replace(/[^a-z0-9]+/g, '-');
                    return `${config.slug}-${addonSlug}-v${VERSION}.zip`;
                })
            }
        }))
    };
    
    await fs.writeFile(
        path.join(BUILD_DIR, 'package-index.json'),
        JSON.stringify(packageIndex, null, 2)
    );
}

async function createZipPackage(sourceDir, zipPath, folderName) {
    const output = fs.createWriteStream(zipPath);
    const archive = archiver('zip', { zlib: { level: 9 } });
    
    return new Promise((resolve, reject) => {
        output.on('close', resolve);
        archive.on('error', reject);
        
        archive.pipe(output);
        archive.directory(sourceDir, folderName);
        archive.finalize();
    });
}

// Dependency check
async function checkDependencies() {
    try {
        require('archiver');
    } catch (error) {
        console.log('📦 Installing required dependencies...');
        execSync('npm install archiver --save-dev', { stdio: 'inherit' });
    }
}

// Main execution
async function main() {
    await checkDependencies();
    await buildEcosystemPackages();
}

main().catch(error => {
    console.error('❌ Fatal error:', error);
    process.exit(1);
});