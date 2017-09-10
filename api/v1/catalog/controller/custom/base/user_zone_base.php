<?php

class ControllerCustomUserZoneBaseAPI extends ApiController
{
	public function index()
    {
	    $array = array (
	        'zone_id' => false
        );

        if (isset($this->request->get['zone_id'])) {
            $array['zone_id'] = $this->setZone();
            $this->response->setOutput($array);
        } else {
            $array['zone_id'] = $this->get();
            $this->response->setOutput($array);
        }
    }

    protected function get()
    {
        return (isset($this->session->data['zone_id'])) ? $this->session->data['zone_id'] : false;
    }

    protected function setZone()
    {
        if (isset($this->request->get['zone_id'])) {
            $this->session->data['zone_id'] = (int)$this->request->get['zone_id'];
            $this->session->write(session_id(), serialize($this->session->data));

            return $this->session->data['zone_id'];
        } else {
            throw new ApiException(ApiResponse::HTTP_RESPONSE_CODE_BAD_REQUEST, ErrorCodes::ERRORCODE_METHOD_NOT_FOUND, ErrorCodes::getMessage(ErrorCodes::ERRORCODE_METHOD_NOT_FOUND));
        }
    }
}

?>