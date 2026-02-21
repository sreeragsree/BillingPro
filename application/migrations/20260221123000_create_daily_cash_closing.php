<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_daily_cash_closing extends CI_Migration {

    public function up()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `daily_cash_closing` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `closing_date` date DEFAULT NULL,
          `opening_cash` decimal(15,2) DEFAULT '0.00',
          `cash_sales` decimal(15,2) DEFAULT '0.00',
          `expenses` decimal(15,2) DEFAULT '0.00',
          `closing_cash` decimal(15,2) DEFAULT '0.00',
          `note` text,
          `store_id` int(11) DEFAULT NULL,
          `created_by` int(11) DEFAULT NULL,
          `created_at` datetime DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // seed basic permissions for admin and default role (store_id 1)
        $perms = array('daily_cash_closing_view','daily_cash_closing_add');
        foreach($perms as $p){
            // insert for super admin role (1) if not exists
            $exists = $this->db->where('store_id',1)->where('role_id',1)->where('permissions',$p)->get('db_permissions')->num_rows();
            if(!$exists) $this->db->insert('db_permissions', array('store_id'=>1,'role_id'=>1,'permissions'=>$p));
            // insert for role 2 (commonly default manager) if not exists
            $exists2 = $this->db->where('store_id',1)->where('role_id',2)->where('permissions',$p)->get('db_permissions')->num_rows();
            if(!$exists2) $this->db->insert('db_permissions', array('store_id'=>1,'role_id'=>2,'permissions'=>$p));
        }
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS `daily_cash_closing`;");
        $this->db->where('permissions','daily_cash_closing_view')->or_where('permissions','daily_cash_closing_add')->delete('db_permissions');
    }
}
