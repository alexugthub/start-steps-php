# Frequently Asked Questions

Find answers to the most common questions about Steps.

## General Questions

### What is Steps?
Steps is a self-hosted project management and note-taking application designed to help you organize your ideas, projects, and tasks in one central location. It starts as a simple note-taking tool and evolves into a comprehensive management system.

### Do I need technical knowledge to use Steps?
Not at all! Steps is designed for ease of use. If you can use a web browser, you can use Steps. Installation does require basic web hosting knowledge, but many users find it straightforward.

### Is Steps free?
Steps offers both free and premium versions:
- **Free Version**: Core note-taking, basic project management, unlimited local use
- **Premium Version**: Advanced features, analytics, collaboration tools, priority support

## Installation & Setup

### What are the system requirements?
- Web server (Apache 2.4+ recommended)
- PHP 8.1 or higher (8.4+ recommended)
- SQLite support (included in most PHP installations)
- 5MB disk space minimum
- Modern web browser

### Can I install Steps on shared hosting?
Yes! Steps is designed to work on most shared hosting providers. You just need PHP support and the ability to upload files to your web directory.

### How do I upgrade Steps?
For single-file installations, simply replace the old `index.php` with the new version. Your data is stored separately in the database and won't be affected.

### Can I migrate my data to a new server?
Yes, you can easily migrate by:
1. Copying your database file (steps.db)
2. Uploading the new index.php to your new server
3. Configuring any environment variables

## Features & Usage

### How many notes/projects can I create?
There are no artificial limits in Steps. The only limitation is your server's storage capacity and performance.

### Can I collaborate with others?
- **Free Version**: Single-user access
- **Premium Version**: Multi-user collaboration, shared projects, team management

### Does Steps work offline?
Steps is a web application that requires an internet connection to access your server. However, if you host it locally, you can access it on your local network without internet.

### Can I export my data?
Yes! Steps supports:
- JSON export for all data
- Individual note/project exports
- Backup functionality for complete data preservation

### Is there a mobile app?
Steps is designed as a responsive web application that works great on mobile browsers. There's no separate mobile app needed.

## Privacy & Security

### Where is my data stored?
Your data is stored on your own server in a SQLite database. Steps doesn't send your data to any third-party services.

### Is my data encrypted?
- Data at rest: Database files should be secured by your hosting provider
- Data in transit: Use HTTPS for encrypted communication
- Premium features may include additional encryption options

### Can I backup my data?
Yes! You can:
- Download database backups from the Settings panel
- Export data in JSON format
- Copy the database file directly from your server

### Who can access my Steps installation?
Only people you give access to. Steps includes user management features to control who can view and edit your data.

## Technical Questions

### What databases does Steps support?
Currently, Steps uses SQLite for simplicity and portability. Future versions may support MySQL/PostgreSQL for larger installations.

### Can I customize the appearance?
Yes! Steps includes:
- Light and dark themes
- Customizable color schemes
- CSS customization options
- Layout preferences

### Does Steps integrate with other tools?
Current integrations are limited to focus on core functionality. Future versions may include:
- Calendar integrations
- Email notifications
- Third-party app connections
- API access for custom integrations

### Can I run Steps on Windows/Mac/Linux?
Yes! Steps runs on any system that supports PHP and a web server. This includes Windows, macOS, Linux, and most hosting providers.

## Troubleshooting

### Steps won't load after installation
Check these common issues:
1. Verify PHP version (8.1+)
2. Ensure mod_rewrite is enabled (Apache)
3. Check file permissions
4. Review server error logs

### I can't save notes or create projects
This usually indicates a database permission issue:
1. Check that PHP can write to the installation directory
2. Verify SQLite is available in PHP
3. Look for error messages in browser developer tools

### The interface looks broken
This might be a caching issue:
1. Clear your browser cache
2. Try a different browser
3. Check if CSS files are loading properly

### I forgot my password
Currently, password reset requires:
1. Access to your server files
2. Manual database modification, or
3. Reinstallation (with data backup/restore)

## Account & Billing

### How do I upgrade to Premium?
Premium features will be available through:
- One-time purchase option
- Simple upgrade process within the application
- Instant activation of premium features

### Can I downgrade from Premium?
Yes, you can always return to the free version. Your data remains intact, but premium features become unavailable.

### Do you offer refunds?
We offer a satisfaction guarantee. Contact support within 30 days if you're not happy with your premium purchase.

## Getting More Help

### Where can I find video tutorials?
Check our [Video Tutorials](tutorials.md) page for step-by-step visual guides.

### How do I report a bug?
- Email: support@steps-app.com
- GitHub: Create an issue on our repository
- Include: Steps version, browser info, and steps to reproduce

### Can I request new features?
Absolutely! We love hearing from users:
- Email feature requests to support@steps-app.com
- Join discussions on our GitHub repository
- Vote on existing feature requests

### How do I contact support?
- **Email**: support@steps-app.com
- **Response time**: 24-48 hours for most inquiries
- **Premium users**: Priority support with faster response times

---

*Don't see your question here? [Contact our support team](support.md) - we're here to help!*
