<?php
/**
 * Copyright (c) 2013-2023 Mastercard
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

namespace Opencart\Admin\Model\Extension\SimplifyCommerce\Payment;
class SimplifyCommerce extends \Opencart\System\Engine\Model
{
    public const AUTHORIZE = 'authorization';
    public const PAYMENT = 'payment';

    public function getOrder($order_id): ?array
    {
        $this->load->model('sale/order');
        $order_info = $this->model_sale_order->getOrder($order_id);

        if ($order_info['payment_method']["code"] === 'simplifycommerce.simplifycommerce') {
            return $order_info;
        }

        return null;
    }


   
    public function getTransactions($order_id): array
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "simplifycommerce_order_transaction` WHERE `order_id` = '" . $this->db->escape($order_id) . "' ORDER BY simplifycommerce_order_transaction_id DESC");


        $transactions = [];
        if ($query->num_rows) {
            foreach ($query->rows as $row) {
                $transactions[] = $this->rowTxn($row);
            }
        }

        return $transactions;
    }

   
    public function getTransaction($order_id, $txn_id)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "simplifycommerce_order_transaction` WHERE `order_id` = '" . $this->db->escape($order_id) . "' ORDER BY simplifycommerce_order_transaction_id DESC");
        $transactions = [];
        
        if ($query->num_rows) {
            foreach ($query->rows as $row) {
                $transactions[] = $this->rowTxn($row);
            }
        }
        
        if (!empty($transactions)) {
            return $transactions[0];
        }
        
        return false;
    }


   
    protected function rowTxn(array $row): array
    {
        $amount = ($row['amount'] / 100);
        $amount = round($amount, 2);
        $row['amount'] = number_format($amount, 2);

        return $row;
    }



    public function install(): void
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "simplifycommerce_order_transaction` (
              `simplifycommerce_order_transaction_id` INT(11) NOT NULL AUTO_INCREMENT,
              `order_id` int(11) NOT NULL,
              `transaction_id` varchar(255),
              `date_added` DATETIME NOT NULL,
              `type` ENUM('payment', 'authorization', 'capture', 'refund', 'cancel') DEFAULT NULL,
              `status` ENUM('Open', 'Pending', 'Completed', 'Suspended', 'Refunded', 'Partially Refunded' , 'Declined', 'Closed', 'Canceled') 	DEFAULT NULL,
              `amount` DECIMAL(10, 2) NOT NULL,
              PRIMARY KEY (`simplifycommerce_order_transaction_id`)
            ) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;
        ");
    }
    

    public function uninstall(): void
    {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "simplifycommerce_order_transaction`;");
        $this->load->model('setting/event');
		
		$this->model_setting_event->deleteEventByCode('simplifycommerce');
		
		if (VERSION < '4.0.2.0') {
			$this->model_setting_event->deleteEventByCode('simplifycommerce_extension_get_extensions_by_type');
			$this->model_setting_event->deleteEventByCode('simplifycommerce_extension_get_extension_by_code');
		}
    }


    public function deleteEvents(): void
    {
        $this->load->model('setting/event');
    
        $this->model_setting_event->deleteEventByCode('simplifycommerce_update_page_header');
    }
    
    public function addEvents()
    {
        $this->load->model('setting/event');
        $eventData = array(
            'code'        => 'simplifycommerce_update_page_header',
            'trigger'     => 'catalog/controller/common/header/before',
            'action'      => 'extension/SimplifyCommerce/payment/simplifycommerce.update_page_header',
            'status'      => 1,
            'sort_order'  => 0,
            'description' => ''
        );
    
        $this->model_setting_event->addEvent($eventData);
    }

    public function addOrderHistory($order_id, $order_status_id, $comment = '', $notify = false) : void {
        
        $this->db->query("INSERT INTO `" . DB_PREFIX . "order_history` SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
    }

    public function getOrderStatusIdByName($statusName) {
        $query = $this->db->query("SELECT order_status_id FROM " . DB_PREFIX . "order_status WHERE name = '" . $this->db->escape($statusName) . "'");
        if ($query->num_rows) {
            return $query->row['order_status_id'];
        } else {
            return false;
        }
    }
   

}
