# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**MultiAnalityca** is a WordPress-based medical/clinical website running on the Avada premium theme with extensive plugin ecosystem. The site uses dual page builders (Elementor and Fusion Builder), comprehensive form systems, and multi-channel communication including WhatsApp integration.

## Technology Stack

- **CMS**: WordPress (custom installation)
- **Theme**: Avada (premium parent) + Avada-Child-Theme
- **Database**: MySQL/MariaDB (`multiana_clinica77`)
- **Table Prefix**: `wptt_` (custom prefix)
- **Server**: Apache with mod_rewrite (Laragon local environment)
- **PHP**: 7.2.24+ (configured for 8.3 recommended)

## Development Commands

### WordPress CLI (WP-CLI)

```bash
# List installed plugins
wp plugin list

# List active themes
wp theme list

# Database export
wp db export

# Database import
wp db import multiana.sql

# Flush rewrite rules (after permalink changes)
wp rewrite flush

# Clear cache (requires WP Rocket or LiteSpeed Cache)
wp cache flush

# Update WordPress core
wp core update

# Update all plugins
wp plugin update --all

# Check site status
wp core check-update
```

### Apache Configuration

The site requires `mod_rewrite` enabled (configured in `wp-cli.yml`). After changes to `.htaccess`, restart Apache via Laragon.

### Database

- **Database Name**: `multiana_clinica77`
- **Credentials**: Configured in `wp-config.php`
- **Backups**: Large SQL files (110 MB) in root: `multiana.sql`, `multiana localhost.sql`
- **Total Tables**: 103 custom tables including WordPress core, Elementor forms, Fusion forms, Action Scheduler, Jetpack WAF, and plugin-specific tables

## Architecture

### Custom Child Theme

Location: `wp-content/themes/Avada-Child-Theme/`

Key customizations in `functions.php`:
- **Custom MIME type support**: Allows `.vcf` (vCard) file uploads for medical contact information
- **Internationalization**: Child theme text domain setup
- **Style enqueuing**: Inherits from Avada parent theme

The child theme is minimal and primarily inherits from the Avada parent. All custom functionality should be added to the child theme's `functions.php` to preserve updates to the parent theme.

### Page Builders

The site uses **dual page builders**:
1. **Elementor** (primary) - Version 3.23.1 with PRO features
   - Form submissions stored in `wptt_e_submissions` and `wptt_e_submissions_values`
   - Custom Elementor widgets and templates
2. **Fusion Builder** - Integrated with Avada theme
   - Form submissions stored in `wptt_fusion_form_*` tables
   - Custom fusion components

When editing pages, check which builder was used originally. Avoid mixing builders on the same page.

### Forms System

Multiple form systems are active:
- **Contact Form 7** (v5.9.6) - Primary contact forms with WhatsApp integration
- **Elementor Forms** - Page builder forms with database storage
- **Fusion Forms** - Avada-specific forms with submissions tracking
- **WPForms Lite** - Additional form capabilities

Form submissions are stored in dedicated database tables. Check `wptt_e_submissions`, `wptt_e_submissions_values`, and `wptt_fusion_form_*` tables for form data.

### Caching Architecture

Multi-layer caching is implemented:
1. **WP Rocket** - Primary caching plugin (enabled via `WP_CACHE` constant)
2. **LiteSpeed Cache** - Server-level caching
3. **Browser caching** - Configured in `.htaccess`

After making changes to templates, styles, or JavaScript, clear all cache layers:
```bash
wp cache flush
# Then clear WP Rocket and LiteSpeed Cache via admin panel
```

### Security Configuration

- **SSL/HTTPS**: Really Simple SSL plugin manages secure connections
  - Security key: `RSSSL_KEY` defined in `wp-config.php`
  - Session cookies configured as secure and httponly
- **WAF**: Jetpack Web Application Firewall (logs in `wptt_jetpack_waf_*` tables)
- **Login Security**: Loginizer plugin (logs in database)
- **Backups**: UpdraftPlus and All-in-One WP Migration
- **Anti-spam**: Akismet for comments, Stop Spammer Registrations for users

### Email System

**Easy WP SMTP** configured for email delivery:
- Debug logs stored in `wptt_easy_wp_smtp_debug_events` table
- Email tasks managed via `wptt_easy_wp_smtp_tasks` table
- Check these tables when debugging email issues

### Background Processing

**Action Scheduler** manages background tasks:
- Queued actions: `wptt_actionscheduler_actions`
- Completed logs: `wptt_actionscheduler_logs`
- Claims: `wptt_actionscheduler_claims`
- Groups: `wptt_actionscheduler_groups`

Monitor these tables for stuck or failed background jobs.

## Medical/Clinical Specific Features

### vCard Upload Support

The child theme enables `.vcf` file uploads for medical professionals' contact information:
- Filter: `upload_mimes`
- MIME type: `text/x-vcard`
- Location: `wp-content/themes/Avada-Child-Theme/functions.php:14-20`

### WhatsApp Integration

Contact Form 7 integrates with WhatsApp for clinic communication via the "Contact Form 7 WhatsApp Integration" plugin. Form submissions can trigger WhatsApp messages.

### Analytics & User Tracking

- **Hotjar**: User behavior and heatmap tracking
- **Google Analytics**: Via Google Analytics for WordPress plugin
- **Google Site Kit**: Google services integration
- **Jetpack**: Site stats and monitoring

## Plugin Management

46 active plugins (22.89 MB total). Key categories:

