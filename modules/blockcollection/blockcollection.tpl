<!--fashion collection-->
<div id="fashion_collection_block" {if !$hide_left_column and !$hide_right_column}class="both-width"{/if}>
    <div id="collection_block">
        {*}<h3>{l s='About Designer' mod='blockcollection'}</h3>{*}
        <div class="wrap_ul">
            <a href="javascript:void(0)" id="collection_block_next" class="btnbgcolorhoveronly next_slide">Next</a>
            <a href="javascript:void(0)" id="collection_block_prev" class="btnbgcolorhoveronly prev_slide">Prev</a>
            <ul>
            {foreach from=$collections item='collection'}
                <li class="slide_item">
                    <input type="hidden" class="name_collection" value="{$collection.name_collection}" />
                    <h3 class="call_slide_product">{l s='About Designer' mod='blockcollection'}</h3>
                    <input type="hidden" value="{$collection.id_collection}" class="id_collection" />
                    <a href="javascript:void(0)" class="call_slide_product" title="{l s='Click on image to load products of the collection' mod='blockcollection'}"><img src="{$module_dir}img/{$collection.file_name}" alt="" /></a>
                    <p class="info"><span class="name">{$collection.name|escape:html:'UTF-8'}</span>&nbsp;<span class="company">{$collection.company|escape:html:'UTF-8'}</span></p>
                    <p class="description">{$collection.text|escape:html:'UTF-8'}</p>
                </li>
            {/foreach}
            </ul>
        </div>
    </div>
    <div id="collection_products_wrap">
        <h3 ><span class="collection_name"></span>{$collections[0].name_collection}</h3>
        <div class="wrap_ul">
        <img class="load-more-img" src="{$module_dir}img/loader.gif" alt="" />
            <ul>
                {if isset($products) AND $products}
                    {foreach from=$products item='product'}
                    <li class="slide_item"> 
                        <a class="product_image" href="{$product.link|escape:'htmlall':'UTF-8'}" title="{$product.name|escape:'htmlall':'UTF-8'}">
                            <img class="post_thumbnail_blog" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html'}" alt="" /></a>
                        <h3 class="product_name"><a href="{$product.link|escape:'htmlall':'UTF-8'}" title="{$product.name|escape:'htmlall':'UTF-8'}">{$product.name|escape:'htmlall':'UTF-8'}</a></h3>
                        {if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
                        <p class="product_price">
                            {if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}<span class="price" style="display: inline;">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span>{/if}
                        </p>
                        {/if}
                    </li>
                    {/foreach}
                {/if}
            </ul>
        </div>
        <a href="javascript:void(0)" id="collection_products_next" class="btnbgcolorhoveronly next_slide pull-right">Next</a>
        <a href="javascript:void(0)" id="collection_products_prev" class="btnbgcolorhoveronly prev_slide pull-right">Prev</a>
        
    </div>
</div>
<!--/fashion collection-->