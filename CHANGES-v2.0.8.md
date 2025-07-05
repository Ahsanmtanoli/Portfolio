# Woo URL Manager - Version 2.0.8 Changelog

## User-Requested Modifications

### 1. ✅ Default Category Changed from "Accessories" to "Watches"

**What was changed:**
- Changed `private $default_slug = 'accessories';` to `private $default_slug = 'watches';`
- Updated admin UI text to reflect the new default
- Added display of current default category in debug information

**Impact:**
- Products without a category assignment now use `/watches/product-name/` instead of `/accessories/product-name/`
- This addresses the issue where Woodmart theme was likely setting products to accessories by default

**Files updated:**
- Main plugin file: Changed default slug property
- Admin instructions: Updated text to mention "watches" as default
- Debug panel: Now shows current default category

### 2. ✅ Added Taxonomy Delete Functionality in UI

**What was added:**
- Red "Delete" button next to each taxonomy in the admin table
- JavaScript confirmation dialog before deletion
- Visual feedback with fade-out animation when deleting
- Warning notification showing what will be deleted
- Final confirmation before form submission if taxonomies are being deleted

**Features:**
- **Delete Button**: Red button with hover effects for each taxonomy row
- **Confirmation Dialog**: "Are you sure you want to delete the taxonomy 'name'? This action cannot be undone."
- **Visual Feedback**: Row fades out when marked for deletion
- **Warning Notice**: Shows which taxonomies will be deleted when saving
- **Final Confirmation**: Additional warning before form submission
- **Error Logging**: Logs deleted taxonomies to debug.log

**JavaScript Features:**
```javascript
// Confirmation dialog before deletion
var confirmMessage = 'Are you sure you want to delete the taxonomy "' + taxonomy + '"? This action cannot be undone.';

// Visual effects: Row fade-out animation
$('#taxonomy-row-' + taxonomy).fadeOut(300, function() {
    $(this).remove();
});

// Hover effects for delete buttons
$(document).on('mouseenter', '.delete-taxonomy', function() {
    $(this).css({
        'background': '#a00',
        'border-color': '#a00'
    });
});
```

**Backend Processing:**
```php
// Handle taxonomy deletions in sanitize_settings
if (isset($input['delete_taxonomies']) && is_array($input['delete_taxonomies'])) {
    foreach ($input['delete_taxonomies'] as $tax_to_delete) {
        $clean_tax = sanitize_key($tax_to_delete);
        if (isset($sanitized['taxonomies'][$clean_tax])) {
            unset($sanitized['taxonomies'][$clean_tax]);
            error_log('Woo URL Manager: Deleted taxonomy: ' . $clean_tax);
        }
    }
}
```

## Updated UI Elements

### Admin Interface Improvements
1. **Updated Instructions**: Added note about taxonomy deletion functionality
2. **Debug Information**: Now shows current default category
3. **Enhanced Table**: Each taxonomy row now has unique ID for targeting
4. **Hidden Fields**: Container for delete requests that get processed on form submission

### User Experience Enhancements
- **Immediate Visual Feedback**: Taxonomy rows fade out when marked for deletion
- **Multiple Confirmations**: Two-level confirmation system prevents accidental deletions
- **Auto-dismissing Notices**: Warning messages auto-fade after 3 seconds
- **Hover Effects**: Delete buttons change color on hover for better UX

## Technical Implementation

### Safety Features
- **Sanitization**: All taxonomy names are sanitized using `sanitize_key()`
- **Validation**: Checks if taxonomy exists before attempting deletion
- **Logging**: Successful deletions are logged to WordPress debug log
- **Error Handling**: All delete operations wrapped in try-catch blocks

### Browser Compatibility
- Uses jQuery (included with WordPress)
- CSS transitions for smooth animations
- Compatible with all modern browsers

## Updated Files

1. **`woo-url-manager-v2.0.8-updated.php`** - Main plugin file with all modifications
2. **`CHANGES-v2.0.8.md`** - This changelog document

## Installation Instructions

1. **Backup Current Plugin**: Download existing plugin file as backup
2. **Replace Plugin File**: Upload `woo-url-manager-v2.0.8-updated.php` and rename to `woo-url-manager.php`
3. **Test Functionality**: 
   - Check that uncategorized products now default to `/watches/`
   - Test taxonomy deletion functionality in admin panel
4. **Clear Caches**: Clear all caches and flush permalinks

## Expected Behavior After Update

### Default Category Behavior
- **Before**: Products without categories → `/accessories/product-name/`
- **After**: Products without categories → `/watches/product-name/`

### Taxonomy Management
- **New Feature**: Red "Delete" button appears next to each taxonomy
- **Deletion Process**: Click Delete → Confirm → Row fades out → Save settings to finalize
- **Safety**: Multiple confirmation dialogs prevent accidental deletions

## Backward Compatibility

- ✅ All existing functionality preserved
- ✅ Existing taxonomies remain unchanged (only default category changed)
- ✅ URL structure remains the same
- ✅ Breadcrumbs continue to work
- ✅ Redirects still function properly

## Testing Checklist

- [ ] Uncategorized products now use `/watches/` URLs
- [ ] Existing categorized products still use correct category URLs
- [ ] Delete buttons appear next to each taxonomy
- [ ] Deletion confirmation dialog works
- [ ] Taxonomy deletion actually removes from list after saving
- [ ] Debug log shows deleted taxonomies
- [ ] All other plugin functionality remains intact

## Notes

- The default category change addresses Woodmart theme's tendency to assign products to "accessories"
- Taxonomy deletion is immediate in the UI but only processed when settings are saved
- All deletions are logged for troubleshooting purposes
- The changes maintain full backward compatibility with existing installations