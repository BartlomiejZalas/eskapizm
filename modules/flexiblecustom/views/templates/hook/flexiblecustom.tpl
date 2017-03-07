{if isset($modules) && $modules}
    {foreach from=$modules item=module name=obj}
        <div class="clearfix clearBoth flexible-custom-box {$moduleLayout}">
            {$module.groups}
        </div>        
    {/foreach}    
{/if}