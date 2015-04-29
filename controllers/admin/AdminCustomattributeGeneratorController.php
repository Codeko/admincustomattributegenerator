<?php

/**
 * Custom attribute generator controlled. Based on the Prestashop's original
 * 
 * @author Codeko <codeko@codeko.com>
 * @copyright (c) Codeko
 * @license https://www.gnu.org/licenses/agpl-3.0.html AFFERO GPL (AGPL 3.0)
 * */

@ini_set('max_execution_time', 3600);

class AdminCustomattributeGeneratorController extends AdminAttributeGeneratorControllerCore {

    public function initProcess() {
        parent::initProcess();
        //Add our template dir
        $tpl_path = _PS_MODULE_DIR_ . 'admincustomattributegenerator/views/templates/admin';
        $this->context->smarty->addTemplateDir($tpl_path);
    }

    /**
     * Custom versión of the AdminAttributeGeneratorControllerCore. This version not delete the old 
     * combinations. Add the images to the combinations and do a correct default combination asignation
     * @see AdminAttributeGeneratorControllerCore::processGenerate()
     */
    public function processGenerate() {
        if (!is_array(Tools::getValue('options')))
            $this->errors[] = Tools::displayError('Please select at least one attribute.');
        else {
            $tab = array_values(Tools::getValue('options'));
            if (count($tab) && Validate::isLoadedObject($this->product)) {
                AdminAttributeGeneratorController::setAttributesImpacts($this->product->id, $tab);
                $this->combinations = array_values(AdminAttributeGeneratorController::createCombinations($tab));
                $values = array_values(array_map(array(
                    $this,
                    'addAttribute'
                                ), $this->combinations));

                // @since 1.5.0
                if ($this->product->depends_on_stock == 0) {
                    $attributes = Product::getProductAttributesIds($this->product->id, true);
                    foreach ($attributes as $attribute)
                        StockAvailable::removeProductFromStockAvailable($this->product->id, $attribute['id_product_attribute'], Context::getContext()->shop);
                }

                SpecificPriceRule::disableAnyApplication();

                //admincustomattributegenerator code start
                //In the original controller all combinations are deteled.
                //we don't delete the combinations
                // $this->product->deleteProductAttributes();
                //Generate the combinations 
                $this->generateCustomMultipleCombinations($this->product, $values, $this->combinations);

                //Assign the combination images
                $images = Tools::getValue('id_image_attr');
                $this->updateImagesAttributes($this->product, $values, $this->combinations, $images);

                //Set the default combination
                $this->product->checkDefaultAttributes();

                //admincustomattributegenerator code end
                // @since 1.5.0
                if ($this->product->depends_on_stock == 0) {
                    $attributes = Product::getProductAttributesIds($this->product->id, true);
                    $quantity = (int) Tools::getValue('quantity');
                    foreach ($attributes as $attribute)
                        StockAvailable::setQuantity($this->product->id, $attribute['id_product_attribute'], $quantity);
                } else
                    StockAvailable::synchronize($this->product->id);

                SpecificPriceRule::enableAnyApplication();
                SpecificPriceRule::applyAllRules(array(
                    (int) $this->product->id
                ));

                Tools::redirectAdmin($this->context->link->getAdminLink('AdminProducts') . '&id_product=' . (int) Tools::getValue('id_product') . '&updateproduct&key_tab=Combinations&conf=4');
            } else
                $this->errors[] = Tools::displayError('Unable to initialize these parameters. A combination is missing or an object cannot be loaded.');
        }
    }

    public function initContent() {
        if (!Combination::isFeatureActive()) {
            $url = '<a href="index.php?tab=AdminPerformance&token=' . Tools::getAdminTokenLite('AdminPerformance') . '#featuresDetachables">' . $this->l('Performance') . '</a>';
            $this->displayWarning(sprintf($this->l('This feature has been disabled. You can activate it here: %s.'), $url));
            return;
        }
        parent::initContent();
        $images = Image::getImages($this->context->language->id, (int) Tools::getValue('id_product'));
        if (is_array($images)) {
            foreach ($images as $k => $image) {
                $images[$k]['src'] = $this->context->link->getImageLink($this->product->link_rewrite[$this->context->language->id], $this->product->id . '-' . $image['id_image'], 'small_default');
            }
        }
        $this->context->smarty->assign('images', $images);
    }

    public function generateCustomMultipleCombinations($product, $combinations, $attributes) {
        $res = true;
        foreach ($combinations as $key => $combination) {
            $id_combination = (int) $product->productAttributeExists($attributes[$key], false, null, true, true);
            $obj = new Combination($id_combination);

            if ($id_combination) {
                $obj->minimal_quantity = 1;
                $obj->available_date = '0000-00-00';
            }

            foreach ($combination as $field => $value) {
                $obj->$field = $value;
            }

            $product->setAvailableDate();

            $obj->save();

            if (!$id_combination) {
                $attribute_list = array();
                foreach ($attributes[$key] as $id_attribute)
                    $attribute_list[] = array(
                        'id_product_attribute' => (int) $obj->id,
                        'id_attribute' => (int) $id_attribute
                    );
                $res &= Db::getInstance()->insert('product_attribute_combination', $attribute_list);
            }
        }
        return $res;
    }

    /**
     * Update a images product attributes
     * 
     * @param unknown $combinations
     * @param unknown $attributes
     * @param unknown $images
     */
    public function updateImagesAttributes($product, $combinations, $attributes, $images) {
        if (is_array($images) && count($images)) {
            foreach ($combinations as $key => $combination) {
                $id_combination = (int) $product->productAttributeExists($attributes[$key], false, null, true, true);
                $this->updateImagesAttribute($id_combination, $images);
            }
        }
    }

    /**
     * Update a images product attribute
     *
     * @param integer $id_combination Product attribute id
     * @param integer $id_images array Image id
     */
    private function updateImagesAttribute($id_combination, $id_images) {
        $combination = new Combination($id_combination);

        if (is_array($id_images) && count($id_images)) {
            $combination->setImages($id_images);
        }
    }

}
