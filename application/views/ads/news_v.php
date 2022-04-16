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

<div class="modal fade" role="dialog" id="confirm_publish">
          <div class="modal-dialog" style="width:400px">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><strong>Confirmation</strong></h4>
              </div>
			 
              <div class="modal-body">
				<h4 class="text-center">Apakah anda yakin untuk<br/> mempublish data ini ? </h4>
				<input type="hidden" id="publish_id" value="">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>               
                <button type="button" class="btn btn-warning yes_publish">Publish</button>               
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
</div>


 <div class="box box-success">
 <div class="box-header">
    <a href="<?php echo site_url('ads/add');?>"><button class="btn btn-success"><i class="fa fa-plus"></i> Add</button></a>
</div>
<div class="box-body">
<div class='alert alert-info alert-dismissable' id="success-alert">
   
    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
    <div id="id_text"><b>Welcome</b></div>
</div>
	<table id="example88" class="table table-bordered table-striped">
		<thead><tr>
			<th style="text-align:center; width:4%">No.</th>
            <th style="text-align:center; width:9%">Tanggal</th>
            <th style="text-align:center; width:20%">Judul</th>		
			<th style="text-align:center; width:20%">Photo</th>			
			<th style="text-align:center; width:29%">Content</th>			
			<th style="text-align:center; width:7%">Action</th>
		</tr>
		</thead>
		<tbody>
			<?php 
				$i =1;
				$view_sub = '';
				$info = '';			
				if(!empty($news)){		
					foreach($news as $n){	
						$view_sub = '';
						$info = $n['id_ads'].'Þ'.$n['judul'].'Þ'.$n['description'];
						$path = '';
						$path = !empty($n['img']) ? base_url('uploads/ads/'.$n['img']) : base_url('uploads/no_photo.jpg');
						echo '<tr>';
						echo '<td align="center">'.$i++.'.</td>';
						echo '<td>'.date('d-m-Y', strtotime($n['tgl'])).'</td>';
						echo '<td>'.$n['judul'].'</td>';
						echo '<td align="center"><img width="200" height="200" src="'.$path.'"></td>';
						echo '<td>'.$n['description'].'</td>';						
						//$view_sub = site_url('category/subcategory/'.$c['id_kategori']);
						if((int)$n['is_publish'] <= 0){
						echo '<td align="center" style="vertical-align: middle;">		
						<button class="btn btn-xs btn-warning btn_publish" id="publish'.$n['id_ads'].'" style="width:77px;"><i class="fa fa-check"></i> Publish</button>
						<a href="'.site_url('ads/add_img/'.$n['id_ads']).'" title="List Image"><button class="btn btn-xs btn-info" style="margin-top:3px;"><i class="fa fa-list"></i> List Image</button></a>
			
			<a href="'.site_url('ads/add/'.$n['id_ads']).'" title="Edit"><button class="btn btn-xs btn-success" style="margin-top:3px; width:77px;"><i class="fa fa-edit"></i> Edit</button></a>
			<button title="Delete" id="'.$n['id_ads'].'" class="btn btn-xs btn-danger del_news" style="margin-top:3px; width:77px;"><i class="fa fa-trash-o"></i> Delete</button>		
						</td>';
						}else{
							echo '<td align="center" style="vertical-align: middle;">
								Published on <br/><b>'.date('d-M-Y',strtotime($n['publish_date'])).'
								<a href="'.site_url('ads/add_img/'.$n['id_ads']).'" title="List Image"><button class="btn btn-xs btn-info" style="margin-top:3px;"><i class="fa fa-list"></i> List Image</button></a>
								<a href="'.site_url('ads/add/'.$n['id_ads']).'" title="View"><button class="btn btn-xs btn-warning" style="margin-top:3px; width:77px;"><i class="fa fa-eye"></i> View</button></a>
							</td>';
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

$('.add_news').click(function(){
	$('#frm_cat').find("input[type=text], select, textarea, input[type=hidden]").val("");
	CKEDITOR.instances['content'].setData('');
	$('#frm_category').modal({
		backdrop: 'static',
		keyboard: false
	});
	$('#frm_category').modal('show');
});
$('.edit_news').click(function(){
	$('#frm_cat').find("input[type=text], select").val("");
	var val = $(this).get(0).id;
	var dt = val.split('Þ');
	$('#id_ads').val(dt[0]);
	$('#judul').val(dt[1]);
	//$('#content').text(dt[1]);
	CKEDITOR.instances['content'].setData(dt[2]);
	$('#frm_category').modal({
		backdrop: 'static',
		keyboard: false
	});
	$('#frm_category').modal('show');
});

$('.del_news').click(function(){
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
	var url = '<?php echo site_url('ads/del');?>';
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
$('.btn_publish').click(function(){
	var val = $(this).get(0).id;
	var id = val.replace('publish','');
	$('#publish_id').val(id);
	$('#confirm_publish').modal({
		backdrop: 'static',
		keyboard: false
	});
	$("#confirm_publish").modal('show');
});
$('.yes_publish').click(function(){
	var id = $('#publish_id').val();
	var url = '<?php echo site_url('ads/publish');?>';
	$.ajax({
		data : {id : id},
		url : url,
		type : "POST",
		success:function(response){
			$('#confirm_publish').modal('hide');
			$("#id_text").html('<b>Success,</b> Data telah dipulish');
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


</script>