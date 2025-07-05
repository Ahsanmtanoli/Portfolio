# How to Create Woo URL Manager Plugin ZIP File

## 📁 Required File Structure

Create a folder named `woo-url-manager` with this exact structure:

```
woo-url-manager/
├── woo-url-manager.php          (Main plugin file)
├── readme.txt                   (WordPress plugin readme)
└── changelog.txt               (Optional: Version history)
```

## 🗂️ Step-by-Step Instructions

### Step 1: Create Plugin Folder
1. Create a new folder on your computer named: `woo-url-manager`

### Step 2: Add Main Plugin File
1. Copy the content from `woo-url-manager-v2.0.8-final.php`
2. Create a new file named: `woo-url-manager.php`
3. Paste the content and save

### Step 3: Create readme.txt (Optional but Recommended)
Create `readme.txt` with the following content:

```
=== Woo URL Manager ===
Contributors: Watchoice.pk
Tags: woocommerce, urls, permalinks, seo, categories
Requires at least: 6.5
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 2.0.8
License: GPL v2 or later

Manage WooCommerce product URLs with custom category-based slugs, taxonomy management, and smart URL detection.

== Description ==

Woo URL Manager allows you to create custom category-based product URLs for your WooCommerce store:

* Products in "Watches" category use: /watches/product-name/
* Products in "Wall Clocks" category use: /wall-clocks/product-name/  
* Products in "Accessories" category use: /accessories/product-name/
* Uncategorized products default to: /watches/product-name/

**Features:**
* Smart URL detection (categories vs products)
* Custom taxonomy management with delete functionality
* SEO-friendly breadcrumbs (Yoast, Rank Math, WooCommerce)
* 301 redirects from old /product/ URLs
* Debug logging for troubleshooting
* Watchoice.pk branding and styling

**Category URLs:** 
* /watches/men/ (shows men's watches)
* /wall-clocks/vintage/ (shows vintage wall clocks)

**Product URLs:**
* /watches/rolex-submariner/ (shows specific product)

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/woo-url-manager/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to WooCommerce → URL Manager to configure settings
4. Go to Settings → Permalinks and click "Save Changes" to flush rules

== Frequently Asked Questions ==

= Why are my category URLs showing 404 errors? =

Make sure:
1. Categories exist in Products → Categories
2. Permalinks are flushed (Settings → Permalinks → Save Changes)
3. No caching conflicts (clear all caches)

= How do I change the default category? =

The default is set to "watches" in version 2.0.8. Products without categories will use /watches/ URLs.

= Can I delete unwanted taxonomies? =

Yes! Use the red "Delete" button next to each taxonomy in WooCommerce → URL Manager.

== Changelog ==

= 2.0.8 (Final) =
* FIXED: Category URL conflicts (smart detection for products vs categories)
* CHANGED: Default category from "accessories" to "watches"
* ADDED: Taxonomy delete buttons in admin UI
* IMPROVED: Enhanced debug logging and error handling
* IMPROVED: Better compatibility with Rank Math, Yoast, Woodmart

= 2.0.7 =
* FIXED: Critical PHP errors and 404 issues
* ADDED: Comprehensive error handling
* ADDED: Enhanced rewrite rules
* IMPROVED: Product lookup with fallback methods

== Upgrade Notice ==

= 2.0.8 =
Major update fixing category URL conflicts and adding taxonomy management. Backup before upgrading.
```

### Step 4: Create ZIP File

**For Windows:**
1. Select the `woo-url-manager` folder
2. Right-click → "Send to" → "Compressed (zipped) folder"
3. Rename to: `woo-url-manager-v2.0.8-final.zip`

**For Mac:**
1. Right-click on the `woo-url-manager` folder
2. Select "Compress woo-url-manager"
3. Rename to: `woo-url-manager-v2.0.8-final.zip`

**For Linux:**
```bash
zip -r woo-url-manager-v2.0.8-final.zip woo-url-manager/
```

## 📋 Final Checklist

Before zipping, ensure:
- [ ] Folder is named exactly: `woo-url-manager`
- [ ] Main file is named exactly: `woo-url-manager.php`
- [ ] Plugin header shows: `Version: 2.0.8 (Final)`
- [ ] No extra files or folders included
- [ ] File structure matches WordPress standards

## 🚀 Installation on WordPress

1. **Upload ZIP**: Plugins → Add New → Upload Plugin
2. **Browse**: Select your `woo-url-manager-v2.0.8-final.zip`
3. **Install**: Click "Install Now"
4. **Activate**: Click "Activate Plugin"
5. **Configure**: Go to WooCommerce → URL Manager
6. **Flush**: Go to Settings → Permalinks → Save Changes

## ⚠️ Important Notes

- The folder name MUST be `woo-url-manager` (WordPress requirement)
- The main file MUST be `woo-url-manager.php` (same as folder name)
- Don't include version numbers in folder/file names
- WordPress will automatically detect the version from the plugin header

## 🎯 Quick Test

After installation:
1. Test category URL: `yoursite.com/watches/men/`
2. Test product URL: `yoursite.com/watches/product-name/`
3. Check admin: WooCommerce → URL Manager
4. Verify delete buttons work for taxonomies

Your plugin ZIP file will be ready for upload to any WordPress site! 🎉