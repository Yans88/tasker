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
$duration = !empty($task->duration) ? $task->duration.' Hari' : '-';
$fee_task = !empty($task->fee_task) ? number_format($task->fee_task,0,'',',') : '-';
$status = !empty($task->status) ? (int)$task->status : '';
$status_name = '';
if($status <= 2) $status_name = 'On Going';
if($status == 3) $status_name = 'Completed';
if($status >= 4) $status_name = 'Cancelled';
?>

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
					
					
				</ul>
				
              </div>
			 
              </div>
			   
				<div class="row">
					<div class="col-sm-12">
						<div class="box direct-chat box-info">
							<div class="box-header with-border">
								<h3 class="box-title">Deskripsi</h3>
								
							</div>
               
							<div class="box-body">						 
							<?php echo $deskripsi;?>
							 
							</div>
					  
					   
						</div>
					</div>
				</div>
				<div class="row">				
					<div class="col-md-6">
						<div class="box direct-chat box-warning">
							<div class="box-header with-border">
								<h3 class="box-title">List Applicant Apply</h3>
								<div class="box-tools pull-right hide">
									<span data-toggle="tooltip" title="" class="badge bg-yellow" data-original-title="3 New Messages">3</span>
								</div>
							</div>
               
							<div class="box-body">						 
								<div class="direct-chat-messages">
									<?php 
									$i = 0;
									if(!empty($dt)){ 
										foreach($dt as $h){
											if((int)$h['status'] != 2 || (int)$h['status'] != 4 || $h['status'] != 5){
												if($i%2 == 0){
													echo '<div class="direct-chat-info clearfix"><span class="direct-chat-text"><b>'.$h['nama'].'</b><br>'.date('d-M-Y H:i', strtotime($h['created_at'])).'</span></span></div>';
												}else{
													echo '<div class="direct-chat-info clearfix"><span class="direct-chat-text" style="background:#F9F9F9; border:1px solid #F9F9F9;"><b>'.$h['nama'].'</b><br>'.date('d-M-Y H:i', strtotime($h['created_at'])).'</span></span></div>';
												}
												
												$i++;
											}
										}
									}else{
										echo '<div class="direct-chat-info clearfix"><span class="direct-chat-text"><b> Empty </b><br><span>-</span></span></div>';
									}?>
									
								</div>
							 
							</div>
					  
					   
						</div>
					</div>
					<div class="col-md-6">
						<div class="box direct-chat box-success">
							<div class="box-header with-border">
								<h3 class="box-title">List Applicant Approve</h3>
								<div class="box-tools pull-right hide">
									<span data-toggle="tooltip" title="" class="badge bg-yellow" data-original-title="3 New Messages">3</span>
								</div>
							</div>
               
							<div class="box-body">						 
								<div class="direct-chat-messages">							
									<?php 
									$i = 0;
									if(!empty($dt)){ 
										foreach($dt as $h){
											if((int)$h['status'] == 2 || (int)$h['status'] == 4 || $h['status'] == 5){
												if($i%2 == 0){
													echo '<div class="direct-chat-info clearfix"><span class="direct-chat-text"><b>'.$h['nama'].'</b><br>'.date('d-M-Y H:i', strtotime($h['created_at'])).'</span></span></div>';
												}else{
													echo '<div class="direct-chat-info clearfix"><span class="direct-chat-text" style="background:#F9F9F9; border:1px solid #F9F9F9;"><b>'.$h['nama'].'</b><br>'.date('d-M-Y H:i', strtotime($h['created_at'])).'</span></span></div>';
												}
												
												$i++;
											}
										}
									}else{
										echo '<div class="direct-chat-info clearfix"><span class="direct-chat-text"><b> Empty</b><br><span>-</span></span></div>';
									}?>
								</div>
					  
					   
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
function approves(id_category){
	$('#status').val('');	
	$('.text_warning').html('Apakah anda yakin ?');	
	$('#id_member').val(id_category);
	$('#status').val(2);
	$('.ttl_appr_rej').html('<strong>Approve request</strong>');
	$('.text_warning').html('Are you sure to approve this request ? ');
	$('.yes_appr_rej').text('Yes, Approve');
	$('#appr_rej_dialog').modal({
		backdrop: 'static',
		keyboard: false
	});
	$("#appr_rej_dialog").modal('show');
}
function rejects(id_category){
	$('#status').val('');	
	$('.text_warning').html('Apakah anda yakin ?');	
	$('#id_member').val(id_category);
	$('#status').val(3);
	$('.ttl_appr_rej').html('<strong>Reject request</strong>');
	$('.text_warning').html('Are you sure to reject this request ?');
	$('.yes_appr_rej').text('Yes, Reject');
	$('#appr_rej_dialog').modal({
		backdrop: 'static',
		keyboard: false
	});
	$("#appr_rej_dialog").modal('show');
}
$('.yes_appr_rej').click(function(){
	var id_request = $('#id_member').val();	
	var status = $('#status').val();
	var url = '<?php echo site_url('device/upd_req');?>';
	$.ajax({
		data : {id_request : id_request, status:status},
		url : url,
		type : "POST",
		success:function(response){
			$('#appr_rej_dialog').modal('hide');
			$("#id_text").html('<b>Success,</b> Data telah diupdate');
			$("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
				$("#success-alert").alert('close');
				location.reload();
				
			});			
		}
	});
	
});
</script>