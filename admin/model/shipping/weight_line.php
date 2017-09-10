<?php
class ModelShippingWeightLine extends Model {

	public function getLists() {
		$query = $this->db->query("
            SELECT * FROM " . DB_PREFIX . "shipping_weight_line 
		    WHERE user_group_id = '" . $this->user->getGroupId() . "' ");

        return $query->rows;
	}

	public function edit($data) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "shipping_weight_line` 
        WHERE user_group_id = '" . $this->user->getGroupId() . "' ");

        if (isset($data['option_value'])) {

            foreach ($data['option_value'] as $option_value) {
                $this->db->query("
                    INSERT INTO " . DB_PREFIX . "shipping_weight_line 
                    SET 
                    weight_first = '" . (int)$option_value['weight_first'] . "', 
                    weight_last = '" . (int)$option_value['weight_last'] . "', 
                    price = '" . (int)$option_value['price'] . "',
                    user_group_id = '" . $this->user->getGroupId() . "'
                    ");
            }
        }
	}
}
