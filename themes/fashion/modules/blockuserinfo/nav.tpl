<!-- Block user information module NAV  -->

<div class="header_user_info">
    <div class="content_container">
    <a class="mainColorHoverOnly" href="{$link->getPageLink('order', true)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Checkout' mod='blockuserinfo'}">
        {l s='Checkout' mod='blockuserinfo'}
    </a>
    <a class="mainColorHoverOnly" href="{$link->getModuleLink('blockwishlist', 'mywishlist', array(), true)|escape:'html':'UTF-8'}"  rel="nofollow" title="{l s='My wishlists' mod='blockuserinfo'}">
        {l s='Wishlists' mod='blockuserinfo'}
    </a>
    <a class="mainColorHoverOnly" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" title="{l s='View my customer account' mod='blockuserinfo'}" class="account" rel="nofollow">{if $is_logged}<span>{$cookie->customer_firstname} {$cookie->customer_lastname}</span>{else}{l s='My Account' mod='blockuserinfo'}{/if}</a>
    </div>
</div>
<!-- /Block usmodule NAV -->