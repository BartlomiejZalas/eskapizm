<input type="hidden" id="ajaxUrl" value="{$postAction}" />
<div class="panel">
    <h3><i class="icon-cog"></i>{l s=' Parallax Block configuration' mod='ovicparallaxblock'}</h3>
    <div class="main-container clearfix">
        <form method="post" action="{$postAction|escape:'htmlall':'UTF-8'}" enctype="multipart/form-data" class="item-form defaultForm  form-horizontal">
            <div class="row">
                <div class="col-sm-3">
                    <div class="panel">
                        <h3><i class="icon-cog"></i>{l s=' Hook position' mod='ovicparallaxblock'}</h3>
                        <div class="main-container">
                            {foreach from=$hookArr item=position name=hookArr}
                                <div class="input-group">
                                  <span class="input-group-addon">
                                    <input id="position_{$smarty.foreach.hookArr.index}" class="select_position"{if $default_position == $smarty.foreach.hookArr.index} checked="checked"{/if} type="radio" name="id_position" value="{$smarty.foreach.hookArr.index}" />
                                  </span>
                                  <label class="form-control" for="position_{$smarty.foreach.hookArr.index}">{$position}</label>
                                </div><!-- /input-group -->
                                <br />
                            {/foreach}
                        </div>
                    </div>
                </div>
                <div class="col-sm-9">
                    <div class="panel">
                        <h3><i class="icon-cog"></i>{l s=' Parallax content' mod='ovicparallaxblock'}</h3>
                        <div id="form_content" class="main-container">
                            {$parallax_form}
                        </div>
                    </div>
                </div>
            </div>
             <div class="panel-footer">
			    <button type="submit" value="1" id="module_form_submit_btn" name="submitParallax" class="btn btn-default pull-right">
					<i class="process-icon-save"></i> Save
			    </button>
			</div>
        </form>
    </div>
</div>