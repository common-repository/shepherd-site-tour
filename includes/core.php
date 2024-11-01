<?php
function eager_uc9OwFyFoSJFPNXT_init() {
  eager_uc9OwFyFoSJFPNXT_get_user_id();
  eager_uc9OwFyFoSJFPNXT_get_site_id();
}

function eager_uc9OwFyFoSJFPNXT_create_site(){
  $userId = eager_uc9OwFyFoSJFPNXT_get_user_id();
  if ($userId == '!EXISTS'){
    return '';
  }

  $url = "https://api.eager.io/sites/register/cms";
  $body = array(
    'homepageURL' => home_url(),
    'metadata' => array(
      'cms' => 'wordpress',
    ),
    'userId' => $userId,
    'title' => get_option('blogname'),
  );

  $resp = wp_remote_post($url, array(
    'headers' => array(
      'Content-Type' => 'application/json',
      'Authorization' => eager_uc9OwFyFoSJFPNXT_get_token(),
    ),
    'body' => json_encode($body),
  ));

  if (is_wp_error($resp) || $resp['response']['code'] >= 400) {
    error_log("Error creating Eager site:\n" . print_r($resp, true));
    wp_die(__('Error creating Eager site') . ' (' . $resp['response']['code'] . ')');
    return;
  } else {
    $site = json_decode($resp['body'], true);
  }

  update_option('eager_site_id', $site['id']);

  return $site['id'];
}

function eager_uc9OwFyFoSJFPNXT_get_installs(){
  $siteId = eager_uc9OwFyFoSJFPNXT_get_site_id();

  $url = "https://api.eager.io/sites/" . $siteId . "/installs";
  $resp = wp_remote_get($url, array(
    'headers' => array(
      'Authorization' => eager_uc9OwFyFoSJFPNXT_get_token(),
    ),
  ));

  if (is_wp_error($resp) || $resp['response']['code'] >= 400) {
    error_log("Error loading Eager installs:\n" . print_r($resp, true));
    $installs = NULL;
  } else {
    $installs = json_decode($resp['body'], true);
  }

  return $installs;
}

function eager_uc9OwFyFoSJFPNXT_uninstall_app($appId){
  $installs = eager_uc9OwFyFoSJFPNXT_get_installs();

  foreach ($installs as $install) {
    if ($install['appId'] == $appId && $install['active'] && !$install['pending']){
      return eager_uc9OwFyFoSJFPNXT_delete_install($install['id']);
    }
  }
}

function eager_uc9OwFyFoSJFPNXT_delete_install($installId){
  $url = "https://api.eager.io/installs/" . $installId;
  $resp = wp_remote_request($url, array(
    'method' => 'DELETE',
    'headers' => array(
      'Authorization' => eager_uc9OwFyFoSJFPNXT_get_token(),
    ),
  ));

  if (is_wp_error($resp) || $resp['response']['code'] >= 400) {
    error_log("Error loading deleting Eager install:\n" . print_r($resp, true));
    wp_die(__('Error loading deleting Eager install') . ' (' . $resp['response']['code'] . ')');
    return;
  }

  return true;
}

function eager_uc9OwFyFoSJFPNXT_create_user(){
  $wpUser = wp_get_current_user();

  $url = "https://api.eager.io/user/register/cms";
  $body = array(
    'metadata' => array(
      'cms' => 'wordpress',
      'cmsURL' => home_url(),
    ),
    'email' => $wpUser->user_email
  );

  if ($wpUser->first_name) {
    $body['firstName'] = $wpUser->first_name;
      if ($wpUser->last_name) {
          $body['lastName'] = $wpUser->last_name;
      }
  } else {
     $body['firstName'] = $wpUser->user_login != 'admin' ? $wpUser->user_login : 'Wordpress Admin';
  }

  $resp = wp_remote_post($url, array(
    'body' => json_encode($body),
    'headers' => array(
      'Content-Type' => 'application/json',
    ),
  ));

  if (!is_wp_error($resp))
    $body = json_decode($resp['body'], true);

  if (is_wp_error($resp) || $resp['response']['code'] >= 400) {
    if (!is_wp_error($resp) && $resp['response']['code'] == 409 && $body['error']['code'] == 'dup-email'){
      return '!EXISTS';
    }

    error_log("Error creating Eager user:\n" . print_r($resp, true));
    wp_die(__('Error creating Eager user') . ' (' . $resp['response']['code'] . ')');
    return;
  }
    
  update_option('eager_user_id', $body['user']['id']);
  update_option('eager_access_token', $body['token']['token']);

  return $body['user']['id'];
}

function eager_uc9OwFyFoSJFPNXT_get_site_id(){
  $siteId = get_option('eager_site_id');

  if (!$siteId){
    $siteId = eager_uc9OwFyFoSJFPNXT_create_site();
  }

  return $siteId;
}

function eager_uc9OwFyFoSJFPNXT_get_user_id(){
  $userId = get_option('eager_user_id');

  if (!$userId){
    $userId = eager_uc9OwFyFoSJFPNXT_create_user();
  }

  return $userId;
}

function eager_uc9OwFyFoSJFPNXT_get_token(){
  eager_uc9OwFyFoSJFPNXT_get_user_id();
  
  return get_option('eager_access_token');
}

function eager_uc9OwFyFoSJFPNXT_transfer_site($destUserId){
  $url = "https://api.eager.io/site/" . eager_uc9OwFyFoSJFPNXT_get_site_id() . "/transfer";
  $body = array(
    'destinationUserId' => $destUserId,
  );

  $resp = wp_remote_post($url, array(
    'body' => json_encode($body),
    'headers' => array(
      'Content-Type' => 'application/json',
      'Authorization' => eager_uc9OwFyFoSJFPNXT_get_token(),
    ),
  ));

  if (is_wp_error($resp) || $resp['response']['code'] >= 400) {
    error_log("Error transferring Eager site:\n" . print_r($resp, true));
    wp_die(__('Error transferring Eager site') . ' (' . $resp['response']['code'] . ')');
    return;
  }
}


function eager_uc9OwFyFoSJFPNXT_deactivate_plugin($appId){
  $installer = "eager-$appId/eager.php";
  $current = get_option('active_plugins');
  $plugin = plugin_basename($installer);

  $index = array_search($plugin, $current);
  if ($index !== false){
    unset($current[$index]);
    sort($current);
    do_action('deactivate_plugin', $plugin);
    update_option('active_plugins', $current);
    do_action('deactivate_'.$plugin);
    do_action('deactivated_plugin', $plugin);
    return true;
  }

  return false;
}
?>
