<?php

class ControllerCustomCategoryBaseAPI extends ApiController
{
	public function index($args = array())
    {
		if ($this->request->isGetRequest()) {
            $categories = array('categories' => $this->getCategories());
            $this->response->setOutput($categories);
		} else {
			throw new ApiException(ApiResponse::HTTP_RESPONSE_CODE_NOT_FOUND, ErrorCodes::ERRORCODE_METHOD_NOT_FOUND, ErrorCodes::getMessage(ErrorCodes::ERRORCODE_METHOD_NOT_FOUND));
		}
	}

    protected function getCategories() {
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');

        $categories = $this->getCategoryById(0);

        return $categories;
    }

    protected function getCategoryById($categoryId, $recursive = true) {
        $categories = $this->model_catalog_category->getCategoriesFranchise($categoryId);

        foreach ($categories as &$category) {
            // Filter elements
            $filteredCategory = array();
            $filteredCategory['category_id'] = (int)$category['category_id'];
            $filteredCategory['name'] = $category['name'];
            $filteredCategory['description'] = $category['description'];
            $filteredCategory['thumb_image'] = $this->model_tool_image->resize($category['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));

            $data = array(
                'filter_category_id'  => $category['category_id'],
                'filter_sub_category' => true);

            $product_total = $this->model_catalog_product->getTotalProducts($data);
            $filteredCategory['total_products'] = (int)$product_total;


            if($recursive == true) {
                $filteredCategory['categories'] = $this->getCategoryById($category['category_id']);
            }
            else {
                $filteredCategory['subcategory_count'] = (int)$this->model_catalog_category->getTotalCategoriesByCategoryId($category['category_id']);
            }

            $category = $filteredCategory;
        }

        return $categories;
    }
}

?>