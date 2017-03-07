<div id="home-category" class="panel">
    <h3><i class="icon-cogs"></i>{l s=' Setting' mod='homecategories'}
    </h3>
    <div class="panel">
        <h3><i class="icon-cogs"></i>{l s=' Hook position 1' mod='homecategories'}</h3>
        <div class="main-container">
            <form method="post" action="{$postAction|escape:'htmlall':'UTF-8'}" enctype="multipart/form-data" class="item-form defaultForm  form-horizontal">
        		<div class="well">
                    <div class="item-field form-group">
        				<label class="control-label col-lg-3 ">Category</label>
        				<div class="col-lg-9">
                            <div class="form-group">
        			            <div class="col-lg-9">
                                    <select id="HOMECATEGORY_1" class="form-control fixed-width-lg" name="HOMECATEGORY_1" >
                                        <option>--</option>
                						{$cate_option1}
                					</select>
        			            </div>
        					
                            </div>                                                            
        				</div>                  
                    </div>
                    <div class="panel-footer">
    				    <button type="submit" value="1" id="module_form_submit_btn" name="submitGlobal1" class="btn btn-default pull-right">
    						<i class="process-icon-save"></i> Save
    				    </button>
    				</div>
        		</div>
        	</form>
        </div>
    </div>
    <div class="panel">
        <h3><i class="icon-cogs"></i>{l s=' Hook position 2' mod='homecategories'}</h3>
        <div class="main-container">
            <form method="post" action="{$postAction|escape:'htmlall':'UTF-8'}" enctype="multipart/form-data" class="item-form defaultForm  form-horizontal">
        		<div class="well">
                    <div class="item-field form-group">
        				<label class="control-label col-lg-3 ">Category</label>
        				<div class="col-lg-9">
                            <div class="form-group">
        			            <div class="col-lg-9">
                                    <select id="HOMECATEGORY_2" class="form-control fixed-width-lg" name="HOMECATEGORY_2" >
                                        <option>--</option>
                						{$cate_option2}
                					</select>
        			            </div>
        						<div class="col-lg-2">
        						</div>
                            </div>                                                            
        				</div>                  
                    </div>
                    <div class="panel-footer">
    				    <button type="submit" value="1" id="module_form_submit_btn" name="submitGlobal2" class="btn btn-default pull-right">
    						<i class="process-icon-save"></i> Save
    				    </button>
    				</div>
        		</div>
        	</form>
        </div>
    </div>
</div>