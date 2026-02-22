<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2026-02-22 02:37:46 --> Query error: Table 'billingprod.daily_cash_closing' doesn't exist - Invalid query: SELECT *
FROM `daily_cash_closing`
WHERE `store_id` = '2'
ORDER BY `closing_date` DESC
ERROR - 2026-02-22 02:37:46 --> Severity: error --> Exception: Call to a member function result() on bool C:\Users\sreer\Herd\BILLING\application\models\Daily_cash_closing_model.php 17
ERROR - 2026-02-22 03:23:56 --> Severity: Warning --> count(): Parameter must be an array or an object that implements Countable C:\Users\sreer\Herd\BILLING\application\models\Sales_model.php 1091
ERROR - 2026-02-22 03:33:23 --> Query error: Incorrect DATE value: '22-02-2026' - Invalid query: SELECT *
FROM `daily_cash_closing`
WHERE `closing_date` = '22-02-2026'
AND `store_id` = '2'
ERROR - 2026-02-22 03:33:23 --> Severity: error --> Exception: Call to a member function num_rows() on bool C:\Users\sreer\Herd\BILLING\application\models\Daily_cash_closing_model.php 118
ERROR - 2026-02-22 03:34:27 --> Query error: Incorrect DATE value: '22-02-2026' - Invalid query: SELECT *
FROM `daily_cash_closing`
WHERE `closing_date` = '22-02-2026'
AND `store_id` = '2'
ERROR - 2026-02-22 03:34:27 --> Daily_cash_closing_model::exists_for_date DB error: Incorrect DATE value: '22-02-2026'
ERROR - 2026-02-22 03:34:27 --> Query error: Incorrect DATE value: '22-02-2026' - Invalid query: SELECT UPPER(payment_type) as ptype, SUM(payment) as total
FROM `db_salespayments`
WHERE `store_id` = '2'
AND `created_date` = '22-02-2026'
GROUP BY `ptype`
ERROR - 2026-02-22 03:34:27 --> Query error: Incorrect DATE value: '22-02-2026' - Invalid query: SELECT IFNULL(SUM(change_return), 0) as total
FROM `db_salespayments`
WHERE `store_id` = '2'
AND `created_date` = '22-02-2026'
ERROR - 2026-02-22 03:34:27 --> Query error: Incorrect DATE value: '22-02-2026' - Invalid query: SELECT IFNULL(SUM(expense_amt), 0) as total
FROM `db_expense`
WHERE `store_id` = '2'
AND `created_date` = '22-02-2026'
ERROR - 2026-02-22 03:34:27 --> Query error: Incorrect DATE value: '22-02-2026' - Invalid query: SELECT IFNULL(SUM(payment), 0) as total
FROM `db_salespayments`
WHERE `store_id` = '2'
AND `created_date` = '22-02-2026'
AND UPPER(payment_type) LIKE '%CASH%'
ERROR - 2026-02-22 03:37:12 --> Query error: Incorrect DATE value: '22-02-2026' - Invalid query: SELECT *
FROM `daily_cash_closing`
WHERE `closing_date` = '22-02-2026'
AND `store_id` = '2'
ERROR - 2026-02-22 03:37:12 --> Daily_cash_closing_model::exists_for_date DB error: Incorrect DATE value: '22-02-2026'
ERROR - 2026-02-22 03:37:12 --> Query error: Incorrect DATE value: '22-02-2026' - Invalid query: SELECT UPPER(payment_type) as ptype, SUM(payment) as total
FROM `db_salespayments`
WHERE `store_id` = '2'
AND `created_date` = '22-02-2026'
GROUP BY `ptype`
ERROR - 2026-02-22 03:37:12 --> Query error: Incorrect DATE value: '22-02-2026' - Invalid query: SELECT IFNULL(SUM(change_return), 0) as total
FROM `db_salespayments`
WHERE `store_id` = '2'
AND `created_date` = '22-02-2026'
ERROR - 2026-02-22 03:37:12 --> Query error: Incorrect DATE value: '22-02-2026' - Invalid query: SELECT IFNULL(SUM(expense_amt), 0) as total
FROM `db_expense`
WHERE `store_id` = '2'
AND `created_date` = '22-02-2026'
ERROR - 2026-02-22 03:37:12 --> Query error: Incorrect DATE value: '22-02-2026' - Invalid query: SELECT IFNULL(SUM(payment), 0) as total
FROM `db_salespayments`
WHERE `store_id` = '2'
AND `created_date` = '22-02-2026'
AND UPPER(payment_type) LIKE '%CASH%'
ERROR - 2026-02-22 08:39:17 --> 404 Page Not Found: Uploads/site
ERROR - 2026-02-22 08:39:34 --> 404 Page Not Found: Uploads/site
ERROR - 2026-02-22 15:48:06 --> 404 Page Not Found: Uploads/site
ERROR - 2026-02-22 15:48:06 --> 404 Page Not Found: Uploads/site
ERROR - 2026-02-22 15:58:42 --> 404 Page Not Found: Faviconico/index
