<?php

class ControllerCustomRbStateBaseAPI extends ApiController {

    public function index()
    {
        if($this->request->isGetRequest()) {
            $this->load->model('catalog/product');
            $data = $this->model_catalog_product->getProductUserId($this->session->data['zone_id']);

            $this->response->setOutput(array('state' => (int)$data['test_mode']));
        } else {
            throw new ApiException(ApiResponse::HTTP_RESPONSE_CODE_NOT_FOUND, ErrorCodes::ERRORCODE_METHOD_NOT_FOUND, ErrorCodes::getMessage(ErrorCodes::ERRORCODE_METHOD_NOT_FOUND));
        }
    }
}

?>