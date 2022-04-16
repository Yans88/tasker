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
</style>

<?php
$tanggal = date('Y-m');
$txt_periode_arr = explode('-', $tanggal);
	if(is_array($txt_periode_arr)) {
		$txt_periode = $txt_periode_arr[1] . ' ' . $txt_periode_arr[0];
	}

?>

<br/>
 <div class="box box-success">

<div class="box-body">
<div class='alert alert-info alert-dismissable' id="success-alert">
   
    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
    <div id="id_text"><b>Welcome</b></div>
</div>
	<!-- <div class="row">
    <div class="col-xs-12"> -->
      <div class="table-responsive">
	<table id="example88" class="table table-bordered table-striped">
		<thead><tr>
			<th style="text-align:center; width:4%">No.</th>
			<th style="text-align:center; width:12%">Task Number</th>
			<th style="text-align:center; width:15%">Title</th>		
			<th style="text-align:center; width:12%">Category</th>
			<th style="text-align:center; width:12%">Duration</th>			
			<th style="text-align:center; width:10%">Employer</th>			
			<th style="text-align:center; width:10%">Applicant</th>			
			<th style="text-align:center; width:10%">Need Applicant</th>							
				
		</tr>
		</thead>
		<tbody>
			
		</tbody>
	
	</table>
</div>
</div>
<!-- </div>
</div> -->

</div>
<link href="<?php echo base_url(); ?>assets/datetimepicker/jquery.datetimepicker.css" rel="stylesheet" type="text/css" />	
<script src="<?php echo base_url(); ?>assets/datetimepicker/jquery.datetimepicker.js"></script>
<script src="<?php echo base_url(); ?>assets/theme_admin/js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/theme_admin/js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
	
<script type="text/javascript">

$("#success-alert").hide();
$("input").attr("autocomplete", "off"); 
var status = '<?php echo $status;?>';
$(function() {
	var path_role= '<?php echo site_url('task/load_data');?>';
    var dataTable = $('#example88').DataTable({
        "processing": true,
        "serverSide": true,
        "scrollX": true,           
        "ajax":{
            url :path_role, // json datasource
            type: "post",  // method  , by default get
			data :{'status' : status},
            error: function(){  // error handling
				$(".employee-grid-error").html("");
                $("#example88").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                $("#example88").css("display","none");
            }
        },
		'columnDefs': [
			
			{
				"targets": 0,
				"className": "text-center",
			}
		],
		"order": [[ 0, "ASC" ]],
        "language": {
			"lengthMenu": "Display _MENU_ Record per halaman",
            "zeroRecords": "Nothing found - sorry",
            "info": "Tampil halaman _PAGE_ dari _PAGES_",
            "infoEmpty": "No records available",
            "infoFiltered": "(filtered from _MAX_ total records)",
            "sSearch":        "Cari ",
            "oPaginate": {
				"sFirst":    "Pertama",
				"sLast":    "Terakhir",
				"sNext":    "Berikut",
				"sPrevious": "Sebelum"
            }
        }
    });
});


</script>
