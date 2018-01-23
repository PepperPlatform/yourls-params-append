<main role="main">
	<div id="new_url">
		<div>
			<form id="new_url_form" action="" method="get">
				<div>
					<strong><?php yourls_e('Enter the URL'); ?></strong>:<input type="text" id="add-url" name="url" value="" class="text" size="80" placeholder="https://"/>
					<?php yourls_e('Optional '); ?> :
					<strong><?php yourls_e('Custom short URL'); ?></strong>:<input type="text" id="add-keyword" name="keyword" value="" class="text" size="8"/>
					<?php yourls_nonce_field('add_url', 'nonce-add'); ?>

					<br/>
					<label><strong>Select Platform to append url parameters</strong></label>
					<?php add_select_to_form() ?>
					<br/>
					<input type="button" id="add-button" name="add-button" value="<?php yourls_e('Shorten The URL'); ?>" class="button" onclick="create_link();"/>
				</div>
			</form>
			<div id="feedback" style="display:none"></div>
		</div>
	</div>
