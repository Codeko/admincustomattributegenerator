<?php

/**
 * Module to add a custome attribute generator system enhanced to not delete the previous combinations and allowing to set the combinations images.
 * 
 * Icons by http://www.flaticon.com/authors/freepik - CC BY 3.0 http://creativecommons.org/licenses/by/3.0/
 * 
 * @author Codeko <codeko@codeko.com>
 * @copyright (c) Codeko
 * @license https://www.gnu.org/licenses/agpl-3.0.html AFFERO GPL (AGPL 3.0)
 */

if (!defined('_PS_VERSION_'))
    exit;

class AdminCustomattributeGenerator extends Module {

    public function __construct() {
        $this->name = 'admincustomattributegenerator';
        $this->tab = 'administration';
        $this->version = '1.1.1';
        $this->author = 'Codeko';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->author_uri = 'http://codeko.com';
        $this->module_key = "186796d5d4e5fdb92c13f63a8e6a997b";

        parent::__construct();

        $this->displayName = $this->l('Enhanced combination generator');
        $this->description = $this->l('Allow to generate/edit attribute combinations without delete the existings and assign images to them');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install() {
        // Agregamos el tab para que funcione el admin controller extendido
        $tab = new Tab();
        $tab->name = array();
        $tab->class_name = 'AdminCustomattributeGenerator';
        $tab->module = 'admincustomattributegenerator';
        $tab->id_parent = -1;

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Enhanced combination generator';
        }

        $tabAdded = $tab->add();

        return $tabAdded && parent::install();
    }

}
