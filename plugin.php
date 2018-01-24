<?php
/*
Plugin Name: URL Params Append
Plugin URI: https://github.com/PepperPlatform/yourls-params-append
Description: Appends url params based on selected platform(e.g facebook is a platform)
Version: 0.2
Author: Tinashe - @Pepper
Author URI: https://github.com/tm57
*/

// No direct call
if (!defined('YOURLS_ABSPATH')) {
	die();
}

// Add customised form
yourls_add_filter('shunt_html_addnew', 'add_create_form');

// Show the saved params
yourls_add_action('show_save_url_params_form', 'show_platform_params');

// Use own js add_link function
yourls_add_action('html_head', 'override_js');


// Register our plugin
yourls_register_plugin_page('save_platform_params', 'Save Platform Url Params', 'save_url_params');

function save_url_params() {


	// Check if a form was submitted
	if (isset($_POST['platform']) && isset($_POST['url_params'])) {
		// Check nonce
		yourls_verify_nonce('save_platform_params');


		$platform_name = trim($_POST['platform']);
		$param = trim($_POST['url_params']);

		db_create_new_params($platform_name, $param);
	} elseif (isset($_GET['id'])) {
		db_remove_platform($_GET['id']);
	}

	// Create nonce
	$nonce = yourls_create_nonce('save_platform_params');

	echo <<<HTML
		<h2>Save Platform Url Params</h2>
		<p>Saves your url params to the database</p>
		<form method="post">
		<input type="hidden" name="nonce" value="$nonce" />
		
		<p><label for="platform">Enter Platform Name</label> 
		<input type="text" id="platform" name="platform" value="" />
		</p>
		
		
		<p><label for="url_params">Enter the url params</label> 
		<input type="text" id="url_params" name="url_params" value="" style="width: 800px" /></p>
		
		<p><input type="submit" value="Save" /></p>
		</form>

HTML;
	yourls_do_action('show_save_url_params_form');
}

function show_platform_params() {

	$data = get_saved_params();
	$rows = generate_platform_rows($data);
	echo <<<HTML
		<h2>Existing Params</h2>
		
<table>
  <tr>
    <th>Platform</th>
    <th>Param</th>
  </tr>
  $rows

</table>
HTML;
}

function render_platform_row($row) {

	return '
<tr>
    <td>' . $row->platform_name . '</td>
    <td>' . $row->params . '</td>
    <td><a href=' . yourls_admin_url('plugins.php?page=save_platform_params&id=' . $row->platform_id) . '>Delete</a></td>
  </tr>';
}

function get_saved_params() {

	global $ydb;
	$table = 'yourls_url_platform_params';
	$sql = "SELECT * FROM `$table`";

	return $ydb->fetchObjects($sql);
}

function generate_platform_rows($rows) {

	$result = '';
	foreach ($rows as $row) {
		$result .= render_platform_row($row);
	}

	return $result;
}

function db_create_new_params($platform, $params) {

	try {
		global $ydb;
		$table = 'yourls_url_platform_params';
		$sql = "INSERT INTO $table (platform_name, params)  VALUES(:platform_name,:params)";
		$binds = array('platform_name' => $platform, 'params' => $params);
		$ydb->fetchAffected($sql, $binds);
	} catch (\Exception $e) {
		//do nothing lolz
	}
}

function db_remove_platform($id) {

	try {
		global $ydb;
		$table = 'yourls_url_platform_params';
		$sql = "DELETE FROM $table WHERE platform_id = :id";
		$binds = array('id' => $id);
		$ydb->fetchAffected($sql, $binds);
	} catch (\Exception $e) {
		//do nothing lolz
	}
}

function add_select_to_form() {

	$existing = get_saved_params();

	$html = '<select name="url_params" id="url_params" form="new_url_form">';
	$html .= "<option value=''>Select Platform</option>";

	foreach ($existing as $row) {

		$html .= "<option value='{$row->params}'" . ">$row->platform_name</option>";

	}

	echo $html . '</select>';

}

function add_create_form() {

	include_once 'form.php';
}

function override_js() {

	echo <<<JS

<script>
function create_link() {
	if ($('#add-button').hasClass('disabled')) {
		return false;
	}
	var newurl = $("#add-url").val();
	var nonce = $("#nonce-add").val();
	var url_params = $("#url_params").val();
	var campaign = $("#campaign").val();
	var params = '';
	
	if(url_params && !campaign ){
		var message = 'Please enter the custom campaign. It is required when platform is selected!';
		feedback(message, 'error');
		return;
		
	}
	
	if(url_params){
	 params = url_params.trim() + '&utm_campaign='+ campaign.trim();
	}
	
	if (!newurl || newurl == 'http://' || newurl == 'https://') {
		return;
	}
	var keyword = $("#add-keyword").val();
	add_loading("#add-button");
	$.getJSON(
		ajaxurl,
		{action: 'add', url: newurl + params, keyword: keyword, nonce: nonce},
		function (data) {
			if (data.status == 'success') {
				$('#main_table tbody').prepend(data.html).trigger("update");
				$('#nourl_found').css('display', 'none');
				zebra_table();
				increment_counter();
				toggle_share_fill_boxes(data.url.url, data.shorturl, data.url.title);
			}

			add_link_reset();
			end_loading("#add-button");
			end_disable("#add-button");

			feedback(data.message, data.status);
		}
	);
}
</script>

JS;
}