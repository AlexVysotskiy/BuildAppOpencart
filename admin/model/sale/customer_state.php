<?php
class ModelSaleCustomerState extends Model {
    public function getState($customer_id) {
        $query = $this->db->query("
            SELECT customer_state
            FROM " . DB_PREFIX . "customer_state
            WHERE customer_id = '" . $customer_id . "'
            AND user_group_franchise_id = '" . $this->user->getGroupFranchiseId() . "'
        ");

        return $query->row['customer_state'];
    }

    public function updateState($customer_id, $customer_state) {
       $this->db->query("
            UPDATE " . DB_PREFIX . "customer_state
            SET customer_state = '" . $customer_state . "'
            WHERE customer_id = '" . $customer_id . "'
            AND user_group_franchise_id = '" . $this->user->getGroupFranchiseId() . "'
        ");
    }
}