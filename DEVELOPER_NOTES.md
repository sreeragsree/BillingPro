## Sales Return & Purchase Return: Batch Dropdown & Batch-wise Handling (2025-10-25)

### Overview
- Aligned batch selection and batch-wise stock behavior for **Sales Return** and **Purchase Return** with existing **Sales** and **Purchase** modules.
- Ensures consistent dropdown labels, per-batch quantities, and stock updates.

### Sales Return (`sales_return/create`)
- **Model**: `application/models/Sales_return_model.php`
  - `get_items_info`, `sales_list`, `return_sales_list` now load `db_batches` for each item (only batches with `quantity > 0`).
  - `return_row_with_data` renders a batch `<select>` (`tr_batch_id_{row}_111`) with option label:
    - `{sales_price (4dp)} - {alphabet_price}`.
  - `verify_save_and_update` stores `batch_id` into `db_salesitemsreturn` and calls `Pos_model::update_stock_in_batch($item_id, $batch_id)` alongside `update_items_quantity`.
- **View / JS**:
  - `application/views/sales-return.php` adds a **Batch No** column and inline `batch_change()` + `checkForDuplicates()` helper functions.
  - `theme/js/sales-return.js` autocomplete includes `batch_id` and passes it into `return_row_with_data(item_id, batch_id)`.

### Purchase Return (`purchase_return/create`)
- **Model**: `application/models/Purchase_returns_model.php`
  - `get_items_info`, `purchase_list`, `return_purchase_list` now query `db_batches` (per item, `quantity > 0`) and pass `batch` + `batch_id` into `return_row_with_data`.
  - `return_row_with_data` renders a batch `<select>` (`tr_batch_id_{row}_111`) with option label:
    - `{purchase_price (4dp)} - {alphabet_price}`.
  - `verify_save_and_update` reads `tr_batch_id_{i}_111`, persists `batch_id` into `db_purchaseitemsreturn`, and calls `Pos_model::update_stock_in_batch($item_id, $batch_id)` after `update_items_quantity`.
- **View / JS**:
  - `application/views/purchase_return.php` adds a **Batch No** column and inline `batch_change()` + `checkForDuplicates()` functions mirroring Purchase.
  - `theme/js/purchase_return.js` autocomplete maps `batch_id` and passes it into `return_row_with_data(item_id, batch_id)`; each new row triggers `.batchListing.change()` to apply the initial batch.

---
# Developer Notes: POS Invoice Updates (2025-10-22)

## Permission Management

### Access Control
- **Admin Level**: Full access to all features and settings
- **Staff Level**: Limited access based on role permissions
  - Can process sales and view reports
  - Restricted from accessing sensitive system settings
  - Limited access to financial data based on role configuration

## Logo Implementation

For adding a store logo to the POS invoice header, use the following HTML snippet:

```html
<tr>
    <td align="center" width="40%" style="padding: 5px 0;">
        <img src="<?= base_url($store_logo);?>" width="50%" height="auto" style="max-width: 200px;">
    </td>
</tr>
```

## QR Code Implementation

For adding QR code to POS invoice, use the following HTML snippet:

```html
<div style="margin: 10px 0;">
    <img src="<?= base_url('theme/dist/img/payQr.png') ?>" alt="QR Code" style="width: 90px; height: 90px; display: block; margin: 0 auto;">
</div>
```

This code should be placed in the POS invoice template where you want the QR code to appear. The QR code is centered and has a fixed size of 90x90 pixels with some margin for better spacing.

## POS: Fix for Duplicate Batch Total Calculation (2025-10-22)

### Overview
- Fixed an issue where adding the same item with different batches would cause incorrect grand total calculation when adding the same item again.
- The problem occurred when merging quantities of duplicate item+batch combinations.

### Key Changes
1. **batch_change Function Update**:
   - Modified the order of operations when merging duplicate item+batch rows
   - Now removes the duplicate row from the DOM before updating quantities and recalculating totals
   - Ensures the grand total is calculated correctly by preventing double-counting of the same item+batch

