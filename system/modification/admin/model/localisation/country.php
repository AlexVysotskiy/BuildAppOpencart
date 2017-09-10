<?php
class ModelLocalisationCountry extends Model {
	public function getCountry($country_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE country_id = '" . (int)$country_id . "' AND status = '1'");

		return $query->row;
	}

	public function getCountries() {
		$country_data = $this->cache->get('country.status');

		if (!$country_data) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE status = '1' ORDER BY name ASC");

			$country_data = $query->rows;

			$this->cache->set('country.status', $country_data);
		}

		return $country_data;
	}

    /**
     * ВЫБИРАЕТ ВСЕ ДЕЙСТВУЮЩИЕ ГОРОДА
     * НА КОТОРЫХ БЫЛИ СОЗДАННЫ ФРАНШИЗЫ
     *
     * @return array
     */
    public function getCountriesFranchise() {

        $filter_data = [];

        $query = $this->db->query("
            SELECT *
            FROM " . DB_PREFIX . "user_zone uz
            LEFT JOIN `" . DB_PREFIX . "zone` z
            ON (uz.zone_id = z.zone_id)
            LEFT JOIN `" . DB_PREFIX . "user` u
            ON (uz.zone_id = u.zone_id)
        ");

        foreach ($query->rows as $filter) {

            if ($filter['zone_id'] !== null) {
                $filter_data[] = array(
                    'zone_id' => $filter['zone_id'],
                    'name' => $filter['name']
                );
            }
        }

        return $filter_data;
    }
}