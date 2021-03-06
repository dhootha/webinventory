 <h2>Create Manager</h2>
<div class="clr search_details">
    <div class="clr span12">
        <div class="span12">
            <div class="row-fluid">
                <div class="tabbable">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tabs1-pane1" data-toggle="tab">Manager Info</a></li>								
                    </ul>
                    <div class="tab-content">
                        <?php echo form_open("user/create_manager", array('id' => 'form_create_manager')); ?>
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
                                        <div class="clr span10">
                                            <span class="fl">
                                                <?php echo form_submit($submit_create_manager); ?>
                                            </span>			
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="fifty fr span6">
                                    <form>
                                        <fieldset>
                                            <legend>Picture</legend>
											<p>Upload a photo for your profile picture.</p>
											<div class="span7 offset2">	
												<div class="pictureBox">
													<img src="https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcRzqaxb6ifkzZ0zi5iDxamjKiS7OAsz8sbPUoQPukI28DZTO-ClK24pXBen" alt="">
												</div>
											</div>
                                            <div class="clr span6">
                                                <span class="">
                                                    <input type="file" class="span2" value="Browse" />
                                                </span>			
												<span class="">
                                                    <button class="btn btn-success fr">Upload </button>
                                                </span>			
                                            </div>
											<div class="clr span9" style="margin:10px;">
												<form action="#" method="post" accept-charset="utf-8" class="form-horizontal"> 
												<div class="control-group">
													<div class="controls pull-right">
														<button class="btn btn-success fl" style="margin-right:5px">cancel </button>
														<button class="btn btn-success fr">Save </button>
													</div>
												</div>
												</form>
											</div>	
                                        </fieldset>
                                    </form>		
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