**Page Builders**: Elementor, Elementor Pro, Fusion Builder, Visual Composer
**Performance**: WP Rocket, LiteSpeed Cache, WP Smushit, Async JavaScript
**SEO**: Yoast SEO, All in One SEO (AIOSEO)
**Security**: Really Simple SSL, Sucuri Scanner, Loginizer, Updraft Plus
**Forms**: Contact Form 7, WPForms Lite, Elementor Forms, Fusion Forms
**Marketing**: Hotjar, Sum.me, Buttonizer, LeadIn (HubSpot)

### Plugin Updates

Before updating plugins:
1. Create full backup via UpdraftPlus or All-in-One WP Migration
2. Export database: `wp db export backup-$(date +%Y%m%d).sql`
3. Update in staging environment first (if available)
4. Clear all cache layers after updates

## Database Schema

103 tables organized by functionality. Key custom tables:

**Elementor**:
- `wptt_e_submissions` - Form submissions
- `wptt_e_submissions_values` - Individual field values

**Fusion Forms**:
- `wptt_fusion_form_*` - Form data and submissions

**Revolution Slider**:
- `wptt_revslider_css` - Slider styles
- `wptt_revslider_layer_animations` - Animation definitions

**SEO**:
- `wptt_aioseo_*` - All in One SEO data
- Yoast tables for SEO metadata

**Email & Tasks**:
- `wptt_easy_wp_smtp_debug_events` - Email debug logs
- `wptt_easy_wp_smtp_tasks` - Email task queue

**Background Jobs**:
- `wptt_actionscheduler_*` - Scheduled task management

**Security**:
- `wptt_jetpack_waf_*` - Web Application Firewall logs
- `wptt_aio_login_activity` - Loginizer security logs

## Important Configuration Files

- **wp-config.php**: Database credentials, security keys, WP_CACHE constant, Really Simple SSL configuration
- **wp-cli.yml**: WP-CLI configuration requiring mod_rewrite
- **.htaccess**: Apache rewrite rules (multiple backup versions exist)
- **php.ini / .user.ini**: PHP configuration overrides

## Development Workflow

### Making Theme Changes

1. Always work in the child theme: `wp-content/themes/Avada-Child-Theme/`
2. Add custom functions to `functions.php`
3. Override parent templates by copying to child theme directory structure
4. Enqueue custom styles in child `style.css`
5. Clear cache after changes

### Making Plugin Changes

Avoid direct plugin modifications. Use hooks and filters in child theme's `functions.php` instead. If plugin modifications are necessary, document them clearly for future updates.

### Database Modifications

1. Always backup before schema changes: `wp db export`
2. Use WordPress database API (`$wpdb`) for queries
3. Respect the `wptt_` table prefix
4. Follow WordPress coding standards for database operations

### Testing Form Submissions

Form submissions are stored in multiple tables. To test:
1. Submit a test form via the frontend
2. Check appropriate database table:
   - Elementor: `wptt_e_submissions`, `wptt_e_submissions_values`
   - Fusion: `wptt_fusion_form_*`
   - Contact Form 7: Email notifications (no DB storage by default)
3. Verify email delivery in `wptt_easy_wp_smtp_debug_events`
4. Check WhatsApp integration functionality

## File Structure Notes

### Custom Directories

- `ot/` - Custom project files (purpose TBD)
- `sistema/` - Custom system code (purpose TBD)
- `doc/` - Documentation directory
- `cgi-bin/` - CGI scripts

### .htaccess Backup Versions

Multiple backup versions exist indicating PHP upgrade and configuration history:
- `.htaccess.bak.1447294509` (2015)
- `.htaccess.bak.1535033974` (2018)
- `.htaccess.php-upgrade-backup`
- `.htaccess.phpupgrader.*`

The current `.htaccess` should be used. Restore from backups only if needed.

## Troubleshooting

### Site Not Loading
1. Check Apache is running in Laragon
2. Verify database connection in `wp-config.php`
3. Check `.htaccess` syntax
4. Review error logs in Laragon

### Forms Not Submitting
1. Check email configuration in Easy WP SMTP
2. Review `wptt_easy_wp_smtp_debug_events` for errors
3. Verify form plugin is active and configured
4. Test email delivery manually

### Cache Issues
1. Clear WP Rocket cache: Admin → WP Rocket → Clear Cache
2. Clear LiteSpeed Cache: Admin → LiteSpeed Cache → Purge All
3. Clear browser cache
4. Use WP-CLI: `wp cache flush`

### Background Jobs Not Running
1. Check Action Scheduler: Admin → Tools → Action Scheduler
2. Review `wptt_actionscheduler_actions` table for stuck jobs
3. Verify WP-Cron is functioning: `wp cron event list`

### SSL/Security Errors
1. Check Really Simple SSL settings
2. Verify `.htaccess` HTTPS redirects
3. Review Jetpack WAF logs in `wptt_jetpack_waf_*` tables

## Important Reminders

- **Always use the child theme** for customizations
- **Database operations**: Never modify database without backup
- **Cache clearing**: Required after template/asset changes
- **Plugin updates**: Test in staging, backup first
- **Table prefix**: Always use `wptt_` when creating custom tables
- **Medical data**: Handle patient information according to privacy regulations
- **vCard uploads**: Use the custom MIME type filter already in place

## Key Contacts & Resources

- **Theme Documentation**: Avada theme documentation at ThemeFusion
- **Elementor Documentation**: https://elementor.com/help/
- **WordPress Codex**: https://codex.wordpress.org/
- **WP-CLI Handbook**: https://make.wordpress.org/cli/handbook/
