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
<div class="modal fade" role="dialog" id="confirm_del2">
          <div class="modal-dialog" style="width:400px">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><strong>Confirmation</strong></h4>
              </div>
			 
              <div class="modal-body">
				
				<h4 class="text-center">Apakah anda yakin <br/>untuk mempublish voucher ini ? </h4>
				<i style="color:red; font-weight:bold;">- Voucher yang sudah dipublish tidak bisa di hapus dan edit</i>
				<input type="hidden" id="del_id2" value="">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>               
                <button type="button" class="btn btn-success yes_publish">Publish</button>               
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
</div>

 <div class="box box-success">
 <div class="box-header">
    <a href="<?php echo site_url('vouchers/add');?>"><button class="btn btn-success add_category"><i class="fa fa-plus"></i> Add</button></a>
</div>
<div class="box-body">
<div class='alert alert-info alert-dismissable' id="success-alert">
   
    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
    <div id="id_text"><b>Welcome</b></div>
</div>
	<table id="example88" class="table table-bordered table-striped">
		<thead><tr>
			<th style="text-align:center; width:4%">No.</th>
					
			<th style="text-align:center; width:35%">Voucher</th>			
			<th style="text-align:center; width:20%">Expired Date</th>	
			<th style="text-align:center; width:20%">Image</th>	
			<th style="text-align:center; width:15%">Action</th>
		</tr>
		</thead>
		<tbody>
			<?php 
				$i =1;
				$view_sub = '';
				$info = '';	
				$path = '';			
				if(!empty($vouchers)){		
					foreach($vouchers as $voucher){	
						$view_sub = '';
						$path = '';		
						$is_publish = '';		
						$is_publish = !empty($voucher['is_publish']) ? 1 : 0;
						$path = !empty($voucher['img']) ? base_url('uploads/vouchers/'.$voucher['img']) : base_url('uploads/no_photo.jpg');
						
						$info = $voucher['id'].'Þ'.$voucher['kode_voucher'].'Þ'.$path;
						echo '<tr>';
						echo '<td align="center">'.$i++.'.</td>';
						if($voucher['tipe'] ==1){
							echo '<td>'.$voucher['kode_voucher'].'<br/><b>Potongan : </b>'.number_format($voucher['nilai_potongan'],0,',','.').'<br/><b>Maks. Potongan : </b>'.number_format($voucher['maks_potongan'],0,',','.').'</td>'; 
						}
						if($voucher['tipe'] ==2){
							echo '<td>'.$voucher['kode_voucher'].'<br/><b>Free Coin </b>'; 
						}
						echo '<td>'.date('d-M-Y', strtotime($voucher['expired_date'])).'</td>';
						
						echo '<td class="first" align="center"><a class="" href="'.$path.'" title="'.$voucher['kode_voucher'].'"><img width="200" height="200" src="'.$path.'"></a></td>';
						if($is_publish == 0){
						echo '<td align="center" style="vertical-align: middle;">		
			<button class="btn btn-xs btn-warning btn_publish" id="p'.$voucher['id'].'" style="width:105px; margin-bottom:3px;"><i class="fa fa-check"></i> Publish</button>
			<br/><a href="'.site_url('vouchers/add/'.$voucher['id']).'" title="Edit"><button class="btn btn-xs btn-success"><i class="fa fa-edit"></i> Edit</button></a>
			<button title="Delete" id="'.$voucher['id'].'" class="btn btn-xs btn-danger del_category"><i class="fa fa-trash-o"></i> Delete</button>		
						</td>';
						}else{
							echo '<td align="center" style="vertical-align: middle;">		
							Published on <br/><b>'.date('d-M-Y',strtotime($voucher['is_publish'])).'
						</b><br/><a href="'.site_url('vouchers/add/'.$voucher['id']).'" title="View"><button class="btn btn-xs btn-warning"><i class="fa fa-eye"></i> View</button></a></td>';
						}
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
	
<script type="text/javascript">
$("#success-alert").hide();
$("input").attr("autocomplete", "off"); 
$('.btn_publish').click(function(){
	var val = $(this).get(0).id;
	var res = val.replace("p", "");
	$('#del_id2').val(res);
	$('#confirm_del2').modal({
		backdrop: 'static',
		keyboard: false
	});
	$("#confirm_del2").modal('show');
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
	var url = '<?php echo site_url('vouchers/del');?>';
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
$('.yes_publish').click(function(){
	var id = $('#del_id2').val();
	var url = '<?php echo site_url('vouchers/publish');?>';
	$.ajax({
		data : {id : id},
		url : url,
		type : "POST",
		success:function(response){
			$('#confirm_del2').modal('hide');
			$("#id_text").html('<b>Success,</b> Voucher telah dipublish');
			$("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
				$("#success-alert").alert('close');
				location.reload();
			});			
		}
	});
	
});

$(function() {               
    $('#example88').dataTable({});
});

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
</script>
