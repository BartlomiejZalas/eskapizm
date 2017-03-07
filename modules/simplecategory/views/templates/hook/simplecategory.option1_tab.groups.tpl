{if isset($simplecategory_groups) && $simplecategory_groups|@count >0}	
	<div class="simple-tab-container">
		<div class="row">
		{foreach from=$simplecategory_groups item=group name=groups}
			{if isset($group.products) && $group.products|@count >0}
			<div class="col-sm-6">
				<h3 class="group-title"><span>{$group.name}</span></h3>
				<div class="group-content">
					<div class="row">
						<div class="group_owl"> 
						{include file="$tpl_dir./product-list5.tpl" products=$group.products}
						</div>
					</div>
				</div>
			</div>
			{/if} 
		{/foreach}
		</div>
	</div>
{/if}
