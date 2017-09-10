<?php
class ModelUserUserZone extends Model {

    public function getZones() {
        $sql = "
            SELECT * FROM `" . DB_PREFIX . "user_zone` uz
            LEFT JOIN `" . DB_PREFIX . "zone` z
            ON (uz.zone_id = z.zone_id)
            ORDER BY z.name ASC";

        $query = $this->db->query($sql);

        return $query->rows;
    }
}