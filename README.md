# PlugHaus Studios - Monorepo

**Complete Property Management Ecosystem for WordPress**

This monorepo contains all PlugHaus Studios plugins, the company website, and shared utilities in a single, unified codebase.

## 🏗️ **Repository Structure**

```
plughaus-studios-monorepo/
├── packages/                          # Individual plugin packages
│   ├── property-management/           # Core property management plugin
│   ├── payments-gateway/              # Future: Payments plugin
│   ├── document-automator/            # Future: Document automation
│   └── shared/                       # Shared utilities & components
├── website/                          # PlugHausStudios.com
├── tools/                            # Build tools & scripts
├── dist/                             # Built/extracted packages
└── docs/                             # Documentation
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

## 📦 **Available Packages**

### Property Management Plugin
- **Free Version**: Basic property, tenant, and lease management
- **Pro Version**: Advanced analytics, automation, and integrations
- **WordPress.org**: Compliant free version for marketplace
- **Commercial**: Licensed pro version with full features

### Shared Utilities
- Common functions across all plugins
- Licensing system integration
- WordPress standards compliance
- PropPlugs ecosystem compatibility

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