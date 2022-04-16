<style type="text/css">
	.row * {
		box-sizing: border-box;
	}
	.kotak_judul {
		 border-bottom: 1px solid #fff; 
		 padding-bottom: 2px;
		 margin: 0;
	}
	.box-header {
		color: #444;
		display: block;
		padding: 10px;
		position: relative;
	}
	.form-control[readonly]{
		background-color: #fff;
		cursor:text;
	}
	
	.toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20px; }
	.toggle.ios .toggle-handle { border-radius: 20px; }
</style>
<?php
$tanggal = date('Y-m');
$txt_periode_arr = explode('-', $tanggal);
	if(is_array($txt_periode_arr)) {
		$txt_periode = $txt_periode_arr[1] . ' ' . $txt_periode_arr[0];
	}

?>

<div class="modal fade" role="dialog" id="import_dialog">
          <div class="modal-dialog" style="width:800px">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><strong>Verifikasi</strong></h4>
              </div>
			 
              <div class="modal-body">
				<form role="form" id="frm_import" method="post" enctype="multipart/form-data" accept-charset="utf-8" autocomplete="off">
				<div class="row">
				<div class="col-md-6">
				<div class="form-group">
                  <label for="pemeriksa">Name</label><span class="label label-danger pull-right jdl_error"></span>
				  <input type="text" class="form-control" name="ktp_name" id="ktp_name" placeholder="Name" readonly>
                  <input type="hidden" class="form-control" name="id_customer" id="id_customer" value="">
                </div>
				<div class="form-group">
                  <label for="pemeriksa">KTP</label><span class="label label-danger pull-right deskripsi_error"></span>
					<div class="fileupload-new thumbnail" style="width: 350px; height: 200px;">
						<img id="blah" style="width: 350px; height: 190px;" src="" alt="">
					</div>
                </div>
				 
                </div>
				<div class="col-md-6">
				<div class="form-group">
                  <label for="pemeriksa">KTP Number</label><span class="label label-danger pull-right link_error"></span>
				  <input type="text" class="form-control" name="ktp_number" id="ktp_number" placeholder="KTP Number" readonly>
                  
                </div>
				
				<div class="form-group">
                  <label for="pemeriksa">Photo Selfie</label><span class="label label-danger pull-right deskripsi_error"></span>
					<div class="fileupload-new thumbnail" style="width: 350px; height: 200px;">
						<img id="blah_selfie" style="width: 350px; height: 190px;" src="" alt="">
					</div>
                </div>
				
                </div>
                </div>
				
              </div>
              <div class="modal-footer verify_acc" style="margin-top:0px;">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>               
                <button type="button" class="btn btn-warning btn_rej">Reject</button>               
                <button type="button" class="btn btn-success btn_appr">Approve</button>               
              </div>
            </div>
			</form>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
</div>



 <div class="box box-success">
 
<div class="box-body">
<div class='alert alert-info alert-dismissable' id="success-alert">
   
    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
    <div id="id_text"><b>Welcome</b></div>
