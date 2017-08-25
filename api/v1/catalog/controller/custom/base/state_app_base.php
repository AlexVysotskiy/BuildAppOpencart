<?php

class ControllerCustomStateAppBaseAPI extends ApiController {

	public function index() {

        //СТАТУС 0 -> ПРИЛОЖЕНИЕ ОТКЛЮЧЕННО
        //СТАТУС 1 -> ПРИЛОЖЕНИЕ ВКЛЮЧЕНО
        $getStateApp = $this->getStateApp();

        $arrayResponse = [
            'status' => $getStateApp
        ];

        $this->response->setOutput($arrayResponse);
    }

    protected function getStateApp() {
        $this->load->model('setting/setting');
        $result = $this->config->get('config_state_app');

        return $result;
    }
}

?>