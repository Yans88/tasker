<!-- search form -->
<a href="<?php echo site_url();?>" class="logo">
			<!-- Add the class icon to your logo image or logo icon to add the margining -->
			 <div style="text-align:center; color:#01DF3A; font-weight:600;">Selamat Datang Dihalaman Administrator Tasker</div>
		</a>
<!-- /.search form -->

<ul class="sidebar-menu">
<li class="hide <?php 
	 $menu_home_arr= array('home', '');
	 if(in_array($this->uri->segment(1), $menu_home_arr)) {echo "active";}?>">
		<a href="<?php echo base_url(); ?>home">
			<img height="20" src="<?php echo base_url().'assets/theme_admin/img/home.png'; ?>"> <span>Beranda</span>
		</a>
</li>

<li class="<?php 
	 $menu_home_arr= array('ads', '');
	 if(in_array($this->uri->segment(1), $menu_home_arr)) {echo "active";}?>">
		<a href="<?php echo base_url(); ?>ads">
			<i class="glyphicon glyphicon-stats"></i> <span>Ads</span>
		</a>
</li>

<?php if ($_CUSTOMER > 0) { ?>
<li class="<?php 
	 $menu_home_arr= array('customer', '');
	 if(in_array($this->uri->segment(1), $menu_home_arr)) {echo "active";}?>">
		<a href="<?php echo base_url(); ?>customer">
			<i class="glyphicon glyphicon-stats"></i> <span>Customer</span>
		</a>
</li>
<?php } ?>

<li  class="treeview <?php 
	 $menu_trans_arr= array('task');
	 if(in_array($this->uri->segment(1), $menu_trans_arr)) {echo "active";}?>">

	<a href="#">
		<i class="glyphicon glyphicon-stats"></i>
		<span>List Task</span>
		<i class="fa fa-angle-left pull-right"></i>
	</a>
	<ul class="treeview-menu">
	<li class="<?php if ($this->uri->segment(1) == 'task' && $this->uri->segment(2) == 'tbt'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>task/tbt"><i class="fa fa-folder-open-o"></i> To be taken </a></li>
	<li class="<?php if ($this->uri->segment(1) == 'task' && $this->uri->segment(2) == ''){ echo 'active'; } ?>"><a href="<?php echo base_url();?>task"><i class="fa fa-folder-open-o"></i> On Going </a></li>
	
	
	<li class="<?php if ($this->uri->segment(1) == 'task' && $this->uri->segment(2) == 'completed'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>task/completed"><i class="fa fa-folder-open-o"></i> Completed </a></li>
	
	<li class="<?php if ($this->uri->segment(1) == 'task' && $this->uri->segment(2) == 'cancelled'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>task/cancelled"><i class="fa fa-folder-open-o"></i> Cancelled </a></li>
	
	</ul>
</li>

<li  class="treeview <?php 
	 $menu_trans_arr= array('bukti_payment');
	 if(in_array($this->uri->segment(1), $menu_trans_arr)) {echo "active";}?>">

	<a href="#">
		<i class="glyphicon glyphicon-stats"></i>
		<span>Bukti Payment</span>
		<i class="fa fa-angle-left pull-right"></i>
	</a>
	<ul class="treeview-menu">
	<li class="<?php if ($this->uri->segment(1) == 'bukti_payment' && $this->uri->segment(2) == 'tbt'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>bukti_payment/tbt"><i class="fa fa-folder-open-o"></i> Waiting Approve </a></li>
	<li class="<?php if ($this->uri->segment(1) == 'bukti_payment' && $this->uri->segment(2) == ''){ echo 'active'; } ?>"><a href="<?php echo base_url();?>bukti_payment"><i class="fa fa-folder-open-o"></i> Complain </a></li>
	
	
	<li class="<?php if ($this->uri->segment(1) == 'bukti_payment' && $this->uri->segment(2) == 'completed'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>bukti_payment/completed"><i class="fa fa-folder-open-o"></i> Completed </a></li>
	<li class="<?php if ($this->uri->segment(1) == 'bukti_payment' && $this->uri->segment(2) == 'failed'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>bukti_payment/failed"><i class="fa fa-folder-open-o"></i> Failed </a></li>
	
	
	
	</ul>
</li>

<li  class="treeview <?php 
	 $menu_trans_arr= array('trans_coin');
	 if(in_array($this->uri->segment(1), $menu_trans_arr)) {echo "active";}?>">

	<a href="#">
		<i class="glyphicon glyphicon-stats"></i>
		<span>Transaksi Coin</span>
		<i class="fa fa-angle-left pull-right"></i>
	</a>
	<ul class="treeview-menu">
	<li class="<?php if ($this->uri->segment(1) == 'trans_coin' && $this->uri->segment(2) == ''){ echo 'active'; } ?>"><a href="<?php echo base_url();?>trans_coin"><i class="fa fa-folder-open-o"></i> Pending </a></li>
	
	<li class="<?php if ($this->uri->segment(1) == 'trans_coin' && $this->uri->segment(2) == 'confirm'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>trans_coin/confirm"><i class="fa fa-folder-open-o"></i> Confirm Payment </a></li>
	
	<li class="<?php if ($this->uri->segment(1) == 'trans_coin' && $this->uri->segment(2) == 'appr'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>trans_coin/appr"><i class="fa fa-folder-open-o"></i> Success </a></li>
	
	<li class="<?php if ($this->uri->segment(1) == 'trans_coin' && $this->uri->segment(2) == 'reject'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>trans_coin/reject"><i class="fa fa-folder-open-o"></i> Cancelled </a></li>
	
	</ul>
