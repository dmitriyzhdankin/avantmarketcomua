<?php

class shopProductDeleteImageController extends waJsonController
{
    public function execute()
    {
        $id = waRequest::get('id', null, waRequest::TYPE_INT);
        if (!$id) {
            throw new waException(_w("Unknown image"));
        }

        $product_images_model = new shopProductImagesModel();
        if (!$product_images_model->delete($id)) {
            throw new waException(_w("Coudn't delete image"));
        }
        $this->response['id'] = $id;
    }
}