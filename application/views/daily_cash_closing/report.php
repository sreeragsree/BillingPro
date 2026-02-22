<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * PDF Report template for Daily Cash Closing
 * Expects: $record, $summary
 */
?><!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Cash Closing Report</title>
    <style>
        body{font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size:12px; color:#222}
        .header{text-align:center; margin-bottom:20px}
        .meta{margin-bottom:10px}
        table{width:100%; border-collapse: collapse; margin-bottom:10px}
        th,td{padding:6px 8px; border:1px solid #ddd}
        th{background:#f6f6f6; text-align:left}
        .right{text-align:right}
        .muted{color:#666; font-size:11px}
        .text-danger{color:#a94442}
        .text-success{color:#3c763d}
    </style>
</head>
<body>
    <div class="header">
        <h2>Daily Cash Closing</h2>
        <div class="muted">Date: <?php echo htmlspecialchars($record->closing_date); ?></div>
    </div>

    <div class="meta">
        <?php $store = get_store_details($record->store_id); $store_name = $store ? $store->store_name : $record->store_id; ?>
        <?php $user = get_user_details($record->created_by); $user_name = $user ? (trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: ($user->username ?? $user->user_name ?? '')) : $record->created_by; ?>
        <strong>Store:</strong> <?php echo htmlspecialchars($store_name); ?>
        &nbsp;&nbsp; <strong>Created by:</strong> <?php echo htmlspecialchars($user_name); ?>
        &nbsp;&nbsp; <strong>Created at:</strong> <?php echo htmlspecialchars($record->created_at); ?>
    </div>
    <?php $other_sales_display = (isset($record->other_sales) && $record->other_sales !== null && $record->other_sales !== '') ? floatval($record->other_sales) : floatval($summary['other_sales'] ?? 0); ?>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Opening Cash</td>
                <td class="right"><?php echo number_format($record->opening_cash,2); ?></td>
            </tr>
            <tr>
                <td>Cash Sales</td>
                <td class="right"><?php echo number_format($summary['cash_sales'] ?? ($record->cash_sales ?? 0),2); ?></td>
            </tr>
            <tr>
                <td>Card Sales</td>
                <td class="right"><?php echo number_format($summary['card_sales'] ?? ($record->card_sales ?? 0),2); ?></td>
            </tr>
            <tr>
                <td>UPI/Online</td>
                <td class="right"><?php echo number_format($summary['upi_sales'] ?? ($record->upi_sales ?? 0),2); ?></td>
            </tr>
            <tr>
                <td>Other Sales (System 2)</td>
                <td class="right"><?php echo number_format($other_sales_display,2); ?></td>
            </tr>
            <tr>
                <td>Expenses</td>
                <td class="right"><?php echo number_format($summary['expenses'] ?? ($record->expenses ?? 0),2); ?></td>
            </tr>
            <tr>
                <td>Refunds</td>
                <td class="right"><?php echo number_format($summary['refunds'] ?? 0,2); ?></td>
            </tr>
            <tr>
                <td>Cash In</td>
                <td class="right"><?php echo number_format($summary['cash_in'] ?? 0,2); ?></td>
            </tr>
            <tr>
                <td>Cash Out</td>
                <td class="right"><?php echo number_format(floatval($summary['cash_out'] ?? 0),2); ?></td>
            </tr>
            <tr>
                <td>Cash Out To Home</td>
                <td class="right"><?php echo number_format(floatval($record->cash_out_to_home ?? 0),2); ?></td>
            </tr>
            <tr>
                <th>Expected Cash</th>
                <?php
                    $other_sales_val = $other_sales_display;
                    $cash_out_to_home_val = floatval($record->cash_out_to_home ?? 0);
                    $expected_calc = floatval($record->opening_cash) + floatval($summary['cash_sales'] ?? 0) - floatval($summary['expenses'] ?? 0) - floatval($summary['refunds'] ?? 0) + floatval($summary['cash_in'] ?? 0) - floatval($summary['cash_out'] ?? 0) + $other_sales_val - $cash_out_to_home_val;
                ?>
                <th class="right"><?php echo number_format(floatval($record->expected_cash ?? $expected_calc),2); ?></th>
            </tr>
            <tr>
                <th>Closing Cash</th>
                <th class="right"><?php echo number_format($record->closing_cash,2); ?></th>
            </tr>
            <?php
                $diff_val = floatval($record->difference ?? (($record->closing_cash - ($record->expected_cash ?? (($record->opening_cash + ($summary['cash_sales'] ?? 0) - ($summary['expenses'] ?? 0) - ($summary['refunds'] ?? 0) + ($summary['cash_in'] ?? 0) - ($summary['cash_out'] ?? 0)))))));
                $diff_class = '';
                if ($diff_val < 0) $diff_class = 'text-danger';
                if ($diff_val > 0) $diff_class = 'text-success';
            ?>
            <tr>
                <th>Difference</th>
                <th class="right <?php echo $diff_class; ?>"><?php echo number_format($diff_val,2); ?></th>
            </tr>
        </tbody>
    </table>

    <?php if (!empty($record->note)): ?>
        <div>
            <strong>Note:</strong>
            <div class="muted"><?php echo nl2br(htmlspecialchars($record->note)); ?></div>
        </div>
    <?php endif; ?>

</body>
</html>
