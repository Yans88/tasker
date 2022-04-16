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
    -webkit-box-shadow: none;
    box-shadow: none;
    padding: 10px;
	padding-bottom:15px;
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
$nama = !empty($customer->nama) ? $customer->nama : '';
$nama_belakang = !empty($customer->last_name) ? $customer->last_name : '';
$nama .= ' '.$nama_belakang;
$photo = !empty($customer->photo) ? base_url('uploads/photo_cv/'.$customer->photo) : base_url('uploads/no_photo.jpg');
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
if($customer->status == 1){
	$status = '<small class="label label-info"><strong>Available</strong></small>';
}
if($customer->status == 2){
	$status = '<small class="label label-warning"><strong>On Progress</strong></small>';
}
if($customer->status == 3){
	$status = '<small class="label label-success"><strong>Completed</strong></small>';
}
if($customer->status == 6){
	$status = '<small class="label label-danger"><strong>Banned</strong></small>';
}

?>
<div class="modal fade" role="dialog" id="confirm_del">
          <div class="modal-dialog" style="width:300px">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><strong>Confirmation</strong></h4>
              </div>
			 
              <div class="modal-body">
				<h4 class="text-center text_warning">Apakah anda yakin ? </h4>
				<input type="hidden" id="memo" value="">
				<input type="hidden" id="del_id" value="">
				<input type="hidden" id="status" value="">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>               
                <button type="button" class="btn btn-success yes_appr">Yes</button>               
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
</div>
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
                  <b>Category</b> <a class="pull-right"><?php echo $task->kategori;?></a>
                </li>
				<li class="list-group-item">
                  <b>Need Applicant</b> <a class="pull-right"><?php echo number_format($task->need_applicant,0,'',',');?></a>
                </li>
				
				<li class="list-group-item">
                  <b>Region</b> <a class="pull-right"><?php echo $task->region_name;?></a>
                </li>
				
				<li class="list-group-item">
                  <b>City</b> <a class="pull-right"><?php echo $task->city_name;?></a>
                </li>
				<li class="list-group-item">
                  <b>Pay Rate</b> <a class="pull-right"><?php echo number_format($task->pay_rate,0,'',',');?></a>
                </li>
				
                
				<li class="list-group-item">
                  <b>Start Date</b> <a class="pull-right"><?php echo date('d M Y', strtotime($task->start_date));?></a>
                </li>
				<li class="list-group-item">
                  <b>End Date</b> <a class="pull-right"><?php echo date('d M Y', strtotime($task->end_date));?></a>
                </li>
				
				<li class="list-group-item">
                  <b>Status</b> <a class="pull-right"><?php echo $status;?></a>
                </li>
						
              </ul>

              
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

        
      
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#activity" data-toggle="tab" aria-expanded="true">Task</a></li>
              <li class=""><a href="#timeline" data-toggle="tab" aria-expanded="false">Image Task</a></li>
              
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="activity">               
                    
                        <span class="username">
							<h3 style="margin-top:5px; margin-bottom:10px;"><strong><?php echo $task->title_task;?></strong></h3>
                          
                        </span>
                    
                 
                  <p>
                    <?php echo $task->deskripsi;?>
                  </p>
                  <ul class="list-inline">
                    <li style="font-size:11px; color:#585858;">Posting Date : <?php echo date('d M Y', strtotime($task->created_at));?></li>
                    
                    
                  </ul>

                  
               
				<br/>
              
				
					<div class="box direct-chat direct-chat-warning">            
						<div class="box-header with-border">
							<h3 class="box-title">List Member</h3>                
						</div>
						<div class="box-body">
						<div class="direct-chat-messages">
							<ul class="products-list product-list-in-box">
								<?php if(!empty($member_report)){
									foreach($member_report as $mr){
										echo '<li class="item">
								  <div class="product-img"><strong>
									'.$mr['nama'].'</strong>
									<span class="pull-right" style="font-size:12px; color:#585858;">'.date('d M Y', strtotime($mr['created_at'])).'</span>
								  </div>
								  <div class="product-info">						
									 
									<span class="product-description">
										'.$mr['reason'].'
									</span>';
									if($mr['status'] == 1) { 
										echo '<button onclick="return reject('.$mr['id'].');" style="margin-left:6px; margin-top:8px;" type="button" class="btn btn-danger btn-xs pull-right btn_rej'.$mr['id'].'">Reject</button>
									  <button onclick="return approve('.$mr['id'].');" style="margin-left:6px; margin-top:8px;" type="button" class="btn btn-success btn-xs pull-right btn_appr'.$mr['id'].'">Approve</button>';
									}
									if($mr['status'] == 2) { 
										echo '<span class="label label-success pull-right">Approved</span>';									 
									}
									if($mr['status'] == 3) { 
										echo '<span class="label label-danger pull-right">Rejected</span>';	
									}
								echo '</div></li>';
									}
								}?>
								
								
							</ul>
						</div>
						</div>
				
               
                <!-- /.post -->
					</div>
              </div>
			  
			  <div class="tab-pane" id="timeline">
                <!-- The timeline -->
               
					
					<div class="row margin-bottom">
					<?php 
						if(!empty($task_img)){
							foreach($task_img as $tm){
								echo ' <div class="col-sm-4">
                      <img class="img-responsive img_task" src="'.base_url('uploads/task/'.$tm['image']).'" alt="Image Task">
                    </div>';
							}
						}else{
							echo '<h3>Image is empty</h3>';
						}
					?>
                   
					
                    <!-- /.col -->
                    
                    <!-- /.col -->
                  </div>
              </div>
              
             <br/>
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
function reject(id_category){
	$('#status').val('');	
	$("#memo").val();
	$('.text_warning').html('Apakah anda yakin ?');	
	$('#del_id').val(id_category);
	$('#status').val(3);
	$('.text_warning').html('Apakah anda yakin untuk <br/><strong>Reject</strong> report ini ? ');
	$('#confirm_del').modal({
		backdrop: 'static',
		keyboard: false
	});
	
	$("#confirm_del").modal('show');
}

function approve(id_category){
	$('#status').val('');
	$("#memo").val();
	$('.text_warning').html('Apakah anda yakin ?');	
	$('#del_id').val(id_category);
	$('#status').val(2);
	$('.text_warning').html('Apakah anda yakin untuk <br><strong>Approve</strong> report ini ? ');
	$('#confirm_del').modal({
		backdrop: 'static',
		keyboard: false
	});
	$("#confirm_del").modal('show');
}
$('.yes_appr').click(function(){
	var id = $('#del_id').val();
	var status = $('#status').val();
	var id_task = '<?php echo (int)$task->id_task;?>';
	var url = '<?php echo site_url('report_task/appr_rej');?>';	
	$.ajax({
		data : {'status' : status,'id':id,'id_task':id_task},
		url : url,
		type : "POST",
		success:function(response){
			if(status == 3){
				$('.btn_appr'+id).prop('disabled', true);
				$('.btn_rej'+id).prop('disabled', true);
				$("#confirm_del").modal('hide');
			}
			if(status == 2) location.reload();;
			
		}
	});
	
});
</script>   