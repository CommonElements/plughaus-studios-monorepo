# PlugHaus Studios - Monorepo Architecture

## 🎯 **Repository Strategy**

Single GitHub repository managing:
- **PlugHaus Property Management Plugin** (free + pro versions)
- **Future plugins** (payments, documents, analytics, etc.)
- **PlugHausStudios.com website** (e-commerce + documentation)
- **Shared libraries** and utilities
- **Build scripts** for clean plugin extraction
- **CI/CD pipelines** for automated releases

## 📁 **Monorepo Structure**

```
plughausstudios-monorepo/
├── .github/                           # GitHub Actions and templates
│   ├── workflows/
│   │   ├── build-plugins.yml          # Plugin build automation
│   │   ├── deploy-website.yml         # Website deployment
│   │   ├── release-management.yml     # Version releases
│   │   └── quality-checks.yml         # Code quality + security
│   └── ISSUE_TEMPLATE/
├── docs/                              # Documentation hub
│   ├── development/                   # Developer guides
│   ├── plugins/                       # Plugin documentation
│   └── api/                          # API documentation
├── packages/                          # Individual plugin packages
│   ├── property-management/           # Core property management
│   │   ├── core/                     # Free features
│   │   ├── pro/                      # Pro features
│   │   ├── package.json              # Plugin-specific config
│   │   ├── composer.json             # PHP dependencies
│   │   └── build.config.js           # Build configuration
│   ├── payments-gateway/              # Future payments plugin
│   ├── document-automator/            # Future document plugin
│   ├── analytics-framework/           # Future analytics plugin
│   └── shared/                       # Shared utilities
│       ├── components/               # Reusable components
│       ├── utilities/                # Utility functions
│       └── licensing/                # Licensing system
├── website/                          # PlugHausStudios.com
│   ├── wp-content/                   # WordPress files
│   │   ├── themes/
│   │   │   └── plughausstudios/      # Custom theme
│   │   └── plugins/                  # Website-specific plugins
│   ├── composer.json                 # Website dependencies
│   └── package.json                  # Asset compilation
├── tools/                            # Build and development tools
│   ├── build/                        # Build scripts
│   │   ├── extract-plugin.js         # Plugin extraction
│   │   ├── build-free-version.js     # WordPress.org build
│   │   ├── build-pro-version.js      # Pro version build
│   │   └── build-website.js          # Website build
│   ├── dev/                          # Development utilities
│   │   ├── local-setup.sh            # Local environment setup
│   │   ├── sync-plugins.js           # Plugin sync for testing
│   │   └── db-fixtures.sql           # Test data
│   └── ci/                           # CI/CD utilities
├── dist/                             # Built/extracted packages
│   ├── plugins/
│   │   ├── property-management-free/ # WordPress.org ready
│   │   ├── property-management-pro/  # Pro version ready
│   │   └── *.zip                     # Distribution packages
│   └── website/                      # Website build output
├── tests/                            # Test suites
│   ├── unit/                         # Unit tests
│   ├── integration/                  # Integration tests
│   └── e2e/                         # End-to-end tests
├── .gitignore                        # Git ignore rules
├── package.json                      # Root package management
├── composer.json                     # PHP dependencies
├── lerna.json                        # Monorepo management
├── README.md                         # Repository overview
└── LICENSE                           # Repository license
```

## 🔧 **Plugin Package Structure**

### **Property Management Plugin Structure**
```
packages/property-management/
├── core/                             # Free features (WordPress.org)
│   ├── includes/
│   │   ├── admin/                   # Admin interface
│   │   ├── api/                     # REST API
│   │   ├── core/                    # Core functionality
│   │   └── public/                  # Frontend
│   ├── assets/
│   │   ├── src/                     # Source files (SCSS, JS)
│   │   └── build/                   # Compiled assets
│   └── languages/                   # Translations
├── pro/                             # Pro features (commercial)
│   ├── includes/
│   │   ├── analytics/              # Dashboard analytics
│   │   ├── automation/             # Automation features
│   │   ├── licensing/              # License validation
│   │   └── integrations/           # Third-party integrations
│   └── assets/
├── shared/                          # Shared utilities (symlinked)
│   ├── utilities/                  # From packages/shared/
│   └── components/                 # Reusable components
├── tests/                          # Plugin-specific tests
├── build/                          # Build configuration
│   ├── webpack.config.js           # Asset compilation
│   ├── extract-free.js            # Free version extraction
│   └── extract-pro.js             # Pro version extraction
├── property-management.php         # Main plugin file
├── package.json                    # NPM dependencies
├── composer.json                   # PHP dependencies
└── README.md                       # Plugin documentation
```

