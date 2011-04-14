<?php
class ModelCatalogReview extends Model {
	public function addReview($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "review (author, product_id, text, rating, status, date_added) VALUES ('" . $this->db->escape($data['author']) . "', '" . $this->db->escape($data['product_id']) . "', '" . $this->db->escape(strip_tags($data['text'])) . "', '" . (int)$data['rating'] . "', '" . (int)$data['status'] . "', NOW())");
	}

	public function editReview($review_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "review SET author = '" . $this->db->escape($data['author']) . "', product_id = '" . $this->db->escape($data['product_id']) . "', text = '" . $this->db->escape(strip_tags($data['text'])) . "', rating = '" . (int)$data['rating'] . "', status = '" . (int)$data['status'] . "', date_added = NOW() WHERE review_id = '" . (int)$review_id . "'");
	}

	public function deleteReview($review_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE review_id = '" . (int)$review_id . "'");
	}

	public function changeStatusReviews($reviews, $status) {
		function check_int($a) { return (int)$a; }
		$arr_reviews = array_map('check_int', $reviews);
		$reviews = implode("' OR review_id = '", $arr_reviews);
		$this->db->query("UPDATE " . DB_PREFIX . "review SET status = '" . (int)(bool)$status . "' WHERE review_id = '" . $reviews . "'");
	}

	public function getReview($review_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "review WHERE review_id = '" . (int)$review_id . "'");

		return $query->row;
	}

	public function getReviews($data = array()) {
		$sql = "SELECT r.review_id, pd.name, r.author, r.rating, r.status, r.date_added FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product_description pd ON (r.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$sort_data = array(
			'pd.name',
			'r.author',
			'r.rating',
			'r.status',
			'r.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY r.date_added";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalReviews() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review");

		return $query->row['total'];
	}

	public function getTotalReviewsAwaitingApproval() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review WHERE status = '0'");

		return $query->row['total'];
	}
}
?>