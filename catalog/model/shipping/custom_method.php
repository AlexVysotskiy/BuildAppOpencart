<?php
class ModelShippingCustomMethod extends Model {

    public function getShippingClimbing() {
        $query = $this->db->query(" 
            SELECT * FROM " . DB_PREFIX . "shipping_climbing c
            LEFT JOIN " . DB_PREFIX . "user u
            ON (c.user_group_id = u.user_group_id)
            WHERE u.zone_id = '" . $this->session->data['zone_id'] . "'
        ");

        return $query->row;
    }

    public function getShippingLift() {
        $query = $this->db->query(" 
            SELECT * FROM " . DB_PREFIX . "shipping_lift l
            LEFT JOIN " . DB_PREFIX . "user u
            ON (l.user_group_id = u.user_group_id)
            WHERE u.zone_id = '" . $this->session->data['zone_id'] . "'
        ");

        return $query->row;
    }

    public function getShippingUnloading() {
        $query = $this->db->query(" 
            SELECT * FROM " . DB_PREFIX . "shipping_unloading un
            LEFT JOIN " . DB_PREFIX . "user u
            ON (un.user_group_id = u.user_group_id)
            WHERE u.zone_id = '" . $this->session->data['zone_id'] . "'
        ");

        return $query->row;
    }

    public function getShippingWinch() {
        $query = $this->db->query(" 
            SELECT * FROM " . DB_PREFIX . "shipping_winch w
            LEFT JOIN " . DB_PREFIX . "user u
            ON (w.user_group_id = u.user_group_id)
            WHERE u.zone_id = '" . $this->session->data['zone_id'] . "'
        ");

        return $query->row;
    }

    public function getShippingWeightLine($weight) {
        $query = $this->db->query(" 
            SELECT * FROM " . DB_PREFIX . "shipping_weight_line wl
            LEFT JOIN " . DB_PREFIX . "user u
            ON (wl.user_group_id = u.user_group_id)
            WHERE  u.zone_id = '" . $this->session->data['zone_id'] . "' 
            AND wl.weight_first <= '$weight' 
            AND wl.weight_last >= '$weight'
         ");

        return $query->row;
    }

    public function getShippingGarbage() {
        $query = $this->db->query(" 
            SELECT * FROM " . DB_PREFIX . "shipping_garbage g
            LEFT JOIN " . DB_PREFIX . "user u
            ON (g.user_group_id = u.user_group_id)
            WHERE u.zone_id = '" . $this->session->data['zone_id'] . "'
        ");

        return $query->row;
    }
}