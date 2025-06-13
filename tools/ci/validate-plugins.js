#!/usr/bin/env node

/**
 * Vireo Designs - Plugin Validation Suite
 * Validates all plugins for WordPress.org compliance and functionality
 */

const fs = require('fs-extra');
const path = require('path');

const PLUGIN_BASE_DIR = path.resolve(__dirname, '../../app/public/wp-content/plugins');

const plugins = [
    'studiosnap',
    'dearedge', 
    'marina-manager',
    'storageflow'
];

const requiredFiles = [
    'readme.txt',
    'core/includes/shared/class-{slug}-utilities.php',
    'core/includes/shared/class-{slug}-post-types.php',
    'core/includes/shared/class-{slug}-capabilities.php',
    'core/includes/shared/class-{slug}-activator.php',
    'build-scripts/build-free.js'
];

const requiredFunctions = [
    'check_pro_license',
    'init',
    'activate'
];

async function validateAllPlugins() {
    console.log('🔍 Validating Vireo Designs plugin ecosystem...\n');
    
    const validationResults = [];
    
    for (const plugin of plugins) {
        console.log(`📋 Validating ${plugin}...`);
        
        const result = await validatePlugin(plugin);
        validationResults.push({
            plugin,
            ...result
        });
        
        if (result.isValid) {
            console.log(`✅ ${plugin} validation passed`);
        } else {
            console.log(`❌ ${plugin} validation failed`);
            result.errors.forEach(error => {
                console.log(`   • ${error}`);
            });
        }
        console.log();
    }
    
    // Generate validation report
    await generateValidationReport(validationResults);
    
    // Summary
    const validPlugins = validationResults.filter(r => r.isValid).length;
    const invalidPlugins = validationResults.filter(r => !r.isValid).length;
    
    console.log('📊 Validation Summary:');
    console.log(`✅ Valid: ${validPlugins}`);
    console.log(`❌ Invalid: ${invalidPlugins}`);
    
    if (invalidPlugins > 0) {
        console.log('\n⚠️  Some plugins failed validation. Check the validation report for details.');
        process.exit(1);
    } else {
        console.log('\n🎉 All plugins passed validation!');
    }
}

async function validatePlugin(pluginSlug) {
    const pluginDir = path.join(PLUGIN_BASE_DIR, pluginSlug);
    const errors = [];
    const warnings = [];
    
    // Check if plugin directory exists
    if (!await fs.pathExists(pluginDir)) {
        return {
            isValid: false,
            errors: [`Plugin directory not found: ${pluginDir}`],
            warnings: []
        };
    }
    
    // Validate required files
    for (const requiredFile of requiredFiles) {
        const filePath = requiredFile.replace(/{slug}/g, getSlugPrefix(pluginSlug));
        const fullPath = path.join(pluginDir, filePath);
        
        if (!await fs.pathExists(fullPath)) {
            errors.push(`Required file missing: ${filePath}`);
        }
    }
    
    // Validate main plugin file
    const mainFile = await findMainPluginFile(pluginDir);
    if (mainFile) {
        const mainFileValidation = await validateMainPluginFile(mainFile, pluginSlug);
        errors.push(...mainFileValidation.errors);
        warnings.push(...mainFileValidation.warnings);
    } else {
        errors.push('Main plugin file not found');
    }
    
    // Validate plugin header
    if (mainFile) {
        const headerValidation = await validatePluginHeader(mainFile);
        errors.push(...headerValidation.errors);
        warnings.push(...headerValidation.warnings);
    }
    
    // Validate PHP syntax (basic check)
    const phpValidation = await validatePHPSyntax(pluginDir);
    errors.push(...phpValidation.errors);
    warnings.push(...phpValidation.warnings);
    
    // Validate WordPress.org compliance
    const complianceValidation = await validateWordPressOrgCompliance(pluginDir);
    errors.push(...complianceValidation.errors);
    warnings.push(...complianceValidation.warnings);
    
    // Validate build script
    const buildScriptValidation = await validateBuildScript(pluginDir);
    errors.push(...buildScriptValidation.errors);
    warnings.push(...buildScriptValidation.warnings);
    
    return {
        isValid: errors.length === 0,
        errors,
        warnings
    };
}