2. **Behavior Before Fix**:
   - When adding the same item with different batches, each would be added as a new row
   - However, when adding the same item again, the grand total would incorrectly include the subtotal of the row being removed

3. **Behavior After Fix**:
   - Duplicate rows are now removed before updating quantities and recalculating totals
   - Grand total is calculated correctly with no duplicate counting
   - Maintains all existing functionality for batch handling and quantity management

### Files Modified
1. `application/views/pos.php`
   - Updated `batch_change` function to handle DOM manipulation and total calculation in the correct order

---

## POS: Multi-batch lines, per-row quantity/tax fixes, and default batch (2025-10-12)

### Overview
- Allow multiple cart lines for the same item, one per selected batch.
- Prevent duplicates based on the Item+Batch combination (not just Item).
- Make quantity row-specific to avoid DOM id collisions when the same item appears multiple times.
- Fix tax computation to use the row’s quantity and reflect Inclusive/Exclusive correctly.
- Auto-select a default batch when an item is added (first available for that item not already used in the cart).

### Key Behavior
- Add-to-cart: the same product can be added multiple times; each row has its own batch select.
- Duplicate rule: choosing a batch that’s already used for that product in the cart shows a warning and reverts the selection (doesn’t delete the row).
- Default batch: when loading batches for a new row, we select the first batch not already used for that product; if all are used, the dropdown stays blank.
- Quantity handling:
  - Visible qty input is now per-row: `#item_qty_vis_{row}`.
  - Hidden row value posted to backend: `item_qty_{row}`.
  - Backend reads `item_qty_{i}` for the i-th row, ensuring independent quantities per row.
- Tax handling:
  - `set_tax_value(row_id)` uses `#item_qty_vis_{row_id}` for the taxable base.
  - Tax amount is stored in `#td_data_{row}_11` and applied by `make_subtotal()` respecting Inclusive/Exclusive.

### Files Modified
1) application/views/pos.php
   - UI: Batch column remains; quantity input now row-scoped (`item_qty_vis_{row}`) with a hidden posted field (`item_qty_{row}`).
   - JS:
     - `addrow()`: renders row-scoped qty inputs; initializes batch select.
     - `populate_batches_for_row(row_id, pro_id)`: fetches batch list and auto-selects the first unused batch for that item (or saved batch on edit).
     - `batch_change(select,row_id,pro_id)`: updates stock, price, and subtotal; validates against duplicate Item+Batch.
     - `checkForDuplicates()`: checks Item+Batch pairs; returns false on duplicates (no row removal).
     - `increment_qty/decrement_qty/item_qty_input`: all read/write `#item_qty_vis_{row}` and sync to `item_qty_{row}`.
     - `set_tax_value(row_id)`: switched to row-scoped qty; keeps writing tax amount to `td_data_{row}_11`.
     - `make_subtotal(item_id,row_id)`: sequence unchanged; uses `td_data_{row}_11` and respects tax type.
     - Totals: aggregates quantities from `#item_qty_row_{i}` to avoid id conflicts.

2) application/controllers/Pos.php
   - New endpoint: `get_batches_by_product` (GET)
     - Params: `pro_id`
     - Returns batch rows (id, batch_no, quantity, sales_price, alphabet_price, mrp_price) with `quantity > 0`.

3) application/models/Pos_model.php
   - Save/Update: reads quantity from `item_qty_{i}` instead of `item_qty_{item_id}`.
   - Persists selected `batch_id` for each line item to `db_salesitems`.
   - Updates batch stock via `update_stock_in_batch($item_id,$batch_id)` in addition to item stock.
   - Edit flow output: renders batch cell with hidden saved id; on POS page load, JS populates and selects the saved batch.

