<?php
class ModelShippingGarbage extends Model {

	public function getLists() {
		$query = $this->db->query("
            SELECT * FROM " . DB_PREFIX . "shipping_garbage
		    WHERE user_group_id = '" . $this->user->getGroupId() . "' ");

        return $query->rows;
	}

	public function edit($data) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "shipping_garbage` 
        WHERE user_group_id = '" . $this->user->getGroupId() . "' ");

        if (isset($data['option_value'])) {

            foreach ($data['option_value'] as $option_value) {
                $this->db->query("
                    INSERT INTO " . DB_PREFIX . "shipping_garbage
                    SET 
                    price = '" . (int)$option_value['price'] . "',
                    user_group_id = '" . $this->user->getGroupId() . "'
                    ");
            }
        }
	}
}
