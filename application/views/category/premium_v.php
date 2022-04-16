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

<div class="modal fade" role="dialog" id="confirm_del">
          <div class="modal-dialog" style="width:400px">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><strong>Confirmation</strong></h4>
              </div>
			 
              <div class="modal-body">
				<h4 class="text-center">Apakah anda yakin untuk menghapusnya ? </h4>
				<input type="hidden" id="del_id" value="">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>               
                <button type="button" class="btn btn-success yes_del">Delete</button>               
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
</div>

<div class="modal fade" role="dialog" id="frm_category">
           <div class="modal-dialog" style="width:350px">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Add</h4>
              </div>
			 
              <div class="modal-body" style="padding-bottom:2px;">
				
				<form role="form" id="frm_cat" method="post" enctype="multipart/form-data" accept-charset="utf-8" autocomplete="off">
                <!-- text input -->
				<div class="row">
				<div class="form-group">
                  <label>Jumlah Hari</label><span class="label label-danger pull-right jml_hari_error"></span>
                  <input type="text" class="form-control" name="jml_hari" id="jml_hari" value="" placeholder="Jumlah hari" autocomplete="off" />
                  <input type="hidden" value="" name="id_premium" id="id_premium">
                </div>
                <div class="form-group">
                  <label>Nominal</label><span class="label label-danger pull-right nominal_error"></span>
                  <input type="text" class="form-control" name="nominal" id="nominal" value="" placeholder="Nominal" autocomplete="off" />
                  
                </div>
                
				</div>
                
              </form>

              </div>
              <div class="modal-footer" style="margin-top:1px;">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>               
                <button type="button" class="btn btn-success yes_save">Save</button>               
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
</div>


 <div class="box box-success">
 <div class="box-header">
    <button class="btn btn-success add_category"><i class="fa fa-plus"></i> Add</button>

</div>
<div class="box-body">
<div class='alert alert-info alert-dismissable' id="success-alert">
   
    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
    <div id="id_text"><b>Welcome</b></div>
</div>
	<table id="example88" class="table table-bordered table-striped">
		<thead><tr>
			<th style="text-align:center; width:4%">No.</th>
						
			<th style="text-align:center; width:50%">Jumlah Hari</th>			
			<th style="text-align:center; width:30%">Nominal</th>			
					
			<th style="text-align:center; width:16%">Action</th>
		</tr>
		</thead>
		<tbody>
			<?php 
				$i =1;
				$view_sub = '';
				$info = '';	
				$path = '';		
				if(!empty($coin)){		
					foreach($coin as $c){	
						$view_sub = '';
						
						$info = $c['id_premium'].'Þ'.$c['jml_hari'].'Þ'.number_format($c['nominal'],0,',','.');
						echo '<tr>';
						echo '<td align="center">'.$i++.'.</td>';
						// echo '<td>'.$c['id_kategori'].'</td>';
						
						echo '<td>'.$c['jml_hari'].'</td>';
						echo '<td>'.number_format($c['nominal'],0,',','.').'</td>';
						
						
						echo '<td align="center" style="vertical-align: middle;">		
			
			<a href="#" id="'.$info.'" title="Edit" class="edit_category"><button class="btn btn-xs btn-success"><i class="fa fa-edit"></i> Edit</button></a>
			<button title="Delete" id="'.$c['id_premium'].'" class="btn btn-xs btn-danger del_category"><i class="fa fa-trash-o"></i> Delete</button>		
						</td>';
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

$('.add_category').click(function(){
	$('#frm_cat').find("input[type=text], select, input[type=hidden]").val("");
	$('#blah').attr('src', '');
	$('#frm_category').modal({
		backdrop: 'static',
		keyboard: false
	});
	$('#frm_category').modal('show');
});
$('.edit_category').click(function(){
	$('#frm_cat').find("input[type=text], select").val("");
	$('#blah').attr('src', '');
	var val = $(this).get(0).id;
	var dt = val.split('Þ');
	$('#id_premium').val(dt[0]);
	$('#jml_hari').val(dt[1]);
	$('#nominal').val(dt[2]);
	
	
	$('#frm_category').modal({
		backdrop: 'static',
		keyboard: false
	});
	$('#frm_category').modal('show');
});

$('.del_category').click(function(){
	var val = $(this).get(0).id;
	$('#del_id').val(val);
	$('#confirm_del').modal({
		backdrop: 'static',
		keyboard: false
	});
	$("#confirm_del").modal('show');
});
$('.yes_del').click(function(){
	var id = $('#del_id').val();
	var url = '<?php echo site_url('premium/del');?>';
	$.ajax({
		data : {id : id},
		url : url,
		type : "POST",
		success:function(response){
			$('#confirm_del').modal('hide');
			$("#id_text").html('<b>Success,</b> Data telah dihapus');
			$("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
				$("#success-alert").alert('close');
				location.reload();
			});			
		}
	});
	
});

$('.yes_save').click(function(){
	$('.jml_hari_error').text('');
	$('.nominal_error').text('');
	var jml_hari = $('#jml_hari').val();
	var nominal = $('#nominal').val();
	if(jml_hari == ''){
		$('.jml_hari_error').text('Required');
		return false;
	}
	var res = jml_hari.replace(".", '');
	if(res % 30 != 0){
		$('.jml_hari_error').text('Jumlah hari harus kelipatan 30');
		return false;
	}
	if(nominal == ''){
		$('.nominal_error').text('Required');
		return false;
	}
	var dt = $('#frm_cat').serialize();
	var url = '<?php echo site_url('premium/simpan_cat');?>';
	$.ajax({
		data:dt,
		type:'POST',
		url : url,
		success:function(response){
			
			if(response == 'taken'){
				$('.category_error').text('Kategori sudah digunakan');
				return false;
			}
			if(response > 0){
				$('#frm_category').modal('hide');
				$("#id_text").html('<b>Success,</b> Data telah disimpan');
				$("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
					$("#success-alert").alert('close');
					location.reload();
				});								
			}
		}
	})
	
});
$('#jml_hari').keyup(function(event) {
  
  // format number
	$(this).val(function(index, value) {
		return value
		.replace(/[^\d]/g, "")
		.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
	});
});
$('#nominal').keyup(function(event) {
  
  // format number
	$(this).val(function(index, value) {
		return value
		.replace(/[^\d]/g, "")
		.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
	});
});


$(function() {               
    $('#example88').dataTable({});
});


</script>
