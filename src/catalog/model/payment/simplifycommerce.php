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

namespace Opencart\Catalog\Model\Extension\SimplifyCommerce\Payment;
class SimplifyCommerce extends  \Opencart\System\Engine\Model
{
    public function getMethods(array $address = []): array {
        $this->load->language('extension/SimplifyCommerce/payment/simplifycommerce');

        if ($this->cart->hasSubscription()) {
            $status = false;
        } elseif (!$this->cart->hasShipping()) {
            $status = false;
        }
        elseif (!$this->config->get('payment_simplifycommerce_geo_zone_id')) {
            $status = true;
        } else {
            $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . (int)$this->config->get('payment_simplifycommerce_geo_zone_id') . "' AND `country_id` = '" . (int)$address['country_id'] . "' AND (`zone_id` = '" . (int)$address['zone_id'] . "' OR `zone_id` = '0')");

            if ($query->num_rows) {
                $status = true;
            } else {
                $status = false;
            }
        }

        $method_data = [];

        if ($status) {
            $option_data['simplifycommerce'] = [
                'code' => 'simplifycommerce.simplifycommerce',
                'name' => $this->config->get('payment_simplifycommerce_title') ?: 'Pay with Card',
            ];

            $method_data = [
                'code'       => 'simplifycommerce',
                'name'       => $this->config->get('payment_simplifycommerce_title') ?: 'Pay with Card',
                'option'     => $option_data,
                'sort_order' =>$this->config->get('payment_simplifycommerce_sort_order')
            ];
        }

        return $method_data;
    }
}
