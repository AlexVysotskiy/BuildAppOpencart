<?php

require_once(DIR_API_APPLICATION . 'controller/social/base/social_base.php');

class ControllerSocialOrderCallApi extends ControllerSocialBaseAPI
{

    public function index($args = array())
    {
        parent::index($args);
    }

    protected function post()
    {
//        $userId = $this->request->post['userId'];

        /* @var $customer Customer */
        $customer = $this->customer;

        $message = 'Поступил запрос на обратный звонок от ' . $customer->getFirstName() . ' ' . $customer->getLastName() . '.' . PHP_EOL . PHP_EOL;

        $message .= 'Телефон: ' . $customer->getTelephone() . PHP_EOL . PHP_EOL;
        $message .= 'Email: ' . $customer->getEmail() . PHP_EOL . PHP_EOL;

        $subject = 'Заказ обратного звонка ' . date('d.m.y');

        $this->sendMail($this->config->get('config_email'), array(
            'subject' => $subject,
            'message' => $message,
        ));

        $response = array(
            'success' => 1,
        );
        $this->response->setOutput($response);
    }

}
