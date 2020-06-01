<?php

class ModelLocalisationSimplifycommerce extends Model
{
    const AUTHORIZE = 'authorize';
    const PAYMENT = 'payment';

    /**
     * @return array
     */
    public function getModes()
    {
        $this->load->language('extension/payment/simplifycommerce');
        return [
            [
                'label' => $this->language->get('choice_payment'),
                'value' => self::PAYMENT
            ],
            [
                'label' => $this->language->get('choice_authorize'),
                'value' => self::AUTHORIZE
            ],
        ];
    }
}
