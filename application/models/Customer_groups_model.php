<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_groups_model extends CI_Model {

    protected $table = 'db_customer_groups';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Return active customer groups for a store, ordered by name.
     */
    public function get_active_groups($store_id = null)
    {
        if ($store_id === null) {
            $store_id = get_current_store_id();
        }

        return $this->db
            ->from($this->table)
            ->where('store_id', $store_id)
            ->where('status', 1)
            ->order_by('group_name', 'asc')
            ->get()
            ->result();
    }
}
