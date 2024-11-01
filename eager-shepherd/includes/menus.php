<?php
function eager_Jc8tYPrySUxWuirA_minimal_menu() {
  // Check for the existence of the Eager Menu Page. If it exists, it's from an outdated version of the plugin, so remove it
  if (!empty($GLOBALS['admin_page_hooks']['eager_options_handle'])) {
    remove_menu_page('eager_options_handle');
  }
  add_menu_page('Shepherd', 'Shepherd', 'activate_plugins', 'eager_app_AalP5veMma6s_options', 'eager_Jc8tYPrySUxWuirA_app_options_page');
  add_menu_page('Eager Apps', 'Add More Apps', 'activate_plugins', 'eager_options_handle', 'eager_Jc8tYPrySUxWuirA_options_page');
}

function eager_Jc8tYPrySUxWuirA_remove_minimal_menu() {
  remove_menu_page('eager_options_handle');
  remove_menu_page('eager_app_AalP5veMma6s_options');
}

function eager_Jc8tYPrySUxWuirA_activation_menu() {
  add_menu_page('Eager Opt In', 'Install Shepherd', 'activate_plugins', 'eager_Jc8tYPrySUxWuirA_optin_handle', 'eager_Jc8tYPrySUxWuirA_optin_page');
  add_submenu_page(null, 'Eager Activate', 'Eager Activate', 'activate_plugins', 'eager_Jc8tYPrySUxWuirA_activate_handle', 'eager_Jc8tYPrySUxWuirA_activate_page');
  add_submenu_page(null, 'Shepherd Deactivate', 'Shepherd Deactivate', 'activate_plugins', 'eager_Jc8tYPrySUxWuirA_deactivate_handle', 'eager_Jc8tYPrySUxWuirA_deactivate_page');
}

function eager_Jc8tYPrySUxWuirA_base_menu() {
  global $eagerActiveApps;

  // Check for the existence of the Eager Menu Page. If it exists, it's from an outdated version of the plugin, so remove it
  if (!empty($GLOBALS['admin_page_hooks']['eager_options_handle'])) {
    remove_menu_page('eager_options_handle');
  }

  add_menu_page('Eager Apps', 'Eager App Store', 'activate_plugins', 'eager_options_handle', 'eager_Jc8tYPrySUxWuirA_options_page');
  add_submenu_page(null, 'Install App', 'Install App', 'activate_plugins', 'eager_install_app_handle', 'eager_Jc8tYPrySUxWuirA_install_app_page');

  $installs = eager_Jc8tYPrySUxWuirA_get_installs();

  $uninstalledApps = $eagerActiveApps;
  if ($uninstalledApps == null)
    $uninstalledApps = array();

  if ($installs){
    foreach ($installs as $install) {
      unset($uninstalledApps[$install['app']['id']]);
      unset($uninstalledApps[$install['app']['alias']]);
    }
  }

  foreach ($uninstalledApps as $appId => $app) {
    add_submenu_page('eager_options_handle', $app['title'], $app['title'] . ' (inactive)', 'activate_plugins',  'eager_app_'.$appId.'_options', 'eager_Jc8tYPrySUxWuirA_app_options_page');
  }

  if ($installs){
    foreach ($installs as $install) {
      add_submenu_page('eager_options_handle', $install['app']['title'], $install['app']['title'], 'activate_plugins',  'eager_app_'.$install['appId'].'_options', 'eager_Jc8tYPrySUxWuirA_app_options_page');
    }
  }

  //add_submenu_page('eager_options_handle', 'Add Eager App', '+ Browse Apps', 'activate_plugins', 'eager_add_app_options', 'eager_Jc8tYPrySUxWuirA_add_app_options');
}

?>
