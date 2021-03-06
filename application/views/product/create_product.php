<h2>Create Product</h2>
<div class="clr search_details">
    <div class="clr span12">
        <div class="span12">
            <div class="row-fluid">
                <div class="tabbable">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tabs1-pane1" data-toggle="tab">Product Info</a></li>
                    </ul>
                    <div class="tab-content">
                        <?php echo form_open("product/create_product", array('id' => 'form_create_product')); ?>
                        <div class="tab-pane active" id="tabs1-pane1">
                            <?php echo $message;?>
                            <p class="clr">										
                            <div class="span5">
                                <fieldset>
                                    <legend>Basic</legend>
                                    <div class="clr span10">
                                        <span class="fl">Product Name</span>
                                        <span class="fr">
                                            <?php echo form_input($name); ?>
                                        </span>			
                                    </div>
                                    <div class="clr span10">
                                        <span class="fl">Product Code</span>
                                        <span class="fr">
                                            <?php echo form_input($code); ?>
                                        </span>			
                                    </div>
                                    <div class="clr span10">
                                        <span class="fl">Product Size</span>
                                        <span class="fr">
                                            <?php echo form_input($size); ?>
                                        </span>			
                                    </div>
                                    <div class="clr span10">
                                        <span class="fl">Product Weight</span>
                                        <span class="fr">
                                            <?php echo form_input($weight); ?>
                                        </span>			
                                    </div>
                                    <div class="clr span10">
                                        <span class="fl">Product Warranty</span>
                                        <span class="fr">
                                            <?php echo form_input($warranty); ?>
                                        </span>			
                                    </div>	
                                    <div class="clr span10">
                                        <span class="fl">Product Quality</span>
                                        <span class="fr">
                                            <?php echo form_input($quality); ?>
                                        </span>			
                                    </div>	
                                    <div class="clr span10">
                                        <span class="fl">Brand Name</span>
                                        <span class="fr">
                                            <?php echo form_input($brand_name); ?>
                                        </span>			
                                    </div>	
                                </fieldset>                                		
                            </div>
                            <div class="span5">                                
                                <fieldset>
                                    <legend>Picture</legend>
                                    <div class="clr span10">
                                        <span class="clr">
                                            <input type="file" class="span2" value="Browse" />
                                        </span>			
                                    </div>                                        
                                </fieldset>                                	
                            </div>
                            <p class="clr"></p>											
                            </p>
                            <p class="clr">
                            <div class="span5">                                
                                <fieldset>
                                    <legend>Sales Info</legend>
                                    <div class="clr span10">
                                        <span class="fl">Unit Price</span>
                                        <span class="fr">
                                            <?php echo form_input($unit_price); ?>
                                        </span>			
                                    </div> 
                                    <div class="clr span10">
                                        <span class="fl"></span>
                                        <span class="fr">
                                            <?php echo form_submit($submit_create_product); ?>
                                        </span>			
                                    </div>                                        
                                </fieldset>                               	
                            </div>
                            <div class="span5">
                                <fieldset>
                                    <legend>Others</legend>
                                    <div class="clr span10">
                                        <span class="fl">Remarks</span>
                                        <span class="fr">
                                            <?php echo form_input($remarks); ?>
                                        </span>			
                                    </div>                                        	
                                </fieldset>                                	
                            </div>
                        </div> 
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
            <hr>
        </div>
    </div>				
    <p class="clr">&nbsp;</p>
</div>
