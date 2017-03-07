{if isset($simplecategory_item) && $simplecategory_item|@count >0}
	{if isset($simplecategory_item.products) && $simplecategory_item.products|@count >0}
	<div class="widget">
		{if $simplecategory_item.display_name == '1'}<h3>{$simplecategory_item.name}</h3>{/if}
		<div class="owl-carousel specials-slider full-product-slider">
			{include file="$tpl_dir./single-product-carousel-style1.tpl" products=$simplecategory_item.products}				
		</div>			
	</div>
	{/if}	
{/if}