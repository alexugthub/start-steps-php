# Installation Guide

This guide will help you install Steps on your web server.

## System Requirements

### Minimum Requirements
- **Web Server**: Apache 2.4+ (with mod_rewrite enabled)
- **PHP**: 8.1 or higher
- **Database**: SQLite support (built into most PHP installations)
- **Disk Space**: 5MB minimum
- **Memory**: 64MB PHP memory limit

### Recommended Requirements
- **Web Server**: Apache 2.4+ or Nginx
- **PHP**: 8.4 or higher
- **Memory**: 128MB+ PHP memory limit
- **Storage**: SSD storage for better performance

## Installation Methods

### Method 1: Single File Upload (Recommended)

1. **Download** the latest `index.php` from the [releases page](https://github.com/alexugthub/start-steps-php/releases)
2. **Upload** the file to your web server's document root or a subdirectory
3. **Set Permissions** (if needed):
   ```bash
   chmod 644 index.php
   chmod 755 /path/to/your/directory
   ```
4. **Access** your installation at `http://yourdomain.com/index.php`
5. **Follow** the setup wizard to complete installation

### Method 2: Full Repository

1. **Clone** the repository:
   ```bash
   git clone https://github.com/yourusername/steps.git
   cd steps/php
   ```
2. **Upload** the contents of the `php` folder to your web server
3. **Configure** your web server to point to the uploaded directory
4. **Access** your installation

## Web Server Configuration

### Apache Configuration

#### Option 1: Using .htaccess (Automatic)
Steps includes a `.htaccess` file that automatically configures Apache. No additional setup required.

#### Option 2: Virtual Host Configuration
Add this to your Apache virtual host configuration:

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /path/to/steps
    
    <Directory /path/to/steps>
        AllowOverride All
        Require all granted
    </Directory>
    
    # Enable mod_rewrite
    RewriteEngine On
</VirtualHost>
```

### Nginx Configuration

If you're using Nginx, add this to your server configuration:

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/steps;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Security: Block access to sensitive files
    location ~ /\.(env|git|htaccess) {
        deny all;
    }
    
    location ~ \.db$ {
        deny all;
    }
}
```

## Verification

After installation, verify everything is working:

1. **Access** your Steps installation
2. **Check** that you see the setup wizard or main interface
3. **Create** a test note to verify database functionality
4. **Check** that URL routing works by navigating to different sections

## Troubleshooting Installation

### Common Issues

#### "Internal Server Error"
- Check Apache error logs
- Verify PHP version compatibility
- Ensure mod_rewrite is enabled

#### "Database Connection Failed"
- Verify SQLite is available in PHP (`php -m | grep sqlite`)
- Check file permissions on the directory
- Ensure PHP can write to the installation directory

#### "Page Not Found" for routes
- Verify .htaccess file is present and readable
- Check that mod_rewrite is enabled
- Confirm AllowOverride is set correctly

#### Permission Issues
```bash
# For uploads and database
chmod 755 /path/to/steps
chmod 644 /path/to/steps/index.php

# If using a dedicated uploads folder
mkdir uploads logs backups
chmod 755 uploads logs backups
```

## Security Considerations

### File Permissions
- Set restrictive permissions on configuration files
- Ensure database files are not web-accessible
- Use HTTPS in production

### Environment Configuration
- Keep sensitive data in environment variables
- Use strong, unique passwords
- Regular security updates

## Next Steps

After successful installation:
1. Complete the [First-Time Setup](setup.md)
2. Follow the [Quick Start Tutorial](quickstart.md)
3. Explore [Core Features](notes.md)

---

*Need help? Check our [Troubleshooting Guide](troubleshooting.md) or [Contact Support](support.md)*
