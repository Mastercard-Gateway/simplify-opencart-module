<?php
/**
 * Copyright (c) 2013-2019 Mastercard
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

class ModelExtensionPaymentSimplifyCommerce extends Model
{
    const AUTHORIZE = 'authorization';
    const PAYMENT = 'payment';

    public function getOrder($order_id) {

        $this->load->model('sale/order');
        $order_info = $this->model_sale_order->getOrder($order_id);

        if ($order_info['payment_code'] === 'simplifycommerce') {
            return $order_info;
        }

        return null;
    }

    public function getTransactions($order_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "simplifycommerce_order_transaction` WHERE `order_id` = '" . $this->db->escape($order_id) . "'");

        $transactions = array();
        if ($query->num_rows) {
            foreach ($query->rows as $row) {
                $row['amount'] = number_format(($row['amount'] / 100), 2);
                $transactions[] = $row;
            }
            return $transactions;
        } else {
            return false;
        }
    }

    public function install()
    {
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "simplifycommerce_order_transaction` (
			  `simplifycommerce_order_transaction_id` INT(11) NOT NULL AUTO_INCREMENT,
              `order_id` int(11) NOT NULL,
			  `transaction_id` varchar(255),
			  `date_added` DATETIME NOT NULL,
			  `type` ENUM('payment', 'authorization', 'capture', 'refund', 'cancel') DEFAULT NULL,
			  `status` ENUM('Open', 'Pending', 'Completed', 'Suspended', 'Declined', 'Closed', 'Canceled') DEFAULT NULL,
			  `amount` DECIMAL( 10, 2 ) NOT NULL,
			  PRIMARY KEY (`simplifycommerce_order_transaction_id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;
        ");
    }

    public function uninstall()
    {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "simplifycommerce_order_transaction`;");
    }

    public function deleteEvents()
    {
        $this->load->model('setting/event');
    }

    public function addEvents()
    {
        $this->load->model('setting/event');
    }
}
