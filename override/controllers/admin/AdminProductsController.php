<?php

/**
 * Adds our generator button to the productos combinations tab
 * 
 * @author Codeko <codeko@codeko.com>
 * @copyright (c) Codeko
 * @license https://www.gnu.org/licenses/agpl-3.0.html AFFERO GPL (AGPL 3.0)
 * */

class AdminProductsController extends AdminProductsControllerCore {

    /**
     * Change the default combinations.tpl to the overrided one
     * @see AdminProductsControllerCore::initFormAttributes($product)
     */
    public function initFormAttributes($product) {
        //If combinations.tpl change the override template dir to module template dir
        if ($this->tpl_form == 'combinations.tpl') {
            $template_dir = _PS_MODULE_DIR_ . 'admincustomattributegenerator/views/templates/admin/';
            $this->context->smarty->addTemplateDir($template_dir, 1);
        }
        return parent::initFormAttributes($product);
    }

}
