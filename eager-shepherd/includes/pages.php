<?php

function eager_Jc8tYPrySUxWuirA_install_app_page() {
  if ( !isset($_GET['appId']) ) {
    $url = admin_url("admin.php?page=eager_options_handle");
    echo '<script>window.location = "' . $url . '";</script>';
    return;
  }

  if (!current_user_can('activate_plugins'))  {
    wp_die(__('You do not have sufficient permissions to access this page.'));
  }

  $appId = $_GET['appId'];

  $ip = gethostbyname($_SERVER['SERVER_NAME']);

  echo '<div class="wrap">';
  echo '<eager-options site-id="' . eager_Jc8tYPrySUxWuirA_get_site_id() . '" user-id="' . eager_Jc8tYPrySUxWuirA_get_user_id() . '" token="' . eager_Jc8tYPrySUxWuirA_get_token() . '" app-id="' . $appId . '" app-store="true"></eager-options>';
  echo '<script src="https://cms-br-app-store.eager.io/js/options.js"></script>';
  echo '</div>';
}

function eager_Jc8tYPrySUxWuirA_options_page() {
  if (!current_user_can('activate_plugins'))  {
    wp_die(__('You do not have sufficient permissions to access this page.'));
  }

  $userId = eager_Jc8tYPrySUxWuirA_get_user_id();
  $siteId = eager_Jc8tYPrySUxWuirA_get_site_id();
  $token = eager_Jc8tYPrySUxWuirA_get_token();

  $csrfToken = wp_create_nonce('eager_options_nonce');

  echo '<div class="wrap">';
  echo '<eager-cms-settings cms-name="wordpress" user-id="'.$userId.'" site-id="'.$siteId.'" token="'.$token.'" csrf-token="'.$csrfToken.'"></eager-cms-settings>';
  echo '<script src="https://cms-br-app-store.eager.io/js/settings.js"></script>';
  echo '</div>';
}

function eager_Jc8tYPrySUxWuirA_app_options_page($args) {
  global $plugin_page;
  
  $matches = null;
  if (!preg_match("/eager_app_([a-zA-Z0-9\-_]+)_options/", $plugin_page, $matches)){
    wp_die("Plugin page not understood");
    return;
  }

  $id = $matches[1];

  if (!current_user_can('activate_plugins'))  {
    wp_die(__('You do not have sufficient permissions to access this page.'));
  }

  $ip = gethostbyname($_SERVER['SERVER_NAME']);

  echo '<div class="wrap">';
  echo '<eager-options site-id="' . eager_Jc8tYPrySUxWuirA_get_site_id() . '" user-id="' . eager_Jc8tYPrySUxWuirA_get_user_id() . '" token="' . eager_Jc8tYPrySUxWuirA_get_token() . '" app-id="' . $id . '" app-store="false"></eager-options>';
  echo '<script src="https://cms-br-app-store.eager.io/js/options.js"></script>';
  echo '</div>';
}

function eager_Jc8tYPrySUxWuirA_add_app_page(){
  echo '<div class="wrap">';
  echo '<eager-app-store site-id="' . eager_Jc8tYPrySUxWuirA_get_site_id() . '" user-id="' . eager_Jc8tYPrySUxWuirA_get_user_id() . '" token="' . eager_Jc8tYPrySUxWuirA_get_token() . '"></eager-app-store>';
  echo '<script src="https://cms-br-app-store.eager.io/js/appStore.js"></script>';
  echo '</div>';
}

function eager_Jc8tYPrySUxWuirA_optin_page() {
  if (!current_user_can('activate_plugins'))  {
    wp_die(__('You do not have sufficient permissions to access this page.'));
  }
  echo <<<HTML
    <style>
      .eager-confirm {
        font-family: "proxima-nova", "Helvetica Neue", Helvetica, Arial, sans-serif;
        padding-top: 15px;
      }
      .eager-confirm a {
        color: #e90f92;
        text-decoration: none;
        -webkit-tap-highlight-color: transparent;
      }
      .eager-confirm a:hover, .eager-confirm a:active {
        color: #ba0c75;
      }
      .eager-confirm-intro {
        float: left;
        margin: 0 0 1em 1em;
      }
      .eager-confirm-intro h1 {
        margin: 12px 0;
      }
      .eager-confirm-logo {
        height: 72px;
        float: left;
      }
      .eager-inline-text {
        display: inline;
        line-height: 28px;
        padding-left: 10px;
      }
        
      .eager-privacy-info {
        clear: both;
        padding: 1px 15px;
        max-width: 70em;
        background-color: rgba(255, 255, 255, 0.5);
      }
      .eager-privacy-info ul {
        list-style: disc inside;
      }
      .eager-privacy-info ul li {
        margin-bottom: 4px;
      }
      .eager-privacy-info ul, .eager-privacy-info p {
        font-size: 90%;
      }
    </style>
    <div class="eager-confirm">
      <img class="eager-confirm-logo" src="//eager-app-images.imgix.net/1wxEij85R5uAPK8fowvt_shepherd-icon.png?h=144">
      <div class="eager-confirm-intro">
        <h1>Welcome to Shepherd!</h1>
        <p>Guide your users through a tour of your site.</p>
      </div>
      <div class="eager-privacy-info">
        <p>Please note that installing this app will share the following information with Shepherd and its service providers:</p>
        <ul>
          <li>Your Name
          <li>Your Email Address
          <li>Your Website’s URL
        </ul>
        <p>All information you share is covered by our Privacy Policy and we will never sell your email address or other personal information.</p>
      </div>
      <p>Would you like to finish installing Shepherd?</p>
      <a href="admin.php?page=eager_Jc8tYPrySUxWuirA_activate_handle" class="button button-primary">Yes, Install Shepherd</a>
      <p class="eager-inline-text">
        (<a href="admin.php?page=eager_Jc8tYPrySUxWuirA_deactivate_handle">Deactivate</a> the Shepherd plugin to decline)
      </p>
    </div>
HTML;
}

function eager_Jc8tYPrySUxWuirA_activate_page() {
  $optin = update_option('eager_optin', 'true');
  if ($optin) {
    $url = admin_url("admin.php?page=eager_app_" . EAGER_Jc8tYPrySUxWuirA_ID . "_options");
    echo '<h1>Awesome!</h1>';
    echo '<h3>Taking you to the Shepherd configuration now...</h3>';
    echo '<script>window.location = "' . $url . '";</script>';
  } else {
    wp_die('There was an error when trying to install the Eager plugin. You can reload this page to try again. If this error persists, please contact Eager at help@eager.io.');
  }
}

function eager_Jc8tYPrySUxWuirA_deactivate_page() {
  $plugin = 'eager-shepherd/base.php';
    if (is_plugin_active($plugin)) {
      deactivate_plugins($plugin);
    }
    $url = admin_url('plugins.php');
    add_settings_error(
      'eager_app_deactivated',
      esc_attr( 'eager_app_deactivated' ),
      'Shepherd successfully deactivated. Redirecting you to plugins page now...',
      'updated'
    );
    echo settings_errors();
    echo '<script>window.location = "' . $url . '";</script>';
}
