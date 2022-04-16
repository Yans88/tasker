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
                <h4 class="modal-title"><strong>Verifikasi Payment</strong></h4>
              </div>
			 
              <div class="modal-body">
				<form role="form" id="frm_import" method="post" enctype="multipart/form-data" accept-charset="utf-8" autocomplete="off">
				<div class="row">
				<div class="col-md-6">
				<div class="form-group">
                  <label for="pemeriksa">Confirmation date</label>
				  <input type="text" class="form-control" name="confirm_date" id="confirm_date" placeholder="Name" readonly>
                  <input type="hidden" class="form-control" name="id_trans" id="id_trans" value="">
                </div>
				<div class="form-group">
					<label for="pemeriksa">Bank</label>
					<input type="text" class="form-control" name="confirm_bank" id="confirm_bank" placeholder="Bank" readonly>		
                </div>
				<div class="form-group">
					<label for="pemeriksa">Sender dan No.Rekening</label>
					<input type="text" class="form-control" name="confirm_sender" id="confirm_sender" placeholder="Sender dan No.Rekening" readonly>		
                </div> 
				<div class="form-group">
					<label for="pemeriksa">Status</label>
					<input type="text" class="form-control" name="confirm_rek" id="confirm_rek" placeholder="No.Rekening" readonly>		
                </div> 
                </div>
				<div class="col-md-6">
				
				
				<div class="form-group">
                  <label for="pemeriksa">Photo bukti pembayaran</label><span class="label label-danger pull-right deskripsi_error"></span>
					<div class="fileupload-new thumbnail" style="width: 350px; height: 255px;">
						<img id="blah_selfie" style="width: 350px; height: 240px;" src="" alt="">
					</div>
                </div>
				
                </div>
				<div class="col-sm-12">
				<div class="form-group">
					<label for="pemeriksa">Alasan</label>
					<?php if($status == 2) { ?>
					<input type="text" class="form-control" name="alasan" id="alasan" placeholder="Alasan">
					<?php } else{ ?>
					<input type="text" class="form-control" name="alasan" id="alasan" placeholder="Alasan" readonly>
					<?php }?>				
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
   <div class="box-header">
	
	<div class="pull-right box-tools">
         <form action="" method="post" autocomplete="off" class="pull-right" id="search_report">
		 
        <label>Tanggal</label>
		
        <input type="text" name="tgl" id="tgl" value="<?php echo $tgl;?>" style="width:150px;" readonly>
        <input type="hidden" name="status" id="status" value="<?php echo $status;?>">
              
        <button type="button" id="btn_submit" class="btn btn-xs btn-success" style="height:27px;"><i class="glyphicon glyphicon-search"></i> Search</button>
		<button type="button" class="btn btn-xs btn-warning res" style="height:27px;"><i class="glyphicon glyphicon-refresh"></i> Reset</button>
        <button type="button" id="print" class="btn btn-xs btn-info" style="height:27px;"><i class="glyphicon glyphicon-share-alt"></i> Export</button>               
    </form>
    </div>
</div>
<div class="box-body">
<div class='alert alert-info alert-dismissable' id="success-alert">
   
    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
    <div id="id_text"><b>Welcome</b></div>
</div>
	<table id="example88" class="table table-bordered table-striped table-hover">
		<thead><tr>
			<th style="text-align:center; width:4%">No.</th>
			<th style="text-align:center; width:8%">Transaksi</th>
			<th style="text-align:center; width:8%">Tanggal</th>
			<th style="text-align:center; width:15%">Member</th>
			<th style="text-align:center; width:13%">Payment</th>			
			<th style="text-align:center; width:8%">Jumlah Hari</th>		
			<th style="text-align:center; width:8%">Nominal</th>		
			<th style="text-align:center; width:8%">Kode Unik</th>		
					
			<th style="text-align:center; width:10%">Total</th>		
			
		</tr>
		</thead>
		<tbody>
			<?php 
				$i =1;				
				$info = '';	
				$verify_acc = '';	
				$status = '';		
				if(!empty($transaksi)){		
					foreach($transaksi as $n){
						$_btn_confirm = '';
						$status = '';
						$total = '';
						$total = number_format($n['total'],2,'.',',');						
						$path_payment = !empty($n['confirm_img']) ? base_url('uploads/payment/'.$n['confirm_img']) : base_url('uploads/no_photo.jpg');
						$info = $n['id_trans'].'Þ'.$n['bank_name'].'Þ'.$n['confirm_bank'].'Þ'.$n['confirm_sender'].'Þ'.$n['confirm_rek'].'Þ'.date('d-m-Y H:i',strtotime($n['confirm_date'])).'Þ'.$path_payment.'Þ';
						
						if($n['status'] == 2){
							$_btn_confirm = '<br/><button title="Confirm" id="'.$info.'" class="btn btn-xs btn-block btn-warning btn_confirm"><i class="glyphicon glyphicon-question-sign"></i> Confirm </button>';
							// 
						}
						if($n['status'] == 3){
							$info .= 'Rejected by '.$n['fullname'].' on '.date('d-m-Y H:i', strtotime($n['status_date'])).'Þ'.$n['reason'];;
							$total = '<a href="#import_dialog" title="View data payment" id="'.$info.'">'.$total.'</a>';							
						}
						if($n['status'] == 4){
							$info .= 'Approved by '.$n['fullname'].' on '.date('d-m-Y H:i', strtotime($n['status_date'])).'Þ'.$n['reason'];;
							$total = '<a href="#import_dialog" title="View data payment" id="'.$info.'">'.$total.'</a>';
						}
						
						echo '<tr>';
						echo '<td align="center">'.$i++.'.</td>';
						echo '<td>'.$n['no_trans'].'</td>';
						echo '<td>'.date('d-m-Y',strtotime($n['created_at'])).'</td>';
						echo '<td>'.$n['nama'].' '.$n['last_name'].' - '.$n['phone'].'</td>';
						echo '<td>Manual Transfer - '.$n['bank_name'].'</td>';
						echo '<td>'.number_format($n['jml'],0,'',',').'</td>';				
						echo '<td>'.number_format($n['nominal'],0,'',',').'</td>';				
						echo '<td>'.number_format($n['kode_unik'],0,'',',').'</td>';				
										
						echo '<td>'.$total.''.$_btn_confirm.'</td>';				
						
						
						
						echo '</tr>';
					}
				}
			?>
		</tbody>
	
	</table>
