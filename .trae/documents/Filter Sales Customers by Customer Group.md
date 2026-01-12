## Objective
Ensure the Sales Add page filters the Customer Name dropdown to only active, non-restricted customers in the selected Customer Group. Fetch/select group first, then load customers matching that group.

## UI Updates
- Add a `Customer Group` dropdown near the `Customer Name` selector on `application/views/sales.php`.
- Populate with `get_customer_groups_select_list(null, get_current_store_id(), true)`.
- Provide a helper `getCustomerGroupSelectionId()` returning `#customer_group_filter_id` to be used by the AJAX layer.
- On group change, clear current customer selection and previous due display.

## AJAX Behavior
- In `theme/js/ajaxselect/customer_select_ajax.js`, include `customer_group_id` in the payload sent to `customers/getCustomers/`:
  - Data sent: `searchTerm`, `store_id`, `customer_group_id`.
- When `#customer_group_filter_id` changes, reset `#customer_id` and let Select2 re-fetch using the updated group.

## Backend Filtering
- In `application/models/Customers_model.php::getCustomersArray($id='')` (around 1378–1421):
  - Always filter: `store_id`, `status = 1`, `delete_bit = 0`.
  - If `customer_group_id` is provided (from `$_REQUEST`), add `where('customer_group_id', $customer_group_id)`.
  - Keep existing searchTerm logic and result formatting.

## Edge Cases
- If no group is selected, show all active, non-restricted customers for the current store (existing behavior with added status/delete filters).
- If editing an existing sale, preselect its customer; changing the group afterward clears the selection and re-filters.
- Users marked `delete_bit = 1` or `status != 1` never appear in the dropdown.

## Verification
- Create a test group with exactly two customers; verify only those two appear after selecting that group.
- Switch to another group; verify customer list updates accordingly.
- Search term still narrows within the selected group.

## Code References
- Customers filter: `application/models/Customers_model.php:1384` (status), `application/models/Customers_model.php:1385` (delete_bit), add `customer_group_id` filter near `application/models/Customers_model.php:1386–1387`.
- AJAX payload: `theme/js/ajaxselect/customer_select_ajax.js:31–35`.
- Group dropdown and helpers: `application/views/sales.php:205–231` (form), `application/views/sales.php:867–871` (helper + change handler).

Please confirm, and I will implement exactly these changes and verify the behavior end-to-end.