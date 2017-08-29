<?php

class ControllerCustomStateAppBaseAPI extends ApiController {

	public function index() {

        //СТАТУС 0 -> ПРИЛОЖЕНИЕ ОТКЛЮЧЕННО
        //СТАТУС 1 -> ПРИЛОЖЕНИЕ ВКЛЮЧЕНО
        $getStateApp = $this->getStateApp();

        $arrayResponse = [
            'status' => $getStateApp
        ];

        if($this->request->isGetRequest()) {
            $this->response->setOutput($arrayResponse);
        }
        else {
            throw new ApiException(ApiResponse::HTTP_RESPONSE_CODE_NOT_FOUND, ErrorCodes::ERRORCODE_METHOD_NOT_FOUND, ErrorCodes::getMessage(ErrorCodes::ERRORCODE_METHOD_NOT_FOUND));
        }
    }

    protected function getStateApp() {
        $this->load->model('setting/setting');
        $result = $this->config->get('config_state_app');

        return $result;
    }
}

?>