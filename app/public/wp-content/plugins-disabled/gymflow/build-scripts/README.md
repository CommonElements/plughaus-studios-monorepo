# GymFlow Build Scripts

This directory contains build and deployment scripts for the GymFlow fitness studio management plugin.

## Scripts Overview

### `build-free.js`
Creates WordPress.org compliant free version by:
- Copying core functionality only
- Removing pro directories and references  
- Modifying plugin file to disable pro features
- Creating distribution ZIP file

**Usage:**
```bash
npm run build:free
# or
node build-scripts/build-free.js
```

**Output:** `dist/gymflow-free.zip`

### `build-pro.js`
Creates Pro version distribution by:
- Copying all functionality (core + pro)
- Enabling pro features and license checking
- Modifying plugin file for pro functionality
- Creating distribution ZIP file

**Usage:**
```bash
npm run build:pro
# or  
node build-scripts/build-pro.js
```

**Output:** `dist/gymflow-pro.zip`

### `deploy.js`
Handles deployment to WordPress.org SVN repository and pro distribution.

**Usage:**
```bash
# Deploy free version to WordPress.org
npm run deploy:free -- --version=1.0.0
node build-scripts/deploy.js --type=free --version=1.0.0

# Deploy pro version
npm run deploy:pro -- --version=1.0.0
node build-scripts/deploy.js --type=pro --version=1.0.0
```

**Requirements:**
- SVN installed (for WordPress.org deployment)
- Clean git working directory
- Version exists in changelog (readme.txt)

### `setup-dev.sh`
Sets up development environment after composer install:
- Creates necessary directories
- Sets proper permissions
- Creates config files (phpunit.xml.dist, webpack.config.js, etc.)
- Initializes test setup

**Usage:**
```bash
npm run setup:dev
# or
bash build-scripts/setup-dev.sh
```

**Auto-runs:** After `npm install` via postinstall script

## Development Workflow

### Initial Setup
```bash
git clone <repository>
cd gymflow
npm install  # Automatically runs setup:dev
composer install
```

### Development
```bash
npm run dev          # Start file watching for assets
npm run lint         # Check code standards
npm run test         # Run tests
npm run validate     # Lint + test
```

### Building
```bash
npm run build        # Compile assets
npm run build:free   # Build free version ZIP
npm run build:pro    # Build pro version ZIP  
npm run package      # Build assets + both ZIPs
```

### Deployment
```bash
# Update version in readme.txt changelog first
npm run deploy:free -- --version=1.0.1
npm run deploy:pro -- --version=1.0.1
```

## Directory Structure

After building, the following structure is created:

```
dist/
├── free/
│   └── gymflow/          # WordPress.org ready
│       ├── gymflow.php   # Pro features disabled
│       ├── readme.txt    # Free version readme
│       ├── core/         # Core functionality only
│       └── assets/       # Compiled assets
├── pro/
│   └── gymflow-pro/      # Pro distribution
│       ├── gymflow.php   # Pro features enabled
│       ├── readme.txt    # Pro version readme  
│       ├── core/         # Core functionality
│       ├── pro/          # Pro features
│       └── assets/       # Compiled assets
├── gymflow-free.zip      # WordPress.org submission
└── gymflow-pro.zip       # Pro distribution
```

## Build Process Details

### Free Version Build Process
1. Clean previous build
2. Copy core files only (excludes `pro/` directory)
3. Modify `gymflow.php` to disable pro features:
   - `check_pro_license()` returns `false`
   - Remove pro feature initialization
4. Update readme.txt for free version
5. Compile and minify assets
6. Create ZIP for WordPress.org

### Pro Version Build Process  
1. Clean previous build
2. Copy all files (core + pro)
3. Modify `gymflow.php` to enable pro features:
   - `check_pro_license()` validates license
   - Load pro feature classes
4. Update readme.txt for pro version
5. Compile and minify assets
6. Create ZIP for pro distribution

### Asset Compilation
Both builds compile assets using:
- **CSS:** SCSS → CSS via node-sass
- **JavaScript:** ES6+ → ES5 via Babel + Webpack
- **Minification:** Production builds are minified
- **Source Maps:** Development builds include source maps

## Requirements

### System Requirements
- Node.js 14+
- npm 6+
- PHP 7.4+
- Composer
- SVN (for WordPress.org deployment)

### npm Dependencies
- **archiver:** ZIP file creation
- **fs-extra:** Enhanced file operations
- **webpack:** JavaScript bundling
- **node-sass:** SCSS compilation
- **babel:** JavaScript transpilation

## Configuration Files

The setup script creates these configuration files:

- **`.gitignore`** - Git ignore patterns
- **`phpunit.xml.dist`** - PHPUnit test configuration
- **`webpack.config.js`** - Webpack build configuration
- **`tests/phpunit/bootstrap.php`** - PHPUnit bootstrap
- **`tests/js/setup.js`** - Jest test setup

## WordPress.org Submission

The free version build is specifically designed for WordPress.org submission:

### Compliance Features
- ✅ GPL v2+ license
- ✅ No external dependencies
- ✅ Secure coding practices
- ✅ WordPress coding standards
- ✅ No "phoning home"
- ✅ Proper sanitization/validation
- ✅ No pro feature enforcement

### Excluded from WordPress.org
- Build scripts and tooling
- Pro features directory
- Development dependencies
- Test files
- Source maps

## Troubleshooting

### Common Issues

**Build fails with "Cannot find module"**
```bash
npm install  # Reinstall dependencies
```

**SVN deployment fails**
```bash
# Ensure SVN is installed
svn --version

# Check SVN credentials
svn info https://plugins.svn.wordpress.org/gymflow
```

**Asset compilation fails**
```bash
# Check Node.js version
node --version  # Should be 14+

# Clear cache and rebuild
npm run clean
npm install
npm run build
```

### Debug Mode
Set environment variable for verbose output:
```bash
DEBUG=1 npm run build:free
DEBUG=1 npm run build:pro
```

## Contributing

When modifying build scripts:

1. Test both free and pro builds
2. Verify WordPress.org compliance
3. Check that all files are properly included/excluded
4. Test deployment process in staging
5. Update this documentation

## Support

For build script issues:
- Check the logs in `logs/` directory
- Verify all requirements are met
- Test with clean `node_modules/`
- Review this documentation

For plugin development:
- See main project README.md
- Check WordPress.org plugin guidelines
- Review Vireo Designs coding standards