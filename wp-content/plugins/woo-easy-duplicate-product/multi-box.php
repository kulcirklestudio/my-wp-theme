<div class="wedp-multiple-products-box">
<style type="text/css">
	
	.wedp-multiple-products-number {
		padding: 0 0 0 8px !important;
	}

	.wedp-hidden {
		display: none;
	}

	.wedp-multi-status-message {
		display: inline-block;
		display: none;
		border: 1px solid #ffdd3c;
		background: #FDFD96;
		margin: 5px;
		padding: 10px;
		font-weight: bold;
	}

	.wedp-green {
		color: #110011  !important;
		border: 2px solid #77DD77;
		background: #e8f4ea;

	}
</style>

<p><a href="#" class="wedp-multiple-products-switch-button">Multiple</a></p>
<div class="wedp-multiple-products-input-container wedp-hidden">How many times? <input type="number" style="color: #444;" name="wedp-multiple-products-number" class="wedp-multiple-products-number" min="2" value="2"><p><input style="" type="button" name="wedp-duplicate-multiple-products-button" class="wedp-duplicate-multiple-products-button button button-primary button-small" value="Duplicate multiple" width="20">
<div class="wedp-multi-status-message"><span>Done!</span></div>
</div>
</div>
<p><small><em><a style="color: #444;" href="https://drift.me/jeanpaul" target="_blank">(Support desk)</a></em></small></p>

<script type="text/javascript">
	(function($){
		"use strict"


		var $input_container = $('.wedp-multiple-products-input-container');
		var $duplicate_multiple_products_button_switch = $('.wedp-multiple-products-switch-button');
		var $duplicate_multiple_products_button = $input_container.find('.wedp-duplicate-multiple-products-button');
		var $duplicate_multiple_products_number_input = $input_container.find('.wedp-multiple-products-number');


		$duplicate_multiple_products_button.on('click', function(e){
			//@todo - disable this button when the product is being multiplied
			
			let duplicate_number = $duplicate_multiple_products_number_input.val();

			if(confirm("Please confirm. This product will be duplicated "+ duplicate_number +" times.")){
						console.log('Multiduplication.', duplicate_number);

						var data = {
							action: 'wedp_duplicate_product',
							product_id : wedp_product_id,
							multiple_products_number: duplicate_number,
							wedp_multiple_product_duplicate: true,
							_wp_nonce: wedp_wp_nonce
						};
						
						let $status_message = $('.wedp-multi-status-message');
						$status_message.text('Multiplying your product...');
						$status_message.show();

						$.post(ajaxurl, data, function(response){


							//console.log(response);
							response = JSON.parse(response);
							let status = response.status;

							if (status == 'success'){

							$status_message.addClass('wedp-green');
							$status_message.text('Done!');
								setTimeout(function hideMultiDoneMessage(){
									$status_message.hide({duration: 300, done: removeGreen});
								}, 3000);

								function removeGreen(){
									$status_message.removeClass('wedp-green');

								}

							}

						});
			}

		});


		$duplicate_multiple_products_button_switch.on('click', function(){

			$input_container.toggle();

		});

	})(jQuery);


</script>