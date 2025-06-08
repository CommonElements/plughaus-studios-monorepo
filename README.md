# PlugHaus Studios - WordPress Plugin Development Studio

**Professional WordPress Plugin Ecosystem & Development Platform**

PlugHaus Studios is a comprehensive WordPress plugin development studio that creates professional-grade plugins for diverse industries. This monorepo contains our complete development ecosystem including plugins, company website, licensing platform, and shared development framework.

## 🏗️ **Repository Structure**

```
plughaus-studios/
├── app/public/wp-content/
│   ├── plugins/
│   │   ├── plughaus-property-management/    # Property Management Plugin (Current)
│   │   ├── license-manager-for-woocommerce/ # Licensing System
│   │   └── woocommerce/                     # E-commerce Platform
│   └── themes/
│       └── plughaus-studios/               # Company Website Theme
├── website/                                # Marketing Site Content
├── packages/                              # Future Plugin Development
│   ├── sports-league-management/          # Planned: Sports leagues
│   ├── fantasy-sports/                    # Planned: Fantasy sports
│   ├── field-service-management/          # Planned: Field services
│   ├── commonelements-integration/        # Planned: CE.com bridge
│   └── shared/                           # Shared Development Framework
├── tools/                                # Build & Development Tools
├── dist/                                 # Distribution Packages
└── docs/                                # Documentation
```

## 🚀 **Quick Start**

### Prerequisites
- Node.js 18+
- PHP 7.4+
- Composer
- Local by Flywheel (for WordPress development)

### Development Setup
```bash
# Clone repository
git clone https://github.com/CommonElements/plughaus-studios-monorepo.git
cd plughaus-studios-monorepo

# Install dependencies
npm install
composer install

# Setup development environment
./tools/dev/local-setup.sh

# Start development
npm run dev
```

## 🎯 **Studio Vision & Product Portfolio**

### **Current Products**

#### Property Management Ecosystem (Active Development)
- **Free Version**: WordPress.org compliant - Property, tenant, lease management
- **Pro Version**: Advanced analytics, automation, reporting ($149/year)
- **Add-on Modules**: HOA/COA, Commercial, STR, Advanced Accounting
- **Target Market**: Small-medium property managers (alternative to Yardi/AppFolio)

### **Planned Product Pipeline**

#### Sports & Recreation
- **Sports League Management**: Team management, schedules, statistics
- **Fantasy Sports Platform**: League administration, scoring systems

#### Business Services  
- **Field Service Management**: Technician dispatch, work orders, scheduling
- **CommonElements Integration**: Bridge CE.com platform features to WordPress

#### Development Framework
- **Shared Architecture**: Common licensing, admin patterns, utilities
- **Rapid Plugin Development**: Standardized freemium model implementation
- **Cross-Product Synergies**: Shared customer base and licensing infrastructure

### **Business Model Strategy**
- **Freemium Foundation**: WordPress.org free versions for user acquisition
- **Pro Licensing**: Direct sales via PlugHausStudios.com for revenue
- **Ecosystem Expansion**: Vertical-specific add-ons for ARPU growth
- **Market Diversification**: Multiple industry verticals reduce risk

## 🔧 **Development Commands**

```bash
# Development
npm run dev                    # Start all development servers
npm run dev:plugins           # Plugin development only
npm run dev:website           # Website development only

# Building
npm run build                 # Build all packages
npm run extract:free         # Extract WordPress.org version
npm run extract:pro          # Extract pro version
npm run extract:all          # Extract all versions

# Testing
npm run test                 # Run all tests
npm run lint                 # Code quality checks

# Release
npm run release              # Automated release management
```

## 🎯 **Architecture Strategy**

### Freemium Plugin Model
- **Core Features**: Available in free version (WordPress.org)
- **Pro Features**: Licensed through PlugHausStudios.com
- **Single Codebase**: Conditional loading based on license status
- **Clean Extraction**: Automated build scripts for distribution

### WordPress.org Compliance
- No artificial limitations in free version
- All basic features fully functional
- Clean, compliant code structure
- Proper GPL licensing

### Scalable Ecosystem
- Modular plugin architecture
- Shared utility library
- Consistent API patterns
- Future plugin expansion ready

## 📚 **Documentation**

- **[Development Guide](docs/development/)** - Setup and development workflows
- **[Plugin Documentation](docs/plugins/)** - Individual plugin guides
- **[API Reference](docs/api/)** - REST API and hooks documentation
- **[Contributing](docs/CONTRIBUTING.md)** - Contribution guidelines

## 🔄 **Release Process**

### Automated Pipeline
1. **Development** → Feature branches
2. **Integration** → Develop branch
3. **Testing** → Automated test suite
4. **Building** → Extract plugin versions
5. **Distribution** → WordPress.org + Pro channels

### Distribution Channels
- **WordPress.org**: Free plugin repository
- **PlugHausStudios.com**: Pro plugin sales & downloads
- **GitHub Releases**: Development builds & documentation

## 🤝 **Contributing**

We welcome contributions! Please see our [Contributing Guide](docs/CONTRIBUTING.md) for details.

### Development Workflow
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## 📄 **License**

- **Free Plugins**: GPL v2 or later
- **Pro Plugins**: Commercial license
- **Shared Utilities**: GPL v2 or later (for ecosystem compatibility)

## 🔗 **Links**

- **Website**: [PlugHausStudios.com](https://plughausstudios.com)
- **Support**: [Support Documentation](https://plughausstudios.com/support)
- **WordPress.org**: [Plugin Directory](https://wordpress.org/plugins/search/plughaus)

---

**PlugHaus Studios** - Professional Property Management for WordPress