<h2>Create Customer</h2>
<div class="clr search_details">
    <div class="clr span12">
        <div class="span12">
            <div class="row-fluid">
                <div class="tabbable">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tabs1-pane1" data-toggle="tab">Customer Info</a></li>								
                    </ul>
                    <div class="tab-content">
                        <?php echo form_open("user/update_customer/".$customer_info['user_id'], array('id' => 'form_update_customer')); ?>
                        <div class="tab-pane active" id="tabs1-pane1">
                            <?php echo $message;?>
                            <p class="clr">										
                            <div class="clr">
                                <div class="fifty fl span6">                                   
                                    <fieldset>
                                        <legend>General Information</legend>                                            
                                        <div class="clr span10">
                                            <span class="fl">Phone</span>
                                            <span class="fr">
                                                <?php echo form_input($phone); ?>
                                            </span>			
                                        </div>
                                        <div class="clr span10">
                                            <span class="fl">First Name</span>
                                            <span class="fr">
                                                <?php echo form_input($first_name); ?>
                                            </span>			
                                        </div>
                                        <div class="clr span10">
                                            <span class="fl">Last Name</span>
                                            <span class="fr">
                                                <?php echo form_input($last_name); ?>
                                            </span>			
                                        </div>
                                        <div class="clr span10">
                                            <span class="fl">Address</span>
                                            <span class="fr">
                                                <?php echo form_input($address); ?>
                                            </span>			
                                        </div>                                                                                      
                                    </fieldset>
                                </div>
                                <div class="fifty fr span6">                                    
                                    <fieldset>
                                        <legend>Picture</legend>
                                        <div class="clr span10">
                                            <span class="clr">
                                                <input type="file" class="span2" value="Browse" />
                                            </span>			
                                        </div> 															
                                    </fieldset>                                    	
                                </div>
                            </div>
                            <div class="clr">
                                <div class="fifty fl span6">                                    
                                    <fieldset>
                                        <legend>Customer Information</legend>
                                        <div class="clr span10">
                                            <span class="fl">Card No</span>
                                            <span class="fr">
                                                <?php echo form_input($card_no); ?>
                                            </span>			
                                        </div>
                                        <div class="clr span10">
                                            <span class="fl">Institution</span>
                                            <span class="fr">
                                                <?php echo form_dropdown('institution_list', $institution_list, $selected_institution); ?> 
                                            </span>			
                                        </div>
                                        <div class="clr span10">
                                            <span class="fl">Profession</span>
                                            <span class="fr">
                                                <?php echo form_dropdown('profession_list', $profession_list, $selected_profession); ?> 
                                            </span>			
                                        </div> 
                                        <div class="clr span10">
                                            <span class="fl">
                                                <?php echo form_input($submit_update_customer); ?>
                                            </span>			
                                        </div>
                                    </fieldset>                                    		
                                </div>                                
                            </div>	
                            </p>
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