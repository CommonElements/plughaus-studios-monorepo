#!/bin/bash

# GymFlow Development Setup Script
# 
# Sets up development environment after composer install
# Creates necessary directories, sets permissions, and initializes config files
#
# @package GymFlow
# @version 1.0.0

set -e

echo "🏋️ Setting up GymFlow development environment..."

# Get the script directory (build-scripts folder)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"

echo "📁 Project directory: $PROJECT_DIR"

# Function to create directory if it doesn't exist
create_dir() {
    if [ ! -d "$1" ]; then
        echo "📁 Creating directory: $1"
        mkdir -p "$1"
    else
        echo "✅ Directory exists: $1"
    fi
}

# Function to make script executable
make_executable() {
    if [ -f "$1" ]; then
        chmod +x "$1"
        echo "🔧 Made executable: $1"
    fi
}

# Create necessary directories
echo "📁 Creating necessary directories..."

create_dir "$PROJECT_DIR/dist"
create_dir "$PROJECT_DIR/dist/free"
create_dir "$PROJECT_DIR/dist/pro"
create_dir "$PROJECT_DIR/logs"
create_dir "$PROJECT_DIR/tests"
create_dir "$PROJECT_DIR/tests/phpunit"
create_dir "$PROJECT_DIR/tests/js"
create_dir "$PROJECT_DIR/core/assets/scss"
create_dir "$PROJECT_DIR/pro/assets/scss"
create_dir "$PROJECT_DIR/languages"

# Create uploads directory structure
create_dir "$PROJECT_DIR/uploads"
create_dir "$PROJECT_DIR/uploads/member-photos"
create_dir "$PROJECT_DIR/uploads/progress-photos"
create_dir "$PROJECT_DIR/uploads/equipment-photos"
create_dir "$PROJECT_DIR/uploads/class-images"
create_dir "$PROJECT_DIR/uploads/trainer-photos"

# Make build scripts executable
echo "🔧 Setting up build scripts..."
make_executable "$SCRIPT_DIR/build-free.js"
make_executable "$SCRIPT_DIR/build-pro.js"
make_executable "$SCRIPT_DIR/setup-dev.sh"

# Create .gitignore if it doesn't exist
if [ ! -f "$PROJECT_DIR/.gitignore" ]; then
    echo "📝 Creating .gitignore..."
    cat > "$PROJECT_DIR/.gitignore" << 'EOF'
# Dependencies
node_modules/
vendor/

# Build outputs
dist/
*.zip

# Logs
logs/
*.log

# Environment files
.env
.env.local
.env.*.local

# IDE files
.vscode/
.idea/
*.swp
*.swo

# OS files
.DS_Store
Thumbs.db

# WordPress
wp-config.php
wp-content/uploads/
wp-content/cache/

# Temporary files
*.tmp
*.temp
.tmp/

# Testing
tests/coverage/
phpunit.xml

# Build caches
.cache/
.parcel-cache/

# NPM/Yarn
npm-debug.log*
yarn-debug.log*
yarn-error.log*
package-lock.json
yarn.lock

# Composer
composer.lock

# Plugin specific
uploads/
*.pot
EOF
fi

# Create phpunit.xml.dist if it doesn't exist
if [ ! -f "$PROJECT_DIR/phpunit.xml.dist" ]; then
    echo "📝 Creating phpunit.xml.dist..."
    cat > "$PROJECT_DIR/phpunit.xml.dist" << 'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<phpunit
    bootstrap="tests/phpunit/bootstrap.php"
    backupGlobals="false"
    colors="true"
    convertErrorsToExceptions="true"
    convertNoticesToExceptions="true"
    convertWarningsToExceptions="true"
    processIsolation="false"
    stopOnFailure="false"
    syntaxCheck="false"
>
    <testsuites>
        <testsuite name="GymFlow Test Suite">
            <directory>./tests/phpunit/</directory>
        </testsuite>
    </testsuites>
    
    <filter>
        <whitelist processUncoveredFilesFromWhitelist="true">
            <directory suffix=".php">./core/includes</directory>
            <directory suffix=".php">./pro/includes</directory>
            <exclude>
                <directory suffix=".php">./vendor</directory>
                <directory suffix=".php">./node_modules</directory>
                <directory suffix=".php">./tests</directory>
            </exclude>
        </whitelist>
    </filter>
    
    <logging>
        <log type="coverage-html" target="tests/coverage"/>
        <log type="coverage-clover" target="tests/coverage/clover.xml"/>
    </logging>
</phpunit>
EOF
fi

# Create webpack.config.js if it doesn't exist
if [ ! -f "$PROJECT_DIR/webpack.config.js" ]; then
    echo "📝 Creating webpack.config.js..."
    cat > "$PROJECT_DIR/webpack.config.js" << 'EOF'
