<?php

/**
 * Description of ControllerSocialBaseAPI
 *
 * @author Alexander
 */
class ControllerSocialBaseAPI extends ApiController
{

    public function index($args = array())
    {
        if ($this->request->isPostRequest()) {
            $this->post();
        } elseif ($this->request->isGetRequest()) {
            $this->get();
        } else {
            throw new ApiException(ApiResponse::HTTP_RESPONSE_CODE_NOT_FOUND, ErrorCodes::ERRORCODE_METHOD_NOT_FOUND, ErrorCodes::getMessage(ErrorCodes::ERRORCODE_METHOD_NOT_FOUND));
        }
    }

    protected function post()
    {
        throw new Exception('method is not defined!');
    }

    protected function get()
    {
        throw new Exception('method is not defined!');
    }

    /**
     * метод отправки email
     * @param type $params
     */
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