function getSlugPrefix(pluginSlug) {
    const prefixes = {
        'studiosnap': 'ss',
        'dearedge': 'de',
        'marina-manager': 'mm',
        'storageflow': 'sf'
    };
    return prefixes[pluginSlug] || pluginSlug.substring(0, 2);
}

async function findMainPluginFile(pluginDir) {
    const possibleFiles = [
        path.basename(pluginDir) + '.php',
        'index.php',
        'plugin.php'
    ];
    
    for (const file of possibleFiles) {
        const filePath = path.join(pluginDir, file);
        if (await fs.pathExists(filePath)) {
            // Check if it contains plugin header
            const content = await fs.readFile(filePath, 'utf8');
            if (content.includes('Plugin Name:')) {
                return filePath;
            }
        }
    }
    
    return null;
}

async function validateMainPluginFile(filePath, pluginSlug) {
    const errors = [];
    const warnings = [];
    
    try {
        const content = await fs.readFile(filePath, 'utf8');
        
        // Check for required functions
        for (const func of requiredFunctions) {
            if (!content.includes(`function ${func}`) && !content.includes(`${func}(`)) {
                errors.push(`Required function missing: ${func}`);
            }
        }
        
        // Check for security header
        if (!content.includes('ABSPATH')) {
            errors.push('Missing ABSPATH security check');
        }
        
        // Check for pro license function
        if (!content.includes('check_pro_license')) {
            errors.push('Missing pro license checking function');
        }
        
        // Check for proper escaping patterns
        if (content.includes('echo $') && !content.includes('esc_')) {
            warnings.push('Potential unescaped output detected - review for security');
        }
        
        // Check for direct database queries
        if (content.includes('$wpdb->query') && !content.includes('prepare')) {
            warnings.push('Direct database queries detected - ensure proper preparation');
        }
        
    } catch (error) {
        errors.push(`Error reading main plugin file: ${error.message}`);
    }
    
    return { errors, warnings };
}

async function validatePluginHeader(filePath) {
    const errors = [];
    const warnings = [];
    
    try {
        const content = await fs.readFile(filePath, 'utf8');
        const headerRegex = /\/\*\*([\s\S]*?)\*\//;
        const headerMatch = content.match(headerRegex);
        
        if (!headerMatch) {
            errors.push('Plugin header not found');
            return { errors, warnings };
        }
        
        const header = headerMatch[1];
        const requiredFields = [
            'Plugin Name:',
            'Description:',
            'Version:',
            'Author:',
            'License:'
        ];
        
        for (const field of requiredFields) {
            if (!header.includes(field)) {
                errors.push(`Missing required header field: ${field}`);
            }
        }
        
        // Check version format
        const versionMatch = header.match(/Version:\s*([^\n\r]*)/);
        if (versionMatch) {
            const version = versionMatch[1].trim();
            if (!/^\d+\.\d+\.\d+$/.test(version)) {
                warnings.push(`Version format should be X.Y.Z: ${version}`);
            }
        }
        
    } catch (error) {
        errors.push(`Error validating plugin header: ${error.message}`);
    }
    
    return { errors, warnings };
}

async function validatePHPSyntax(pluginDir) {
    const errors = [];
    const warnings = [];
    
    // This is a basic check - in production you'd use actual PHP syntax checking
    try {
        const phpFiles = await findPHPFiles(pluginDir);
        
        for (const file of phpFiles) {
            const content = await fs.readFile(file, 'utf8');
            
            // Basic syntax checks
            const openTags = (content.match(/<\?php/g) || []).length;
            const closeTags = (content.match(/\?>/g) || []).length;
            
            // Check for common syntax issues
            if (content.includes('<?') && !content.includes('<?php')) {
                warnings.push(`File uses short PHP tags: ${path.relative(pluginDir, file)}`);
            }
            
            // Check for trailing whitespace after closing tags
            if (content.includes('?>\n') || content.includes('?>\r')) {
                warnings.push(`Trailing whitespace after closing PHP tag: ${path.relative(pluginDir, file)}`);
            }
        }
        
    } catch (error) {
        errors.push(`Error validating PHP syntax: ${error.message}`);
    }
    
    return { errors, warnings };
}

