<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ReduxSAFramework_premium_only {

      public function __construct(){

      }

      public function render(){
      ?>
            <div class="swift-centered">
                  <?php esc_html_e('This feature is available only in premium version.', 'swift-performance');?><br>
            </div>
      <?php
      }
}
?>
