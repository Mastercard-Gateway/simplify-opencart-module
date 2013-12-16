<link rel="stylesheet" href="catalog/view/theme/default/stylesheet/simplifycommerce.css" />
<script type="text/javascript" src="https://www.simplify.com/commerce/v1/simplify.js"></script>
<h2><?php echo $text_payment_title; ?></h2>
<div class="content" id="payment">
  <table id="fualable_layout">
  	<tr>
  		<td>
  			<table id="str3" style="display:block;" class="form">
    			<tr>
    				<td>
    					<?php echo $entry_name_on_card; ?><br />
      					<input type="text" id="entry_name_on_card" name="entry_name_on_card" value="" class="simplifycommerce-field"/>
      				</td>
    			</tr>
    			<tr>
    				<td>
    					<span class="required">*</span>
    					<?php echo $entry_card_number; ?><br />
      					<input type="text" id="entry_card_number" name="entry_card_number" class="simplifycommerce-field"/>
      					<br>
      				</td>
    			</tr>
			    <tr>
			      <td><span class="required">*</span><?php echo $entry_card_expiration; ?><br />
			        <select id="entry_card_month" name="entry_card_month">
			          	<option value=""></option>
			          	<?php foreach ($months as $month) { ?>
			          	<option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
			          	<?php } ?>
			        </select>
			        /
			        <select id="entry_card_year" name="entry_card_year">
			          	<option value=""></option>
			          	<?php foreach ($year_expire as $year) { ?>
			          	<option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
			          	<?php } ?>
			        </select><br></td>
			    </tr>
    			<tr>
    				<td>
    					<?php echo $entry_cvc; ?><br />
      					<input type="text" id="entry_cvc" name="entry_cvc" class="simplifycommerce-cvc-field"/>
      				</td>
    			</tr>
                <tr>
                    <td>
                        <span id="sc_error" class="error"/>
                    </td>
                </tr>
    		</table>
  		</td>
  	</tr>
  </table>
	<div class="buttons">
	  <div class="right">
	    <input type="button" value="<?php echo $button_pay; ?>" id="button-pay" class="button" />
	  </div>
	</div>
</div>
<script type="text/javascript">
	
	var SimplifyCommerceHandler = function(){

	    var resetErrors = function(){
			$('.entry_card_number_error').remove() 
			$('.entry_card_year_error').remove() 
		};

		var displayError = function(error){

            switch (error.code) {
                case 'validation':
                    for (var i = 0 ; i < error.fieldErrors.length; i++) {
        				var field = error.fieldErrors[i].field;
        				var errmsg = error.fieldErrors[i].message;
                    
        				if (field == 'card.number') {
		        			$('#entry_card_number + br').after('<span class="error entry_card_number_error">' + errmsg + '</span>');
        				}
        				if (field == 'card.expMonth' || field == 'card.expYear') {
           					$('.entry_card_year_error').remove() 
        					$('#entry_card_year + br').after('<span class="error entry_card_year_error">' + errmsg + '</span>');
        				}
                    }
                    break;
                default:
		        	$('#sc_error').after('<span class="error entry_card_number_error">' + error.message + '</span>');
                    break;
            }
		};

		return{

			handle : function(response){

				resetErrors();
				if (response.error) {
				    $('#button-pay').attr("disabled", false);
				    displayError(response.error)

				} else {

				    $.ajax({
						url: 'index.php?route=payment/simplifycommerce/charge',
						type: 'post',
						data: {token: response['id'], amount: <?php echo $amount; ?>, currency: '<?php echo $currency; ?>', description: '<?php echo $description; ?>'},
						dataType: 'json',		
						beforeSend: function() {
							$('#button-pay').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
						},
						complete: function() {
							$('#button-pay').attr('disabled', false); 
							$('.wait').remove();
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
	  $("#button-pay").mousedown(function(event) {

	    $('#button-pay').attr("disabled", true);
	    SimplifyCommerce.generateToken({
	        key: "<?php echo $pub_key; ?>",
	        card: {
	            number: $('#entry_card_number').val(),
	            cvc: $('#entry_cvc').val(),
	            expMonth: $('#entry_card_month').val(),
	            expYear: $('#entry_card_year').val()
	        }
	    }, SimplifyCommerceHandler.handle);

	    // prevent the form from submitting with the default action
	    return false;
	  });
	});
</script>
