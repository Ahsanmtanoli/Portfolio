# Quick Installation Guide - Woo URL Manager v2.0.8

## Files Provided
1. `woo-url-manager-fixed.php` - The updated plugin file (v2.0.8)
2. `woo-url-manager-fix-documentation.md` - Comprehensive documentation
3. `install-instructions.md` - This quick guide

## Step-by-Step Installation

### Step 1: Create Backup
```bash
# Before making any changes, backup:
# 1. Your entire website (via hosting panel or backup plugin)
# 2. Current plugin folder: /wp-content/plugins/woo-url-manager/
# 3. WordPress database
```

### Step 2: Access Your Website Files
- Via cPanel File Manager, FTP, or hosting file manager
- Navigate to: `/wp-content/plugins/woo-url-manager/`

### Step 3: Replace Plugin File
**Option A: Safe Method (Recommended)**
1. Rename existing folder: `woo-url-manager` to `woo-url-manager-backup`
2. Create new folder: `woo-url-manager`
3. Upload `woo-url-manager-fixed.php` to the new folder
4. Rename the file to: `woo-url-manager.php`

**Option B: Direct Replace**
1. Download current `woo-url-manager.php` as backup
2. Replace it with `woo-url-manager-fixed.php`
3. Rename to: `woo-url-manager.php`

### Step 4: Activate Plugin
1. Go to WordPress Admin → Plugins
2. Find "Woo URL Manager" and activate if needed
3. If you see a critical error, restore backup and check debug log

### Step 5: Configure Settings
1. Go to WooCommerce → URL Manager
2. Verify settings:
   - Product Slugs: `watches,wall-clocks,accessories`
   - Enable Redirect: ✓ (checked)
3. Click "Save & Flush Permalinks"

### Step 6: Flush Permalinks
1. Go to Settings → Permalinks
2. Click "Save Changes" (no changes needed)
3. This ensures rewrite rules are registered

### Step 7: Test URLs
Test these specific URLs:
- `https://watchoice.pk/watches/michael-kors-rose-gold-womens-chronograph-watch-black-dial-crystal-strap/`
- `https://watchoice.pk/wall-clocks/guess-womens-stainless-steel-crystal-accented-black-dial-watch/`
- `https://watchoice.pk/accessories/ornare-auctor/`

## If Something Goes Wrong

### Critical Error Appears
1. **Immediately restore backup**
2. Check `/wp-content/debug.log` for error details
3. Enable WordPress debug mode in `wp-config.php`:
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   define('WP_DEBUG_DISPLAY', false);
   ```

### URLs Still Return 404
1. **Flush permalinks again**: Settings → Permalinks → Save Changes
2. **Check product setup**: Ensure products are in correct categories
3. **Clear all caches**: Plugin caches, server caches, browser cache
4. **Check .htaccess**: Ensure it contains proper WordPress rewrite rules

### Plugin Conflicts
1. **Test with minimal plugins**: Disable all except WooCommerce and Woo URL Manager
2. **Switch to default theme**: Temporarily use Twenty Twenty-Five theme
3. **Check for specific conflicts**: Rank Math, Yoast, WP Rocket, Woodmart

## Debug Information
- Check WooCommerce → URL Manager for system information
- Monitor debug log for entries starting with "Woo URL Manager:"
- Look for successful URL mapping messages

## Success Indicators
✅ Website loads without critical errors
✅ Product URLs resolve correctly (200 status)
✅ Breadcrumbs show correct category structure
✅ Old /product/ URLs redirect to new category URLs
✅ No 404 errors in debug log

## Need Help?
If issues persist after following these steps:
1. Check the comprehensive documentation file
2. Review debug log entries
3. Test with minimal plugins/default theme
4. Verify product categories and slugs match exactly

## Post-Installation Checklist
- [ ] Website loads without errors
- [ ] All three test URLs work
- [ ] Breadcrumbs display correctly
- [ ] Old URLs redirect properly
- [ ] Debug log shows no errors
- [ ] Backup of working configuration created