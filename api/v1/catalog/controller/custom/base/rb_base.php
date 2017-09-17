<?php

class ControllerCustomRbBaseAPI extends ApiController {

    public function index()
    {
        if($this->request->isGetRequest() && $this->request->get['order_id']) {

            $this->response->setOutput(array('url' => $this->get()));
        } else {
            throw new ApiException(ApiResponse::HTTP_RESPONSE_CODE_NOT_FOUND, ErrorCodes::ERRORCODE_METHOD_NOT_FOUND, ErrorCodes::getMessage(ErrorCodes::ERRORCODE_METHOD_NOT_FOUND));
        }
    }

    protected function get()
    {
        //ПОЛУЧАЕМ ДАННЫЕ О ФРАНШИЗЕ
        $this->load->model('catalog/product');
        $dataCP = $this->model_catalog_product->getProductUserId($this->session->data['zone_id']);

        //НОМЕР ЗАКАЗА
        $order_id = (int)$this->request->get['order_id'];

        //ПОЛУЧАЕМ ДАННЫЕ О ЗАКАЗЕ
        $this->load->model('checkout/order');
        $dataCO = $this->model_checkout_order->getOrder($order_id);

        if ($dataCP && $dataCO) {

            //УНИКАЛЬНЫЙ ID МАГАЗИНА
            $idShop = $dataCP['shop_id'];
            //ПЕРВЫЙ ПАРОЛЬ
            $mrh_pass1 = $dataCP['pass1'];

            //ПОЛУЧАЕМ СУММУ ЗАКАЗА
            $sum = $dataCO['total'];

            $url = 'https://auth.robokassa.ru/Merchant/Index.aspx?';

            //ID магазина
            $url .= "&MrchLogin=" . $idShop;

            //НОМЕР ЗАКАЗА
            $url .= '&InvId=' . $order_id;

            //ОПИСАНИЕ
            //$url .= "&Desc=ROBOKASSA";

            //СУММА ЗАКАЗА
            $url .= "&OutSum=" . $sum;

            //ТИП ТОВАРА
            $shp_item = 1;

            //ВАЛЮТА
            $in_curr = "";

            //ЯЗЫК
            $url .= "&Culture=ru";

            //КОДИРОВКА
            $url .= "&Encoding=utf-8";

            //ВКЛЮЧЁН ТЕСТОВЫЙ РЕЖИМ
            if ($dataCP['test_mode'] == 1) {
                $url .= '&IsTest=1';
            }

            $url .= '&Shp_item=' . $shp_item;

            //ФОРМИРУЕМ ПОДПИСЬ
            $url .= '&SignatureValue=' . md5("$idShop:$sum:$order_id:$mrh_pass1:Shp_item=$shp_item");

            return $url;

        }  else {
            throw new ApiException(ApiResponse::HTTP_RESPONSE_CODE_NOT_FOUND, ErrorCodes::ERRORCODE_METHOD_NOT_FOUND, ErrorCodes::getMessage(ErrorCodes::ERRORCODE_METHOD_NOT_FOUND));
        }
    }

}

?>