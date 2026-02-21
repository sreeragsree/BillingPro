<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_daily_cash_closing_add_expected_and_unique extends CI_Migration {

    public function up()
    {
        // Add expected_cash column if not exists
        $col = $this->db->query("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_cash_closing' AND COLUMN_NAME = 'expected_cash'")->row();
        if (empty($col) || intval($col->cnt) === 0) {
            $this->db->query("ALTER TABLE `daily_cash_closing` ADD COLUMN `expected_cash` DECIMAL(15,2) DEFAULT '0.00' AFTER `closing_cash`");
        }

        // Add difference column if not exists
        $col2 = $this->db->query("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_cash_closing' AND COLUMN_NAME = 'difference'")->row();
        if (empty($col2) || intval($col2->cnt) === 0) {
            $this->db->query("ALTER TABLE `daily_cash_closing` ADD COLUMN `difference` DECIMAL(15,2) DEFAULT '0.00' AFTER `expected_cash`");
        }

        // Add unique index on closing_date, store_id if not exists
        $idx = $this->db->query("SELECT COUNT(*) AS cnt FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_cash_closing' AND INDEX_NAME = 'unique_closing_store'")->row();
        if (empty($idx) || intval($idx->cnt) === 0) {
            $this->db->query("ALTER TABLE `daily_cash_closing` ADD UNIQUE KEY `unique_closing_store` (`closing_date`,`store_id`)");
        }
    }

    public function down()
    {
        // Drop unique index if exists
        $idx = $this->db->query("SELECT COUNT(*) AS cnt FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_cash_closing' AND INDEX_NAME = 'unique_closing_store'")->row();
        if (!empty($idx) && intval($idx->cnt) > 0) {
            $this->db->query("ALTER TABLE `daily_cash_closing` DROP INDEX `unique_closing_store`");
        }

        // Drop difference column if exists
        $col2 = $this->db->query("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_cash_closing' AND COLUMN_NAME = 'difference'")->row();
        if (!empty($col2) && intval($col2->cnt) > 0) {
            $this->db->query("ALTER TABLE `daily_cash_closing` DROP COLUMN `difference`");
        }

        // Drop expected_cash column if exists
        $col = $this->db->query("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'daily_cash_closing' AND COLUMN_NAME = 'expected_cash'")->row();
        if (!empty($col) && intval($col->cnt) > 0) {
            $this->db->query("ALTER TABLE `daily_cash_closing` DROP COLUMN `expected_cash`");
        }
    }
}
