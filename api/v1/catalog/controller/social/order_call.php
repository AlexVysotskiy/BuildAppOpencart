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

        $message = 'Поступило обращение от пользователя ' . $customer->getFirstName() . ' ' . $customer->getLastName() . '.' . PHP_EOL . PHP_EOL;
        $message = 'Текст обращения: ' . html_entity_decode(trim($this->request->post['text'])) . PHP_EOL . PHP_EOL;

        $message = 'Телефон: ' . $customer->getTelephone() . PHP_EOL . PHP_EOL;
        $message = 'Email: ' . $customer->getEmail() . PHP_EOL . PHP_EOL;

        $subject = 'Обращение от пользователя ' . date('d.m.y');

        $this->sendMail($customer->getEmail(), array(
            'subject' => $subject,
            'message' => $message,
        ));

        $response = array(
            'success' => 1,
        );
        $this->response->setOutput($response);
    }

}
