<?php
/**
 * Displays footer site info
 */

?>
<?php if( get_theme_mod( 'tanawul_bakery_hide_show_scroll',true) != '' || get_theme_mod( 'tanawul_bakery_enable_disable_scrolltop',true) != '') { ?>
    <?php $tanawul_bakery_theme_lay = get_theme_mod( 'tanawul_bakery_footer_options','Right');
        if($tanawul_bakery_theme_lay == 'Left align'){ ?>
            <a href="#" class="scrollup left"><i class="<?php echo esc_attr(get_theme_mod('tanawul_bakery_scroll_icon_changer','fas fa-long-arrow-alt-up')); ?>"></i><span class="screen-reader-text"><?php esc_html_e( 'Scroll Up', 'tanawul-bakery' ); ?></span></a>
        <?php }else if($tanawul_bakery_theme_lay == 'Center align'){ ?>
            <a href="#" class="scrollup center"><i class="<?php echo esc_attr(get_theme_mod('tanawul_bakery_scroll_icon_changer','fas fa-long-arrow-alt-up')); ?>"></i><span class="screen-reader-text"><?php esc_html_e( 'Scroll Up', 'tanawul-bakery' ); ?></span></a>
        <?php }else{ ?>
            <a href="#" class="scrollup"><i class="<?php echo esc_attr(get_theme_mod('tanawul_bakery_scroll_icon_changer','fas fa-long-arrow-alt-up')); ?>"></i><span class="screen-reader-text"><?php esc_html_e( 'Scroll Up', 'tanawul-bakery' ); ?></span></a>
    <?php }?>
<?php }?>
<div class="site-info">
  <div class="container"> 
    <div class="row">
	<div class="col-lg-4 col-md-12 col-12 align-self-center"><?php tanawul_bakery_credit(); ?> <?php echo esc_html(get_theme_mod('tanawul_bakery_footer_text',__('By ThemesEye','tanawul-bakery'))); ?> </div>
     <div class="col-lg-4 col-md-12 col-12 align-self-center">
    <?php if (get_theme_mod('tanawul_bakery_show_footer_social_icon', true)){ ?>  
    <div class="socialicons col-lg-4 col-md-12 col-12 align-self-center">                       
        <?php if( get_theme_mod( 'tanawul_bakery_footer_facebook_url') != '') { ?>
			<a href="<?php echo esc_url( get_theme_mod( 'tanawul_bakery_footer_facebook_url','' ) ); ?>" target="_blank" ><i class="<?php echo esc_attr(get_theme_mod('tanawul_bakery_footer_facebook_icon','fab fa-facebook-f')); ?>" aria-hidden="true"></i><span class="screen-reader-text"><?php esc_html_e( 'Facebook','tanawul-bakery' );?></span></a>
		<?php } ?>
		<?php if( get_theme_mod( 'tanawul_bakery_footer_twitter_url') != '') { ?>
			<a href="<?php echo esc_url( get_theme_mod( 'tanawul_bakery_footer_twitter_url','' ) ); ?>" target="_blank"><i class="<?php echo esc_attr(get_theme_mod('tanawul_bakery_footer_twitter_icon','fab fa-twitter')); ?>"></i><span class="screen-reader-text"><?php esc_html_e( 'Twitter','tanawul-bakery' );?></span></a>
		<?php } ?>
		<?php if( get_theme_mod( 'tanawul_bakery_footer_youtube_url') != '') { ?>
			<a href="<?php echo esc_url( get_theme_mod( 'tanawul_bakery_footer_youtube_url','' ) ); ?>" target="_blank" ><i class="<?php echo esc_attr(get_theme_mod('tanawul_bakery_footer_youtube_icon','fab fa-youtube')); ?>"></i><span class="screen-reader-text"><?php esc_html_e( 'Youtube','tanawul-bakery' );?></span></a>
		<?php } ?>
		<?php if( get_theme_mod( 'tanawul_bakery_footer_linkedin_url') != '') { ?>
			<a href="<?php echo esc_url( get_theme_mod( 'tanawul_bakery_footer_linkedin_url','' ) ); ?>" target="_blank" ><i class="<?php echo esc_attr(get_theme_mod('tanawul_bakery_footer_linkedin_icon','fab fa-linkedin-in')); ?>"></i><span class="screen-reader-text"><?php esc_html_e( 'Linkedin','tanawul-bakery' );?></span></a>
		<?php } ?>
		<?php if( get_theme_mod( 'tanawul_bakery_footer_instagram_url') != '') { ?>
			<a href="<?php echo esc_url( get_theme_mod( 'tanawul_bakery_footer_instagram_url','' ) ); ?>" target="_blank" ><i class="<?php echo esc_attr(get_theme_mod('tanawul_bakery_footer_instagram_icon','fab fa-instagram')); ?>"></i><span class="screen-reader-text"><?php esc_html_e( 'Instagram','tanawul-bakery' );?></span></a>
		<?php } ?>

    </div>	
    <?php }?>
    </div>
    <div class="footer_text col-lg-4 col-md-12 col-12 align-self-center"><?php echo esc_html_e('Powered By WordPress','tanawul-bakery') ?></div>
  </div>  
  </div>
</div>