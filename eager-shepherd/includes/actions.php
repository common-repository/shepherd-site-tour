<?php

add_action('activated_plugin', 'eager_Jc8tYPrySUxWuirA_activated');
add_action('wp_ajax_eager_save_login', 'eager_Jc8tYPrySUxWuirA_save_login_callback');
add_action('wp_ajax_eager_get_installs', 'eager_Jc8tYPrySUxWuirA_get_installs_callback');
add_action('plugins_loaded', 'eager_Jc8tYPrySUxWuirA_hook');
register_uninstall_hook(__FILE__, 'eager_Jc8tYPrySUxWuirA_uninstall');

function eager_Jc8tYPrySUxWuirA_hook() {
  if (eager_Jc8tYPrySUxWuirA_load()) {
    eager_Jc8tYPrySUxWuirA_init();
    add_action('admin_init', 'eager_Jc8tYPrySUxWuirA_admin_init');
    $optin = get_option('eager_optin');
  }
}

function eager_Jc8tYPrySUxWuirA_load() {
  // if we're on an admin page
  if (is_admin()){
    global $eagerBaseActivated;
    global $eagerMinimalActivated;
    global $eagerActiveApps;

    $optin = get_option('eager_optin');

    // If AppId !== 'BASE' then we need to add it to the list of apps
    if ('AalP5veMma6s' !== 'BASE') {
      $eagerActiveApps[EAGER_Jc8tYPrySUxWuirA_ID] = array('title' => "Shepherd");
    }

    // only generate eager menu/pages once
    if (isset($eagerBaseActivated)) {
      return false;
    }

    // If a minimal plugin is already active, handle conversion to base
    // No need for additional logic because we know there are multiple plugins installed
    if (isset($eagerMinimalActivated)) {
      add_action('admin_menu', 'eager_Jc8tYPrySUxWuirA_remove_minimal_menu');
      if ($optin) {
        add_action('admin_menu', 'eager_Jc8tYPrySUxWuirA_base_menu');
        unset($eagerMinimalActivated);
        $eagerBaseActivated = true;
        return true;
      }
    }

    // Handle base vs minimal logic

    // TODO set to <no value> when available
    $minimal = true;

    $installs = eager_Jc8tYPrySUxWuirA_get_installs();

    // Don't count this current plugin when determining if more than one app is installed (for mininmal vs base purposes)
    if ($installs) {
      foreach ($installs as $install) {
        if ($install['app']['id'] !== 'AalP5veMma6s') {
          $minimal = false;
          break;
        }
      }
    }

    $menu = '';

    if ($minimal) {
      $menu = 'eager_Jc8tYPrySUxWuirA_minimal_menu';
      $eagerMinimalActivated = true;
    } else {
      $menu = 'eager_Jc8tYPrySUxWuirA_base_menu';
      $eagerBaseActivated = true;
    }

    // Opt-in page
    if (!$optin) {
      add_action('admin_menu', 'eager_Jc8tYPrySUxWuirA_activation_menu');
    } else {
      add_action('admin_menu', $menu);
    }

    return true;

  } else {
    // non admin page - embed the eager code so apps display on site
    require_once(EAGER_Jc8tYPrySUxWuirA_DIR . 'includes/embed.php');
  }

}


function eager_Jc8tYPrySUxWuirA_activated($plugin) {
  if ($plugin !== 'eager-shepherd/base.php') {
    return;
  }
  $optin = get_option('eager_optin');
  $url = '';
  if (!$optin) {
    $url = "admin.php?page=eager_Jc8tYPrySUxWuirA_optin_handle";
  } else {
    $url = "admin.php?page=eager_app_" . EAGER_Jc8tYPrySUxWuirA_ID . "_options";
  }
  exit(wp_redirect(admin_url($url)));
}

function eager_Jc8tYPrySUxWuirA_admin_init() {
wp_register_style('eager_css', EAGER_Jc8tYPrySUxWuirA_URL.'styles/main.css');
  wp_enqueue_style('eager_css');

  wp_register_script('eager_js', EAGER_Jc8tYPrySUxWuirA_URL.'scripts/main.js');
  wp_enqueue_script('eager_js');
}

function eager_Jc8tYPrySUxWuirA_uninstall() {
  global $eagerActiveApps;

  if ($eagerActiveApps != null) {
    foreach($eagerActiveApps as $appId => $app){
      eager_Jc8tYPrySUxWuirA_deactivate_plugin($appId);
    }
  }
}

/**
 * CALLBACKS
 */

function eager_Jc8tYPrySUxWuirA_save_login_callback() {
  $data = json_decode(stripslashes($_POST['body']), true);

  if ($data == null){
    status_header(500);
    wp_die("Error reading JSON data");
  } else {
    check_ajax_referer('eager_options_nonce', 'csrf_token');

    if ($data['reset']){
      delete_option('eager_site_id');
      delete_option('eager_user_id');
      delete_option('eager_access_token');
      wp_die("Cleared");
      return;
    }

    $existingToken = get_option('eager_access_token');
    $existingSiteId = get_option('eager_site_id');
    if ($existingSiteId && $existingToken){
      eager_Jc8tYPrySUxWuirA_transfer_site($data['user']['id']);
      } else if ($existingSiteId){
      delete_option('eager_site_id');
    }

    update_option('eager_user_id', $data['user']['id']);
    update_option('eager_access_token', $data['token']['token']);

    wp_die("Saved");
  }
}

function eager_Jc8tYPrySUxWuirA_get_installs_callback() {
  $installs = eager_Jc8tYPrySUxWuirA_get_installs();

  $return = array(
    'installs' => $installs
  );

  echo json_encode($return);
  wp_die();
}
