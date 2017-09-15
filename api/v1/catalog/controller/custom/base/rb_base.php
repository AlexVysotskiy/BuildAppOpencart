<?php

class ControllerCustomRbBaseAPI extends ApiController {

    public function index()
    {
        //if($this->request->isPostRequest()) {
        if($this->request->isGetRequest()) {

            $this->response->setOutput(array('url' => $this->get()));
        } else {
            throw new ApiException(ApiResponse::HTTP_RESPONSE_CODE_NOT_FOUND, ErrorCodes::ERRORCODE_METHOD_NOT_FOUND, ErrorCodes::getMessage(ErrorCodes::ERRORCODE_METHOD_NOT_FOUND));
        }
    }

    protected function get()
    {
        //ДОСТАЁМ ПО ПРЕВОМУ ПРОДУКТУ
        //ГРУППУ ПОЛЬЗОВАТЕЛЯ ДЛЯ ЗАКРПЛЕНИЯ ЗАКАЗА
        //В ПАНЕЛИ АДМИНИСТРАТИРОВАНИЯ ЗА КОНКРЕТНЫМ ПРОДОВЦОМ
        $this->load->model('catalog/product');
        $order_data['user_group_franchise_id'] = $this->model_catalog_product->getProductUserId($shippingAddress['zone_id']);



        $idShop = 'snabjenec';
        $order_id = 1;
        //ПЕРВЫЙ ПАРОЛЬ
        $mrh_pass1 = "pqUqbT92WqL076ciTAwm";
        $sum = 8.96;


        $url = 'https://auth.robokassa.ru/Merchant/Index.aspx?';

        //ID магазина
        $url .= "&MrchLogin=" . $idShop;
        // номер заказа
        $url .= '&InvId=' . $order_id;
        // описание заказа
        $url .= "&Desc=ROBOKASSA";
        // сумма заказа
        $url .= "&OutSum=" . $sum;
        // тип товара
        $shp_item = 1;

        // предлагаемая валюта платежа
        $in_curr = "";

        // язык
        $url .= "&Culture=ru";

        // кодировка
        $url .= "&Encoding=utf-8";

        //$test = '';
        $url .= '&IsTest=1';

        $url .= '&Shp_item=1';

        // формирование подписи
        $url .= '&SignatureValue=' .md5("$idShop:$sum:$order_id:$mrh_pass1:Shp_item=$shp_item");

        return $url;
    }

}

?>