### Notes
- The batch dropdown option label remains: `{sales_price(4dp)} - {alphabet_price}`.
- If you prefer a different auto-select policy (e.g., oldest expiry, highest quantity), adjust the selector in `populate_batches_for_row()`.
- Ensure `db_salesitems.batch_id` exists; no schema migration included here.

---

## POS: Batch-wise Stock Selection and Pricing (2025-10-11)

### Overview
- Implemented batch selection in Add POS, aligned with Add Sales behavior.
- Item price, stock, tax, and subtotal now depend on the selected batch.
- Batch list shows purchase-defined label format: "{sales_price} - {alphabet_price}" (e.g., "280.0000 - GTA").
- First available batch is selected by default on row add.
- Prevents duplicate item+batch combinations in the table.

### Files Modified
1. application/views/pos.php
   - UI: Added a new "Batch" column next to item name in the POS items table.
   - JS: Added functions to populate and handle batch selection per row:
     - populate_batches_for_row(row_id, pro_id) → loads batches via POS endpoint.
     - batch_change(select, row_id, pro_id) → fetches batch-specific details and recalculates.
     - checkForDuplicates() → prevents duplicate item+batch combination.
   - Behavior: Auto-selects the first batch from the fetched list and triggers recalculation.

2. application/controllers/Pos.php
   - Added get_batches_by_product (GET) endpoint returning batches for a product with quantity > 0:
     - Path: pos/get_batches_by_product?pro_id={id}
     - Returns: id, batch_no, quantity, sales_price, alphabet_price, mrp_price

3. application/models/Pos_model.php
   - Reads selected batch id from tr_batch_id_{i}_111 for each row.
   - Persists batch_id to db_salesitems on save/update.
   - Calls update_stock_in_batch($item_id, $batch_id) to maintain batch-level inventory.

### Behavior Details
- Batch label in dropdown uses purchase data: sales_price (4 decimals) + " - " + alphabet_price.
- On batch change, the POS fetches details from purchase/get_item_details_with_batch_and_productid with pro_id and batch_id to update:
  - Stock cell (batch quantity)
  - Row price (batch sales_price)
  - Tax/subtotal recalculation
- The first batch is automatically selected when the row is added, ensuring immediate correct pricing without extra clicks.
- Duplicate protection: Adding the same item with the same batch twice removes the later row and shows a warning.

### Notes & Compatibility
- The batch dropdown is only rendered for POS new rows; POS edit rendering can be extended similarly if required.
- Sales module already had batch selection; this change mirrors that behavior for POS and preserves existing endpoints.
- No changes were made to DB schema; db_salesitems must already include a batch_id column for persistence.

---



## Overview

This document outlines the changes made to the POS invoice template, including the removal of the dynamic QR code and addition of a static payment QR code image.

## POS Invoice Template Updates

### 1. `application/views/sal-invoice-pos.php`

**Purpose**: Updated the POS invoice template to replace the dynamic QR code with a static payment QR code image.

#### Changes Made:

- Removed dynamic QR code generation using `$CI->print_qr($sales_code)`
- Added static payment QR code image: `theme/dist/img/payQr.png`
- Positioned the QR code above the print button for better visibility
- Added proper spacing and styling for the QR code display

#### Key Features:

- Simplified the invoice footer by removing unnecessary QR code generation
- Improved print layout with better spacing
- Added a clean, professional payment QR code for customer convenience

### 2. Image Assets

- Added: `theme/dist/img/payQr.png` - Static payment QR code image
- Removed: `theme/dist/img/logo1.png` - Unused logo image

## Previous Notes: Item Details Modal Implementation

### Overview

This section documents the implementation of the item details modal in the billing system. The modal displays item information, supplier details, and batch information in a table format.

## Files Modified

### 1. `application/controllers/Items.php`

**Purpose**: Added backend functionality to fetch item details with supplier and batch information.

#### Changes Made:

- **Added `get_item_details()` method** (Lines 11-107)
  - Fetches basic item information with joins to categories, units, and brands
  - Retrieves batch information from `db_batches` table
  - Gets supplier name from most recent purchase transaction
  - Includes proper error handling and debugging information

