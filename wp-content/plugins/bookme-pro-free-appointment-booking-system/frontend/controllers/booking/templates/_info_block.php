<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( $info_message ) : ?>
    <div class="bookme-pro-box bookme-pro-well">
        <div class="bookme-pro-round bookme-pro-margin-sm"><i class="bookme-pro-icon-sm bookme-pro-icon-i"></i></div>
        <div>
            <?php echo nl2br( $info_message ) ?>
        </div>
    </div>
<?php endif ?>