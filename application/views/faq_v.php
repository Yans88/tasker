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
          <div class="modal-dialog" style="width:850px">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Add FAQ</h4>
              </div>
			 
              <div class="modal-body" style="padding-bottom:2px;">
				
				<form role="form" id="frm_cat" autocomplete="off">
                <!-- text input -->
				<div class="row">
                
                <div class="form-group">
                  <label>Question</label><span class="label label-danger pull-right content_error"></span>
                  <textarea class="form-control" name="question" id="question" value="" placeholder="Question" autocomplete="off"></textarea>
                  <input type="hidden" value="" name="id_faq" id="id_faq">
                </div>
                
				 <div class="form-group">
                  <label>Answer</label><span class="label label-danger pull-right content_error"></span>
                  <textarea class="form-control" name="answer" id="answer" value="" placeholder="Answer" autocomplete="off"></textarea>
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
    <a href="#"><button class="btn btn-success add_news"><i class="fa fa-plus"></i> Add FAQ</button></a>
</div>
<div class="box-body">
<div class='alert alert-info alert-dismissable' id="success-alert">
   
    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
    <div id="id_text"><b>Welcome</b></div>
</div>
	<table id="example88" class="table table-bordered table-striped">
		<thead><tr>
			<th style="text-align:center; width:4%">No.</th>
            
            <th style="text-align:center; width:40%">Question</th>		
			<th style="text-align:center; width:40%">Answer</th>			
			<th style="text-align:center; width:13%">Action</th>
		</tr>
		</thead>
		<tbody>
			<?php 
				$i =1;
				$view_sub = '';
				$info = '';			
				if(!empty($faq)){		
					foreach($faq as $f){	
						$view_sub = '';
						$info = $f['id_faq'].'Þ'.$f['question'].'Þ'.$f['answer'];
						echo '<tr>';
						echo '<td align="center">'.$i++.'.</td>';
						
						echo '<td>'.$f['question'].'</td>';
						echo '<td>'.$f['answer'].'</td>';
						//$view_sub = site_url('category/subcategory/'.$c['id_kategori']);
						echo '<td align="center" style="vertical-align: middle;">		
			
			<a href="#" id="'.$info.'" title="Edit" class="edit_news"><button class="btn btn-xs btn-success"><i class="fa fa-edit"></i> Edit</button></a>
			<button title="Delete" id="'.$f['id_faq'].'" class="btn btn-xs btn-danger del_news"><i class="fa fa-trash-o"></i> Delete</button>		
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
<script src="<?php echo base_url(); ?>assets/theme_admin/js/plugins/ckeditor/ckeditor.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/theme_admin/js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
<script type="text/javascript">
$("#success-alert").hide();
$("input").attr("autocomplete", "off"); 

$('.add_news').click(function(){
	$('#frm_cat').find("input[type=text], select, textarea, input[type=hidden]").val("");
	CKEDITOR.instances['answer'].setData('');
	CKEDITOR.instances['question'].setData('');
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
	$('#id_faq').val(dt[0]);
	CKEDITOR.instances['question'].setData(dt[1]);
	//$('#content').text(dt[1]);
	CKEDITOR.instances['answer'].setData(dt[2]);
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
	var url = '<?php echo site_url('faq/del');?>';
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
	//var content = $('#content').val();
	var answer = CKEDITOR.instances['answer'].getData();
	var question = CKEDITOR.instances['question'].getData();
	$('.content_error').text('');
	if(answer <= 0 || answer == '') {
		$('.content_error').text('Answer harus diisi');
		return false;
	}
	if(question <= 0 || question == '') {
		$('.content_error').text('Question harus diisi');
		return false;
	}
	for ( instance in CKEDITOR.instances )
        CKEDITOR.instances[instance].updateElement();
	var dt = $('#frm_cat').serialize();
	var url = '<?php echo site_url('faq/simpan');?>';
	$.ajax({
		data:dt,
		type:'POST',
		url : url,
		success:function(response){			
			if(response > 0){
				$('#frm_category').modal('hide');
				$("#id_text").html('<b>Success,</b> Data telah disimpan');
				$("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
					$("#success-alert").alert('close');
					location.reload();
				});								
			}else{
				
			}
		}
	})
});


$(function() {               
    $('#example88').dataTable({});
});

$("input").attr("autocomplete", "off"); 
 $(function (config) {
	CKEDITOR.config.allowedContent = true;
	CKEDITOR.replace('answer');
	CKEDITOR.replace('question');
});
</script>