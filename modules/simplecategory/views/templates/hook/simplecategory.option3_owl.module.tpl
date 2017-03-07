{if isset($simplecategory_item) && $simplecategory_item|@count >0}
	{if isset($simplecategory_item.products) && $simplecategory_item.products|@count >0}
	<section id="products-section" class="section">
		<div class="container">	
			<div class="carousel-container">
				{if $simplecategory_item.display_name == '1'}<h2 class="carousel-title big text-center">{$simplecategory_item.name}</h2>{/if}
				<div class="row">
					<div class="owl-carousel products-section-carousel center-buttons color2">
						{include file="$tpl_dir./group-product-carousel-style3.tpl" products=$simplecategory_item.products}	
					</div>
				</div>					
			</div>
		</div>
		<div class="section-btn-container"><a href="#collection-section" class="section-btn btn-prev" title="Previous Section">Previous Section</a> <a href="#lookbook-section" class="section-btn btn-next" title="Next Section">Next Section</a></div>
	</section>
	{/if}	
{/if}
