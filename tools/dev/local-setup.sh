#!/bin/bash

echo "🚀 Setting up PlugHaus Studios monorepo for local development..."

# Check requirements
echo "📋 Checking requirements..."

# Check Node.js
if ! command -v node &> /dev/null; then
    echo "❌ Node.js not found. Please install Node.js 18+"
    exit 1
fi

# Check Composer
if ! command -v composer &> /dev/null; then
    echo "❌ Composer not found. Please install Composer"
    exit 1
fi

echo "✅ Requirements check passed"

# Install root dependencies
echo "📦 Installing root dependencies..."
npm install
composer install

# Setup each plugin package
echo "🔧 Setting up plugin packages..."
for plugin_dir in packages/*/; do
    if [ -f "$plugin_dir/package.json" ]; then
        echo "   Setting up $(basename "$plugin_dir")..."
        cd "$plugin_dir"
        npm install
        if [ -f "composer.json" ]; then
            composer install
        fi
        cd - > /dev/null
    fi
done

# Create dist directories
echo "📁 Creating distribution directories..."
mkdir -p dist/plugins
mkdir -p dist/website

# Make build scripts executable
echo "🔧 Making build scripts executable..."
chmod +x tools/build/extract-plugin.js
chmod +x tools/dev/local-setup.sh

# Create symlinks for shared utilities (for development only)
echo "🔗 Creating development symlinks..."
for plugin_dir in packages/*/; do
    plugin_name=$(basename "$plugin_dir")
    if [ "$plugin_name" != "shared" ] && [ -d "$plugin_dir" ]; then
        shared_link="$plugin_dir/shared"
        if [ ! -L "$shared_link" ]; then
            ln -sf "../../shared" "$shared_link"
            echo "   ✓ Created shared symlink for $plugin_name"
        fi
    fi
done

echo ""
echo "✅ Local development setup complete!"
echo ""
echo "🎯 Next steps:"
echo "   npm run dev          # Start development servers"
echo "   npm run extract:free # Extract free plugin version"
echo "   npm run extract:pro  # Extract pro plugin version"
echo ""
echo "📂 Key directories:"
echo "   packages/            # Plugin source code"
echo "   dist/plugins/        # Extracted plugins ready for distribution"
echo "   tools/               # Build and development utilities"
echo ""