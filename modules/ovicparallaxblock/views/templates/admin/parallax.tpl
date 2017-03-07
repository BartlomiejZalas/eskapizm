<input type="hidden" name="id_parallax" value="{if isset($item->id_parallax)}{$item->id_parallax}{/if}" />
<div class="image item-field form-group">
	<label class="control-label col-lg-3">Parallax Background</label>
	<div class="col-lg-9">
        <div class="form-group">
            <div class="col-lg-10">
                {if isset($item->image)}
                    <img src="{$image_baseurl}{$item->image}" class="img-thumbnail" alt="" />
                {/if}
				<input type="file" name="parallax_bg" />
                <input type="hidden" name="has_image" value="{if isset($has_image)}{$has_image}{/if}"/>
                <input type="hidden" name="old_parallax_bg" value="{if isset($item->image)}{$item->image}{/if}"/>
            </div>
            <div class="col-lg-2">
            </div>
        </div>
	</div>                    
</div>
<div id="linkContainer" class="link_detail">
    <div class="item-field form-group">
    	<label class="control-label col-lg-3 ">Ratio</label>
    	<div class="col-lg-9">
            <div class="form-group">
                <div class="col-lg-10">
    				<input class="form-control" type="text" name="ratio" value="{if isset($item->ratio)}{$item->ratio}{/if}"/>
                    <p class="help-block newline">
                    {l s='As with parallax elements, the ratio is relative to the natural scroll speed. For ratios lower than 1, to avoid jittery scroll performance' mod='advancetopmenu'}
                    </p>
                </div>
                <div class="col-lg-2">
                </div>
            </div>
    	</div>
        	
    </div>
</div>
<div class="item-field form-group ">
    <label class="control-label col-lg-3">Switch Content</label>
    <div class="col-lg-9">
        <div class="form-group">
            <div class="col-lg-10">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" class="sw_type" name="sw_type" id="active_html" {if isset($item->type)&&$item->type == 1 || !isset($item->type)}checked="checked"{/if} value="html"/>
                    <label for="active_html">HTML</label>
                    <input type="radio" class="sw_type" name="sw_type" id="active_module" {if isset($item->type)&&$item->type == 0}checked="checked"{/if} value="module" />
                    <label for="active_module">Module</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
            <div class="col-lg-2">
			</div>	
        </div>
    </div>
</div>
<div id="html_container" class="item-field form-group sw_content" {if isset($item->type)&&$item->type == 0}style="display:none;"{/if}>
	<label class="control-label col-lg-3">HTML</label>
	<div class="col-lg-9">
        <div class="form-group">
        {foreach from=$langguages.all item=lang}
            <div class="translatable-field lang-{$lang.id_lang|escape:'htmlall':'UTF-8'}" {if $langguages.default_lang != $lang.id_lang}style="display:none"{/if}>
	            <div class="col-lg-10">
	                <textarea class="rte" name="item_html_{$lang.id_lang}" style="margin-bottom:10px; height:200px;" >{if isset($item->content[$lang.id_lang])}{$item->content[$lang.id_lang]}{/if}</textarea>
	            </div>
				<div class="col-lg-2">
					<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
						{$lang.iso_code|escape:'htmlall':'UTF-8'}
						<i class="icon-caret-down"></i>
					</button>
					{$lang_ul}
				</div>
			</div>
		  {/foreach}	
     </div> 
	</div>
</div>
<div id="module_container" class="item_type sw_content" {if isset($item->type)&&$item->type == 1 || !isset($item->type)}style="display:none;"{/if}>
    <div class="item-field form-group">
        <input type="hidden" id="ajaxurl" value="{$ajaxPath}"/>
		<label class="control-label col-lg-3">Module</label>
		<div class="col-lg-9">
            <div class="form-group">
	            <div class="col-lg-10">
	                <select class="form-control fixed-width-lg" name="module" id="module_select" >
						{$moduleOption}
					</select>
	            </div>
				<div class="col-lg-2">
				</div>
             </div>
		</div>
    </div>
    <div class="item-field form-group">
		<label class="control-label col-lg-3">Hook</label>
		<div class="col-lg-9">
            <div class="form-group">
	            <div class="col-lg-10">
	                <select class="form-control fixed-width-lg" name="hook" id="hook_select" >
						{$hookOption}
					</select>
	            </div>
				<div class="col-lg-2">
				</div>
             </div>
		</div>
    </div>
</div>
<div class="item-field form-group ">
    <label for="active" class="control-label col-lg-3">Active</label>
    <div class="col-lg-9">
        <div class="form-group">
            <div class="col-lg-10">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="active" id="active_on" {if isset($item->active)&&$item->active == 1 || !isset($item->active)}checked="checked"{/if} value="1"/>
                    <label for="active_on">Yes</label>
                    <input type="radio" name="active" id="active_off" {if isset($item->active)&&$item->active == 0}checked="checked"{/if} value="0" />
                    <label for="active_off">No</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
            <div class="col-lg-2">
			</div>	
        </div>
    </div>
</div>