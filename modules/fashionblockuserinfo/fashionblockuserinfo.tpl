<!-- Block user information module NAV  -->
<div class="clearfix">
<div class="header_user_info visible-mobile">
    <div class="content_container">
    <a href="{$link->getPageLink('products-comparison', true)|escape:'html':'UTF-8'}" title="{l s='My compare' mod='fashionblockuserinfo'}">
        {l s='Compare' mod='fashionblockuserinfo'}
    </a>
    <a href="{$link->getModuleLink('blockwishlist', 'mywishlist', array(), true)|escape:'html':'UTF-8'}"  rel="nofollow" title="{l s='My wishlists' mod='fashionblockuserinfo'}">
        {l s='Wishlists' mod='fashionblockuserinfo'}
    </a>
    <a class="account" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" title="{l s='View my customer account' mod='fashionblockuserinfo'}"  rel="nofollow">{if $is_logged}<span>{$cookie->customer_firstname} {$cookie->customer_lastname}</span>{else}{l s='My Account' mod='fashionblockuserinfo'}{/if}</a>
    </div>
</div>
</div>
<!-- /Block usmodule NAV -->