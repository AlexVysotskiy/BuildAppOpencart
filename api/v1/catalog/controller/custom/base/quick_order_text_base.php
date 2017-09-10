<?php

class ControllerCustomQuickOrderTextBaseAPI extends ApiController {

	public function index() {
        if($this->request->isPostRequest()) {
            $this->response->setOutput($this->post());
        } else {
            throw new ApiException(ApiResponse::HTTP_RESPONSE_CODE_NOT_FOUND, ErrorCodes::ERRORCODE_METHOD_NOT_FOUND, ErrorCodes::getMessage(ErrorCodes::ERRORCODE_METHOD_NOT_FOUND));
        }
    }

    protected function post()
    {
        $customer = $this->customer;

        $message = 'Поступило обращение от пользователя ' . $customer->getFirstName() . ' ' . $customer->getLastName() . '.' . PHP_EOL . PHP_EOL;
        $message .= 'Текст обращения: ' . html_entity_decode(trim($this->request->post['text'])) . PHP_EOL . PHP_EOL;

        $message .= 'Телефон: ' . $customer->getTelephone() . PHP_EOL . PHP_EOL;
        $message .= 'Email: ' . $customer->getEmail() . PHP_EOL . PHP_EOL;

        $subject = 'Обращение от пользователя ' . date('d.m.y');

        //ВЫБИРАЕМ ПОЧТУ ПРОДОВЦА ДЛЯ ОТПРАВКИ ПИСЬМО
        //ПО zone_id ПОЛЬЗОВАТЕЛЯ
        $this->load->model('checkout/quick_order');
        $email = $this->model_checkout_quick_order->getShopEmail($this->session->data['zone_id']);

        $this->sendMail($email, array(
            'subject' => $subject,
            'message' => $message,
        ));

        $response = array(
            'success' => 1,
        );

        return $response;
    }

    protected function sendMail($email, $params)
    {
        $subject = sprintf($params['subject'], html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

        $message = $params['message'];

        $mail = new Mail();
        $mail->protocol = $this->config->get('config_mail_protocol');
        $mail->parameter = $this->config->get('config_mail_parameter');
        $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
        $mail->smtp_username = $this->config->get('config_mail_smtp_username');
        $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
        $mail->smtp_port = $this->config->get('config_mail_smtp_port');
        $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

        $mail->setTo($email);
        $mail->setFrom($this->config->get('config_email'));
        $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
        $mail->setSubject($subject);
        $mail->setText($message);
        $mail->send();
    }
}

?>