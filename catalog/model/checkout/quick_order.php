<?php
class ModelCheckoutQuickOrder extends Model {

    public function getShopEmail($zone_id) {

		$query = $this->db->query("
            SELECT * FROM `" . DB_PREFIX . "user` 
            WHERE zone_id = '" . $zone_id . "' 
        ");

        return $query->row['email'];
	}
}