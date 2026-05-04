<?php
/**
 * Setup Wizard UI
 *
 * @package Portfolio_Filter_Gallery
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$step = isset( $step ) ? $step : 1;
$total_steps = 4;

// Define step content
$steps = array(
    1 => array(
        'title' => __( 'Welcome to Portfolio Filter Gallery!', 'portfolio-filter-gallery' ),
        'icon'  => 'dashicons-images-alt2',
    ),
    2 => array(
        'title' => __( 'Create Your First Gallery', 'portfolio-filter-gallery' ),
        'icon'  => 'dashicons-plus-alt',
    ),
    3 => array(
        'title' => __( 'Set Up Filters', 'portfolio-filter-gallery' ),
        'icon'  => 'dashicons-filter',
    ),
    4 => array(
        'title' => __( 'You\'re All Set!', 'portfolio-filter-gallery' ),
        'icon'  => 'dashicons-yes-alt',
    ),
);
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php esc_html_e( 'Portfolio Filter Gallery Setup', 'portfolio-filter-gallery' ); ?></title>
    <?php wp_head(); ?>
    
</head>
<body class="pfg-setup-body">
    <div class="pfg-wizard">
        <div class="pfg-wizard-header">
            <div class="pfg-wizard-logo">
                <span class="dashicons dashicons-images-alt2"></span>
            </div>
            <h1><?php esc_html_e( 'Portfolio Filter Gallery', 'portfolio-filter-gallery' ); ?></h1>
        </div>
        
        <div class="pfg-wizard-progress">
            <?php for ( $i = 1; $i <= $total_steps; $i++ ) : ?>
                <div class="pfg-wizard-progress-dot <?php echo esc_attr( $i === $step ? 'active' : '' ); ?> <?php echo esc_attr( $i < $step ? 'completed' : '' ); ?>"></div>
            <?php endfor; ?>
        </div>
        
        <div class="pfg-wizard-content">
            <div class="pfg-wizard-step-icon">
                <span class="dashicons <?php echo esc_attr( $steps[ $step ]['icon'] ); ?>"></span>
            </div>
            
            <?php if ( $step === 1 ) : ?>
                <h2><?php esc_html_e( 'Welcome!', 'portfolio-filter-gallery' ); ?></h2>
                <p><?php esc_html_e( 'Create stunning filterable galleries in minutes. Let\'s get you started with a quick setup.', 'portfolio-filter-gallery' ); ?></p>
                
                <div class="pfg-wizard-features">
                    <div class="pfg-wizard-feature">
                        <span class="dashicons dashicons-yes"></span>
                        <span><?php esc_html_e( 'Responsive Layouts', 'portfolio-filter-gallery' ); ?></span>
                    </div>
                    <div class="pfg-wizard-feature">
                        <span class="dashicons dashicons-yes"></span>
                        <span><?php esc_html_e( 'Smooth Filtering', 'portfolio-filter-gallery' ); ?></span>
                    </div>
                    <div class="pfg-wizard-feature">
                        <span class="dashicons dashicons-yes"></span>
                        <span><?php esc_html_e( 'Hover Effects', 'portfolio-filter-gallery' ); ?></span>
                    </div>
                    <div class="pfg-wizard-feature">
                        <span class="dashicons dashicons-yes"></span>
                        <span><?php esc_html_e( 'Easy Shortcodes', 'portfolio-filter-gallery' ); ?></span>
                    </div>
                </div>
                
            <?php elseif ( $step === 2 ) : ?>
                <h2><?php esc_html_e( 'Create Your First Gallery', 'portfolio-filter-gallery' ); ?></h2>
                <p><?php esc_html_e( 'Start by creating a new gallery. Add images, set a layout, and customize the appearance.', 'portfolio-filter-gallery' ); ?></p>
                
            <?php elseif ( $step === 3 ) : ?>
                <h2><?php esc_html_e( 'Set Up Filters', 'portfolio-filter-gallery' ); ?></h2>
                <p><?php esc_html_e( 'Create filter categories to organize your images. Visitors can filter the gallery by clicking these buttons.', 'portfolio-filter-gallery' ); ?></p>
                
            <?php elseif ( $step === 4 ) : ?>
                <h2><?php esc_html_e( 'You\'re All Set!', 'portfolio-filter-gallery' ); ?></h2>
                <p><?php esc_html_e( 'You\'re ready to create beautiful filterable galleries. Need help? Check out our documentation.', 'portfolio-filter-gallery' ); ?></p>
            <?php endif; ?>
        </div>
        
        <div class="pfg-wizard-actions">
            <?php if ( $step === 1 ) : ?>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=pfg-setup-wizard&step=2' ) ); ?>" class="pfg-wizard-btn pfg-wizard-btn-primary">
                    <?php esc_html_e( 'Get Started', 'portfolio-filter-gallery' ); ?>
                    <span class="dashicons dashicons-arrow-right-alt"></span>
                </a>
                
            <?php elseif ( $step === 2 ) : ?>
                <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=awl_filter_gallery' ) ); ?>" class="pfg-wizard-btn pfg-wizard-btn-primary">
                    <span class="dashicons dashicons-plus-alt"></span>
                    <?php esc_html_e( 'Create Gallery', 'portfolio-filter-gallery' ); ?>
                </a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=pfg-setup-wizard&step=3' ) ); ?>" class="pfg-wizard-btn pfg-wizard-btn-secondary">
                    <?php esc_html_e( 'Skip', 'portfolio-filter-gallery' ); ?>
                </a>
                
            <?php elseif ( $step === 3 ) : ?>
                <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awl_filter_gallery&page=pfg-filters' ) ); ?>" class="pfg-wizard-btn pfg-wizard-btn-primary">
                    <span class="dashicons dashicons-filter"></span>
                    <?php esc_html_e( 'Manage Filters', 'portfolio-filter-gallery' ); ?>
                </a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=pfg-setup-wizard&step=4' ) ); ?>" class="pfg-wizard-btn pfg-wizard-btn-secondary">
                    <?php esc_html_e( 'Skip', 'portfolio-filter-gallery' ); ?>
                </a>
                
            <?php elseif ( $step === 4 ) : ?>
                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                    <input type="hidden" name="action" value="pfg_wizard_complete">
                    <?php wp_nonce_field( 'pfg_wizard_complete' ); ?>
                    <button type="submit" class="pfg-wizard-btn pfg-wizard-btn-success">
                        <span class="dashicons dashicons-yes"></span>
                        <?php esc_html_e( 'Finish Setup', 'portfolio-filter-gallery' ); ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>
        
        <?php if ( $step < 4 ) : ?>
            <div class="pfg-wizard-skip" style="text-align: center; padding-bottom: 20px;">
                <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=pfg_wizard_skip' ), 'pfg_wizard_skip' ) ); ?>">
                    <?php esc_html_e( 'Skip setup wizard', 'portfolio-filter-gallery' ); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <?php wp_footer(); ?>
</body>
</html>
