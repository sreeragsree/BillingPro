<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_upi_card_columns_daily_cash_closing extends CI_Migration {

    public function up()
    {
        // Add card_sales, upi_sales, other_sales if not present
        $cols = [
            'card_sales' => "ALTER TABLE `daily_cash_closing` ADD COLUMN `card_sales` DECIMAL(15,2) DEFAULT '0.00' AFTER `cash_sales`",
            'upi_sales'  => "ALTER TABLE `daily_cash_closing` ADD COLUMN `upi_sales` DECIMAL(15,2) DEFAULT '0.00' AFTER `card_sales`",
            'other_sales'=> "ALTER TABLE `daily_cash_closing` ADD COLUMN `other_sales` DECIMAL(15,2) DEFAULT '0.00' AFTER `upi_sales`",
        ];

        foreach ($cols as $col => $sql) {
            $q = $this->db->query("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_cash_closing' AND COLUMN_NAME = '".$col."'")->row();
            if (empty($q) || intval($q->cnt) === 0) {
                $this->db->query($sql);
            }
        }
    }

    public function down()
    {
        // Drop the columns if present
        $drop = ['card_sales','upi_sales','other_sales'];
        foreach ($drop as $col) {
            $q = $this->db->query("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_cash_closing' AND COLUMN_NAME = '".$col."'")->row();
            if (!empty($q) && intval($q->cnt) > 0) {
                $this->db->query("ALTER TABLE `daily_cash_closing` DROP COLUMN `".$col."`");
            }
        }
    }
}