</li>

<li  class="treeview <?php 
	 $menu_trans_arr= array('trans_premium');
	 if(in_array($this->uri->segment(1), $menu_trans_arr)) {echo "active";}?>">

	<a href="#">
		<i class="glyphicon glyphicon-stats"></i>
		<span>Transaksi Premium</span>
		<i class="fa fa-angle-left pull-right"></i>
	</a>
	<ul class="treeview-menu">
	<li class="<?php if ($this->uri->segment(1) == 'trans_premium' && $this->uri->segment(2) == ''){ echo 'active'; } ?>"><a href="<?php echo base_url();?>trans_premium"><i class="fa fa-folder-open-o"></i> Pending </a></li>
	
	<li class="<?php if ($this->uri->segment(1) == 'trans_premium' && $this->uri->segment(2) == 'confirm'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>trans_premium/confirm"><i class="fa fa-folder-open-o"></i> Confirm Payment </a></li>
	
	<li class="<?php if ($this->uri->segment(1) == 'trans_premium' && $this->uri->segment(2) == 'appr'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>trans_premium/appr"><i class="fa fa-folder-open-o"></i> Success </a></li>
	
	<li class="<?php if ($this->uri->segment(1) == 'trans_premium' && $this->uri->segment(2) == 'reject'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>trans_premium/reject"><i class="fa fa-folder-open-o"></i> Cancelled </a></li>
	
	</ul>
</li>
<li class="<?php 
	 $menu_home_arr= array('vouchers', '');
	 if(in_array($this->uri->segment(1), $menu_home_arr)) {echo "active";}?>">
		<a href="<?php echo base_url(); ?>vouchers">
			<i class="glyphicon glyphicon-stats"></i> <span>Voucher</span>
		</a>
</li>
<li  class="treeview <?php 
	 $menu_trans_arr= array('report_task');
	 if(in_array($this->uri->segment(1), $menu_trans_arr)) {echo "active";}?>">

	<a href="#">
		<i class="glyphicon glyphicon-stats"></i>
		<span>Report Task</span>
		<i class="fa fa-angle-left pull-right"></i>
	</a>
	<ul class="treeview-menu">
	<li class="<?php if ($this->uri->segment(1) == 'report_task' && $this->uri->segment(2) == ''){ echo 'active'; } ?>"><a href="<?php echo base_url();?>report_task"><i class="fa fa-folder-open-o"></i> Need Action </a></li>
	
	<li class="<?php if ($this->uri->segment(1) == 'report_task' && $this->uri->segment(2) == 'completed'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>report_task/completed"><i class="fa fa-folder-open-o"></i> Completed </a></li>
	
	
	
	</ul>
</li>

<?php if ($_ROLE > 0 || $_USERS > 0 || $_MCOIN > 0 || $_MPREMIUM > 0 || $_CATEGORY > 0 || $_FAQ > 0 || $_SETTING > 0) { ?>
<li  class="treeview <?php 
	 $menu_trans_arr= array('setting','user','category','faq','role','coin','premium','region');
	 if(in_array($this->uri->segment(1), $menu_trans_arr)) {echo "active";}?>">

	<a href="#">
		<img height="20" src="<?php echo base_url().'assets/theme_admin/img/data.png'; ?>">
		<span>Master Data</span>
		<i class="fa fa-angle-left pull-right"></i>
	</a>
	<ul class="treeview-menu">
	<li class="<?php if ($this->uri->segment(1) == 'region'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>region"><i class="fa fa-folder-open-o"></i> Region </a></li>
	<?php if ($_CATEGORY > 0) { ?>
	<li class="<?php if ($this->uri->segment(1) == 'category'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>category"><i class="fa fa-folder-open-o"></i> Category </a></li>
	<?php } if ($_MCOIN > 0) { ?>
	<li class="<?php if ($this->uri->segment(1) == 'coin'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>coin"><i class="fa fa-folder-open-o"></i> Master Data Coin </a></li>
	<?php } if ($_MPREMIUM > 0) { ?>
	<li class="<?php if ($this->uri->segment(1) == 'premium'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>premium"><i class="fa fa-folder-open-o"></i> Master Premium </a></li>
	<?php } if ($_ROLE > 0) { ?>
	<li class="<?php if ($this->uri->segment(1) == 'role'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>role"><i class="fa fa-folder-open-o"></i> Level </a></li>
	<?php }  if ($_USERS > 0) { ?>
	<li class="<?php if ($this->uri->segment(1) == 'user'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>user"><i class="fa fa-folder-open-o"></i> User </a></li>
	<?php }  if ($_FAQ > 0) { ?>
	 <li class="<?php if ($this->uri->segment(1) == 'faq'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>faq"><i class="fa fa-folder-open-o"></i> FAQ </a></li>
	<?php }  if ($_SETTING > 0) { ?>
	<li class="<?php if ($this->uri->segment(1) == 'setting'){ echo 'active'; } ?>"><a href="<?php echo base_url();?>setting"><i class="fa fa-folder-open-o"></i> Setting </a></li>
	<?php } ?>
	</ul>
</li>
<?php } ?>


</ul>