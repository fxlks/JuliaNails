(function($) {
    'use strict';

    // Close admin bar menu when clicking booking submenu items
    $(document).on('click', '#wp-admin-bar-amelia-add-appointment a, #wp-admin-bar-amelia-add-package a, #wp-admin-bar-amelia-add-event a', function(e) {
        // Close the admin bar menu
        $('#wpadminbar li.menupop').removeClass('hover');
        
        // If we're already on the bookings page, just update the hash
        var currentPage = window.location.href.split('#')[0];
        var targetPage = this.href.split('#')[0];
        
        if (currentPage === targetPage) {
            e.preventDefault();
            var hash = this.href.split('#')[1];
            if (hash) {
                window.location.hash = hash;
            }
        }
    });

})(jQuery);
