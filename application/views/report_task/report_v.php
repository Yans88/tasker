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

 <div class="box box-success">

<div class="box-body">
<div class='alert alert-info alert-dismissable' id="success-alert">
   
    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
    <div id="id_text"><b>Welcome</b></div>
</div>
	<table id="example88" class="table table-bordered table-striped table-hover">
		<thead><tr>
			<th style="text-align:center; width:4%">No.</th>
			<th style="text-align:center; width:8%">Tanggal</th>
			
			<th style="text-align:center; width:35%">Task</th>			
			<th style="text-align:center; width:8%">Jumlah Laporan</th>		
				
					
			<th style="text-align:center; width:8%">Action</th>		
			
		</tr>
		</thead>
		<tbody>
			<?php 
				$i =1;				
				$info = '';	
				$verify_acc = '';	
				$status = '';		
				if(!empty($report)){		
					foreach($report as $n){
						$_btn_confirm = '';
						$status = '';
						$total = '';
						$_btn_confirm = '<a href="'.site_url('report_task/view/'.$n['id_task']).'" title="View"><button class="btn btn-xs btn-warning"><i class="fa fa-eye"></i> View</button></a>';
						echo '<tr>';
						echo '<td align="center">'.$i++.'.</td>';					
						echo '<td>'.date('d-m-Y H:i',strtotime($n['created_at'])).'</td>';
						echo '<td>'.$n['title_task'].'</td>';						
						echo '<td align="right">'.number_format($n['cnt'],0,'',',').'</td>';										
						echo '<td align="center">'.$_btn_confirm.'</td>';					
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

$(function() {               
    $('#example88').dataTable({});
});


</script>