</div>

</div>
<script src="<?php echo base_url(); ?>assets/theme_admin/js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/theme_admin/js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
<link href="<?php echo base_url(); ?>assets/daterangepicker-master/daterangepicker.css" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url(); ?>assets/daterangepicker-master/moment.min.js"></script>
<script src="<?php echo base_url(); ?>assets/daterangepicker-master/daterangepicker.js"></script>
	
<script type="text/javascript">
$("#success-alert").hide();
$("input").attr("autocomplete", "off"); 
$('.btn_rej').click(function(){
	var id_trans = $('#id_trans').val();
	var url = '<?php echo site_url('trans_premium/appr_reject');?>';
	var alasan = $('#alasan').val();
	$.ajax({
		data : {id_trans : id_trans,status:3,alasan:alasan},
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
	var id_trans = $('#id_trans').val();
	var url = '<?php echo site_url('trans_premium/appr_reject');?>';
	$.ajax({
		data : {id_trans : id_trans,status:4},
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
	$('#blah_selfie').attr('src', '');
	var val = $(this).get(0).id;
	var dt = val.split('Þ');
	$('#confirm_date').val(dt[5]);
	$('#confirm_bank').val(dt[2]);	
	$('#confirm_sender').val(dt[3]+' - '+dt[4]);
	$('#confirm_rek').val(dt[7]);	
	$('#alasan').val(dt[8]);
	$('#blah_selfie').attr('src', dt[6]);
	
	$('#import_dialog').modal({
		backdrop: 'static',
		keyboard: false
	});
   $('#import_dialog').modal('show');
});
$('.btn_confirm').click(function(){
	$('.verify_acc').show();
	$('#frm_cat').find("input[type=text], select").val("");	
	$('#blah_selfie').attr('src', '');
	var val = $(this).get(0).id;
	var dt = val.split('Þ');
	$('#id_trans').val(dt[0]);
	$('#confirm_date').val(dt[5]);
	$('#confirm_bank').val(dt[2]);	
	$('#confirm_sender').val(dt[3]+' - '+dt[4]);	
	$('#confirm_rek').val('-');	
	$('#alasan').val('');
	$('#blah_selfie').attr('src', dt[6]);
	
	$('#import_dialog').modal({
		backdrop: 'static',
		keyboard: false
	});
	$('#import_dialog').modal('show');
});
$(function() {
	$("#tgl").css({"color":"#000","border": "1px solid #cccccc"});
	
  $('input[name="tgl"]').daterangepicker({
    opens: 'left',
	autoUpdateInput: false,
	maxDate: moment().format('D/MM/Y'),
	locale: {
      format: 'D/MM/Y'
    }
  });
});
$('input[name="tgl"]').on('apply.daterangepicker', function(ev, picker) {
	$("#tgl").css({"color":"#000","border": "1px solid #cccccc"});
	
    $(this).val(picker.startDate.format('D/MM/Y') + ' - ' + picker.endDate.format('D/MM/Y'));
	
});

$('input[name="tgl"]').on('cancel.daterangepicker', function(ev, picker) {
	$("#tgl").css({"color":"#000","border": "1px solid #cccccc"});
	
    $(this).val('');
});
$(function() {               
    $('#example88').dataTable({});
});
var url = '<?php $url;?>';
$('.res').click(function(){
	window.location.href = url;
});
$("#print").click(function(){	
	var _url = '<?php echo site_url('trans_premium/export_r');?>';
	$('#search_report').attr('action', _url);
	$('#search_report').submit();
});
$("#btn_submit").click(function(){
	var tgl = $('#tgl').val();
	if(tgl == '' || tgl == 'Tanggal harus diisi'){
		$('#tgl').val('Tanggal harus diisi');
		$("#tgl").css({"color":"red","border": "1px solid red"});
		return false;
	}
	$('#search_report').attr('action', url);
	$('#search_report').submit();
});

</script>
