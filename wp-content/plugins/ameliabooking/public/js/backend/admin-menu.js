jQuery(function ($) {
    var $upgradeLink = $('a[href="admin.php?page=wpamelia-upgrade"]');

    if ($upgradeLink.length) {
        $upgradeLink.attr('target', '_blank');
        $upgradeLink.attr('rel', 'noopener noreferrer');
    }
});
