<?php
function eager_Jc8tYPrySUxWuirA_get_embed_html() {
  if (!is_admin()){
    $host = "fast.eager.io";

    if ($_GET['__eager_embed']){
      add_filter('show_admin_bar', '__return_false');

      $host = "fast-direct.eager.io";

      echo <<<HTML
        <script>
          if (window.parent && window.parent.postMessage){
            window.parent.postMessage({type: "eager-proxy:loaded", info: {}}, "*")
          }
        </script>
HTML;
    }

    echo '<script data-cfasync="false" data-pagespeed-no-defer src="//' . $host . '/' . eager_Jc8tYPrySUxWuirA_get_site_id() . '.js"></script>';
  }
}

if (!$GLOBALS['eagerEmbedBound'])
  add_action('wp_head', 'eager_Jc8tYPrySUxWuirA_get_embed_html');

$GLOBALS['eagerEmbedBound'] = true;
?>
