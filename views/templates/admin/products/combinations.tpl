{**
 * Adds our custom combination button to the admin
 * @author Codeko <codeko@codeko.com>
 * @license https://www.gnu.org/licenses/agpl-3.0.html AFFERO GPL (AGPL 3.0)
 * @see: admin/themes/default/template/controllers/products/combinations.tpl
**}
{if isset($product->id) && !$product->is_virtual}
    {include file="controllers/products/combinations.tpl" product_tab="Combinations"}
    <a href="index.php?tab=AdmincustomAttributeGenerator&amp;id_product={$product->id}&amp;admincustomattributegenerator&amp;token={getAdminToken tab='AdminCustomattributeGenerator'}" id="desc-product-newCustomCombination" class="btn btn-default confirm_leave">
            <i class="process-icon-cogs"></i> 
            <span>{l s='Add/edit combinations' mod='admincustomattributegenerator'}</span>
    </a>
    <script>
        jQuery(function($){
            $("#desc-product-newCustomCombination").appendTo("#product-combinations > .panel-footer");
        });
    </script>
{/if}
