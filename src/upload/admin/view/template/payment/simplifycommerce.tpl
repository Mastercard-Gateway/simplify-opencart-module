<?php echo $header; ?>
<div id="content">
 <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
	<div class="box">
	  <div class="heading">
		<h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title; ?></h1>
		<div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
	  </div>
	  <div class="content">
		<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
		  <table class="form">
			<tr>
	            <td><?php echo $entry_test; ?></td>
	            <td>
	              <?php if ($simplifycommerce_test) { ?>
	              <input type="radio" name="simplifycommerce_test" value="1" checked="checked" />
	              <?php echo $text_test; ?>
	              <input type="radio" name="simplifycommerce_test" value="0" />
	              <?php echo $text_prod; ?>
	              <?php } else { ?>
	              <input type="radio" name="simplifycommerce_test" value="1" />
	              <?php echo $text_test; ?>
	              <input type="radio" name="simplifycommerce_test" value="0" checked="checked" />
	              <?php echo $text_prod; ?>
	              <?php } ?>
	           </td>
          	</tr>
			<tr>
			  <td><?php echo $entry_livesecretkey; ?></td>
			  <td><input type="text" name="simplifycommerce_livesecretkey" value="<?php echo $simplifycommerce_livesecretkey; ?>" /></td>
			</tr>
			<tr>
			  <td><?php echo $entry_livepubkey; ?></td>
			  <td><input type="text" name="simplifycommerce_livepubkey" value="<?php echo $simplifycommerce_livepubkey; ?>" /></td>
			</tr>
			<tr>
			  <td><span class="required">*</span> <?php echo $entry_testsecretkey; ?></td>
			  <td><input type="text" name="simplifycommerce_testsecretkey" value="<?php echo $simplifycommerce_testsecretkey; ?>" />
				<?php if ($error_testsecretkey) { ?>
				<span class="error"><?php echo $error_testsecretkey; ?></span>
				<?php } ?></td>
			</tr>
			<tr>
			  <td><span class="required">*</span> <?php echo $entry_testpubkey; ?></td>
			  <td><input type="text" name="simplifycommerce_testpubkey" value="<?php echo $simplifycommerce_testpubkey; ?>" />
				<?php if ($error_testpubkey) { ?>
				<span class="error"><?php echo $error_testpubkey; ?></span>
				<?php } ?></td>
			</tr>
			<tr>
			  <td><span class="required">*</span> <?php echo $entry_title; ?><br /><span class="help"> <?php echo $entry_title_help; ?></span></td>
			  <td><input type="text" name="simplifycommerce_title" value="<?php echo $simplifycommerce_title; ?>" />
				<?php if ($error_title) { ?>
				<span class="error"><?php echo $error_title; ?></span>
				<?php } ?></td>
			</tr>
			<tr>
			  <td><?php echo $entry_order_status; ?></td>
			  <td><select name="simplifycommerce_order_status_id">
				  <?php foreach ($order_statuses as $order_status) { ?>
				  <?php if ($order_status['order_status_id'] == $simplifycommerce_order_status_id) { ?>
				  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
				  <?php } else { ?>
				  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
				  <?php } ?>
				  <?php } ?>
				</select></td>
			</tr>
			<tr>
			  <td><?php echo $entry_declined_order_status; ?></td>
			  <td><select name="simplifycommerce_declined_order_status_id">
				  <?php foreach ($order_statuses as $declined_order_status) { ?>
				  <?php if ($declined_order_status['order_status_id'] == $simplifycommerce_declined_order_status_id) { ?>
				  <option value="<?php echo $declined_order_status['order_status_id']; ?>" selected="selected"><?php echo $declined_order_status['name']; ?></option>
				  <?php } else { ?>
				  <option value="<?php echo $declined_order_status['order_status_id']; ?>"><?php echo $declined_order_status['name']; ?></option>
				  <?php } ?>
				  <?php } ?>
				</select></td>
			</tr>
			<tr>
			  <td><?php echo $entry_geo_zone; ?></td>
			  <td><select name="simplifycommerce_geo_zone_id">
				  <option value="0"><?php echo $text_all_zones; ?></option>
				  <?php foreach ($geo_zones as $geo_zone) { ?>
				  <?php if ($geo_zone['geo_zone_id'] == $simplifycommerce_geo_zone_id) { ?>
				  <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
				  <?php } else { ?>
				  <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
				  <?php } ?>
				  <?php } ?>
				</select></td>
			</tr>
			<tr>
			  <td><?php echo $entry_status; ?></td>
			  <td><select name="simplifycommerce_status">
				  <?php if ($simplifycommerce_status) { ?>
				  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
				  <option value="0"><?php echo $text_disabled; ?></option>
				  <?php } else { ?>
				  <option value="1"><?php echo $text_enabled; ?></option>
				  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
				  <?php } ?>
				</select></td>
			</tr>
			<tr>
			  <td><?php echo $entry_sort_order; ?></td>
			  <td><input type="text" name="simplifycommerce_sort_order" value="<?php echo $simplifycommerce_sort_order; ?>" size="1" /></td>
			</tr>
		  </table>
		</form>
	  </div>
	</div>
</div>
<?php echo $footer; ?>