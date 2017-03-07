<div class="col-sm-12">
    <div class="panel">
        <div class="panel-heading">
            {l s="List modules" mod="simplecategory"}
    		<span class="panel-heading-action">
                <a href="javascript:void(0)" onclick="showModal('modalModule')" class="list-toolbar-btn link-add" title="{l s='Add new' mod='simplecategory'}"><i class="process-icon-new"></i></a>
    		</span>
        </div>
        <div class="panel-body" style="padding:0">
            <div class="table-responsive">
                <table class="table" id="modList">
        			<thead>
        				<tr class="nodrag nodrop">
                            <th width="50" class="center">{l s="ID" mod="simplecategory"}</th>
                            <th class="">{l s="Name" mod="simplecategory"}</th>                            
                            <th class="center">{l s="Category" mod="simplecategory"}</th>
                            <th>{l s="Position" mod="simplecategory"}</th>
                            <th width="80" class="center">{l s="Type" mod="simplecategory"}</th>
                            <th >{l s="Layout" mod="simplecategory"}</th>                            
                            <th width="100" class="center">{l s="Ordering" mod="simplecategory"}</th>
                            <th width="50" class="center">{l s="Status" mod="simplecategory"}</th>                        
                            <th class="center" width="50" class="">#</th>
                        </tr>				
                    </thead>
                    <tbody>
                    	{if $items|@count >0}
	                    {foreach from=$items item=item}
	                    <tr id="mod_{$item.id}" class="list-module-item">
	                        <td class="center">{$item.id}</td>
	                        <td><a href="javascript:void(0)" class="lik-module" data-id="{$item.id}">{$item.name}</a></td>
	                        <td class="center">{SimpleCategory::getCategoryNameById($item.category_id)}</td>                
	                        <td>{$item.position_name}</td>
	                        <td class="center">{$item.type}</td>                    
	                        <td>{SimpleCategory::_getLayoutName($item.layout)}</td>
	                        <td class="pointer dragHandle center" ><div class="dragGroup"><div class="positions">{$item.ordering}</div></div></td>
	                        <td class="center">
	                            {if $item.status == '1'}
	                                <a title="Enabled" class="list-action-enable action-enabled lik-module-status" data-id="{$item.id}" data-value="{$item.status}"><i class="icon-check"></i></a>
	                            {else}
	                                <a title="Disabled" class="list-action-enable action-disabled lik-module-status" data-id="{$item.id}" data-value="{$item.status}"><i class="icon-check"></i></a>
	                            {/if}
	                        </td>
	                        <td class="center"><a href="javascript:void(0)" item-id="{$item.id}" class="lik-module-edit" title="{l s='Edit module content' mod='simplecategory'}"><i class="icon-edit"></i></a>&nbsp;<a href="javascript:void(0)" item-id="{$item.id}" class="lik-module-delete" title="{l s='Delete module content' mod='simplecategory'}"><i class="icon-trash" ></i></a></td>
	                    </tr>
	                    {/foreach}
	                    {/if} 
                    </tbody>
    	       </table>            
            </div>        
        </div> 
    </div>       
</div>
<div class="col-sm-12" id="group_of_module" style="display: none;">
    <div class="panel">
        <div class="panel-heading">
            {l s="Groups of Module" mod="flexcategory"}&nbsp;<span id="header-module-name"></span>
    		<span class="panel-heading-action">
                <a href="javascript:void(0)" onclick="showModal('groupModal')" class="list-toolbar-btn" title="{l s='Add new' mod='simplecategory'}"><i class="process-icon-new"></i></a>
    		</span>
        </div>
        <div class="panel-body" style="padding:0">
            <div class="table-responsive">
                <table class="table" id="groupList">
        			<thead>
        				<tr class="nodrag nodrop">                            
                            <th>{l s="Name" mod="simplecategory"}</th>
                            <th width="80" class="center">{l s="Type" mod="simplecategory"}</th>
                            <th width="100" class="center">{l s="Display" mod="simplecategory"}</th>
                            <th width="80" class="center">{l s="Order By" mod="simplecategory"}</th>
                            <th width="80" class="center">{l s="Order way" mod="simplecategory"}</th>
                            <th width="80" class="center">{l s="Max item" mod="simplecategory"}</th>
                            <th width="100" class="center">{l s="Ordering" mod="simplecategory"}</th>
                            <th width="50" class="center">{l s="Status" mod="simplecategory"}</th>                          
                            <th class="center" width="50" class="">#</th>
                        </tr>				
                    </thead>    
                    <tbody></tbody>    
    	       </table>            
            </div>
        </div>
    </div>
</div>

