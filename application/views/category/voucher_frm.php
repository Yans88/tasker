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
$id = !empty($vouchers) ? (int)$vouchers->id : 0;
$is_publish = !empty($vouchers->is_publish) ? 1 : 0;
?>

<div class="box box-success">

    <div class="box-body">
        <table class="table table-bordered table-reponsive">
            <form name="frm_cat" id="frm_cat" method="post" enctype="multipart/form-data" accept-charset="utf-8"
                autocomplete="off">
                <tr class="header_kolom">

                    <th style="vertical-align: middle; text-align:center"> Informasi Voucher </th>
                </tr>
                <tr>

                    <td>
                        <table class="table table-responsive">
							<tr style="vertical-align:middle;">
                                <td width="10%"><b>Kode Voucher </b> </td>
                                <td width="2%">:</td>
                                <td width="25%">
                                   <input type="hidden" value="<?php echo $id;?>" name="id">
                                    <span class="label label-danger pull-right kd_vc_error"></span>
                                    <input class="form-control" name="kd_vc" id="kd_vc"
                                        placeholder="Kode Voucher" type="text"
                                        value="<?php echo !empty($vouchers->kode_voucher) ? $vouchers->kode_voucher : '';?>">
                                </td>
                                
                                <td align="right" width="10%"><b>Tipe Voucher </b></td>
                                <td>:</td>
                                <td colspan=4>
									<span class="label label-danger pull-right tipe_error"></span>
                                    <select class="form-control" name="tipe" id="tipe" onchange="return get_type(this.value);">
										<option value="">-- Tipe Voucher --</option>
										<option value="1" <?php echo $vouchers->tipe == 1 ? ' selected' : '';?>>Potongan Coin</option>
										<option value="2" <?php echo $vouchers->tipe == 2 ? ' selected' : '';?>>Free Coin</option>
									</select>
                                </td>
								

                            </tr>
							
                            <tr style="vertical-align:middle;">
                                <td width="12%"><b>Expired Date </b> </td>
                                <td width="2%">:</td>
                                <td width="25%">
                                   
                                    <span class="label label-danger pull-right exp_date_error"></span>
                                    <input class="form-control" name="exp_date" id="exp_date"
                                        placeholder="Expired Date" type="text"
                                        value="<?php echo !empty($vouchers->expired_date) ? date('d-m-Y', strtotime($vouchers->expired_date)) : '';?>">
                                </td>
								<td align="right"><b>Potongan(%) </b></td>
                                <td>:</td>
                                <td width="15%">
									<span class="label label-danger pull-right potongan_error"></span>
                                    <input class="form-control" name="potongan" id="potongan"
                                        placeholder="Potongan(%)" type="text" disabled
                                        value="<?php echo $vouchers->nilai_potongan > 0 && $vouchers->tipe == 1 ? number_format($vouchers->nilai_potongan,0,',','.') : '';?>">
                                </td>
                                <td align="right"><b>Maks. Potongan Coin</b></td>
                                <td>:</td>
                                <td width="17%">
									<span class="label label-danger pull-right maks_potongan_error"></span>
                                    <input class="form-control" name="maks_potongan" id="maks_potongan"
                                        placeholder="Maks. Potongan Coin" type="text" disabled
                                        value="<?php echo $vouchers->maks_potongan > 0 && $vouchers->tipe == 1 ? number_format($vouchers->maks_potongan,0,',','.') : '';?>">
                                </td>


                            </tr>

                            
                            <tr>
                                <td><b>Description</b></td>
                                <td width="2%">:</td>
                                <td colspan=7>
                                    <textarea name="deskripsi" id="deskripsi" class="form-control" 
                                        rows="5"><?php echo !empty($vouchers->deskripsi) ? $vouchers->deskripsi : '';?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Image</b></td>
                                <td width="2%">:</td>
                                <td colspan=7>
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
        </table>
		
       


    </div>
	
    <div class="box-footer" style="height:50px;">
        <div class="clearfix"></div>
        <div class="pull-right">
            <a href="<?php echo site_url('vouchers');?>"> <button type="button" class="btn btn-danger"><i
                        class="glyphicon glyphicon-remove"></i> Cancel</button></a>
			<?php if($is_publish <= 0){?>
            <button type="button" class="btn btn-success btn_save"><i class="glyphicon glyphicon-ok"></i> Save</button>
			<?php } ?>
        </div>
    </div>
	
</div>
<link href="<?php echo base_url(); ?>assets/datetimepicker/jquery.datetimepicker.css" rel="stylesheet" type="text/css" />	

<script src="<?php echo base_url(); ?>assets/datetimepicker/jquery.datetimepicker.js"></script>

<script type="text/javascript">
$("#success-alert").hide();
var img = '<?php echo !empty($vouchers->img) ? base_url('uploads/vouchers/'.$vouchers->img) : base_url('uploads/no_photo.jpg');?>';
$('#exp_date').datetimepicker({
	dayOfWeekStart : 1,
	changeYear: false,
	timepicker:false,
	scrollInput:false,
	format:'d-m-Y',
	lang:'en',
	minDate:'0'
});

var id = '<?php echo (int)$vouchers->id > 0 ? (int)$vouchers->id : 0;?>';
var tipe = '<?php echo (int)$vouchers->tipe > 0 ? (int)$vouchers->tipe : 0;?>';
get_type(tipe);
function get_type(val){	
	$('#potongan').prop('disabled', true);
	$('#maks_potongan').prop('disabled', true);
	if(val == 1){
		$('#potongan').prop('disabled', false);
		$('#maks_potongan').prop('disabled', false);
	}	
}
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
    var kode_voucher = $('#kd_vc').val();
    var tipe = $('#tipe').val();
    var exp_date = $('#exp_date').val();
    var potongan = $('#potongan').val();
    
    $('.kd_vc_error').text('');
    $('.tipe_error').text('');
    $('.exp_date_error').text('');
    $('.potongan_error').text('');
    $('.min_pembelian_error').text('');
    $('.maks_potongan_error').text('');
    if (kode_voucher <= 0 || kode_voucher == '') {
        $('.kd_vc_error').text('Kode voucher harus diisi');
        return false;
    }
    if(tipe == '' || tipe <= 0){
		$('.tipe_error').text('Tipe voucher harus diisi');
        return false;
	}
	
	if (exp_date <= 0 || exp_date == '') {
        $('.exp_date_error').text('Expired date harus diisi');
        return false;
    }
	if(tipe == 2) potongan = 100
	if (potongan <= 0 || potongan == '') {
        $('.potongan_error').text('Potongan harus diisi');
        return false;
    }	


    var url = '<?php echo site_url('vouchers/chk_voucher');?>';
	$.ajax({
		data:{kode_voucher : kode_voucher,id:id},
		type:'POST',
		url : url,
		success:function(response){	
			
			if(response > 0){
				$('.kd_vc_error').text('Kode voucher sudah digunakan');
				return false;							
			}else{
				$('#maks_potongan').prop('disabled', false);
				var url2 = '<?php echo site_url('vouchers/simpan_voucher');?>';
				$('#frm_cat').attr('action', url2);
				$('#frm_cat').submit();
			}				
		}
	})
    
});



$('#maks_potongan').keyup(function(event) {
    // format number
    $(this).val(function(index, value) {
        return value
            .replace(/\D/g, "")
            .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    });
});

$('#potongan').keyup(function(event) {
	if($(this).val() > 100) $(this).val(100);
    // format number
    $(this).val(function(index, value) {		
        return value
            .replace(/\D/g, "")
            .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    });
});



</script>