</div>
	<table id="example88" class="table table-bordered table-striped table-hover">
		<thead><tr>
			<th style="text-align:center; width:4%">No.</th>
			<th style="text-align:center; width:20%">Nama</th>
			<th style="text-align:center; width:15%">Phone</th>			
			<th style="text-align:center; width:20%">Email</th>		
			<th style="text-align:center; width:10%">Coin</th>		
			<th style="text-align:center; width:15%">Verify Acc</th>		
			
		</tr>
		</thead>
		<tbody>
			<?php 
				$i =1;				
				$info = '';	
				$verify_acc = '';	
				$status = '';		
				if(!empty($customer)){		
					foreach($customer as $n){
						$path_selfie = !empty($n['photo_selfie']) ? base_url('uploads/ktp_selfie/'.$n['photo_selfie']) : base_url('uploads/no_photo.jpg');
						$path_ktp = !empty($n['photo_ktp']) ? base_url('uploads/ktp_selfie/'.$n['photo_ktp']) : base_url('uploads/no_photo.jpg');
						$info = $n['id_customer'].'Þ'.$n['ktp_name'].'Þ'.$n['ktp_number'].'Þ'.$path_ktp.'Þ'.$path_selfie;
						$verify_acc = '<small class="label label-danger"><strong>Unverified</strong></small>';	
						if($n['status'] > 0){
							$status = '<small class="label label-success"><strong>Active</strong></small>';
						}else{
							$status = '<small class="label label-danger"><strong>Inactive</strong></small>';
						}
						$verify_email = '';
						// $verify_email = (int)$n['verify_email'] > 0 ? '<small class="label label-success"><strong>Verified</strong></small>' : '<small class="label label-danger"><strong>Unverified</strong></small>';
						if((int)$n['verify_acc'] == 1){
							$verify_acc = '<a href="#import_dialog" title="View data KTP" id="'.$info.'">Approved by '.$n['fullname'].' <br/>on '.date('d-m-Y H:i', strtotime($n['appr_acc_date'])).'</a>';
						}
						if((int)$n['verify_acc'] == 4){
							$verify_acc = '<a href="#import_dialog" title="View data KTP" id="'.$info.'">Rejected by '.$n['fullname'].' <br/>on '.date('d-m-Y H:i', strtotime($n['appr_acc_date'])).'</a>';
						}
						if((int)$n['verify_acc'] == 2){
							$verify_acc = '';
							$verify_acc = '<button title="View data KTP" id="'.$info.'" class="btn btn-xs btn-warning edit_category"><i class="fa fa-eye"></i> View Data</button>';
						}
						echo '<tr>';
						echo '<td align="center">'.$i++.'.</td>';
						
						echo '<td><a href="'.site_url('customer/detail/'.$n['id_customer']).'" title="View Detail">'.$n['nama'].' '.$n['last_name'].'</a><br/>'.$status.'</td>';
						echo '<td>'.$n['phone'].'</td>';
						echo '<td>'.$n['email'].' '.$verify_email.'</td>';				
						echo '<td>'.number_format($n['coin'],0,'',',').'</td>';				
						echo '<td align="center">'.$verify_acc.'</td>';				
						
						
						
						echo '</tr>';
					}
				}
			?>
		</tbody>
	
	</table>
</div>

</div>
<script src="<?php echo base_url(); ?>assets/bootstrap-toggle/js/bootstrap-toggle.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/theme_admin/js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/theme_admin/js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
	
<script type="text/javascript">
$("#success-alert").hide();
$("input").attr("autocomplete", "off"); 
$('.btn_rej').click(function(){
	var id_member = $('#id_customer').val();
	var url = '<?php echo site_url('customer/appr_reject');?>';
	$.ajax({
		data : {id_member : id_member,status:4},
		url : url,
		type : "POST",
		success:function(response){
			$('#import_dialog').modal('hide');
			$("#id_text").html('<b>Success,</b> Data has been updated');
			$("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
				$("#success-alert").alert('close');
				location.reload();
			});			
		}
	});
});
$('.btn_appr').click(function(){
	var id_member = $('#id_customer').val();
	var url = '<?php echo site_url('customer/appr_reject');?>';
	$.ajax({
		data : {id_member : id_member,status:1},
		url : url,
		type : "POST",
		success:function(response){
			$('#import_dialog').modal('hide');
			$("#id_text").html('<b>Success,</b> Data has been updated');
			$("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
				$("#success-alert").alert('close');
				location.reload();
			});			
		}
	});
});
$('a[href$="#import_dialog"]').on( "click", function() {
	$('.verify_acc').hide();
	$('#blah').attr('src', '');
	$('#blah_selfie').attr('src', '');
	var val = $(this).get(0).id;
	var dt = val.split('Þ');
	// $('#id_customer').val(dt[0]);
	$('#ktp_name').val(dt[1]);
	$('#ktp_number').val(dt[2]);	
	$('#blah').attr('src', dt[3]);
	$('#blah_selfie').attr('src', dt[4]);
	
	$('#import_dialog').modal({
		backdrop: 'static',
		keyboard: false
	});
   $('#import_dialog').modal('show');
});
$('.edit_category').click(function(){
	$('.verify_acc').show();
	$('#frm_cat').find("input[type=text], select").val("");
	$('#blah').attr('src', '');
	$('#blah_selfie').attr('src', '');
	var val = $(this).get(0).id;
	var dt = val.split('Þ');
	$('#id_customer').val(dt[0]);
	$('#ktp_name').val(dt[1]);
	$('#ktp_number').val(dt[2]);	
	$('#blah').attr('src', dt[3]);
	$('#blah_selfie').attr('src', dt[4]);
	
	$('#import_dialog').modal({
		backdrop: 'static',
		keyboard: false
	});
	$('#import_dialog').modal('show');
});

$(function() {               
    $('#example88').dataTable({});
});


</script>
