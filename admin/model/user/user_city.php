<?php
class ModelUserUserCity extends Model {

    public function getCitys() {
        $sql = "SELECT * FROM `" . DB_PREFIX . "user_city` ORDER BY name ASC";

        $query = $this->db->query($sql);

        return $query->rows;
    }
}