<div id="modalModule" class="modal fade" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <span class="modal-title"><i class="icon-cloud"></i>{l s=' Add or edit module' mod='simplecategory'}</span>
            </div>
            <div class="modal-body form-horizontal" id="forgotBody">                                                
                <form id="frmModule">
                    <div class="clearfix">                        
                        <div class="col-md-3">
                            <div class="list-group" id="list-group">
                                <a href="#module-info" class="list-group-item active">{l s='Module config' mod="simplecategory"}</a>
                                <a href="#module-description" class="list-group-item">{l s='Module description' mod="simplecategory"}</a>           
                                <a href="#module-banners" class="list-group-item">{l s='Module banners' mod="simplecategory"}</a>                            
                            </div>
                        </div>  
                        <div class="col-md-9">
                            <div class="tab-content">                                
                                <div id="module-info" class="tab-pane fade in active">
                                    {$moduleForm.config}                                    
                                </div>
                                <div id="module-description" class="tab-pane fade">
                                    {$moduleForm.description}                                    
                                </div>
                                <div id="module-banners" class="tab-pane fade">
                                    <div class="col-sm-12" id="form-banners">                                        
                                        {$moduleForm.banners}
                                    </div>
                                    <div class="col-lg-12" style="margin-top: 15px">
                                        <div class="col-sm-3">
                                            <button id="module-banner-uploader" class="btn btn-default" type="button"><i class="icon-upload"></i>Add new banner</button>                                                
                                        </div>
                                        <div class="col-sm-2">
                                            <select onchange="moduleChangeLanguage(this.value)" class="module-lang">{$langOptions}</select>
                                        </div>
                                    </div>
                                </div>                            
                            </div>
                        </div>    
                    </div>
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="icon-off"></i> {l s='Cancel' mod='simplecategory'}</button>
                <button type="button" class="btn btn-primary btnForgot" onclick="saveModule()"><i class="icon-save"></i> {l s='Save' mod='simplecategory'}</button>
            </div>
        </div>
    </div>
</div>
<div id="groupModal" class="modal fade" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <span class="modal-title"><i class="icon-cloud"></i>{l s=' Add or edit group' mod='simplecategory'}</span>
            </div>            
            <div class="modal-body form-horizontal" id="forgotBody">
                <form id="frmGroup">
                    <div class="clearfix">                        
                        <div class="col-md-3">
                            <div class="list-group" id="list-group-group">
                                <a href="#group-info" class="list-group-item active">{l s='Group config' mod="simplecategory"}</a>
                                <a href="#group-description" class="list-group-item">{l s='Group description' mod="simplecategory"}</a>                
                                <a href="#group-banners" class="list-group-item">{l s='Group banners' mod="simplecategory"}</a>                            
                            </div>
                        </div>  
                        <div class="col-md-9">
                            <div class="tab-content">                                
                                <div id="group-info" class="tab-pane fade in active">
                                    {$groupForm.config}
                                </div>
                                <div id="group-description" class="tab-pane fade">
                                    {$groupForm.description}                                    
                                </div>
                                <div id="group-banners" class="tab-pane fade">
                                    <div class="col-sm-12" id="form-group-banners">                                        
                                        {$groupForm.banners}
                                    </div>
                                    <div class="col-lg-12" style="margin-top: 15px">
                                        <div class="col-sm-3">
                                            <button id="group-banner-uploader" class="btn btn-default" type="button"><i class="icon-upload"></i>{l s="Add new banner" mod="simplecategory"}</button>                                                
                                        </div>
                                        <div class="col-sm-2">
                                            <select onchange="groupChangeLanguage(this.value)" class="group-lang">{$langOptions}</select>
                                        </div>
                                    </div>
                                </div>                            
                            </div>
                        </div>    
                    </div>    
                    
                </form>
            </div>
            <div class="modal-footer">                
                <button type="button" class="btn btn-primary btnForgot" onclick="saveGroup()"><i class="icon-save"></i> {l s='Save' mod='simplecategory'}</button>
            </div>
        </div>
    </div>
</div>
<div id="productsModal" class="modal fade" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog" style="width: 760px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <span class="modal-title"><i class="icon-cloud"></i>{l s=' List product of category' mod='simplecategory'}</span>
            </div>
            <div class="modal-body">
                <div class="table-responsive" >
                    <div class="clearfix">
                        <div class="form-inline text-right">
                            <input type="text" class="form-control" id="keyword" placeholder="{l s='ID or Name' mod='flexiblecustom'}" />
                            <button class="btn btn-default" type="button" onclick="loadProductsByCategory('1')"><i class="icon-search"></i> {l s='Search' mod='simplecategory'}</button>
                        </div>                        
                    </div>
                    <table class="table" id="allProductList" style="margin-top: 10px">
            			<thead>
            				<tr class="nodrag nodrop">                            
                                <th width="30" class="center">{l s='ID' mod='simplecategory'}</th>
                                <th width="50" class="center">#</th>
                                <th>{l s='Name' mod='simplecategory'}</th>
                                <th>{l s='Reference' mod='simplecategory'}</th>
                                <th class="center">{l s='Price' mod='simplecategory'}</th>
                                <th class="center">{l s='Quantity' mod='simplecategory'}</th>                                
                                <th width="30" class="">#</th>
                            </tr>				
                        </thead>    
                        <tbody></tbody>
        	       </table>
                   <div id="allProductList-pagination"></div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="icon-off"></i> {l s='Cancel' mod='flexsinglecategory'}</button>
            </div>
        </div>
    </div>
</div>
{addJsDefL name=description}{l s='Description.' mod='simplecategory' js=1}{/addJsDefL}
<script type="text/javascript">
    $(document).on('focusin', function(e) {
        if ($(e.target).closest(".mce-window").length) {
    		e.stopImmediatePropagation();
    	}
    });
    var secure_key = "{$secure_key}";
    var baseModuleUrl = "{$baseModuleUrl}";
    var currentUrl = "{$currentUrl}";
    var moduleInfoNew = "";
    var moduleBannerNew = "";
    var moduleDescriptionNew = '';
    var groupInfoNew = "";
    var groupDescriptionNew = "";
    var groupBannerNew = "";
    var moduleId = '0';
    var groupId = '0';
    var iso = "{$iso}";
    var ad = "{$ad}";
    
</script>