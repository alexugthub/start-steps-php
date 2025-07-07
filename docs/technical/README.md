# Steps Technical Documentation

Technical documentation for developers, system administrators, and advanced users.

## Table of Contents

### Architecture & Design
- [System Architecture](architecture.md)
- [Database Schema](database.md)
- [API Documentation](api.md)
- [Security Design](security.md)

### Development
- [Developer Setup](dev-setup.md)
- [Code Standards](coding-standards.md)
- [Testing Guidelines](testing.md)
- [Contributing Guide](contributing.md)

### Deployment & Operations
- [Deployment Guide](deployment.md)
- [Server Configuration](server-config.md)
- [Performance Optimization](performance.md)
- [Monitoring & Logging](monitoring.md)

### Integration & Customization
- [Plugin Development](plugins.md)
- [Theme Customization](themes.md)
- [Custom Integrations](integrations.md)
- [Configuration Reference](config-reference.md)

### Maintenance
- [Backup & Recovery](backup.md)
- [Troubleshooting](tech-troubleshooting.md)
- [Update Procedures](updates.md)
- [Migration Guide](migration.md)

---

## Quick Reference

### File Structure
```
Steps/
├── php/
│   ├── index.php           # Main application file
│   ├── .htaccess          # Apache configuration
│   ├── .env               # Environment configuration
│   └── steps.db           # SQLite database
├── docs/                  # Documentation
└── releases/              # Release packages
```

### Key Technologies
- **Backend**: PHP 8.4+
- **Database**: SQLite 3
- **Frontend**: Vanilla JavaScript, CSS3
- **Server**: Apache 2.4+ with mod_rewrite

### Development Stack
- **Version Control**: Git
- **Code Quality**: PHP CodeSniffer (PSR-12)
- **Documentation**: Markdown
- **Build**: No build process (single-file architecture)

---

*For user-facing documentation, see the [User Guide](../user/README.md)*
