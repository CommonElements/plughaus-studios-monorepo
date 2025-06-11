# 🚀 Vireo Designs Development Workflow

## 📋 Branch Strategy

### **Main Branches**
- **`main`**: Production-ready stable code only
  - All code must be tested and verified
  - Only accepts merges via pull requests
  - Direct commits are **blocked** by git hooks
  - Automatically deployed to production environments

- **`develop`**: Active development branch
  - All ongoing development work
  - Integration point for features
  - Regularly tested and verified
  - Base branch for new feature branches

### **Supporting Branches**
- **`feature/*`**: New feature development
  - Branch from: `develop`
  - Merge back to: `develop`
  - Example: `feature/pro-license-system`

- **`hotfix/*`**: Emergency production fixes
  - Branch from: `main`
  - Merge back to: `main` AND `develop`
  - Example: `hotfix/critical-security-patch`

- **`release/*`**: Prepare new production releases
  - Branch from: `develop`
  - Merge back to: `main` AND `develop`
  - Example: `release/v1.2.0`

## 🔒 Branch Protection

### **Automated Safeguards**
- **Pre-commit hooks** prevent commits to `main`
- **Pre-push hooks** block direct pushes to `main`
- **Syntax validation** for PHP and JavaScript
- **Security scanning** for sensitive information
- **File size limits** to prevent large binary commits

### **Manual Safeguards**
- Pull request reviews required for `main`
- Comprehensive testing before merges
- Documentation updates with code changes

## 📝 Commit Message Standards

### **Format Template**
```
🎯 [TYPE]: Brief description (max 72 chars)

📝 Detailed explanation:
- What was changed
- Why it was changed
- Any breaking changes

🔧 Technical details:
- Implementation notes
- Dependencies affected

🎯 Next steps:
- Follow-up tasks needed

🤖 Generated with [Claude Code](https://claude.ai/code)

Co-Authored-By: Claude <noreply@anthropic.com>
```

### **Commit Types**
| Emoji | Type | Description |
|-------|------|-------------|
| 🚀 | FEATURE | New feature or major functionality |
| 🔧 | FIX | Bug fix or error correction |
| 📝 | DOCS | Documentation updates |
| 🎨 | STYLE | Code formatting, no functional changes |
| ♻️ | REFACTOR | Code refactoring without new features |
| 🧪 | TEST | Adding or updating tests |
| 🔨 | BUILD | Build system or external dependencies |
| 📦 | RELEASE | Version release preparation |
| 🚨 | HOTFIX | Critical emergency fix |
| 🧹 | CLEANUP | Code cleanup and maintenance |

## 🛠️ Development Process

### **Starting New Work**
```bash
# Switch to develop branch
git checkout develop

# Pull latest changes
git pull origin develop

# Create feature branch
git checkout -b feature/your-feature-name

# Start development...
```

### **Regular Development**
```bash
# Stage changes
git add .

# Commit with descriptive message
git commit -m "🚀 FEATURE: Add license validation system"

# Push feature branch
git push origin feature/your-feature-name
```

### **Merging Features**
```bash
# Switch to develop
git checkout develop

# Pull latest changes
git pull origin develop

# Merge feature branch
git merge feature/your-feature-name

# Push updated develop
git push origin develop

# Clean up feature branch
git branch -d feature/your-feature-name
git push origin --delete feature/your-feature-name
```

### **Creating Releases**
```bash
# Create release branch from develop
git checkout develop
git checkout -b release/v1.2.0

# Finalize version numbers, documentation
# Test thoroughly

# Merge to main
git checkout main
git merge release/v1.2.0
git tag -a v1.2.0 -m "Release version 1.2.0"

# Merge back to develop
git checkout develop
git merge release/v1.2.0

# Push everything
git push origin main develop --tags
```

## 🧪 Quality Assurance

### **Pre-commit Checks**
- ✅ PHP syntax validation
- ✅ JavaScript syntax validation  
- ✅ Large file detection (>10MB)
- ✅ Sensitive information scanning
- ✅ Branch protection enforcement
- ⚠️ TODO/FIXME comment warnings

### **Code Standards**
- **PHP**: WordPress Coding Standards
- **JavaScript**: ESLint with WordPress preset
- **CSS**: WordPress CSS Coding Standards
- **Documentation**: Clear, comprehensive inline docs

### **Testing Requirements**
- **Unit Tests**: Core functionality
- **Integration Tests**: Plugin interactions
- **Manual Testing**: User workflows
- **Browser Testing**: Cross-browser compatibility

## 🚨 Emergency Procedures

### **Hotfix Process**
```bash
# Create hotfix from main
git checkout main
git checkout -b hotfix/critical-issue

# Fix the issue
# Test thoroughly

# Merge to main
git checkout main
git merge hotfix/critical-issue
git tag -a v1.1.1 -m "Hotfix version 1.1.1"

# Merge to develop
git checkout develop
git merge hotfix/critical-issue

# Push and deploy
git push origin main develop --tags
```

### **Rollback Procedures**
```bash
# Revert specific commit
git revert <commit-hash>

# Reset to previous stable version
git reset --hard <stable-commit-hash>

# Force push (use with extreme caution)
git push --force-with-lease origin main
```

## 📊 Monitoring & Maintenance

### **Regular Tasks**
- **Weekly**: Review and clean up old feature branches
- **Monthly**: Update dependencies and security patches
- **Quarterly**: Performance review and optimization
- **Annually**: Major version planning and architecture review

### **Health Checks**
- Git repository size and optimization
- Branch synchronization status
- Code quality metrics
- Security vulnerability scans

## 🎯 Best Practices

### **DO**
- ✅ Commit frequently with small, focused changes
- ✅ Write descriptive commit messages
- ✅ Test thoroughly before merging
- ✅ Keep branches up to date with develop
- ✅ Use meaningful branch names
- ✅ Document breaking changes

### **DON'T**
- ❌ Commit directly to main branch
- ❌ Force push to shared branches
- ❌ Commit large binary files
- ❌ Include sensitive information
- ❌ Use generic commit messages
- ❌ Leave TODO comments in production code

## 🔧 Troubleshooting

### **Common Issues**

**Blocked from committing to main:**
```bash
# Switch to develop branch
git checkout develop
# Or create feature branch
git checkout -b feature/my-changes
```

**Large file rejection:**
```bash
# Remove large file
git rm path/to/large/file
# Add to .gitignore
echo "path/to/large/file" >> .gitignore
```

**Sensitive information detected:**
```bash
# Remove sensitive data
git rm --cached path/to/sensitive/file
# Use environment variables instead
```

### **Getting Help**
- Check git status: `git status`
- View commit history: `git log --oneline -10`
- Check branch info: `git branch -vv`
- View git configuration: `git config --list`

---

**This workflow ensures code quality, prevents errors, and maintains a stable production environment while enabling efficient development.**