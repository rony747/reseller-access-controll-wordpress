# Reseller Access

A WordPress plugin that creates a role-based access control system for resellers, providing them with a dedicated frontend dashboard and restricting content access based on user roles.

## Features

- **User Role Management**: Creates and manages a dedicated "Reseller" role in WordPress
- **Content Restriction**: Easily restrict pages and posts to be accessible only to resellers
- **Frontend Dashboard**: Provides resellers with their own frontend dashboard
- **Profile Management**: Allows resellers to update their profile information from the frontend
- **Password Management**: Enables resellers to change their passwords securely
- **Admin Access Control**: Prevents resellers from accessing the WordPress admin area
- **Administrator Override**: Administrators can view all reseller-restricted content for testing and management
- **Customizable Messages**: Configure custom messages for non-resellers trying to access restricted content
- **Redirection**: Option to redirect non-resellers to a specific page when they try to access restricted content

## Installation

1. Download the plugin ZIP file
2. Log in to your WordPress admin panel
3. Navigate to Plugins → Add New
4. Click on the "Upload Plugin" button
5. Choose the ZIP file and click "Install Now"
6. Activate the plugin through the 'Plugins' menu in WordPress

## Usage

### Setting Up the Plugin

1. After activation, go to Settings → Reseller Access
2. Configure the plugin settings:
   - Enable/disable the frontend dashboard
   - Select a page to be used as the dashboard
   - Set a page for redirecting non-resellers (optional)
   - Customize the access denied message

### Creating Reseller Users

1. Go to Users → Add New
2. Create a new user and assign them the "Reseller" role
3. The user will now have reseller privileges

### Restricting Content

1. Edit any post or page
2. Look for the "Reseller Access" meta box in the sidebar
3. Check the "Restrict to Resellers" option
4. Update the post or page

### Setting Up the Dashboard

1. Create a new page (e.g., "Reseller Dashboard")
2. Go to Settings → Reseller Access
3. Select this page as the "Dashboard Page"
4. Resellers who visit this page will see their dashboard including:
   - Profile update form
   - Password change form

## Frequently Asked Questions

**Q: Can administrators see reseller-restricted content?**  
A: Yes, administrators can view all reseller-restricted content for testing and management purposes.

**Q: Will resellers see the WordPress admin bar?**  
A: No, resellers are restricted from accessing the WordPress admin area and will not see the admin bar.

**Q: How do I customize the dashboard appearance?**  
A: You can customize the dashboard appearance by editing the CSS file at `public/css/reseller-access-dashboard.css`.

## Credits

This plugin was developed by [Your Name/Company].

## License

This plugin is licensed under the GPL v2 or later.

## Changelog

### 1.0.0
- Initial release
