<?php if ($simplifycommerce_payment_mode) { /* hosted payments mode */ ?>
<script type="text/javascript" src="https://www.simplify.com/commerce/simplify.pay.js"></script>
<?php } else { /* standard mode */ ?>
<script type="text/javascript" src="https://www.simplify.com/commerce/v1/simplify.js"></script>
<div class="form-horizontal" id="payment">
	<fieldset>
		<legend><?php echo $text_card_details; ?></legend>
		<div class="form-group required">
			<label class="col-sm-2 control-label" for="entry_name_on_card"><?php echo $entry_name_on_card; ?></label>
			<div class="col-sm-10">
				<input type="text" name="entry_name_on_card" value="" placeholder="<?php echo $entry_name_on_card; ?>" id="entry_name_on_card" class="form-control" />
			</div>
		</div>
		<div class="form-group required">
			<label class="col-sm-2 control-label" for="entry_card_number"><?php echo $entry_card_number; ?></label>
			<div class="col-sm-10">
				<input type="text" name="entry_card_number" value="" placeholder="<?php echo $entry_card_number; ?>" id="entry_card_number" class="form-control" />
			</div>
		</div>
		<div class="form-group required">
			<label class="col-sm-2 control-label" for="entry_card_month"><?php echo $entry_card_expiration; ?></label>
			<div class="col-sm-3">
				<select name="entry_card_month" id="entry_card_month" class="form-control">
					<?php foreach ($months as $month) { ?>
					<option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col-sm-3">
				<select name="entry_card_year" id="entry_card_year" class="form-control">
					<?php foreach ($year_expire as $year) { ?>
					<option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="form-group required">
			<label class="col-sm-2 control-label" for="entry_cvc"><?php echo $entry_cvc; ?></label>
			<div class="col-sm-10">
				<input type="text" name="entry_cvc" value="" placeholder="<?php echo $entry_cvc; ?>" id="entry_cvc" class="form-control" />
			</div>
		</div>
		<div id="sc_error"></div>
	</fieldset>
<?php } ?>
	<div class="buttons">
	  <div class="pull-right">
<?php if ($simplifycommerce_payment_mode) { ?>
			<button id="simplify-button"
					data-color="<?php echo $button_color?>"
					data-sc-key="<?php echo $pub_key; ?>"
					data-name="<?php echo $store_name; ?>"
					data-reference="<?php echo $description; ?>"
					data-amount="<?php echo $amount; ?>"
					data-operation="create.token"
					data-receipt="false"
					data-redirect-url="<?php echo $redirect_url ?>"
					>
			<?php echo $button_pay; ?>
			</button>
<?php } else { ?>
		  <input type="button" value="<?php echo $button_pay; ?>" id="button-pay" class="btn btn-primary" />
<?php } ?>
	  </div>
	</div>
</div>
<?php if (!$simplifycommerce_payment_mode) { ?>
<script type="text/javascript">

	var SimplifyCommerceHandler = function(){

		var resetErrors = function(){
			$('.entry_card_number_error').remove();
			$('.entry_card_month_error').remove();
			$(".entry_name_on_card_error").remove();
			$(".entry_cvc_error").remove();
		};

		var ajaxDoneHooks = function(){
			$('#button-pay').button('reset');
			if(typeof triggerLoadingOff !== "undefined"){
				triggerLoadingOff(); // fix for "Journal - Advanced Opencart Theme"
			}
		}

		var displayError = function(error){

			switch (error.code) {
				case 'validation':
					for (var i = 0 ; i < error.fieldErrors.length; i++) {
						var field = error.fieldErrors[i].field;
						var errmsg = error.fieldErrors[i].message;

						if (field == 'card.number') {
							if(! $('.entry_card_number_error').length){
								$('#entry_card_number').after('<div class="text-danger entry_card_number_error">Enter a valid card number</span>');
							}
						}
						if (field == 'card.expMonth' || field == 'card.expYear') {
							$('.entry_card_month_error').remove()
							$('#entry_card_month').after('<div class="text-danger entry_card_month_error">' + errmsg + '</span>');
						}
						if(field == 'card.name'){
							$('#entry_name_on_card').after('<div class="text-danger entry_name_on_card_error">' + errmsg + '</span>');
						}
						if(field == 'card.cvc'){
							$('#entry_cvc').after('<div class="text-danger entry_cvc_error">' + errmsg + '</span>');
						}
					}
					break;
				default:
					$('#sc_error').after('<div class="text-danger entry_card_number_error">' + error.message + '</span>');
					break;
			}
		};

		return{

			handle : function(response){

				resetErrors();
				if (response.error) {

					$('#button-pay').attr("disabled", false);
					displayError(response.error)
					ajaxDoneHooks();

				} else {
					$.ajax({
						url: 'index.php?route=payment/simplifycommerce/charge',
						type: 'post',
						dataType: 'json',
						data: {cardToken: response['id']},
						beforeSend: function() {
							$('#button-pay').button('loading');
						},
						complete: function() {
							ajaxDoneHooks();
						},
						success: function(response) {
							if (response['error']) {
								alert(response['error']);
							}

							if (response['success']) {
								location = response['success'];
							}
						}
					});

				}
			}
		}
	}();

	$(document).ready(function() {
		var action = function(event) {
			$(".entry_cvc_error").remove();
			if($.trim($('#entry_cvc').val()) == ""){
				$('#entry_cvc').after('<div class="text-danger entry_cvc_error">Field required</span>');
				ajaxDoneHooks();
				return false;
			}
			$('#button-pay').attr("disabled", true);
			SimplifyCommerce.generateToken({
				key: "<?php echo $pub_key; ?>",
				card: {
					name: $('#entry_name_on_card').val(),
					number: $('#entry_card_number').val(),
					cvc: $('#entry_cvc').val(),
					expMonth: $('#entry_card_month').val(),
					expYear: $('#entry_card_year').val()
				  }
			}, SimplifyCommerceHandler.handle);

			// prevent the form from submitting with the default action
			return false;
		};
		$("#button-pay").click(action);
	  });
</script>
<?php } ?>