#### Key Features:

```php
// Basic item details with joins
$this->db->select('i.*, c.category_name, u.unit_name, b.brand_name');
$this->db->from('db_items i');
$this->db->join('db_category c', 'c.id = i.category_id', 'left');
$this->db->join('db_units u', 'u.id = i.unit_id', 'left');
$this->db->join('db_brands b', 'b.id = i.brand_id', 'left');

// Batch information
$this->db->select('*');
$this->db->from('db_batches');
$this->db->where('pro_id', $item_id);

// Supplier information from most recent purchase
$this->db->select('s.supplier_name');
$this->db->from('db_purchaseitems pi');
$this->db->join('db_purchase p', 'p.id = pi.purchase_id', 'left');
$this->db->join('db_suppliers s', 's.id = p.supplier_id', 'left');
```

### 2. `application/views/items-list.php`

**Purpose**: Updated frontend JavaScript to handle modal display and data population.

#### Changes Made:

- **Updated `viewItemDetails()` function** (Lines 213-324)

  - Simplified error handling and modal initialization
  - Fixed AJAX request with proper CSRF token handling
  - Updated modal content population logic
- **Fixed DataTable action column rendering** (Lines 396-441)

  - Improved item ID extraction from checkbox HTML
  - Added event delegation for view button clicks
  - Enhanced error handling in render function
- **Updated modal content structure** (Lines 267-383)

  - Complete modal content rebuild with all item data
  - Added supplier information display
  - Implemented batches table with responsive design
  - Added conditional display for batches

#### Key JavaScript Features:

