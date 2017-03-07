{if isset($simplecategory_modules) && $simplecategory_modules|@count >0}
    {foreach from=$simplecategory_modules item=module name=modules}                       
        {$module}
    {/foreach}    
{/if}
{strip}
{addJsDefL name=loggin_required}{l s='You must be logged in to manage your wishlist.' mod='simplecategory' js=1}{/addJsDefL}
{addJsDefL name=added_to_wishlist}{l s='Added to your wishlist.' mod='simplecategory' js=1}{/addJsDefL}
{addJsDef mywishlist_url=$link->getModuleLink('blockwishlist', 'simplecategory', array(), true)|escape:'quotes':'UTF-8'}
{/strip}