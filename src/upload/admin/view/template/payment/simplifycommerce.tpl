<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-sc" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-sc" class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="test-mode"><?php echo $entry_test; ?></label>
                        <div class="col-sm-10">
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
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="livesecretkey"><?php echo $entry_livesecretkey; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="simplifycommerce_livesecretkey" value="<?php echo $simplifycommerce_livesecretkey; ?>" class="form-control" id="livesecretkey"/>
                        </div>
                        <?php if ($error_livesecretkey) { ?>
                        <div class="text-danger"><?php echo $error_livesecretkey; ?></div>
                        <?php } ?>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="livepubkey"><?php echo $entry_livepubkey; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="simplifycommerce_livepubkey" value="<?php echo $simplifycommerce_livepubkey; ?>" class="form-control" id="livepubkey"/>
                        </div>
                        <?php if ($error_livepubkey) { ?>
                        <div class="text-danger"><?php echo $error_livepubkey; ?></div>
                        <?php } ?>
                    </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="testsecretkey"><?php echo $entry_testsecretkey; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="simplifycommerce_testsecretkey" value="<?php echo $simplifycommerce_testsecretkey; ?>"  class="form-control" id="testsecretkey" />
                        </div>
                        <?php if ($error_testsecretkey) { ?>
                        <div class="text-danger"><?php echo $error_testsecretkey; ?></div>
                        <?php } ?>
                    </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="testpubkey"><?php echo $entry_testpubkey; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="simplifycommerce_testpubkey" value="<?php echo $simplifycommerce_testpubkey; ?>"  class="form-control" id="testpubkey" />
                        </div>
                        <?php if ($error_testpubkey) { ?>
                        <div class="text-danger"><?php echo $error_testpubkey; ?></div>
                        <?php } ?>
                    </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="sc-title"><span data-toggle="tooltip" title="<?php echo $entry_title_help; ?>"><?php echo $entry_title; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="simplifycommerce_title" value="<?php echo $simplifycommerce_title; ?>"  class="form-control" id="sc-title" />
                        </div>
                        <?php if ($error_title) { ?>
                        <div class="text-danger"><?php echo $error_title; ?></div>
                        <?php } ?></td>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
                        <div class="col-sm-10">
                            <select name="simplifycommerce_order_status_id" id="input-order-status" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $simplifycommerce_order_status_id) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-declined-order-status"><?php echo $entry_declined_order_status; ?></label>
                        <div class="col-sm-10">
                            <select name="simplifycommerce_declined_order_status_id" id="input-declined-order-status" class="form-control">
                                <?php foreach ($order_statuses as $declined_order_status) { ?>
                                <?php if ($declined_order_status['order_status_id'] == $simplifycommerce_declined_order_status_id) { ?>
                                <option value="<?php echo $declined_order_status['order_status_id']; ?>" selected="selected"><?php echo $declined_order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $declined_order_status['order_status_id']; ?>"><?php echo $declined_order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
                        <div class="col-sm-10">
                            <select name="simplifycommerce_geo_zone_id" id="input-geo-zone" class="form-control">
                                <option value="0"><?php echo $text_all_zones; ?></option>
                                <?php foreach ($geo_zones as $geo_zone) { ?>
                                <?php if ($geo_zone['geo_zone_id'] == $simplifycommerce_geo_zone_id) { ?>
                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                        <div class="col-sm-10">
                            <select name="simplifycommerce_status" id="input-status" class="form-control">
                                <?php if ($simplifycommerce_status) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="simplifycommerce_sort_order" value="<?php echo $simplifycommerce_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                        </div>
                    </div>
                </form>
	        </div>
	    </div>
    </div>
</div>
<?php echo $footer; ?>