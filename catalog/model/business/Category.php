<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Category
 *
 * @author surya
 */
class ModelBusinessCategory  extends Model {

    public function getMaxDiscountInACategory($categoryId){

        $query = "SELECT p.product_id, p.price, PDC.price, (PDC.price/p.price) * 100 as discount
                    FROM" . DB_PREFIX ."product p LEFT JOIN ". DB_PREFIX ."product_description pd ON (p.product_id = pd.product_id)
                    LEFT JOIN ". DB_PREFIX ."product_to_store p2s ON (p.product_id = p2s.product_id)
                    LEFT JOIN ". DB_PREFIX ."product_to_category p2c ON (p.product_id = p2c.product_id)
                    LEFT JOIN ". DB_PREFIX ."product_discount PDC ON (p.product_id = PDC.product_id)
                    AND (PDC.date_start >= current_date or PDC.date_start = '0000-00-00')
                    WHERE pd.language_id = '1' AND p.status = '1'
                    AND p.date_available <= NOW() AND p2s.store_id = '0'
                    AND p2c.category_id = '13' GROUP BY p.product_id
                    having ((PDC.price/p.price) * 100) = max(discount)
                    ORDER BY discount DESC
                    LIMIT 1";
        $results = $this->db->query($query);
        return $results->row;
    }
}

?>
