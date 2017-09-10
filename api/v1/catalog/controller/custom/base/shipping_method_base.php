<?php

class ControllerCustomShippingMethodBaseAPI extends ApiController
{
	public function index()
    {
        if($this->request->isGetRequest()) {
            $shippingMethod = array('shipping_methods' => $this->getList());
            $this->response->setOutput($shippingMethod);
        } else {
            throw new ApiException(ApiResponse::HTTP_RESPONSE_CODE_NOT_FOUND, ErrorCodes::ERRORCODE_METHOD_NOT_FOUND, ErrorCodes::getMessage(ErrorCodes::ERRORCODE_METHOD_NOT_FOUND));
        }
    }

    protected function getList()
    {
        //ПОДСОЕДИНЯЕМСЯ К КОРЗИНЕ
        //ДЛЯ ТОГО ЧТОБЫ УЗНАТЬ СУМАРНЫЙ ВЕС ТОВАРОВ
        $data = parent::getInternalRouteData('checkout/cart');
        ApiException::evaluateErrors($data, false);

        //ВЫБОРКА СУММАРНОГО ВЕСА
        $totalWeight = $this->getCartTotalWeight($data);

        $this->load->model('shipping/custom_method');
        $data = array();

        //ЭТАЖ ПОДЪЁМА
        $stage = 1;

        if (isset($this->request->get['stage'])) {
            $stage = (int)$this->request->get['stage'];
        } else if (isset($this->request->post['stage'])) {
        $stage = (int)$this->request->post['stage'];
        }

        ////////////////////////////////
        //ПОДЪЁМ ПЕШКОМ
        $this->load->language('checkout/checkout');

        $result = $this->model_shipping_custom_method->getShippingClimbing();
        //расчётная цена
        $weight = $result['weight'];
        $price  = $result['price'];
        $sum    = ($totalWeight * $price / $weight) * $stage;

        $data['shipping_climbing']['text']      = $this->language->get('text_shipping_climbing');
        $data['shipping_climbing']['post_name'] = 'shipping_climbing';
        $data['shipping_climbing']['sum']       = round($sum, 2);

        ////////////////////////////////
        //ПОДЪЁМ НА ЛИФТЕ
        $result = $this->model_shipping_custom_method->getShippingLift();
        //расчётная цена
        $weight = $result['weight'];
        $price  = $result['price'];
        $sum    = $totalWeight * $price / $weight;

        $data['shipping_lift']['text']      = $this->language->get('text_shipping_lift');
        $data['shipping_lift']['post_name'] = 'shipping_lift';
        $data['shipping_lift']['sum']       = round($sum, 2);

        ////////////////////////////////
        //ПОДЪЁМ НА ЛЕБЁДКЕ
        $result = $this->model_shipping_custom_method->getShippingWinch();
        //расчётная цена
        $weight      = $result['weight'];
        $price       = $result['price'];
        $price_first = $result['first_price'];
        $sum    = $price_first + $totalWeight * $weight / $price;

        $data['shipping_winch']['text']      = $this->language->get('text_shipping_winch');
        $data['shipping_winch']['post_name'] = 'shipping_winch';
        $data['shipping_winch']['sum']       = round($sum, 2);

        ////////////////////////////////
        //РАЗГРУЗКА
        $result = $this->model_shipping_custom_method->getShippingUnloading();
        //расчётная цена
        $weight = $result['weight'];
        $price  = $result['price'];
        $sum    = $totalWeight * $price / $weight;

        $data['shipping_unloading']['text']      = $this->language->get('text_shipping_unloading');
        $data['shipping_unloading']['post_name'] = 'shipping_unloading';
        $data['shipping_unloading']['sum']       = round($sum, 2);

        ////////////////////////////////
        //ПОДЪЁМ ЗА ЕДЕНИЦУ ВЕСА
        $result = $this->model_shipping_custom_method->getShippingWeightLine($weight);
        //расчётная цена
        $weight = $result['weight_first'];
        $price  = $result['price'];
        $sum    = $totalWeight * $price / $weight;

        $data['shipping_weight_line']['text']      = $this->language->get('text_shipping_weight_line');
        $data['shipping_weight_line']['post_name'] = 'shipping_weight_line';
        $data['shipping_weight_line']['sum']       = round($sum, 2);

        ////////////////////////////////
        //ВЫВОЗ МУСОРА
        $result = $this->model_shipping_custom_method->getShippingGarbage();
        //расчётная цена
        $price  = $result['price'];
        $sum    = $price;

        $data['shipping_garbage']['text']      = $this->language->get('text_shipping_garbage');
        $data['shipping_garbage']['post_name'] = 'shipping_garbage';
        $data['shipping_garbage']['sum']       = round($sum, 2);

        return $data;
    }

    protected function getCartTotalWeight($data) {
        return (int)$data['weight'];
    }
}


?>