<?php

require_once(DIR_API_APPLICATION . 'controller/social/base/social_base.php');

class ControllerSocialOrderCallApi extends ControllerSocialBaseAPI
{

    public function index($args = array())
    {
        parent::index($args);
    }

    protected function get()
    {
        $response = array(
            'success' => 1,
            'phone' => $this->config->get('config_telephone'),
        );

        $this->response->setOutput($response);
    }

}