```javascript
// Item ID extraction from checkbox
var match = row[0].match(/value=([^'"]*)/);
if (match) {
    itemId = match[1];
}

// Event delegation for view buttons
$(document).on('click', '.view-item-btn', function(e) {
    e.preventDefault();
    var itemId = $(this).data('item-id');
    viewItemDetails(itemId);
});

// Complete modal content with batches table
var modalContent = `
  <div class="row">
    <div class="col-md-4">
      <div class="form-group">
        <label><strong>Item Name:</strong></label>
        <p id="view_item_name">${response.data.item_name || 'N/A'}</p>
      </div>
    </div>
    // ... more fields
  </div>
  ${response.data.batches && response.data.batches.length > 0 ? `
  <div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <label><strong>Batches:</strong></label>
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Batch No</th>
                <th>Purchase Price</th>
                <th>Sales Price</th>
                <th>Wholesale Price</th>
                <th>MRP Price</th>
                <th>Alphabet Price</th>
                <th>Quantity</th>
              </tr>
            </thead>
            <tbody>
              ${response.data.batches.map(batch => `
                <tr>
                  <td>${batch.batch_no || 'N/A'}</td>
                  <td>${batch.purchase_price ? parseFloat(batch.purchase_price).toFixed(2) : '0.00'}</td>
                  // ... more batch data
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  ` : 'No batches found'}
`;
```

### 3. `application/views/modals/view_item_modal.php`

**Purpose**: Updated modal header title for better user experience.

#### Changes Made:

- **Updated modal title** (Line 34)
  - Changed from language line to static "ITEM DETAILS" title
  - Improved clarity and professionalism

```php
// Before
<h4 class="modal-title text-center"><?= $this->lang->line('view_item'); ?></h4>

// After
<h4 class="modal-title text-center">ITEM DETAILS</h4>
```

## Database Tables Used

### Primary Tables:

1. **`db_items`** - Main items table
2. **`db_category`** - Categories (singular, not plural)
3. **`db_units`** - Units table
4. **`db_brands`** - Brands table
5. **`db_batches`** - Batch information
6. **`db_suppliers`** - Supplier information

### Related Tables:

1. **`db_purchaseitems`** - Purchase items linking table
2. **`db_purchase`** - Purchase transactions
3. **`db_suppliers`** - Supplier details

## Key Features Implemented

### 1. **Item Information Display**

Item name, code, category, brand, unit

Stock quantity, purchase price, sales price

Alert quantity, description

Supplier name from most recent purchase

**Supplier Item code added (Req by Yadhukrishna on 11-10-2025)**

### 2. **Batches Table**

- Responsive table showing all batches
- Columns: Batch No, Purchase Price, Sales Price, Wholesale Price, MRP Price, Alphabet Price, Quantity
- Conditional display (shows "No batches found" if none exist)

## Technical Implementation Details

### AJAX Request Flow:

1. User clicks "View" button in DataTable
2. JavaScript extracts item ID from checkbox value
3. AJAX request sent to `items/get_item_details`
4. Controller fetches item, supplier, and batch data
5. JSON response returned with all data
6. Modal content updated with complete information

### Data Flow:

```
Frontend (JavaScript) → Controller (PHP) → Database Queries → JSON Response → Modal Display
```

### Security Features:

- CSRF token validation
- Permission checking (`items_view`)
- SQL injection prevention (using CodeIgniter query builder)
- Input validation

## Debugging Features Added

### Controller Debugging:

```php
// Debug: Log the item ID and store ID
$current_store_id = get_current_store_id();

// Simple query first to check if item exists
$this->db->select('*');
$this->db->from('db_items');
$this->db->where('id', $item_id);
$this->db->where('store_id', $current_store_id);
```

### JavaScript Debugging:

```javascript
console.log('Extracted item ID:', itemId, 'from row[0]:', row[0]);
console.log('AJAX Response:', response);
```

# POS Invoice Template Updates Req by(11-10-2025 Yadhukrishna)

## Changes Made

1. **Other Charges Display**

   - Added support for displaying other charges in the invoice
   - Implemented proper handling for the other charges label
   - Added input validation and sanitization
   - Set a default label of "Other Charges" when no specific label is provided
2. **Code Improvements**

   - Added null checks for the `$other_charges_input` variable
   - Added input trimming and type checking
   - Added HTML escaping for security
   - Improved code formatting and readability
3. **Validation**

   - Added checks to ensure the other charges amount is greater than 0
   - Added validation to prevent numeric values as labels
   - Ensured proper fallback to default label when needed

## Implementation Details

The changes were made to:

- [application/views/sal-invoice-pos.php](cci:7://file:///c:/xampp/htdocs/BILLING/application/views/sal-invoice-pos.php:0:0-0:0)
  - Added new section to display other charges
  - Implemented proper variable handling and security measures
  - Maintained consistent styling with the rest of the invoice

## Testing

The changes should be tested to ensure:

1. Other charges are displayed correctly when amount > 0
2. Default label appears when no custom label is provided
3. Numeric values are not used as labels
4. HTML is properly escaped to prevent XSS
5. The layout remains consistent with the existing design

## 2. Balance Payable Feature Update

### Overview

Added a dynamic Balance Payable feature to the sales form that calculates and displays the remaining balance when processing cash payments.

### Files Modified

#### [application/views/sales.php](cci:7://file:///c:/xampp/htdocs/BILLING/application/views/sales.php:0:0-0:0)

**Purpose**: Added interactive balance calculation for cash payments.

**Changes Made**:

1. **Added Cash Collected Input Field**

   - Positioned next to the payment type dropdown
   - Formatted for currency input
   - Updates the balance in real-time
2. **Enhanced Balance Payable Display**

   - Large, prominent display (24px font)
   - Color-coded feedback:
     - Orange (#f39c12) when balance is due
     - Red (#f56954) for overpayment
     - Green (#00a65a) when fully paid
   - Visual styling with background, border, and shadow
3. **JavaScript Functionality**

   - Real-time calculation of balance (Grand Total - Cash Collected)
   - Automatic updates when:
     - Page loads
     - Cash collected amount changes
     - Payment type changes
     - Grand total changes

**Key Code Snippets**:

```javascript
// Balance calculation function
function calculate_balance_payable() {
   var grand_total = parseFloat($("#total_amt").text().replace(/,/g, '')) || 0;
   var cash_collected = parseFloat($("#cash_collected").val().replace(/,/g, '')) || 0;
   var balance_payable = grand_total - cash_collected;
   
   // Update display and styling based on balance
   $("#balance_payable").text(balance_payable.toFixed(2));
   
   if (balance_payable > 0) {
      $("#balance_payable").css('color', '#f39c12'); // Orange
   } else if (balance_payable < 0) {
      $("#balance_payable").css('color', '#f56954'); // Red
   } else {
      $("#balance_payable").css('color', '#00a65a'); // Green
   }
}
```

## Tasks

* [X] Item list view model created with image view in it.
* [X] in pos template view added other charges in prints
* [X] supplier code / supplier item displayed in view
* [X] Balance payable to customer

  FILE TOUCHED

  applications/views/sales.php
* [X] Fixed logo loading issue in bill and payment qr at bottom of bill (change the name of logo placed at top of pos receipt view from kdc_logo_pos.png to kdc_logo_pos4.png and for Qr code placed in theme/dist/img/payQr.png)

FILE TOUCHED

application/views/sal-invoice-pos.php

---

**Developer**: SREERAG MURALI
**Date**: Current Implementation
**Version**: 1.0
**Status**: Complete and Functional

---

## POS: Multiple Batch Quantity & Grand Total Fix (2025-10-22)

### Issue Description
When adding items with multiple batches in POS, there were errors in:
1. **Grand Total Calculation**: Incorrect totaling due to quantity field naming conflicts
2. **Quantity Display**: Wrong quantity aggregation when same item had multiple batch rows

### Root Cause Analysis
The problem was in the quantity field naming convention:
- **Visible quantity input**: Originally named `item_qty_{item_id}` causing conflicts when same item appears in multiple rows with different batches
- **Hidden quantity field**: Correctly named `item_qty_{row_index}` for backend processing
- **JavaScript calculations**: Used row-based IDs but HTML structure had item-based naming conflicts

### Files Modified

#### 1. `application/views/pos.php`

**Lines 696-699**: Fixed quantity input field naming
```javascript
// BEFORE (conflicting names)
quantity +='<input type="text" ... name="item_qty_'+item_id+'">';

// AFTER (row-based names)
quantity +='<input type="text" ... name="item_qty_vis_'+rowcount+'">';
```

**Lines 788-791**: Fixed row count increment
```javascript
// BEFORE (using parseFloat)
$("#hidden_rowcount").val(parseFloat($("#hidden_rowcount").val())+1);

// AFTER (using parseInt with proper initialization)
var currentRowCount = parseInt($("#hidden_rowcount").val()) || 0;
$("#hidden_rowcount").val(currentRowCount + 1);
```

**Lines 1090-1116**: Enhanced `make_subtotal()` function
- Added proper NaN handling for all numeric inputs
- Improved variable initialization with fallback values
- Added subtotal validation to prevent NaN results

```javascript
var tax_type = $("#tr_tax_type_"+rowcount).val() || 'Exclusive';
var tax_amount = parseFloat($("#td_data_"+rowcount+"_11").val()) || 0;
var sales_price = parseFloat($("#sales_price_"+rowcount).val()) || 0;
var item_qty = parseFloat($("#item_qty_vis_"+rowcount).val()) || 0;
var discount_amt = parseFloat($("#item_discount_"+rowcount).val()) || 0;

// NaN prevention
if(isNaN(subtotal)) { subtotal = 0; }
```

**Lines 1130-1162**: Enhanced `final_total()` function
- Added proper NaN handling with `|| 0` operators
- Improved quantity aggregation logic
- Fixed parseInt/parseFloat usage for row counting
- Enhanced error prevention in calculations
- Improved element existence checking

```javascript
// Key improvements:
var rowcount = parseInt($("#hidden_rowcount").val()) || 0;
var discount_input = parseFloat($("#discount_input").val()) || 0;
var discount_type = $("#discount_type").val() || 'in_fixed';

// Safer element checking and total calculation
for(var i=0; i<rowcount; i++){
  var item_id_element = document.getElementById('tr_item_id_'+i);
  if(item_id_element){
    var row_total_element = $("#td_data_"+i+"_4");
    var row_qty_element = $("#item_qty_row_"+i);
    
    if(row_total_element.length && row_qty_element.length){
      var row_total = parseFloat(row_total_element.val()) || 0;
      var row_qty = parseFloat(row_qty_element.val()) || 0;
      total += row_total;
      item_qty += row_qty;
    }
  }
}
```

### Behavior After Fix

1. **Multiple Batch Support**: Same item can be added multiple times, each row representing a different batch
2. **Correct Quantity Tracking**: Each row maintains independent quantity that properly syncs between visible and hidden fields
3. **Accurate Grand Total**: All calculations (subtotal, tax, discount, grand total) work correctly
4. **Proper Form Submission**: Backend receives correctly named `item_qty_0`, `item_qty_1`, etc.

### Technical Details

#### Quantity Field Architecture
- **Visible Input**: `item_qty_vis_{rowcount}` - User interface field
- **Hidden Input**: `item_qty_{rowcount}` - Form submission field (matches backend expectation)
- **Synchronization**: All increment/decrement/manual input functions sync both fields

#### Backend Compatibility
- `Pos_model.php` line 337: `$_REQUEST['item_qty_'.$i]` correctly matches our row-based naming
- No backend changes required - the model was already expecting row-based quantities

### Testing Verification
- [ ] Add same item with different batches - each should be separate rows
- [ ] Modify quantities independently for each batch row
- [ ] Verify grand total updates correctly
- [ ] Confirm form submission includes all quantities
- [ ] Test increment/decrement buttons work for each row
- [ ] Validate manual quantity input synchronization

### Impact
- ✅ **Fixed**: Grand total calculation errors
- ✅ **Fixed**: Quantity aggregation conflicts
- ✅ **Enhanced**: Error handling and NaN prevention
- ✅ **Maintained**: All existing functionality for single batch items
- ✅ **Improved**: Multi-batch inventory management

---

# Project Overview

## System Architecture

**Framework**: CodeIgniter 3.x PHP Framework  
**Database**: MySQL/MariaDB  
**Frontend**: Bootstrap, jQuery, AdminLTE  
**Environment**: XAMPP (Windows Development)

### Key Dependencies (composer.json)
- **Twilio SDK** (v6.16+): SMS/communication services
- **Laminas Barcode** (v2.11+): Barcode generation
- **DomPDF** (v2.0+): PDF generation for invoices
- **chillerlan/php-qrcode** (v4.3+): QR code generation

## Directory Structure

```
BILLING/
├── application/          # Main CodeIgniter application
│   ├── controllers/      # Business logic controllers
│   ├── models/           # Database interaction models
│   ├── views/            # UI templates and views
│   ├── config/           # Configuration files
│   └── logs/             # Application logs
├── system/               # CodeIgniter framework core
├── theme/                # UI assets (CSS, JS, images)
├── uploads/              # File uploads directory
├── dbbackup/             # Database backups
├── setup/                # Installation scripts
└── help/                 # Documentation files
```

## Core Modules

### 1. **Inventory Management**
- Item master data with categories, brands, units
- Batch-wise stock tracking
- Supplier management
- Purchase order processing

### 2. **Sales & Billing**
- POS (Point of Sale) interface
- Sales invoice generation
- Multiple payment methods support
- Real-time stock updates

### 3. **Reporting**
- Sales reports
- Stock reports
- Financial summaries

### 4. **Administration**
- User management
- Store/branch management
- System configuration

## Key Database Tables

### Primary Tables
- `db_items` - Product/item master
- `db_batches` - Batch tracking for inventory
- `db_sales` - Sales transaction headers
- `db_salesitems` - Sales transaction line items
- `db_purchase` - Purchase transaction headers
- `db_purchaseitems` - Purchase transaction line items
- `db_suppliers` - Supplier information
- `db_category` - Product categories
- `db_brands` - Brand master data
- `db_units` - Unit of measurement

### System Tables
- `db_users` - User accounts
- `db_stores` - Multi-store support
- `db_permissions` - Role-based access control

## Development Environment Setup

### Prerequisites
- XAMPP with PHP 7.4+
- MySQL/MariaDB
- Composer for dependency management

### Installation Steps
1. Clone/copy project to `C:\xampp\htdocs\BILLING`
2. Run `composer install` to install dependencies
3. Configure database connection in `application/config/database.php`
4. Import database from `dbbackup/` directory
5. Set appropriate file permissions for `uploads/` directory
6. Access via `http://localhost/BILLING`

## Coding Standards & Best Practices

### CodeIgniter Conventions
- Controllers: PascalCase (e.g., `Items.php`, `Pos.php`)
- Models: PascalCase with `_model` suffix (e.g., `Pos_model.php`)
- Views: lowercase with hyphens (e.g., `items-list.php`)
- Database tables: prefix `db_` (e.g., `db_items`)

### Security Measures
- CSRF token validation enabled
- SQL injection prevention via query builder
- Input validation and sanitization
- HTML escaping for output
- Permission-based access control

### Frontend Standards
- Bootstrap responsive design
- jQuery for DOM manipulation
- AdminLTE for admin interface
- DataTables for data grids
- Select2 for enhanced dropdowns

## Common Development Tasks

### Adding New Features
1. Create controller in `application/controllers/`
2. Create model in `application/models/`
3. Create views in `application/views/`
4. Add routes in `application/config/routes.php`
5. Update permissions if needed

### Database Changes
1. Create migration scripts
2. Update model methods
3. Test data integrity
4. Update backup procedures

### UI Modifications
1. Follow Bootstrap/AdminLTE patterns
2. Ensure responsive design
3. Test across different screen sizes
4. Maintain consistent styling

## Testing Procedures

### Manual Testing Checklist
- [ ] POS functionality (add items, checkout)
- [ ] Inventory updates after sales
- [ ] Batch selection and stock tracking
- [ ] Invoice generation and printing
- [ ] User permissions and access control
- [ ] Report generation
- [ ] Multi-store operations

### Performance Considerations
- Database indexing for frequently queried tables
- Pagination for large data sets
- Caching for static content
- Optimized SQL queries

## Troubleshooting Common Issues

### Database Connection Issues
- Check `application/config/database.php` settings
- Verify MySQL service is running
- Ensure proper user privileges

### Permission Errors
- Check file/folder permissions on `uploads/`
- Verify user has proper role assignments
- Check `.htaccess` configuration

### JavaScript Errors
- Check browser console for errors
- Verify jQuery and plugin loading
- Ensure CSRF tokens are included

## Backup Procedures

### Database Backups
- Automated backups stored in `dbbackup/`
- Naming convention: `dbbackupDD-MMM-YYYY-HH-MM-SS.gz`
- Regular backup schedule recommended

### File Backups
- Include `uploads/` directory
- Custom theme modifications
- Configuration files

## Support & Maintenance

### Log Files
- Application logs: `application/logs/`
- Error logs: Check web server error logs
- PHP error logs: Check XAMPP logs

### Version Control Best Practices
- Commit granular changes
- Use descriptive commit messages
- Tag releases appropriately
- Maintain separate branches for features

### Documentation Updates
- Update this file when making significant changes
- Document new features and modifications
- Include testing procedures for new functionality
- Maintain changelog for version tracking

---
