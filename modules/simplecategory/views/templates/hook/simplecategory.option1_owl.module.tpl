{if isset($simplecategory_item) && $simplecategory_item|@count >0}
	{if isset($simplecategory_item.products) && $simplecategory_item.products|@count >0}
		<div class="container">	
			<div class="carousel-container">
				{if $simplecategory_item.display_name == '1'}<h2 class="carousel-title">{$simplecategory_item.name}</h2>{/if}
				<div class="row">
					<div class="owl-carousel bestsellers-carousel">
						{include file="$tpl_dir./group-product-carousel-style2.tpl" products=$simplecategory_item.products}	
					</div>
				</div>					
			</div>
		</div>
	{/if}	
{/if}

