<style type="text/css">
.row * {
    box-sizing: border-box;
}

.kotak_judul {
    border-bottom: 1px solid #fff;
    padding-bottom: 2px;
    margin: 0;
}

.table>tbody>tr>td {
    vertical-align: middle;
}

.custom-file-input::-webkit-file-upload-button {
    visibility: hidden;
}

.custom-file-input::before {
    content: 'Select Photo';
    display: inline-block;
    background: -webkit-linear-gradient(top, #f9f9f9, #e3e3e3);
    border: 1px solid #999;
    border-radius: 3px;
    padding: 1px 4px;
    outline: none;
    white-space: nowrap;
    -webkit-user-select: none;
    cursor: pointer;
    text-shadow: 1px 1px #fff;
    font-weight: 700;
}

.custom-file-input:hover::before {
    color: #d3394c;
}

.custom-file-input:active::before {
    background: -webkit-linear-gradient(top, #e3e3e3, #f9f9f9);
    color: #d3394c;
}
</style>
<?php
$tanggal = date('Y-m');
$id = !empty($vouchers) ? (int)$vouchers->id_ads : 0;
$is_publish = (int)$vouchers->is_publish > 0 ? 1 : 0;
?>

<div class="box box-success">

    <div class="box-body">
        <table class="table table-bordered table-reponsive">
            <form name="frm_cat" id="frm_cat" method="post" enctype="multipart/form-data" accept-charset="utf-8"
                autocomplete="off">
                <tr class="header_kolom">

                    <th style="vertical-align: middle; text-align:center"> Informasi </th>
                </tr>
                <tr>

                    <td>
                        <table class="table table-responsive">
							<tr style="vertical-align:middle;">
                                <td width="1%"><b>Name </b> </td>
                                <td width="1%">:</td>
                                <td width="98%">
                                   <input type="hidden" value="<?php echo $id;?>" name="id">
                                    <span class="label label-danger pull-right judul_error"></span>
                                    <input class="form-control" name="judul" id="judul"
                                        placeholder="Name" type="text"
                                        value="<?php echo !empty($vouchers->judul) ? $vouchers->judul : '';?>">
                                </td>                      
								

                            </tr>
							
                            

                            
                            <tr>
                                <td><b>Description</b>
								<span class="label label-danger pull-right deskripsi_error"></span>
								</td>
                                <td width="2%">:</td>
                                <td>
									
                                    <textarea name="deskripsi" id="deskripsi" class="form-control" placeholder="Description"
                                        rows="5"><?php echo !empty($vouchers->description) ? $vouchers->description : '';?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Image</b></td>
                                <td width="2%">:</td>
                                <td>
                                    <input type="file" class="form-control custom-file-input"
                                        name="userfile" id="userfile"
                                        accept="image/*" />
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td width="2%"></td>
                                <td colspan=4>
                                    <div class="fileupload-new thumbnail"
                                        style="width: 200px; height: 160px; margin-bottom:5px;">
                                        <img id="blah" style="width: 200px; height: 150px;" src="" alt="">
                                    </div>
                                </td>
                            </tr>

                        </table>
                    </td>

                </tr>
				</form>
        </table>
		
       


    </div>
	
    <div class="box-footer" style="height:50px;">
        <div class="clearfix"></div>
        <div class="pull-right">
            <a href="<?php echo site_url('ads');?>"> <button type="button" class="btn btn-danger"><i
                        class="glyphicon glyphicon-remove"></i> Cancel</button></a>
			<?php if($is_publish <= 0){?>
            <button type="button" class="btn btn-success btn_save"><i class="glyphicon glyphicon-ok"></i> Save</button>
			<?php } ?>
        </div>
    </div>
	
</div>
<link href="<?php echo base_url(); ?>assets/datetimepicker/jquery.datetimepicker.css" rel="stylesheet" type="text/css" />	

<script src="<?php echo base_url(); ?>assets/datetimepicker/jquery.datetimepicker.js"></script>
<script src="<?php echo base_url(); ?>assets/theme_admin/js/plugins/ckeditor/ckeditor.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/theme_admin/js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
<script type="text/javascript">
$("#success-alert").hide();
var img = '<?php echo !empty($vouchers->img) ? base_url('uploads/ads/'.$vouchers->img) : base_url('uploads/no_photo.jpg');?>';

var id = '<?php echo (int)$vouchers->id_ads > 0 ? (int)$vouchers->id_ads : 0;?>';$("input").attr("autocomplete", "off"); 
 $(function (config) {
	CKEDITOR.config.allowedContent = true;
	CKEDITOR.replace('deskripsi');
});

$("#userfile").change(function() {
    $('#blah').attr('src', '');
    readURL(this);
});

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#blah').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
if (img != '') {
    $('#blah').attr('src', img);
}

$('.btn_save').click(function() {
    var judul = $('#judul').val();
   
    var deskripsi = CKEDITOR.instances['deskripsi'].getData();
	
	for ( instance in CKEDITOR.instances )
        CKEDITOR.instances[instance].updateElement();
    
    $('.judul_error').text('');
    $('.deskripsi_error').text('');
    
    if (judul <= 0 || judul == '') {
        $('.judul_error').text('Name harus diisi');
        return false;
    }
    if(deskripsi == '' || deskripsi <= 0){
		$('.deskripsi_error').text('Description harus diisi');
        return false;
	}
	
	var url = '<?php echo site_url('ads/simpan');?>';
	$('#frm_cat').attr('action', url);
	$('#frm_cat').submit();

    
    
});





</script>