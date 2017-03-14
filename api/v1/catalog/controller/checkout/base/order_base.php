<?php

class ControllerCheckoutOrderBaseAPI extends ApiController
{

    /**
     *
     * @var type 
     */
    protected $_defaults = array(
        'city' => 'Саратов',
        'country_id' => 176,
        'zone_id' => 2783,
        'payment_method' => 'cod',
        'shipping_method' => 'flat'
    );

    public function index($args = array())
    {
        if ($this->request->isPostRequest()) {

            $success = false;
            $response = array();
            try {

                $orderId = $this->post();

                $response['order'] = $orderId;
                $success = true;
            } catch (ApiException $e) {

                throw $e;
            } catch (Exception $e) {

                $response['error'] = $e->getMessage();
                $success = false;
            }

            $response['success'] = $success == true;

            $this->response->setOutput($response);
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
        $default = $this->_defaults;

        // Validate if customer is logged in.
        if (!$this->customer->isLogged()) {
            $json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
        }

        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            //   $json['redirect'] = $this->url->link('checkout/cart');
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

        // адрес доставки в деволтном городе
        $address = trim(@$this->request->post['address']);

        if ((utf8_strlen($address) < 3) || (utf8_strlen($address) > 500)) {
            $json['error']['address'] = 'error_address';
        }

        $city = isset($this->request->post['city']) ? $this->request->post['city'] : $default['city'];

        if ((utf8_strlen($address) < 3) || (utf8_strlen($address) > 500)) {
            $json['error']['city'] = 'error_city';
        }

        // все ок, можем оформлять заказ
        if (!$json) {

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
                'city' => $city,
                'zone_id' => $default['zone_id'],
                'country_id' => $default['country_id']
            );

            if (!($address_id = $this->model_account_address->hasAddress($address, $city))) {

                // сохранение адреса оплаты
                $address_id = $this->model_account_address->addAddress($addressData);
            }

            // адрес оплаты
            $paymentAddress = $this->model_account_address->getAddress($address_id);

            // адрес доставки
            $shippingAddress = $paymentAddress;

            // загружаем способ доставки
            /* @TODO добавить валидацию опций */
            $code = 'pickup';
            $this->load->model('shipping/' . $code);
            $quoute = $this->{'model_shipping_' . $code}->getQuote($shippingAddress);
            $shippingMethod = $quoute['quote'][$code];

            // загружаем способ оплаты
            /* @TODO добавить валидацию опций */
            /* @TODO добавить валидацию  $total */
            $total = 1;
            $code = 'cod';
            $this->load->model('payment/' . $code);
            $paymentMethod = $this->{'model_payment_' . $code}->getMethod($paymentAddress, $total);
            $this->session->data['payment_method'] = $paymentMethod;

            // сохранили заказ
            $orderId = $this->makeOrder($paymentAddress, $paymentMethod, $shippingAddress, $shippingMethod);

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

            return $orderId;
        }

        ApiException::evaluateErrors($json);
    }

