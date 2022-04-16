<style type="text/css">
media (min-width: 768px)
.col-sm-offset-2 {
    margin-left: 16.66666667%;
}
.select2 {
width:100%!important;
border-radius: 0px !important;
}
.list-group-unbordered>.list-group-item {
    border-left: 0;
    border-right: 0;
    border-radius: 0;
    padding-left: 0;
    padding-right: 0;
	background-color:#f5f5f5;
	padding:9px;
}
.list-group-item {
    position: relative;
    display: block;
    padding: 10px 15px;
    margin-bottom: -1px;
    background-color: #fff;
    border: 1px solid #ddd;
}
.list-group-item:first-child {
    border: 0px;
    
}
.timeline > li > .timeline-item {margin-left:10px;}
.timeline:before {background:none; border:none;}
.margin {
    margin: 10px;
	margin-bottom:2px;
}
.timeline > li > .timeline-item > .timeline-body, .timeline > li > .timeline-item > .timeline-footer {
    padding-left: 40px;
}
a{color:#000;}
a:hover{color:#000;}
.img-thumbnail{display:inline-block; margin:3px; height:180px;}
hr {
    border-top: 1px solid #555;
	margin-top:0;
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
	.direct-chat-messages {
		-webkit-transform: translate(0, 0);
		-ms-transform: translate(0, 0);
		-o-transform: translate(0, 0);
		transform: translate(0, 0);
		padding: 10px;
		height: 300px;
		overflow: auto;
	}
	
	.direct-chat-msg, .direct-chat-text {
		display: block;
	}	
	.direct-chat-info {
		display: block;
		margin-bottom: 2px;
		font-size: 12px;
	}
	
	.direct-chat-text:before {
		border-width: 8px !important;
		margin-top: 3px;
	}
	.direct-chat-text:before {
		position: absolute;
		right: 100%;		
		
		border-right-color: #d2d6de;
		content: ' ';
		height: 0;
		width: 0;
		pointer-events: none;
	}
	
	.direct-chat-text {
		display: block;
	}
	.direct-chat-text {
		border-radius: 5px;
		position: relative;
		padding: 5px 10px;
		background: #ecf4f3;
		border: 1px solid #ecf4f3;
		margin: 5px 0 0 5px;
		color: #444;
	}
	.direct-chat-warning .right>.direct-chat-text{
		background: #ddd;
		border-color: #ddd;
		color: #000;
	}
	.time_date {
		color: #747474;		
		font-size: 12px;
		margin-left:45px;
	}
	.msg_history {
	  height: 370px;
	  overflow-y: auto;      
	}
	.media{
		background-clip: border-box;
		width:201px;
		border: 1px solid rgba(0,0,0,.125);
		background-color: #fff;
		word-wrap: break-word;
		box-shadow: 0px 1px 3px rgb(0 0 0 / 10%);
	}
	.media-body{
		-webkit-box-flex: 1;
		-ms-flex: 1 1 auto;
		flex: 1 1 auto;
		padding-left: 0.5rem;
		padding-top: 0.5rem;
	}
	.outgoing_msg {
		overflow: hidden;		
	}
	.sent_msg {
		float: right;
		
	}
	.bg-red{
		background-color: #dd4b39 !important;
	}
	.bg-green{
		background-color: #00a65a !important;
	}
</style>
<?php 
$id = !empty($task) ? (int)$task->id_task : 0;

$no_task = !empty($task->no_task) ? $task->no_task : '-';
$title_task = !empty($task->title_task) ? $task->title_task : '-';
$nama = !empty($task->nama) ? $task->nama.' '.$task->last_name : '-';
$category = !empty($task->kategori) ? $task->kategori : '-';
$region_name = !empty($task->region_name) ? $task->region_name : '-';
$city_name = !empty($task->city_name) ? $task->city_name : '-';
$pay_rate = !empty($task->pay_rate) ? number_format($task->pay_rate,0,'',',') : '-';
$deskripsi = !empty($task->deskripsi) ? $task->deskripsi : '-';
$need_applicant = !empty($task->need_applicant) ? $task->need_applicant : '-';
$jml_applicant = !empty($task->jml_applicant) ? $task->jml_applicant : '-';
$start_date = !empty($task->start_date) ? date('d-M-Y', strtotime($task->start_date)) : '-';
$end_date = !empty($task->end_date) ? date('d-M-Y', strtotime($task->end_date)) : '-';
$upload_date_bukti_pembayaran = !empty($list_apply->upload_date_bukti_pembayaran) ? date('d-M-Y H:i', strtotime($list_apply->upload_date_bukti_pembayaran)) : '-';
$applicant = !empty($list_apply->nama) ? $list_apply->nama.' '.$list_apply->last_name : '-';
$duration = !empty($task->duration) ? $task->duration.' Hari' : '-';
$fee_task = !empty($task->fee_task) ? number_format($task->fee_task,0,'',',') : '-';
$status = !empty($task->status) ? (int)$task->status : '';
$bukti_pembayaran = !empty($list_apply->bukti_pembayaran) ? $list_apply->bukti_pembayaran : '';
$status_bukti_pembayaran = !empty($list_apply->status_bukti_pembayaran) ? $list_apply->status_bukti_pembayaran : '';
$id_apply = (int)$list_apply->id > 0 ? (int)$list_apply->id : '';
$status_name = '';
if($status <= 2) $status_name = 'On Going';
if($status == 3) $status_name = 'Completed';
if($status >= 4) $status_name = 'Cancelled';
?>
<div class="modal fade" role="dialog" id="confirm_del">
          <div class="modal-dialog" style="width:380px">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><strong>Confirmation</strong></h4>
              </div>
			 
              <div class="modal-body">
				<h4 class="text-center">Apakah anda yakin ? </h4>
				<input type="hidden" id="del_id" value="<?php echo $id_apply;?>">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>               
                <button type="button" class="btn btn-warning yes_fail">Failed</button>               
                <button type="button" class="btn btn-success yes_success">Success</button>               
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
</div>
<br/>
<div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#activity" data-toggle="tab" aria-expanded="true">Main</a></li>
              
              <li class=""><a href="#gambar" data-toggle="tab" aria-expanded="false">Image</a></li>
             
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="activity">
                <!-- Post -->
				<div class='alert alert-info alert-dismissable' id="success-alert">
   
					<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
					<div id="id_text"><b>Welcome</b></div>
				</div>
				<div class="row">
				
			  <div class="col-md-6">
			  
                <ul class="list-group list-group-unbordered">
					<li class="list-group-item">
					  <b>Task Number</b> <a class="pull-right"><?php echo $no_task;?></a>
					</li>
					<li class="list-group-item">
					  <b>Employer</b> <a class="pull-right"><?php echo $nama;?></a>
					</li>
					<li class="list-group-item">
					  <b>Title</b> <a class="pull-right"><?php echo $title_task;?></a>
					</li>
					<li class="list-group-item">
					  <b>Category</b> <a class="pull-right"><?php echo $category;?></a>
					</li>
					<li class="list-group-item">
					  <b>Region</b> <a class="pull-right"><?php echo $region_name;?></a>
					</li>
					<li class="list-group-item">
					  <b>City</b> <a class="pull-right"><?php echo $city_name;?></a>
					</li>
					<li class="list-group-item">
					  <b>Pay Rate</b> <a class="pull-right"><?php echo $pay_rate;?></a>
					</li>
					<li class="list-group-item">
					  <b>Tgl. Upload Bukti Pembayaran</b> <a class="pull-right"><?php echo $upload_date_bukti_pembayaran;?></a>
					</li>
										
					
					
				</ul>
              </div>
			  <div class="col-md-6">
                <ul class="list-group list-group-unbordered">
					<li class="list-group-item">
					  <b>Status</b> <a class="pull-right"><?php echo $status_name;?></a>
					</li>
					
					<li class="list-group-item">
					  <b>Start date</b> <a class="pull-right"><?php echo $start_date;?></a>
					</li>
					<li class="list-group-item">
					  <b>End date</b> <a class="pull-right"><?php echo $end_date;?></a>
					</li>
					<li class="list-group-item">
					  <b>Duration</b> <a class="pull-right"><?php echo $duration;?></a>
					</li>
					<li class="list-group-item">
					  <b>Need Applicant</b> <a class="pull-right"><?php echo $need_applicant;?></a>
					</li>
					
					<li class="list-group-item">
					  <b>Jumlah Applicant</b> <a class="pull-right"><?php echo $jml_applicant;?></a>
					</li>
					<li class="list-group-item">
					  <b>Fee Task</b> <a class="pull-right"><?php echo $fee_task;?></a>
					</li>
					<li class="list-group-item">
					  <b>Applicant</b> <a class="pull-right"><?php echo $applicant;?></a>
					</li>
					
				</ul>
				
              </div>
			 
              </div>
			   
				<div class="row">
					<div class="col-sm-6">
						<div class="box direct-chat box-info">
							<div class="box-header with-border">
								<h3 class="box-title">Deskripsi</h3>
								
							</div>
               
							<div class="box-body">						 
							<?php echo $deskripsi;?>
							 
							</div>
					  
					   
						</div>
					</div>
					<div class="col-md-6">
						<div class="box direct-chat box-success">
							<div class="box-header with-border">
								<h3 class="box-title">History Payment</h3>
								
								<div class="box-tools pull-right">
									<?php if($status_bukti_pembayaran < 3){ ?>
										<button type="button" class="btn btn-warning btn-sm del_category">Action</button>
									<?php } 
										if($status_bukti_pembayaran == 3) echo '<span data-toggle="tooltip" title="" class="badge bg-green" data-original-title="Success">Success</span>';
										if($status_bukti_pembayaran == 4) echo '<span data-toggle="tooltip" title="" class="badge bg-red" data-original-title="Failed">Failed</span>';
									?>
								</div>
								
							</div>
               
							
							<div class="box-body msg_history" style="background-color: rgba(229, 245, 255, 0.2);">						 
								
								<div class="incoming_msg" style="width:50%;">              
								  <div class="received_msg">
									<strong>Employer</strong>
									<span class="time_date"><?php echo $upload_date_bukti_pembayaran;?></span>
									<div class="received_withd_msg">
										<div class="media">
											<div class="media-left">									   
												<img src="<?php echo $bukti_pembayaran;?>" alt="Bukti Pembayaran" class="media-object" style="width: 200px; height: 150px; border-top-left-radius: 4px; border-top-right-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,.15);">										
											</div>
											<div class="media-body">
												<p>Bukti Pembayaran</p>										
											</div>									
										</div>
									</div>
							  </div>
							</div>
							
							<?php 
								$i=0;
								if(!empty($issue_bp)){
									foreach($issue_bp as $ib){ 
										if($i > 0){
											if($ib['member'] == 'Tasker') {
									?>
									
										<div class="outgoing_msg" style="margin-top:10px;">              
										  <div class="sent_msg">
											<strong><?php echo $ib['member'];?></strong>
											<span class="time_date"><?php echo date('d-M-Y H:i', strtotime($ib['created_at']));?></span>
											<div class="received_withd_msg">
												<div class="media">
													<?php if(!empty($ib['img'])) {?>
														<div class="media-left">									   
															<img src="<?php echo $bukti_pembayaran;?>" alt="Bukti Pembayaran" class="media-object" style="width: 200px; height: 150px; border-top-left-radius: 4px; border-top-right-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,.15);">
														</div>
													<?php } ?>
													<div class="media-body">
														<p><?php echo $ib['keterangan'];?></p>									
													</div>									
												</div>
											</div>
									  </div>
									</div>
							<?php	
								}else{ ?>
									<div class="incoming_msg" style="width:50%; margin-top:10px;">              
								  <div class="received_msg">
									<strong><?php echo $ib['member'];?></strong>
									<span class="time_date"><?php echo date('d-M-Y H:i', strtotime($ib['created_at']));?></span>
									<div class="received_withd_msg">
										<div class="media">
										<?php if(!empty($ib['img'])) {?>
											<div class="media-left">									   
												<img src="<?php echo $bukti_pembayaran;?>" alt="Bukti Pembayaran" class="media-object" style="width: 200px; height: 150px; border-top-left-radius: 4px; border-top-right-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,.15);">										
											</div>
										<?php } ?>
											<div class="media-body">
												<p><?php echo $ib['keterangan'];?></p>										
											</div>									
										</div>
									</div>
							  </div>
							</div>
							<?php	}
								}
									$i++;}
								}
							?>
							
							
							
							
									
							</div>
								
					  
					   
							</div>
						</div>
					</div>
				</div>
				
              
			 
              <!-- /.tab-pane -->

				<div class="tab-pane" id="gambar" style="min-height:383px;">
					<br/>
					
					<div class="row">
						<?php if(!empty($task_img)){
							foreach($task_img as $ti){
								echo '<div class="col-sm-2 first" style="padding-left:0; padding-right:0;">
									<a class="" href="'. base_url('uploads/task/'.$ti['image']).'" title="'.$title_task.'">
									<img class="img-thumbnail img-rounded" height="200" width="160" src="'. base_url('uploads/task/'.$ti['image']).'">
									</a>
								</div>';
							}
						
						}else{
							echo '<h3><b>Image not found</b></h3>';
						}?>
							
							
						</div>
				</div>
			  
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
<script>
$("#success-alert").hide();
$('.first').magnificPopup({
		delegate: 'a',
		type: 'image',
		tLoading: 'Loading image #%curr%...',
		mainClass: 'mfp-img-mobile',
		closeOnContentClick: true,
		closeBtnInside: false,
		fixedContentPos: true,
		gallery: {
			enabled: true,
			navigateByImgClick: true,
			preload: [0,1] // Will preload 0 - before current, and 1 after the current image
		},
		image: {
			tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
			titleSrc: function(item) {
				return item.el.attr('title');
				// return item.el.attr('title') + '<small>by Marsel Van Oosten</small>';
			}
		}
	});
$('.del_category').click(function(){	
	$('#confirm_del').modal({
		backdrop: 'static',
		keyboard: false
	});
	$("#confirm_del").modal('show');
});
$('.yes_fail').click(function(){
	var id = $('#del_id').val();
	var url = '<?php echo site_url('bukti_payment/appr_reject');?>';
	$.ajax({
		data : {id : id, status:4},
		url : url,
		type : "POST",
		success:function(response){
			$('#confirm_del').modal('hide');
			$("#id_text").html('<b>Success,</b> Data telah diupdate');
			$("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
				$("#success-alert").alert('close');
				location.reload();
			});			
		}
	});
	
});
$('.yes_success').click(function(){
	var id = $('#del_id').val();
	var url = '<?php echo site_url('bukti_payment/appr_reject');?>';
	$.ajax({
		data : {id : id, status:3},
		url : url,
		type : "POST",
		success:function(response){
			$('#confirm_del').modal('hide');
			$("#id_text").html('<b>Success,</b> Data telah diupdate');
			$("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
				$("#success-alert").alert('close');
				location.reload();
			});			
		}
	});
	
});
</script>