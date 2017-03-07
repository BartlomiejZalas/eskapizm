<!-- Block user information module NAV  -->
<div class="header_user_info hidden-mobile">
    <div class="content_container">
    <a href="{$link->getPageLink('order', true)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Checkout' mod='fashionblockuserinfo'}">
        {l s='Checkout' mod='fashionblockuserinfo'}
    </a>
    <a href="{$link->getPageLink('products-comparison', true)|escape:'html':'UTF-8'}" title="{l s='My compare' mod='fashionblockuserinfo'}">
        {l s='Compare' mod='fashionblockuserinfo'}
    </a>
    <a href="{$link->getModuleLink('blockwishlist', 'mywishlist', array(), true)|escape:'html':'UTF-8'}"  rel="nofollow" title="{l s='My wishlists' mod='fashionblockuserinfo'}">
        {l s='Wishlists' mod='fashionblockuserinfo'}
    </a>
    <a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" title="{l s='View my customer account' mod='fashionblockuserinfo'}" class="account" rel="nofollow">{if $is_logged}<span>{$cookie->customer_firstname} {$cookie->customer_lastname}</span>{else}{l s='My Account' mod='fashionblockuserinfo'}{/if}</a>
    </div>
</div>
<!-- /Block usmodule NAV -->