## 🚀 **Build System Architecture**

### **1. Plugin Extraction Scripts**

#### **Free Version Extraction**
```javascript
// tools/build/extract-plugin.js
const extractFreeVersion = {
    source: 'packages/property-management/',
    target: 'dist/plugins/property-management-free/',
    include: [
        'core/**/*',
        'shared/utilities/**/*',
        'assets/build/**/*',
        'languages/**/*',
        'property-management.php'
    ],
    exclude: [
        'pro/**/*',
        'shared/pro-utilities/**/*',
        'tests/**/*',
        'build/**/*'
    ],
    transforms: [
        {
            file: 'property-management.php',
            replace: {
                'check_pro_license()': 'return false;',
                'load_pro_features()': '// Pro features disabled'
            }
        }
    ],
    addFiles: {
        'readme.txt': 'templates/wordpress-org-readme.txt'
    }
};
```

#### **Pro Version Extraction**
```javascript
const extractProVersion = {
    source: 'packages/property-management/',
    target: 'dist/plugins/property-management-pro/',
    include: [
        'core/**/*',
        'pro/**/*',
        'shared/**/*',
        'assets/build/**/*',
        'languages/**/*',
        'property-management.php'
    ],
    exclude: [
        'tests/**/*',
        'build/**/*'
    ],
    transforms: [
        {
            file: 'property-management.php',
            replace: {
                'check_pro_license()': 'return PHPM_License_Manager::is_valid();'
            }
        }
    ],
    addFiles: {
        'readme.txt': 'templates/pro-readme.txt'
    }
};
```

### **2. Automated Build Pipeline**

#### **GitHub Actions Workflow**
```yaml
# .github/workflows/build-plugins.yml
name: Build Plugins
on:
  push:
    branches: [main, develop]
    paths: ['packages/**']

jobs:
  build-property-management:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup Node.js
        uses: actions/setup-node@v3
        with:
          node-version: '18'
          
      - name: Install dependencies
        run: |
          npm install
          cd packages/property-management && npm install
          
      - name: Build assets
        run: |
          cd packages/property-management
          npm run build
          
      - name: Extract free version
        run: node tools/build/extract-plugin.js --type=free --plugin=property-management
        
      - name: Extract pro version
        run: node tools/build/extract-plugin.js --type=pro --plugin=property-management
        
      - name: Run tests
        run: |
          npm run test:unit
          npm run test:integration
          
      - name: Create release packages
        run: |
          cd dist/plugins
          zip -r property-management-free.zip property-management-free/
          zip -r property-management-pro.zip property-management-pro/
          
      - name: Upload artifacts
        uses: actions/upload-artifact@v3
        with:
          name: plugin-builds
          path: dist/plugins/*.zip
```

### **3. Development Workflow**

#### **Local Development Setup**
```bash
# tools/dev/local-setup.sh
#!/bin/bash

echo "Setting up PlugHaus Studios monorepo..."

# Install root dependencies
npm install
composer install

# Setup each plugin package
for plugin in packages/*/; do
    if [ -f "$plugin/package.json" ]; then
        echo "Setting up $plugin..."
        cd "$plugin"
        npm install
        composer install
        cd - > /dev/null
    fi
done

# Setup website
cd website
npm install
composer install
cd ..

# Create symlinks for shared utilities
echo "Creating shared utility symlinks..."
for plugin in packages/*/; do
    if [ -d "$plugin" ] && [ "$plugin" != "packages/shared/" ]; then
        ln -sf ../../shared "$plugin/shared"
    fi
done

echo "Setup complete! Run 'npm run dev' to start development."
```

