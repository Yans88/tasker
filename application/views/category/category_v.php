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
          <div class="modal-dialog" style="width:380px">
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
          <div class="modal-dialog" style="width:600px">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Add Category</h4>
              </div>
			 
              <div class="modal-body" style="padding-bottom:2px;">
				
				<form role="form" id="frm_cat" method="post" enctype="multipart/form-data" accept-charset="utf-8" autocomplete="off">
                <!-- text input -->
				<div class="row">
				<div class="form-group">
                  <label>Category</label><span class="label label-danger pull-right category_error"></span>
                  <input type="text" class="form-control" name="category" id="category" value="" placeholder="Category" autocomplete="off" />
                  <input type="hidden" value="" name="id_category" id="id_category">
                  <input type="hidden" value="" name="tipe" id="tipe">
                </div>
                <div class="form-group">
                  <label>Image</label><span class="label label-danger pull-right category_error"></span>
                  <input type="file" class="form-control custom-file-input" name="userfile" id="userfile" accept="image/*" />
                 
                </div>
                <div class="form-group">
					<div class="fileupload-new thumbnail" style="width: 200px; height: 160px; margin-bottom:5px;">
						<img id="blah" style="width: 200px; height: 150px;" src="" alt="">
				
					</div>                 
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
<div class="modal fade" role="dialog" id="import_dialog">
          <div class="modal-dialog" style="width:400px">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><strong>Import</strong></h4>
              </div>
			 
              <div class="modal-body">
				<form role="form" action="<?php echo site_url('category/import');?>" id="frm_import" method="post" enctype="multipart/form-data" accept-charset="utf-8" autocomplete="off">
				<div class="row">
				 <div class="form-group">
                  <label>Pilih file (.csv or .xls)</label><span class="label label-danger pull-right"></span>
                  <input type="file" class="form-control custom-file-input" name="user_import" id="user_import" accept=".csv, .xls" required />
                 
                </div>
                </div>
				
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>               
                <button type="submit" class="btn btn-success">Import</button>               
              </div>
            </div>
			</form>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
</div>

 <div class="box box-success">
 <div class="box-header">
    <button class="btn btn-success add_category"><i class="fa fa-plus"></i> Add Category</button>

</div>
<div class="box-body">
<div class='alert alert-info alert-dismissable' id="success-alert">
   
    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
    <div id="id_text"><b>Welcome</b></div>
</div>
	<table id="example88" class="table table-bordered table-striped">
		<thead><tr>
			<th style="text-align:center; width:4%">No.</th>
						
			<th style="text-align:center; width:60%">Category</th>			
			<th style="text-align:center; width:20%">Image</th>			
			<th style="text-align:center; width:16%">Action</th>
		</tr>
		</thead>
		<tbody>
			<?php 
				$i =1;
				$view_sub = '';
				$info = '';	
				$path = '';		
				if(!empty($category)){		
					foreach($category as $c){	
						$view_sub = '';
						$path = !empty($c['img']) ? base_url('uploads/kategori/'.$c['img']) : base_url('uploads/no_photo.jpg');
						$info = $this->converter->encode($c['id_kategori']).'Þ'.$c['nama_kategori'].'Þ'.$path;
						echo '<tr>';
						echo '<td align="center">'.$i++.'.</td>';
						// echo '<td>'.$c['id_kategori'].'</td>';
						
						echo '<td>'.$c['nama_kategori'].'</td>';
						echo '<td class="first" align="center"><a class="" href="'.$path.'" title="'.$c['nama_kategori'].'"><img width="200" height="200" src="'.$path.'"></a></td>';
						echo '<td align="center" style="vertical-align: middle;">		
			
			<a href="#" id="'.$info.'" title="Edit" class="edit_category"><button class="btn btn-xs btn-success"><i class="fa fa-edit"></i> Edit</button></a>
			<button title="Delete" id="'.$c['id_kategori'].'" class="btn btn-xs btn-danger del_category"><i class="fa fa-trash-o"></i> Delete</button>		
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
	$('#blah').attr('src', '<?php echo base_url('uploads/no_photo.jpg');?>');
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
	$('#id_category').val(dt[0]);
	$('#category').val(dt[1]);
	$('#blah').attr('src', dt[2]);
	
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
	var url = '<?php echo site_url('category/del');?>';
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
	var category = $('#category').val();
	$('.category_error').text('');
	if(category <= 0 || category == '') {
		$('.category_error').text('Kategori harus diisi');
		return false;
	}
	
	var url = '<?php echo site_url('category/simpan_cat');?>';
	$('#frm_cat').attr('action', url);
	$('#frm_cat').submit();
	
});
$("#userfile").change(function(){
	$('#blah').attr('src', '<?php echo base_url('uploads/no_photo.jpg');?>');
	readURL(this);
});
function readURL(input) {
   if (input.files && input.files[0]) {
        var reader = new FileReader();            
        reader.onload = function (e) {
			if(e.target.result != '') $('#blah').attr('src', e.target.result);
        }            
        reader.readAsDataURL(input.files[0]);
    }
}
$('.btn_import').click(function(){	
	$('#import_dialog').modal({
		backdrop: 'static',
		keyboard: false
	});
	$('#import_dialog').modal('show');
});
$('.btn_export').click(function(){	
	var url = '<?php echo site_url('category/export_r');?>';
	window.location.href = url;
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