async function findPHPFiles(dir) {
    const phpFiles = [];
    
    async function searchDir(currentDir) {
        const items = await fs.readdir(currentDir);
        
        for (const item of items) {
            const itemPath = path.join(currentDir, item);
            const stat = await fs.stat(itemPath);
            
            if (stat.isDirectory() && !item.startsWith('.') && item !== 'node_modules') {
                await searchDir(itemPath);
            } else if (stat.isFile() && item.endsWith('.php')) {
                phpFiles.push(itemPath);
            }
        }
    }
    
    await searchDir(dir);
    return phpFiles;
}

async function validateWordPressOrgCompliance(pluginDir) {
    const errors = [];
    const warnings = [];
    
    // Check for readme.txt
    const readmePath = path.join(pluginDir, 'readme.txt');
    if (!await fs.pathExists(readmePath)) {
        errors.push('readme.txt file missing (required for WordPress.org)');
    } else {
        const readmeValidation = await validateReadmeTxt(readmePath);
        errors.push(...readmeValidation.errors);
        warnings.push(...readmeValidation.warnings);
    }
    
    // Check for forbidden patterns
    const forbiddenPatterns = [
        'eval(',
        'base64_decode(',
        'system(',
        'shell_exec(',
        'curl_exec('
    ];
    
    const phpFiles = await findPHPFiles(pluginDir);
    for (const file of phpFiles) {
        const content = await fs.readFile(file, 'utf8');
        
        for (const pattern of forbiddenPatterns) {
            if (content.includes(pattern)) {
                warnings.push(`Potentially dangerous function found: ${pattern} in ${path.relative(pluginDir, file)}`);
            }
        }
    }
    
    return { errors, warnings };
}

async function validateReadmeTxt(readmePath) {
    const errors = [];
    const warnings = [];
    
    try {
        const content = await fs.readFile(readmePath, 'utf8');
        
        const requiredSections = [
            '=== ',
            '== Description ==',
            '== Installation ==',
            '== Changelog =='
        ];
        
        for (const section of requiredSections) {
            if (!content.includes(section)) {
                errors.push(`Missing required readme.txt section: ${section}`);
            }
        }
        
        // Check for required fields
        if (!content.includes('Requires at least:')) {
            errors.push('Missing "Requires at least:" field in readme.txt');
        }
        
        if (!content.includes('Tested up to:')) {
            errors.push('Missing "Tested up to:" field in readme.txt');
        }
        
        if (!content.includes('Stable tag:')) {
            errors.push('Missing "Stable tag:" field in readme.txt');
        }
        
    } catch (error) {
        errors.push(`Error validating readme.txt: ${error.message}`);
    }
    
    return { errors, warnings };
}

async function validateBuildScript(pluginDir) {
    const errors = [];
    const warnings = [];
    
    const buildScriptPath = path.join(pluginDir, 'build-scripts', 'build-free.js');
    
    if (!await fs.pathExists(buildScriptPath)) {
        errors.push('Build script missing: build-scripts/build-free.js');
        return { errors, warnings };
    }
    
    try {
        const content = await fs.readFile(buildScriptPath, 'utf8');
        
        // Check for required build script components
        const requiredComponents = [
            'buildFreeVersion',
            'modifyMainPluginFile',
            'updateReadmeFile',
            'createZipArchive'
        ];
        
        for (const component of requiredComponents) {
            if (!content.includes(component)) {
                errors.push(`Missing build script component: ${component}`);
            }
        }
        
    } catch (error) {
        errors.push(`Error validating build script: ${error.message}`);
    }
    
    return { errors, warnings };
}

async function generateValidationReport(results) {
    const reportData = {
        validationDate: new Date().toISOString(),
        totalPlugins: results.length,
        validPlugins: results.filter(r => r.isValid).length,
        invalidPlugins: results.filter(r => !r.isValid).length,
        results: results
    };
    
    const reportDir = path.resolve(__dirname, '../../dist/validation');
    await fs.ensureDir(reportDir);
    
    const reportPath = path.join(reportDir, 'validation-report.json');
    await fs.writeFile(reportPath, JSON.stringify(reportData, null, 2));
    
    console.log(`📄 Validation report generated: ${reportPath}`);
}

// Main execution
async function main() {
    await validateAllPlugins();
}

// Run if called directly
if (require.main === module) {
    main().catch(error => {
        console.error('💥 Validation failed:', error);
        process.exit(1);
    });
}

module.exports = {
    validateAllPlugins,
    validatePlugin,
    plugins
};