#### **Development Commands**
```json
// Root package.json scripts
{
  "scripts": {
    "dev": "concurrently \"npm run dev:plugins\" \"npm run dev:website\"",
    "dev:plugins": "lerna run dev --parallel",
    "dev:website": "cd website && npm run dev",
    
    "build": "npm run build:plugins && npm run build:website",
    "build:plugins": "lerna run build",
    "build:website": "cd website && npm run build",
    
    "extract:free": "node tools/build/extract-plugin.js --type=free",
    "extract:pro": "node tools/build/extract-plugin.js --type=pro",
    "extract:all": "npm run extract:free && npm run extract:pro",
    
    "test": "npm run test:plugins && npm run test:website",
    "test:plugins": "lerna run test",
    "test:website": "cd website && npm run test",
    
    "lint": "npm run lint:plugins && npm run lint:website",
    "lint:plugins": "lerna run lint",
    "lint:website": "cd website && npm run lint",
    
    "release": "node tools/build/release-manager.js"
  }
}
```

## 🔀 **Branching Strategy**

### **Git Flow with Monorepo**
```
main                    # Production-ready code
├── develop            # Integration branch
├── feature/*          # Feature development
├── plugin/*           # Plugin-specific features
├── website/*          # Website-specific features
├── release/*          # Release preparation
└── hotfix/*           # Critical fixes
```

### **Branch Naming Convention**
```
feature/property-mgmt/add-tenant-portal
feature/website/update-pricing-page
plugin/payments-gateway/stripe-integration
website/docs/api-documentation
release/property-mgmt-v1.2.0
hotfix/property-mgmt/security-patch
```

## 📦 **Package Management Strategy**

### **Lerna Configuration**
```json
// lerna.json
{
  "version": "independent",
  "npmClient": "npm",
  "command": {
    "publish": {
      "conventionalCommits": true,
      "message": "chore(release): publish packages"
    },
    "version": {
      "allowBranch": ["main", "release/*"],
      "conventionalCommits": true
    }
  },
  "packages": [
    "packages/*",
    "website"
  ]
}
```

### **Workspace Dependencies**
```json
// Root package.json
{
  "workspaces": [
    "packages/*",
    "website",
    "tools/*"
  ],
  "devDependencies": {
    "lerna": "^6.0.0",
    "concurrently": "^7.0.0",
    "@wordpress/scripts": "^25.0.0",
    "jest": "^29.0.0"
  }
}
```

## 🚀 **Release Management**

### **Automated Versioning**
```javascript
// tools/build/release-manager.js
const releaseManager = {
  plugins: {
    'property-management': {
      freeVersion: '1.0.0',
      proVersion: '1.0.0',
      wpOrgSubmission: true
    }
  },
  
  async createRelease(plugin, type) {
    // 1. Extract plugin version
    // 2. Run tests
    // 3. Create distribution package
    // 4. Tag release in git
    // 5. Upload to appropriate distribution channel
  }
};
```

### **Distribution Channels**
```
Free Plugins:
├── WordPress.org SVN repository
├── GitHub releases
└── PlugHausStudios.com download

Pro Plugins:
├── PlugHausStudios.com secure download
├── License validation required
└── Automatic update server

Website:
├── Production server deployment
├── Staging environment
└── CDN distribution
```

## 🔒 **Security & Access Control**

### **Repository Secrets**
```
GitHub Secrets:
├── WP_ORG_USERNAME       # WordPress.org submission
├── WP_ORG_PASSWORD       # WordPress.org submission
├── DEPLOY_SSH_KEY        # Website deployment
├── LICENSE_API_KEY       # License validation
├── STRIPE_SECRET_KEY     # Payment processing
└── CLOUDFLARE_API_TOKEN  # CDN management
```

### **Access Levels**
```
Repository Access:
├── Admin: Full access + settings
├── Maintainer: Push to all branches
├── Developer: Push to feature branches
└── Read-only: Documentation access
```

This monorepo structure gives us:
- **Single source of truth** for all PlugHaus Studios development
- **Clean plugin extraction** for WordPress.org and pro distribution  
- **Shared utilities** across all plugins
- **Integrated website development** for the e-commerce platform
- **Automated build/release pipeline** for efficient releases
- **Scalable architecture** for future plugins and features

Ready to implement this structure?