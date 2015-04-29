{**
* Based on Prestashop Core combination generator
* @author Codeko <codeko@codeko.com>
* @license https://www.gnu.org/licenses/agpl-3.0.html AFFERO GPL (AGPL 3.0)
* @see admin/themes/default/template/controllers/attribute_generator/content.tpl
**}
{include file="controllers/attribute_generator/content.tpl"}
<script>
    jQuery(function($){
        $("table tbody[name='result_table'] tr").remove();
        $("table tbody[name='result_table']").parent().hide();
        $("#combination_generator_images").insertBefore($("#generator [name='generate']").prev());
    });
</script>
<div style="display:none;">
    <div id="combination_generator_images" class="row form-group" style="margin-top:20px;">
        {if $images|count}
            <label class="control-label col-lg-3">{l s='Image Combination'  mod='admincustomattributegenerator'}</label>
            <div class="col-lg-9">
                <ul id="id_image_attr" class="list-inline">
                    {foreach from=$images key=k item=image}
                    <li>
                            <input type="checkbox" name="id_image_attr[]" value="{$image.id_image}" id="id_image_attr_{$image.id_image}" />
                            <label for="id_image_attr_{$image.id_image}">
                                    <img class="img-thumbnail" src="{$image.src}" alt="{$image.legend|escape:'html':'UTF-8'}" title="{$image.legend|escape:'html':'UTF-8'}" />
                            </label>
                    </li>
                    {/foreach}
                </ul>
            </div>
        {/if}
    </div>
</div>