<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-11-19 07:12:08 --> 404 Page Not Found: Uploads/site
ERROR - 2025-11-19 07:12:09 --> 404 Page Not Found: Uploads/site
ERROR - 2025-11-19 12:42:21 --> Query error: Incorrect DATE value: '' - Invalid query: SELECT coalesce(sum(credit_amt), 0) as credit_amt
FROM `ac_transactions` `a`
WHERE `a`.`transaction_date` >= ''
AND `credit_account_id` = '2'
AND `transaction_date` < ''
ERROR - 2025-11-19 12:42:21 --> Severity: error --> Exception: Call to a member function row() on bool C:\Users\sreer\Herd\BILLING\application\controllers\Account_transactions.php 98
