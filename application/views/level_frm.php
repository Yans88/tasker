<style type="text/css">
	.row * {
		box-sizing: border-box;
	}
	.kotak_judul {
		 border-bottom: 1px solid #fff; 
		 padding-bottom: 2px;
		 margin: 0;
	}
	.table > tbody > tr > td{
		vertical-align : middle;
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

	
$id_level = isset($level->id) ? $level->id : '';
$level_name = isset($level->level_name) ? $level->level_name : '';
$m_coin = isset($level->m_coin) && $level->m_coin > 0 ? 'checked' : '';
$m_premium	 = isset($level->m_premium	) && $level->m_premium	 > 0 ? 'checked' : '';
$category = isset($level->category) && $level->category > 0 ? 'checked' : '';
$faq = isset($level->faq) && $level->faq > 0 ? 'checked' : '';
$customer = isset($level->customer) && $level->customer > 0 ? 'checked' : '';
$setting = isset($level->setting) && $level->setting > 0 ? 'checked' : '';

$_level = isset($level->role) && $level->role > 0 ? 'checked' : '';
$users = isset($level->users) && $level->users > 0 ? 'checked' : '';

?>

<div class="box box-success">

<div class="box-body">	
<form name="frm_editrole" id="frm_editrole"  method="post">
	<table  class="table table-bordered table-reponsive">	
		<tr class="header_kolom">
			<th style="vertical-align: middle; text-align:left">Level</th>			
		</tr>
		<tr style="border:none;">			
			<td class="h_tengah" style="vertical-align:middle; border:none;">		
				<table class="table table-responsive">
					<tr style="vertical-align:middle; border:none;">
						<td style="border:none;" width="10%"><b> Level Name </td>
						<td style="border:none;" width="2%">:</td>
						<td style="border:none;">
						<input name="level_name" value="<?php echo $level_name;?>" type="text" style="width:99%; padding-left: 5px; height:34px;"/>
						</td>
						<input name="id_level" value="<?php echo $id_level;?>" type="hidden"/>
					</tr>
				</table>
			</td>			
		</tr>	
	</table>
	
	<table  class="table table-bordered table-reponsive">	
		<tr class="header_kolom">
			<th style="vertical-align: middle; text-align:left" colspan=2>Role(Hak akses management)</th>			
		</tr>
		<tr style="border-top:none;">			
			<td class="h_tengah" style="vertical-align:middle; border-top:none; width:50%;">		
				<table class="table table-responsive">
					<tr style="border-bottom:1px solid #ddd;">
						<td style="border-top:none; text-align:left;" width="40%"><b>Customer</b></td>					
						<td style="border-top:none;">
							<input name="customer" <?php echo $customer;?> type="checkbox" value=1 />
						</td>
					</tr>
					<tr style="border-bottom:1px solid #ddd;">
						<td style="border-top:none; text-align:left;"><b>Category</b></td>				
						<td style="border-top:none;">
							<input name="category" <?php echo $category;?> type="checkbox" value=1 />
						</td>
					</tr>
					<tr style="border-bottom:1px solid #ddd;">
						<td style="border-top:none; text-align:left;"><b>Master Date Coin</b></td>				
						<td style="border-top:none;">
							<input name="m_coin" <?php echo $m_coin;?> type="checkbox" value=1 />
						</td>
					</tr>
					<tr style="border-bottom:1px solid #ddd;">
						<td style="border-top:none; text-align:left;"><b>Master Date Premium</b></td>				
						<td style="border-top:none;">
							<input name="m_premium" <?php echo $m_premium;?> type="checkbox" value=1 />
						</td>
					</tr>
					
				</table>
			</td>			
			<td class="h_tengah" style="width:50%; border-top:none;">		
				<table class="table table-responsive">
					<tr style="border-bottom:1px solid #ddd;">
						<td style="border-top:none; text-align:left;" width="40%"><b>Level</b></td>		
						<td style="border-top:none;">
							<input name="level" <?php echo $_level;?> type="checkbox" value=1 />
						</td>
					</tr>
				
					<tr style="border-bottom:1px solid #ddd;">
						<td style="border-top:none; text-align:left;"><b>User</b></td>		
						<td style="border-top:none;">
							<input name="users" <?php echo $users;?> type="checkbox" value=1 />
						</td>
					</tr>
					<tr style="border-bottom:1px solid #ddd;">
						<td style="border-top:none; text-align:left;"><b>FAQ</b></td>		
						<td style="border-top:none;">
							<input name="faq" <?php echo $faq;?> type="checkbox" value=1 />
						</td>
					</tr>
					
					<tr style="border-bottom:1px solid #ddd;">
						<td style="border-top:none; text-align:left;"><b>Setting</b></td>		
						<td style="border-top:none;">
							<input name="setting" <?php echo $setting;?> type="checkbox" value=1 />
						</td>
					</tr>
				
					
				</table>
			</td>			
		</tr>	
	</table>
	
</form>
	

</div>
<div class="box-footer" style="height:55px;">
	<div class="clearfix"></div>
	<div class="pull-right">
		<button type="button" class="btn btn-danger canc"><i class="glyphicon glyphicon-remove"></i> Cancel</button>	
		<button type="button" class="btn btn-success save"><i class="glyphicon glyphicon-ok"></i> Save</button>		
	</div>
</div>
</div>

<script src="<?php echo base_url(); ?>assets/theme_admin/js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/theme_admin/js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
	
<script type="text/javascript">
$(".canc").click(function(){
	window.location = '<?php echo site_url('role');?>';
});
$('.save').click(function(){
	var data = $("#frm_editrole").serialize();
	console.log(data);
	var url = '<?php echo site_url('role/save');?>';
	$.ajax({
		url : url,
		type : 'POST',
		data : data,
		success:function(res){
			console.log(res);
			if(res > 0){
				window.location = '<?php echo site_url('role');?>';
			}
		}
	});
});

 
</script>
