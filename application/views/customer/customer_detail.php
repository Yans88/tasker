<style>
	.profile-user-img {
		margin: 0 auto;
		width: 100px;
		padding: 3px;
		border: 3px solid #d2d6de;
	}

	.img-circle {
		border-radius: 50%;
	}
	.list-group-unbordered>.list-group-item {
		border-left: 0;
		border-right: 0;
		border-radius: 0;
		padding-left: 0;
		padding-right: 0;
	}
	.direct-chat-messages {
		-webkit-transform: translate(0, 0);
		-ms-transform: translate(0, 0);
		-o-transform: translate(0, 0);
		transform: translate(0, 0);
		padding: 10px;
		height: 230px;
		overflow: auto;
	}
	.direct-chat-text {
		border-radius: 5px;
		position: relative;
		padding: 5px 10px;
		background: #d2d6de;
		border: 1px solid #d2d6de;
		margin: 5px 0 0 5px;
		color: #444;
		display: block;
	}
	.direct-chat-warning .right>.direct-chat-text{
		background: #ddd;
		border-color: #ddd;
		color: #000;
	}
	.direct-chat-msg:before, .direct-chat-msg:after {
		content: " ";
		display: table;
	}
	:after, :before {
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
	}
	
	.direct-chat-msg:before, .direct-chat-msg:after {
		content: " ";
		display: table;
	}
	:after, :before {
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
	}
	
	.direct-chat-messages, .direct-chat-contacts {
		-webkit-transition: -webkit-transform .5s ease-in-out;
		-moz-transition: -moz-transform .5s ease-in-out;
		-o-transition: -o-transform .5s ease-in-out;
		transition: transform .5s ease-in-out;
	}
	.direct-chat-msg{
		display: block;
	}	
	.direct-chat-info {
		display: block;
		margin-bottom: 2px;
		font-size: 12px;
	}
	.direct-chat-text:before {
		position: absolute;
		right: 100%;		
		border-width: 8px !important;
		margin-top: 3px;
		border-right-color: #d2d6de;
		content: ' ';
		height: 0;
		width: 0;
		pointer-events: none;
	}
	.abt_me{
		height: 295px;
		overflow: auto;
	}
	.list-group{margin-bottom:0px;}
	.products-list {
		list-style: none;
		margin: 0;
		padding: 0;
	}
	.product-list-in-box>.item {
    -webkit-box-shadow: none;
    box-shadow: none;
    border-radius: 0;
    border-bottom: 1px solid #f4f4f4;
}
.products-list>.item {
    border-radius: 3px;
    -webkit-box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    padding: 10px 0;
    background: #fff;
}
.img_task {
    margin: 0 auto;
	margin-bottom:10px;
    padding: 3px;
    border: 1px solid #d2d6de;
}
</style>
<?php 
$id_member = (int)$customer->id_customer > 0 ? (int)$customer->id_customer : 0;
$nama = !empty($customer->nama) ? $customer->nama : '';
$nama_belakang = !empty($customer->last_name) ? $customer->last_name : '';
$nama .= ' '.$nama_belakang;
$photo = !empty($customer->photo) ? base_url('uploads/photo_cv/'.$customer->photo) : base_url('uploads/no_photo.jpg');
$_ktp = !empty($customer->photo_ktp) ? base_url('uploads/ktp_selfie/'.$customer->photo_ktp) : base_url('uploads/no_photo.jpg');
$photo_selfie = !empty($customer->photo_selfie) ? base_url('uploads/ktp_selfie/'.$customer->photo_selfie) : base_url('uploads/no_photo.jpg');
$phone = !empty($customer->phone) ? $customer->phone : '';
$email = !empty($customer->email) ? $customer->email : '';
$coin = !empty($customer->coin) ? $customer->coin : 0;
$dob = !empty($customer->dob) ? date('d-M-Y', strtotime($customer->dob)) : '';
$premium_start_date = !empty($customer->premium_start_date) ? date('d M Y', strtotime($customer->premium_start_date)) : '';
$premium_end_date = !empty($customer->premium_end_date) ? date('d M Y', strtotime($customer->premium_end_date)) : '';
$referal_code = !empty($customer->referal_code) ? $customer->referal_code : '';
$fb = !empty($customer->fb) ? $customer->fb : '';
$ig = !empty($customer->ig) ? $customer->ig : '';
$twitter = !empty($customer->twitter) ? $customer->twitter : '';
$linkedin = !empty($customer->linkedin) ? $customer->linkedin : '';
$invite = (int)$customer->invite_by ? (int)$customer->invite_by : 0;
if($customer->status > 0){
	$status = '<small class="label label-success"><strong>Active</strong></small>';
}else{
	$status = '<small class="label label-danger"><strong>Inactive</strong></small>';
}
$verify_acc = '<small class="label label-danger"><strong>Unverified</strong></small>';
if((int)$customer->verify_acc == 1){
	$verify_acc = '<small class="label label-success"><strong>Active</strong></small>';
}
$verify_email = (int)$customer->verify_email > 0 ? '<small class="label label-success"><strong>Active</strong></small>' : '<small class="label label-danger"><strong>Unverified</strong></small>';
$cv_file = !empty($customer->cv_file) ? base_url('uploads/photo_cv/'.$customer->cv_file) : '';
?>