    protected function makeOrder($paymentAddress, $paymentMethod, $shippingAddress, $shippingMethod)
    {
        $order_data = array();

        $order_data['totals'] = array();
        $total = 0;
        $taxes = $this->cart->getTaxes();

        $this->load->model('extension/extension');

        $sort_order = array();

        $results = $this->model_extension_extension->getExtensions('total');

        foreach ($results as $key => $value) {
            $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
        }

        array_multisort($sort_order, SORT_ASC, $results);

        foreach ($results as $result) {
            if ($this->config->get($result['code'] . '_status')) {
                $this->load->model('total/' . $result['code']);

                $this->{'model_total_' . $result['code']}->getTotal($order_data['totals'], $total, $taxes);
            }
        }

        $sort_order = array();

        foreach ($order_data['totals'] as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $order_data['totals']);

        $this->load->language('checkout/checkout');

        $order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
        $order_data['store_id'] = $this->config->get('config_store_id');
        $order_data['store_name'] = $this->config->get('config_name');

        if ($order_data['store_id']) {
            $order_data['store_url'] = $this->config->get('config_url');
        } else {
            $order_data['store_url'] = HTTP_SERVER;
        }

        if ($this->customer->isLogged()) {

            $this->load->model('account/customer');

            $customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

            $order_data['customer_id'] = $this->customer->getId();
            $order_data['customer_group_id'] = $customer_info['customer_group_id'];
            $order_data['firstname'] = $customer_info['firstname'];
            $order_data['lastname'] = $customer_info['lastname'];
            $order_data['email'] = $customer_info['email'];
            $order_data['telephone'] = $customer_info['telephone'];
            $order_data['fax'] = $customer_info['fax'];
            $order_data['custom_field'] = unserialize($customer_info['custom_field']);
        }

        $order_data['payment_firstname'] = $paymentAddress['firstname'];
        $order_data['payment_lastname'] = $paymentAddress['lastname'];
        $order_data['payment_company'] = $paymentAddress['company'];
        $order_data['payment_address_1'] = $paymentAddress['address_1'];
        $order_data['payment_address_2'] = $paymentAddress['address_2'];
        $order_data['payment_city'] = $paymentAddress['city'];
        $order_data['payment_postcode'] = $paymentAddress['postcode'];
        $order_data['payment_zone'] = $paymentAddress['zone'];
        $order_data['payment_zone_id'] = $paymentAddress['zone_id'];
        $order_data['payment_country'] = $paymentAddress['country'];
        $order_data['payment_country_id'] = $paymentAddress['country_id'];
        $order_data['payment_address_format'] = $paymentAddress['address_format'];
        $order_data['payment_custom_field'] = (isset($paymentAddress['custom_field']) ? $paymentAddress['custom_field'] : array());

        if (isset($paymentMethod['title'])) {
            $order_data['payment_method'] = $paymentMethod['title'];
        } else {
            $order_data['payment_method'] = '';
        }

        if (isset($paymentMethod['code'])) {
            $order_data['payment_code'] = $paymentMethod['code'];
        } else {
            $order_data['payment_code'] = '';
        }

        if ($this->cart->hasShipping()) {

            $order_data['shipping_firstname'] = $shippingAddress['firstname'];
            $order_data['shipping_lastname'] = $shippingAddress['lastname'];
            $order_data['shipping_company'] = $shippingAddress['company'];
            $order_data['shipping_address_1'] = $shippingAddress['address_1'];
            $order_data['shipping_address_2'] = $shippingAddress['address_2'];
            $order_data['shipping_city'] = $shippingAddress['city'];
            $order_data['shipping_postcode'] = $shippingAddress['postcode'];
            $order_data['shipping_zone'] = $shippingAddress['zone'];
            $order_data['shipping_zone_id'] = $shippingAddress['zone_id'];
            $order_data['shipping_country'] = $shippingAddress['country'];
            $order_data['shipping_country_id'] = $shippingAddress['country_id'];
            $order_data['shipping_address_format'] = $shippingAddress['address_format'];
            $order_data['shipping_custom_field'] = (isset($shippingAddress['custom_field']) ? $shippingAddress['custom_field'] : array());

            if (isset($shippingMethod['title'])) {
                $order_data['shipping_method'] = $shippingMethod['title'];
            } else {
                $order_data['shipping_method'] = '';
            }

            if (isset($shippingMethod['code'])) {
                $order_data['shipping_code'] = $shippingMethod['code'];
            } else {
                $order_data['shipping_code'] = '';
            }
        } else {
            $order_data['shipping_firstname'] = '';
            $order_data['shipping_lastname'] = '';
            $order_data['shipping_company'] = '';
            $order_data['shipping_address_1'] = '';
            $order_data['shipping_address_2'] = '';
            $order_data['shipping_city'] = '';
            $order_data['shipping_postcode'] = '';
            $order_data['shipping_zone'] = '';
            $order_data['shipping_zone_id'] = '';
            $order_data['shipping_country'] = '';
            $order_data['shipping_country_id'] = '';
            $order_data['shipping_address_format'] = '';
            $order_data['shipping_custom_field'] = array();
            $order_data['shipping_method'] = '';
            $order_data['shipping_code'] = '';
        }

        $order_data['products'] = array();

        foreach ($this->cart->getProducts() as $product) {
            $option_data = array();

            foreach ($product['option'] as $option) {
                $option_data[] = array(
                    'product_option_id' => $option['product_option_id'],
                    'product_option_value_id' => $option['product_option_value_id'],
                    'option_id' => $option['option_id'],
                    'option_value_id' => $option['option_value_id'],
                    'name' => $option['name'],
                    'value' => $option['value'],
                    'type' => $option['type']
                );
            }

            $order_data['products'][] = array(
                'product_id' => $product['product_id'],
                'name' => $product['name'],
                'model' => $product['model'],
                'option' => $option_data,
                'download' => $product['download'],
                'quantity' => $product['quantity'],
                'subtract' => $product['subtract'],
                'price' => $product['price'],
                'total' => $product['total'],
                'tax' => $this->tax->getTax($product['price'], $product['tax_class_id']),
                'reward' => $product['reward']
            );
        }

        // Gift Voucher
        $order_data['vouchers'] = array();

        $order_data['comment'] = ''
        ;
        $order_data['total'] = $total;


        $order_data['affiliate_id'] = 0;
        $order_data['commission'] = 0;
        $order_data['marketing_id'] = 0;
        $order_data['tracking'] = '';

        $order_data['language_id'] = $this->config->get('config_language_id');
        $order_data['currency_id'] = $this->currency->getId();
        $order_data['currency_code'] = $this->currency->getCode();
        $order_data['currency_value'] = $this->currency->getValue($this->currency->getCode());
        $order_data['ip'] = $this->request->server['REMOTE_ADDR'];

        if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
            $order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
            $order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
        } else {
            $order_data['forwarded_ip'] = '';
        }

        if (isset($this->request->server['HTTP_USER_AGENT'])) {
            $order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
        } else {
            $order_data['user_agent'] = '';
        }

        if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
            $order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
        } else {
            $order_data['accept_language'] = '';
        }

        $this->load->model('checkout/order');

        $this->session->data['order_id'] = $this->model_checkout_order->addOrder($order_data);

        return $this->session->data['order_id'];
    }

}

?>