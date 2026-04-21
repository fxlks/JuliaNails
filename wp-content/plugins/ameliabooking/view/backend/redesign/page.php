<?php defined('ABSPATH') or die('No script kiddies please!'); ?>

<style>
  .am-theme-dark {
    background-color: rgb(16, 22, 30);
  }
</style>

<script>
  const match = document.cookie.match(/(?:^|;\s*)amelia_theme=([^;]*)/);
  const theme = match?.[1]?.trim() || null;
  if (theme === 'dark') {
    document.body.classList.add('am-theme-dark');
  }
</script>

<script>
    window.wpAmeliaPluginAjaxURL = location.protocol === 'https:' ? '<?php echo AMELIA_ACTION_URL; ?>'.replace('http:', 'https:') : '<?php echo AMELIA_ACTION_URL; ?>';
    window.wpAmeliaPluginURL = location.protocol === 'https:' ? '<?php echo AMELIA_URL; ?>'.replace('http:', 'https:') : '<?php echo AMELIA_URL; ?>';
    window.wpAmeliaNonce = '<?php echo wp_create_nonce('ajax-nonce'); ?>';
    <?php
    $timeZones = json_encode(\DateTimeZone::listIdentifiers(\DateTimeZone::ALL));
    echo "var wpAmeliaTimeZones = $timeZones;";
    ?>
    var wpAmeliaSMSVendorId = '<?php echo AMELIA_SMS_VENDOR_ID; ?>';
    var wpAmeliaSMSIsSandbox = <?php echo AMELIA_SMS_IS_SANDBOX ? 'true' : 'false'; ?>;
    var wpAmeliaSMSProductId10 = '<?php echo AMELIA_SMS_PRODUCT_ID_10; ?>';
    var wpAmeliaSMSProductId20 = '<?php echo AMELIA_SMS_PRODUCT_ID_20; ?>';
    var wpAmeliaSMSProductId50 = '<?php echo AMELIA_SMS_PRODUCT_ID_50; ?>';
    var wpAmeliaSMSProductId100 = '<?php echo AMELIA_SMS_PRODUCT_ID_100; ?>';
    var wpAmeliaSMSProductId200 = '<?php echo AMELIA_SMS_PRODUCT_ID_200; ?>';
    var wpAmeliaSMSProductId500 = '<?php echo AMELIA_SMS_PRODUCT_ID_500; ?>';
</script>

<div id="amelia-redesign-page" data-page="<?php echo esc_attr($page); ?>">
    <?php
    $indexHtmlPath = __DIR__ . '/../../../redesign/dist/index.html';
    echo file_get_contents($indexHtmlPath);
    ?>
</div>