<br/>
      <div class="row">
        <div class="col-md-3">

          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="<?php echo $photo;?>" alt="User profile picture">

              <h3 class="profile-username text-center"><?php echo $nama;?></h3>

              <p class="text-muted text-center">+<?php echo $phone;?></p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Coin</b> <a class="pull-right"><?php echo number_format($coin,0,'',',');?></a>
                </li>
               
				
                <li class="list-group-item">
                  <b>Date of Birth</b> <a class="pull-right"><?php echo $dob;?></a>
                </li>
				<li class="list-group-item">
                  <b>Referal Code</b> <a class="pull-right"><?php echo $referal_code;?></a>
                </li>
				<li class="list-group-item">
                  <b>Status</b> <a class="pull-right"><?php echo $status;?></a>
                </li>
				
				<li class="list-group-item">
                  <b>Verified user</b> <a class="pull-right"><?php echo $verify_acc;?></a>
                </li>
				
				
				
				
              </ul>

              
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

          <!-- About Me Box -->
          <div class="box box-primary abt_me">
            <div class="box-header with-border">
              <h3 class="box-title">About Me</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <strong><i class="fa fa-envelope margin-r-5"></i> Email</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $verify_email;?>

              <p class="text-muted">
                <?php echo $email;?>
              </p>
				<hr style="margin-bottom:8px;margin-top:0px;">

              <strong><i class="fa fa-calendar margin-r-5"></i> Premium date</strong>
				<p class="text-muted"><?php echo $premium_start_date.' - '.$premium_end_date;?></p>
              <hr style="margin-bottom:8px;margin-top:0px;">
				<?php if(!empty($fb)){ ?>
				<strong><i class="fa fa-facebook-square margin-r-5"></i> Facebook</strong>             
                <p class="text-muted"><?php echo $fb;?></p> 
				<hr style="margin-bottom:8px;margin-top:0px;">
				<?php } ?>
				<?php if(!empty($ig)){ ?>
				<strong><i class="fa fa-instagram margin-r-5"></i> Instagram</strong>             
                <p class="text-muted"><?php echo $ig;?></p> 
				<hr style="margin-bottom:8px;margin-top:0px;">
				<?php } ?>
				<?php if(!empty($twitter)){ ?>
				<strong><i class="fa fa-twitter margin-r-5"></i> Twitter</strong>             
                <p class="text-muted"><?php echo $twitter;?></p> 
				<hr style="margin-bottom:8px;margin-top:0px;">
				<?php } ?>
				<?php if(!empty($linkedin)){ ?>
				<strong><i class="fa fa-linkedin-square margin-r-5"></i> Linkedin</strong>             
                <p class="text-muted"><?php echo $linkedin;?></p> 
				<hr style="margin-bottom:8px;margin-top:0px;">
				<?php } ?>
				<?php if($invite > 0){ ?>
				<strong><i class="glyphicon glyphicon-link margin-r-5"></i> Invite By</strong>             
                <p class="text-muted">Malibu, California</p> 
				<hr style="margin-bottom:8px;margin-top:0px;">
				<?php } ?>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#activity" data-toggle="tab" aria-expanded="true">History</a></li>
              <li class=""><a href="#timeline" data-toggle="tab" aria-expanded="false">Task</a></li>
              <li class=""><a href="#settings" data-toggle="tab" aria-expanded="false">KTP - Photo Selfie</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="activity">
				
					<div class="box direct-chat direct-chat-warning">            
						<div class="box-header with-border">
							<h3 class="box-title">Transaksi Coin</h3>                
						</div>
						<div class="box-body">
                 
						<div class="direct-chat-messages">
						
							<?php 
							echo '<ul class="todo-list ui-sortable msg_history">';
							$date_point = '';
							if(!empty($trans_coin)){
								foreach($trans_coin as $ph){								
									$date_point = '';
									$date_point = date("d-M-Y", strtotime($ph['created_at']));
									echo '<li><span class="text" style="color:#000 !important;font-size: 12px; font-weight:normal;"><b>ID Transaksi : #'.$ph['id_trans'].'<br/>Jumlah coin : ' .number_format($ph['jml'],0,'',',').'<br/>Nominal : '.number_format($ph['nominal'],2,',','.').'<br/>Kode unik : '.$ph['kode_unik'].'<br/>Total Rp. ' .number_format($ph['total'],2,',','.').'</b></span><div class="tools" style="display:inline-block; color:#dd4b39 !important">';					
										
									echo '<span style="font-size:12px; font-weight:normal;">'.$date_point.'</span>';
									if($ph['status'] == 1) echo '<br/><small class="label label-info"><strong>New Order</strong></small>';
									if($ph['status'] == 2) echo '<br/><small class="label label-warning"><strong>Confirmed</strong></small>';
										if($ph['status'] == 3) echo '<br/><small class="label label-danger"><strong>Rejected</strong></small>';
									if($ph['status'] == 4) echo '<br/><small class="label label-success"><strong>Approved</strong></small>';	
				  
								}
							}else{
								echo '<li><span class="text" style="color:#000 !important;font-size: 12px;"><b>Not found</b></li>';
							}
							echo '</ul>';
							?>
					
							
					

						</div>
                 
						</div>
					</div>
				
              
				
					<div class="box direct-chat direct-chat-warning">            
						<div class="box-header with-border">
							<h3 class="box-title">Transaksi Premium</h3>                
						</div>
						<div class="box-body">
                 
						<div class="direct-chat-messages">
							<?php 
							echo '<ul class="todo-list ui-sortable msg_history">';
							$date_point = '';
							if(!empty($trans_premium)){
								foreach($trans_premium as $ph){								
									$date_point = '';
									$date_point = date("d-M-Y", strtotime($ph['created_at']));
									echo '<li><span class="text" style="color:#000 !important;font-size: 12px; font-weight:normal;"><b>ID Transaksi : #'.$ph['no_trans'].'<br/>Jumlah hari : ' .number_format($ph['jml'],0,'',',').'<br/>Nominal : '.number_format($ph['nominal'],2,',','.').'<br/>Kode unik : '.$ph['kode_unik'].'<br/>Total Rp. ' .number_format($ph['total'],2,',','.').'</b></span><div class="tools" style="display:inline-block; color:#dd4b39 !important">';					
										
									echo '<span style="font-size:12px; font-weight:normal;">'.$date_point.'</span>';
									if($ph['status'] == 1) echo '<br/><small class="label label-info"><strong>New Order</strong></small>';
									if($ph['status'] == 2) echo '<br/><small class="label label-warning"><strong>Confirmed</strong></small>';
									if($ph['status'] == 3) echo '<br/><small class="label label-danger"><strong>Rejected</strong></small>';
									if($ph['status'] == 4) echo '<br/><small class="label label-success"><strong>Approved</strong></small>';		
									echo '</li>';
								}
							}else{
								echo '<li><span class="text" style="color:#000 !important;font-size: 12px;"><b>Not found</b></li>';
							}
							echo '</ul>';
							?>
					
					

						</div>
                 
						</div>
					</div>
				
               
                <!-- /.post -->
              </div>
              
              <!-- /.tab-pane -->
              <div class="tab-pane" id="timeline">
                <!-- The timeline -->
                <div class="box direct-chat direct-chat-warning">            
						<div class="box-header with-border">
							<h3 class="box-title">Posting Task</h3>                
						</div>
						<div class="box-body">
                 
						<div class="direct-chat-messages">
							<ul class="products-list product-list-in-box my_task">
                
               
			   
                
							</ul>
					
					

						</div>
                 
						</div>
					</div>
					
					<div class="box direct-chat direct-chat-warning">            
						<div class="box-header with-border">
							<h3 class="box-title">Apply Task</h3>                
						</div>
						<div class="box-body">
                 
						<div class="direct-chat-messages">
							<ul class="products-list product-list-in-box my_apply">
                
               
			   
                
							</ul>
					
					

						</div>
                 
						</div>
					</div>
              </div>
              <!-- /.tab-pane -->

              <div class="tab-pane" id="settings">
                <div class="row margin-bottom">
					<div class="col-sm-6">
						<p><strong>KTP</strong></p>
						<img class="img-responsive img_task" src="<?php echo $_ktp;?>" alt="KTP">
                    </div>
					<div class="col-sm-6">
						<p><strong>Photo Selfie</strong></p>
						<img class="img-responsive img_task" src="<?php echo $photo_selfie;?>" alt="Photo Selfie">
                    </div>
					 <!--
					<br/>
                   <div class="col-sm-12">
					<?php if(!empty($cv_file)) { ?>
					<iframe src="https://docs.google.com/gview?url='<?php echo $cv_file;?>'&embedded=true" style="width:700px; height:400px;" frameborder="0"></iframe>
                  </div>
                    <?php } else{
						echo '<h3><strong>CV File Not Found</strong></h3>';
					}?>
                    <!-- /.col -->
                  </div>
              </div>
			  <div class="box-footer" style="height:35px;">
	<div class="clearfix"></div>
	<div class="pull-right">
		<button type="button" class="btn btn-danger back"><i class="glyphicon glyphicon-arrow-left"></i> Back</button>	
			
	</div>
</div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
			
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
<script>
$('.back').click(function(){
	history.back();
});
my_task();
my_apply();
function my_task(){
	var id_member = '<?php echo $id_member;?>';
	var url = '<?php echo site_url('customer/my_task');?>';
	$('#list_items').html('');
	$.ajax({
		data:{'id_member' : id_member},
		type:'POST',
		url : url,
		success:function(response){
			
			$('.my_task').html(response);
		}
	})	
}
function my_apply(){
	var id_member = '<?php echo $id_member;?>';
	var url = '<?php echo site_url('customer/my_apply');?>';
	$('#list_items').html('');
	$.ajax({
		data:{'id_member' : id_member},
		type:'POST',
		url : url,
		success:function(response){
			
			$('.my_apply').html(response);
		}
	})	
}
</script>   