const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = (env, argv) => {
    const isProduction = argv.mode === 'production';
    
    return {
        entry: {
            'admin': './core/assets/js/admin.js',
            'public': './core/assets/js/public.js',
            'pro-admin': './pro/assets/js/admin.js',
            'pro-public': './pro/assets/js/public.js'
        },
        
        output: {
            path: path.resolve(__dirname, 'assets/js'),
            filename: '[name].min.js',
            clean: false
        },
        
        module: {
            rules: [
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    use: {
                        loader: 'babel-loader',
                        options: {
                            presets: ['@babel/preset-env']
                        }
                    }
                },
                {
                    test: /\.s?css$/,
                    use: [
                        MiniCssExtractPlugin.loader,
                        'css-loader',
                        'sass-loader'
                    ]
                }
            ]
        },
        
        plugins: [
            new MiniCssExtractPlugin({
                filename: '../css/[name].min.css'
            })
        ],
        
        devtool: isProduction ? false : 'source-map',
        
        optimization: {
            minimize: isProduction
        }
    };
};
EOF
fi

# Create test setup files
if [ ! -f "$PROJECT_DIR/tests/phpunit/bootstrap.php" ]; then
    echo "📝 Creating test bootstrap..."
    cat > "$PROJECT_DIR/tests/phpunit/bootstrap.php" << 'EOF'
<?php
/**
 * PHPUnit bootstrap file for GymFlow
 */

// Define testing environment
define('GYMFLOW_TESTING', true);

// Load WordPress test environment
$_tests_dir = getenv('WP_TESTS_DIR');

if (!$_tests_dir) {
    $_tests_dir = '/tmp/wordpress-tests-lib';
}

// Give access to tests_add_filter() function
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested
 */
function _manually_load_plugin() {
    require dirname(dirname(__DIR__)) . '/gymflow.php';
}
tests_add_filter('muplugins_loaded', '_manually_load_plugin');

// Start up the WP testing environment
require $_tests_dir . '/includes/bootstrap.php';
EOF
fi

if [ ! -f "$PROJECT_DIR/tests/js/setup.js" ]; then
    echo "📝 Creating JS test setup..."
    cat > "$PROJECT_DIR/tests/js/setup.js" << 'EOF'
/**
 * Jest setup file for GymFlow
 */

// Mock WordPress globals
global.wp = {
    ajax: {
        post: jest.fn(),
        send: jest.fn()
    },
    hooks: {
        addAction: jest.fn(),
        addFilter: jest.fn(),
        doAction: jest.fn(),
        applyFilters: jest.fn()
    }
};

global.jQuery = require('jquery');
global.$ = global.jQuery;

// Mock gymflow_ajax object
global.gymflow_ajax = {
    ajax_url: '/wp-admin/admin-ajax.php',
    nonce: 'test-nonce',
    strings: {
        loading: 'Loading...',
        success: 'Success!',
        error: 'Error occurred',
        required_field: 'This field is required',
        invalid_email: 'Please enter a valid email address',
        cancel_booking: 'Are you sure you want to cancel this booking?'
    }
};
EOF
fi

# Set proper permissions
echo "🔧 Setting permissions..."
chmod -R 755 "$PROJECT_DIR/core/assets"
chmod -R 755 "$PROJECT_DIR/pro/assets"
chmod -R 755 "$PROJECT_DIR/uploads"

# Check for Node.js
if command -v node >/dev/null 2>&1; then
    echo "✅ Node.js version: $(node --version)"
else
    echo "⚠️ Node.js not found. Please install Node.js for asset compilation."
fi

# Check for npm
if command -v npm >/dev/null 2>&1; then
    echo "✅ npm version: $(npm --version)"
else
    echo "⚠️ npm not found. Please install npm for dependency management."
fi

# Check for PHP
if command -v php >/dev/null 2>&1; then
    echo "✅ PHP version: $(php --version | head -n 1)"
else
    echo "⚠️ PHP not found. Please install PHP for WordPress development."
fi

# Check for Composer
if command -v composer >/dev/null 2>&1; then
    echo "✅ Composer version: $(composer --version | head -n 1)"
else
    echo "⚠️ Composer not found. Please install Composer for PHP dependency management."
fi

echo ""
echo "🎉 GymFlow development environment setup complete!"
echo ""
echo "Next steps:"
echo "1. Run 'npm install' to install JavaScript dependencies"
echo "2. Run 'composer install' to install PHP dependencies"
echo "3. Run 'npm run dev' to start development with file watching"
echo "4. Run 'npm run build:free' to build free version"
echo "5. Run 'npm run build:pro' to build pro version"
echo ""
echo "Happy coding! 🏋️‍♀️"