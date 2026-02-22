<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_cash_out_to_home_daily_cash_closing extends CI_Migration {
    public function up()
    {
        $fields = array(
            'cash_out_to_home' => array('type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00')
        );
        $this->dbforge->add_column('daily_cash_closing', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('daily_cash_closing', 'cash_out_to_home');
    }
}
