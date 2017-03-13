<?php

class ControllerCheckoutOrderBaseAPI extends ApiController
{

    public function index($args = array())
    {
        if ($this->request->isPostRequest()) {
            $this->post();
        } else {
            throw new ApiException(ApiResponse::HTTP_RESPONSE_CODE_NOT_FOUND, ErrorCodes::ERRORCODE_METHOD_NOT_FOUND, ErrorCodes::getMessage(ErrorCodes::ERRORCODE_METHOD_NOT_FOUND));
        }
    }

    public function redirect($url, $status = 302)
    {
        
    }

    /**
     * обрабатываем оформление заказа на одной странице
     * Пользователь должен быть залогирован
     * 
     * Процесс:
     * Step 1: POST checkout/payment_address to set the payment address.
     * Step 2: POST checkout/shipping_address to set the shipping address.
     * Step 3: GET checkout/shipping_methods to get all available shipping methods.
     * Step 4: POST checkout/shipping_method to set the shipping method.
     * Step 5: GET checkout/payment_methods to get all available payment methods.
     * Step 6: POST checkout/payment_method to set the payment method.
     * Step 7: GET checkout/confirm to get an overview of the order.
     * Step 8: GET checkout/pay
     * Step 9: GET checkout/success to clear the carts content and unset some session data.
     * Resource methods
     */
    public function post()
    {
        $json = array();

        // дефолтные значения
        // способ платежа - наличные cod
        // способ доставки - Фиксированная ставка flat
        $default = array(
            'city' => 'Саратов',
            'country_id' => 176,
            'zone_id' => 2783,
            'payment_method' => 'cod',
            'shipping_method' => 'flat'
        );

        // Validate if customer is logged in.
        if (!$this->customer->isLogged()) {
            $json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
        }

        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $json['redirect'] = $this->url->link('checkout/cart');
        }

        // Validate minimum quantity requirements.
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $json['redirect'] = $this->url->link('checkout/cart');

                break;
            }
        }

        // все ок, можем оформлять заказ
        if (!$json) {

            // адрес доставки в деволтном городе
            $address = $this->request->post['address'];
            // опции заказа (доставка, разгрузка, подъем)
            $options = $this->request->post['options'];

            $this->load->model('account/address');

            // пиздец, товарищи
            $addressData = array(
                'firstname' => $this->customer->getFirstName(),
                'lastname' => $this->customer->getLastName(),
                'company' => '',
                'address_1' => $address,
                'address_2' => '',
                'postcode' => '',
                'city' => $default['city'],
                'zone_id' => $default['zone_id'],
                'country_id' => $default['country_id']
            );

            // сохранение адреса оплаты
            $address_id = $this->model_account_address->addAddress($addressData);

            // адрес оплаты
            $paymentAddress = $this->model_account_address->getAddress($address_id);

            // адрес доставки
            $shippingAddress = $paymentAddress;

            // загружаем способ доставки
            /* @TODO добавить валидацию опций */
            $code = 'pickup';
            $this->load->model('shipping/' . $code);
            $quoute = $this->{'model_shipping_' . $code}->getQuote($shippingAddress);
            $shippingMethod = $quoute[$code];

            // загружаем способ оплаты
            /* @TODO добавить валидацию опций */
            /* @TODO добавить валидацию  $total */
            $total = 0;
            $code = 'cod';
            $this->load->model('payment/' . $code);
            $paymentMethod = $this->{'model_payment_' . $code}->getMethod($this->session->data['payment_address'], $total);

            // оплата 
            $this->payment = new APIPayment();
            $paymentMethodCode = $paymentMethod['code'];

            // Do not intercept view data because the mail send to user when confirming the order
            // may contain html from templates which are loaded through the loader's view method.
            $this->load->setInterceptViewData(false);

            // Internally execute the confirmation route.
            $action = new Action($this->payment->getPaymentConfirmationRoute($paymentMethodCode));
            $action->execute($this->registry);

            // обновление активности пользователя
            $this->load->model('account/activity');

            $activity_data = array(
                'customer_id' => $this->customer->getId(),
                'name' => $this->customer->getFirstName() . ' ' . $this->customer->getLastName()
            );

            $this->model_account_activity->addActivity('address_add', $activity_data);
        }

        ApiException::evaluateErrors($json);
    }

}

?>