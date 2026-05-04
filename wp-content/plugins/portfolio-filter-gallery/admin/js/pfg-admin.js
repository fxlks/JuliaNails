/**
 * Portfolio Filter Gallery - Admin JavaScript
 * 
 * @package Portfolio_Filter_Gallery
 * @version 2.0.0
 */

/**
 * Escape HTML special characters to prevent XSS in DOM insertions.
 *
 * @param {string} str Raw string.
 * @return {string} Escaped string safe for innerHTML.
 */
function escapeHtml(str) {
    if (typeof str !== 'string') return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

(function($) {
    'use strict';

    /**
     * Admin gallery editor functionality
     */
    const PFGAdmin = {

        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.initTabs();
            this.initSortable();
            this.initColorPickers();
            this.initRangeSliders();
            this.initMediaUploader();
            this.initConditionalFields();
        },

        /**
         * Initialize conditional field visibility
         * Shows/hides settings based on parent toggle state
         */
        initConditionalFields: function() {
            const self = this;
            
            // Process all conditional elements
            $('.pfg-conditional').each(function() {
                self.updateConditionalVisibility($(this));
            });
            
            // Listen for changes on checkboxes that control conditional fields
            $(document).on('change', 'input[type="checkbox"]', function() {
                const inputName = $(this).attr('name');
                if (!inputName) return;
                
                // Find all elements that depend on this checkbox
                $('.pfg-conditional[data-depends="' + inputName + '"]').each(function() {
                    self.updateConditionalVisibility($(this));
                });
            });
        },
        
        /**
         * Update visibility of a conditional element based on its controller
         */
        updateConditionalVisibility: function($element) {
            const depends = $element.data('depends');
            if (!depends) return;
            
            const $controller = $('input[name="' + depends + '"]');
            if (!$controller.length) return;
            
            if ($controller.is(':checked')) {
                $element.slideDown(200);
            } else {
                $element.slideUp(200);
            }
        },


        /**
         * Bind event handlers
         */
        bindEvents: function() {
            // Copy shortcode
            $(document).on('click', '.pfg-copy-shortcode', this.copyShortcode);

            // Delete image
            $(document).on('click', '.pfg-image-delete', this.deleteImage);

            // Edit image
            $(document).on('click', '.pfg-image-edit', this.editImage);

            // Toggle settings
            $(document).on('change', '.pfg-toggle input', this.handleToggle);

            // Filter actions
            $(document).on('click', '.pfg-add-filter', this.addFilter);
            $(document).on('click', '.pfg-filter-delete', this.deleteFilter);
            $(document).on('blur', '.pfg-filter-name-input', this.updateFilter);
        },

        /**
         * Initialize tabs
         */
        initTabs: function() {
            $('.pfg-tab').on('click', function(e) {
                e.preventDefault();
                
                const $this = $(this);
                const target = $this.data('tab');

                // Update tab buttons
                $this.siblings().removeClass('active');
                $this.addClass('active');

                // Update tab content
                $this.closest('.pfg-tabs-wrapper').find('.pfg-tab-content').removeClass('active');
                $('#' + target).addClass('active');
            });
        },

        /**
         * Initialize sortable image grid
         */
        initSortable: function() {
            if (!$.fn.sortable) return;

            $('.pfg-image-grid').sortable({
                items: '.pfg-image-item',
                cursor: 'move',
                opacity: 0.8,
                placeholder: 'pfg-image-placeholder',
                tolerance: 'pointer',
                update: function(event, ui) {
                    PFGAdmin.updateImageOrder();
                }
            });

            // Filters list
            $('.pfg-filters-list').sortable({
                items: '.pfg-filter-item',
                handle: '.pfg-filter-drag',
                cursor: 'move',
                opacity: 0.8,
                update: function(event, ui) {
                    PFGAdmin.updateFilterOrder();
                }
            });
        },

        initColorPickers: function() {
            if (!$.fn.wpColorPicker) return;

            $('.pfg-color-input').wpColorPicker({
                change: function(event, ui) {
                    // Update the input value when color is changed via drag
                    $(this).val(ui.color.toCSS()).trigger('change');
                },
                clear: function() {
                    $(this).trigger('change');
                }
            });
        },

        /**
         * Initialize range sliders
         */
        initRangeSliders: function() {
            $('.pfg-range input[type="range"]').on('input', function() {
                const $this = $(this);
                const value = $this.val();
                const suffix = $this.data('suffix') || '';
                
                $this.closest('.pfg-range').find('.pfg-range-value').text(value + suffix);
            });
        },

        /**
         * Initialize media uploader
         */
        initMediaUploader: function() {
            let mediaFrame;

            $(document).on('click', '.pfg-upload-area, .pfg-add-images', function(e) {
                e.preventDefault();

                if (mediaFrame) {
                    mediaFrame.open();
                    return;
                }

                mediaFrame = wp.media({
                    title: pfgAdmin.i18n.selectImages,
                    button: {
                        text: pfgAdmin.i18n.useSelected
                    },
                    multiple: true,
                    library: {
                        type: 'image'
                    }
                });

                mediaFrame.on('select', function() {
                    const selection = mediaFrame.state().get('selection');
                    const imageIds = [];

                    selection.each(function(attachment) {
                        imageIds.push(attachment.id);
                    });

                    if (imageIds.length) {
                        PFGAdmin.uploadImages(imageIds);
                    }
                });

                mediaFrame.open();
            });
            
            // Initialize drag and drop file upload
            this.initDragDropUpload();
        },
        
        /**
         * Initialize drag and drop file upload
         */
        initDragDropUpload: function() {
            const $uploadArea = $('.pfg-upload-area');
            
            if (!$uploadArea.length) return;
            
            // Prevent default behavior for drag events on the whole document
            $(document).on('dragover dragleave drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
            });
            
            // Highlight upload area on drag over
            $uploadArea.on('dragover dragenter', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('pfg-dragover');
            });
            
            // Remove highlight on drag leave
            $uploadArea.on('dragleave dragend drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('pfg-dragover');
            });
            
            // Handle file drop
            $uploadArea.on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('pfg-dragover');
                
                const files = e.originalEvent.dataTransfer.files;
                
                if (files.length > 0) {
                    PFGAdmin.uploadDroppedFiles(files);
                }
            });
        },
        
        /**
         * Upload dropped files via AJAX
         */
        uploadDroppedFiles: function(files) {
            const $grid = $('.pfg-image-grid');
            const $uploadArea = $('.pfg-upload-area');
            
            // Show loading state
            $uploadArea.addClass('pfg-uploading');
            $uploadArea.find('.pfg-upload-text').text('Uploading...');
            
            // Create FormData object
            const formData = new FormData();
            formData.append('action', 'pfg_upload_dropped_files');
            formData.append('security', pfgAdmin.nonce);
            formData.append('gallery_id', pfgAdmin.galleryId);
            
            // Add all image files
            let imageCount = 0;
            for (let i = 0; i < files.length; i++) {
                if (files[i].type.match(/^image\//)) {
                    formData.append('files[]', files[i]);
                    imageCount++;
                }
            }
            
            if (imageCount === 0) {
                PFGAdmin.showNotice('error', 'Please drop only image files.');
                PFGAdmin.resetUploadArea();
                return;
            }
            
            $.ajax({
                url: pfgAdmin.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $grid.addClass('pfg-loading');
                },
                success: function(response) {
                    if (response.success) {
                        PFGAdmin.refreshImageGrid(response.data.images);
                        PFGAdmin.showNotice('success', response.data.message || 'Images uploaded successfully!');
                    } else {
                        PFGAdmin.showNotice('error', response.data.message || 'Upload failed.');
                    }
                },
                error: function() {
                    PFGAdmin.showNotice('error', 'Upload failed. Please try again.');
                },
                complete: function() {
                    $grid.removeClass('pfg-loading');
                    PFGAdmin.resetUploadArea();
                }
            });
        },
        
        /**
         * Reset upload area after upload
         */
        resetUploadArea: function() {
            const $uploadArea = $('.pfg-upload-area');
            $uploadArea.removeClass('pfg-uploading');
            $uploadArea.find('.pfg-upload-text').text('Drag & drop images here or click to upload');
        },

        /**
         * Upload images via AJAX
         */
        uploadImages: function(imageIds) {
            const $grid = $('.pfg-image-grid');

            $.ajax({
                url: pfgAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'pfg_upload_images',
                    security: pfgAdmin.nonce,
                    gallery_id: pfgAdmin.galleryId,
                    image_ids: imageIds
                },
                beforeSend: function() {
                    $grid.addClass('pfg-loading');
                },
                success: function(response) {
                    if (response.success) {
                        PFGAdmin.refreshImageGrid(response.data.images);
                    } else {
                        PFGAdmin.showNotice('error', response.data.message);
                    }
                },
                error: function() {
                    PFGAdmin.showNotice('error', pfgAdmin.i18n.error);
                },
                complete: function() {
                    $grid.removeClass('pfg-loading');
                }
            });
        },

        /**
         * Delete image
         */
        deleteImage: function(e) {
            e.preventDefault();

            if (!confirm(pfgAdmin.i18n.confirmDelete)) {
                return;
            }

            const $item = $(this).closest('.pfg-image-item');
            const imageId = $item.data('id');

            $.ajax({
                url: pfgAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'pfg_remove_image',
                    security: pfgAdmin.nonce,
                    gallery_id: pfgAdmin.galleryId,
                    image_id: imageId
                },
                success: function(response) {
                    if (response.success) {
                        // Remove from master array BEFORE removing from DOM
                        if (typeof window.pfgRemoveImageFromMaster === 'function') {
                            window.pfgRemoveImageFromMaster(imageId);
                        }
                        
                        $item.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Mark as structurally modified
                            if (typeof window.pfgMarkImagesModified === 'function') {
                                window.pfgMarkImagesModified();
                            }
                            
                            // Update pagination UI
                            if (typeof window.pfgUpdatePaginationUI === 'function') {
                                window.pfgUpdatePaginationUI();
                            }
                        });
                    } else {
                        PFGAdmin.showNotice('error', response.data.message);
                    }
                }
            });
        },

        /**
         * Edit image (open modal)
         */
        editImage: function(e) {
            e.preventDefault();

            const $item = $(this).closest('.pfg-image-item');
            const imageId = $item.data('id');

            // TODO: Open edit modal with image details
        },

        /**
         * Update image order
         */
        updateImageOrder: function() {
            const order = [];
            
            $('.pfg-image-item').each(function() {
                order.push($(this).data('id'));
            });


            $.ajax({
                url: pfgAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'pfg_reorder_images',
                    security: pfgAdmin.nonce,
                    gallery_id: pfgAdmin.galleryId,
                    order: order
                }
            });
            
            // Reorder master array to ensure save works correctly
            if (typeof window.pfgReorderMasterArray === 'function') {
                window.pfgReorderMasterArray(order);
            } else {
            }
            
            // Mark images as modified for chunked save
            if (typeof window.pfgMarkImagesModified === 'function') {
                window.pfgMarkImagesModified();
            }
        },

        /**
         * Copy shortcode to clipboard
         */
        copyShortcode: function(e) {
            e.preventDefault();

            const $btn = $(this);
            const $code = $($btn.data('clipboard-target'));
            const text = $code.text();

            // Try modern clipboard API first, fallback to execCommand
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function() {
                    PFGAdmin.showCopySuccess($btn);
                }).catch(function() {
                    PFGAdmin.fallbackCopy(text, $btn);
                });
            } else {
                PFGAdmin.fallbackCopy(text, $btn);
            }
        },

        /**
         * Fallback copy using execCommand for non-HTTPS
         */
        fallbackCopy: function(text, $btn) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-9999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                document.execCommand('copy');
                PFGAdmin.showCopySuccess($btn);
            } catch (err) {
            }
            
            document.body.removeChild(textArea);
        },

        /**
         * Show copy success feedback
         */
        showCopySuccess: function($btn) {
            const originalHtml = $btn.html();
            $btn.text('Copied!');
            
            setTimeout(function() {
                $btn.html('<span class="dashicons dashicons-clipboard"></span> Copy');
            }, 2000);
        },

        /**
         * Handle toggle change
         */
        handleToggle: function() {
            const $toggle = $(this);
            const $related = $($toggle.data('toggle-related'));

            if ($toggle.is(':checked')) {
                $related.slideDown(200);
            } else {
                $related.slideUp(200);
            }
        },

        /**
         * Add new filter
         */
        addFilter: function(e) {
            e.preventDefault();

            const $input = $('.pfg-new-filter-input');
            const name = $input.val().trim();

            if (!name) {
                $input.focus();
                return;
            }

            $.ajax({
                url: pfgAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'pfg_add_filter',
                    security: pfgAdmin.nonce,
                    name: name
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        PFGAdmin.showNotice('error', response.data.message);
                    }
                }
            });
        },

        /**
         * Delete filter
         */
        deleteFilter: function(e) {
            e.preventDefault();

            if (!confirm(pfgAdmin.i18n.confirmDelete)) {
                return;
            }

            const $item = $(this).closest('.pfg-filter-item');
            const filterId = $item.data('id');

            $.ajax({
                url: pfgAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'pfg_delete_filter',
                    security: pfgAdmin.nonce,
                    filter_id: filterId
                },
                success: function(response) {
                    if (response.success) {
                        $item.fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        PFGAdmin.showNotice('error', response.data.message);
                    }
                }
            });
        },

        /**
         * Update filter name
         */
        updateFilter: function() {
            const $input = $(this);
            const $item = $input.closest('.pfg-filter-item');
            const filterId = $item.data('id');
            const name = $input.val().trim();

            if (!name) {
                return;
            }

            $.ajax({
                url: pfgAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'pfg_update_filter',
                    security: pfgAdmin.nonce,
                    filter_id: filterId,
                    name: name
                }
            });
        },

        /**
         * Update filter order
         */
        updateFilterOrder: function() {
            const order = [];
            
            $('.pfg-filter-item').each(function() {
                order.push($(this).data('id'));
            });

            $.ajax({
                url: pfgAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'pfg_reorder_filters',
                    security: pfgAdmin.nonce,
                    order: order
                }
            });
        },

        /**
         * Refresh image grid
         */
        refreshImageGrid: function(images) {
            const $grid = $('.pfg-image-grid');
            
            // Remove "no images" message if present
            $grid.find('.pfg-no-images').remove();
            
            // Get current highest index
            let currentIndex = $('.pfg-image-item').length;
            
            // Get reference to masterImagesArray for adding new images
            const masterImages = (typeof window.pfgGetMasterImages === 'function') ? window.pfgGetMasterImages() : null;
            
            images.forEach(function(image) {
                const html = PFGAdmin.getImageItemHtml(image, currentIndex);
                $grid.append(html);
                currentIndex++;
                
                // Push new image into masterImagesArray so it's included on save
                if (masterImages) {
                    masterImages.push({
                        id: image.id,
                        title: image.title || '',
                        alt: image.alt || '',
                        description: image.description || '',
                        link: image.link || '',
                        type: image.type || 'image',
                        filters: image.filters || '',
                        original_id: image.original_id || image.id
                    });
                }
            });
            
            // Show bulk actions bar if we have images
            if ($('.pfg-image-item').length > 0) {
                $('#pfg-bulk-actions').css('display', 'flex');
            }
            
            // Update pagination counts
            if (typeof window.pfgUpdatePaginationUI === 'function') {
                window.pfgUpdatePaginationUI();
            }
            
            // Mark images as modified for chunked save
            if (typeof window.pfgMarkImagesModified === 'function') {
                window.pfgMarkImagesModified();
            }
        },

        /**
         * Get image item HTML
         */
        getImageItemHtml: function(image, index) {
            if (typeof index === 'undefined') {
                index = $('.pfg-image-item').length;
            }
            
            // Handle alt text and description from image data
            var altText = image.alt || '';
            var description = image.description || '';
            
            var safeTitle = escapeHtml(image.title);
            var safeAlt = escapeHtml(altText);
            var safeDesc = escapeHtml(description);
            var safeThumb = escapeHtml(image.thumbnail);
            var safeId = escapeHtml(String(image.id));
            var safeIndex = parseInt(index, 10);

            return '<div class="pfg-image-item" data-id="' + safeId + '" data-index="' + safeIndex + '">' +
                '<label class="pfg-image-checkbox" style="position: absolute; top: 8px; left: 8px; z-index: 10;">' +
                    '<input type="checkbox" class="pfg-image-select" style="width: 18px; height: 18px; cursor: pointer;">' +
                '</label>' +
                '<img src="' + safeThumb + '" alt="' + safeTitle + '" class="pfg-image-thumb" loading="lazy">' +
                '<div class="pfg-image-actions">' +
                    '<button type="button" class="pfg-image-action pfg-image-edit" title="Edit"><span class="dashicons dashicons-edit"></span></button>' +
                    '<button type="button" class="pfg-image-action pfg-image-delete" title="Delete"><span class="dashicons dashicons-trash"></span></button>' +
                '</div>' +
                '<div class="pfg-image-info">' +
                    '<p class="pfg-image-title">' + safeTitle + '</p>' +
                '</div>' +
                '<input type="hidden" name="pfg_images[' + safeIndex + '][id]" value="' + safeId + '">' +
                '<input type="hidden" name="pfg_images[' + safeIndex + '][title]" value="' + safeTitle + '">' +
                '<input type="hidden" name="pfg_images[' + safeIndex + '][alt]" value="' + safeAlt + '">' +
                '<input type="hidden" name="pfg_images[' + safeIndex + '][description]" value="' + safeDesc + '">' +
                '<input type="hidden" name="pfg_images[' + safeIndex + '][link]" value="">' +
                '<input type="hidden" name="pfg_images[' + safeIndex + '][type]" value="image">' +
                '<input type="hidden" name="pfg_images[' + safeIndex + '][filters]" value="">' +
                '<input type="hidden" name="pfg_images[' + safeIndex + '][original_id]" value="' + safeId + '">' +
            '</div>';
        },

        /**
         * Show admin notice
         */
        showNotice: function(type, message) {
            var safeType = escapeHtml(type);
            var safeMessage = escapeHtml(message);
            const $notice = $('<div class="pfg-notice pfg-notice-' + safeType + '"><span class="pfg-notice-content">' + safeMessage + '</span></div>');

            $('.pfg-admin-wrap').prepend($notice);

            setTimeout(function() {
                $notice.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        PFGAdmin.init();
    });

    // Expose to global scope
    window.PFGAdmin = PFGAdmin;

})(jQuery);

/* Extracted from meta-box-settings.php */
jQuery(document).ready(function($) {
    var templateSettings = pfgAdmin.templateSettings;

    $('.pfg-template-card').on('click', function() {
        var $this = $(this);
        
        // Skip selection if template is already selected
        if ($this.hasClass('selected')) {
            return false;
        }
        
        var templateId = $this.data('template');
        var layoutType = $this.data('layout') || 'grid';
        
        // Update UI
        $('.pfg-template-card').removeClass('selected');
        $this.addClass('selected');
        
        // Update hidden inputs
        $('#pfg-template').val(templateId);
        
        // Set flag before triggering change to prevent template deselection
        isTemplateClick = true;
        
        // Update Layout Type dropdown to match template
        $('#pfg-layout').val(layoutType).trigger('change');
        
        // Apply template defaults to sliders and inputs
        if (templateSettings[templateId]) {
            var ts = templateSettings[templateId];
            
            // Helper: update a range slider and its display value
            function applySlider(name, value, suffix) {
                var $slider = $('input[name="pfg_settings[' + name + ']"]');
                if ($slider.length) {
                    $slider.val(value).trigger('input');
                    $slider.siblings('.pfg-range-value').text(value + (suffix || ''));
                }
            }
            
            // Apply columns (desktop, tablet, mobile)
            applySlider('columns', ts.columns, '');
            applySlider('columns_md', ts.columns_md, '');
            applySlider('columns_sm', ts.columns_sm, '');
            
            // Apply gap and border radius
            applySlider('gap', ts.gap, 'px');
            applySlider('border_radius', ts.border_radius, 'px');
            

            
            // Apply hover effect
            if (ts.hover_effect) {
                $('select[name="pfg_settings[hover_effect]"]').val(ts.hover_effect);
            }
            
            // Apply show title
            var $showTitle = $('input[name="pfg_settings[show_title]"]');
            if ($showTitle.length) {
                $showTitle.prop('checked', !!ts.show_title);
            }
            
            // Apply title position
            if (ts.title_position) {
                $('select[name="pfg_settings[title_position]"]').val(ts.title_position);
            }
            
            showTemplateNotice('Template applied: ' + $this.find('.pfg-template-name').text());
        }
        
        // Update layout options visibility (without triggering the change handler conflict)
        updateLayoutOptions();
    });
    
    // When Layout Type dropdown changes manually
    $('#pfg-layout').on('change', function() {
        // Skip notification if this change was triggered by template click
        if (isTemplateClick) {
            isTemplateClick = false;
            updateLayoutOptions();
            return;
        }
        
        var selectedLayout = $(this).val();
        var layoutNames = {
            'grid': 'Grid',
            'masonry': 'Masonry', 
            'justified': 'Justified',
            'packed': 'Packed'
        };
        
        // Keep template selected - it's a style preset that works with any layout
        showTemplateNotice('Layout changed to ' + (layoutNames[selectedLayout] || selectedLayout));
        
        updateLayoutOptions();
    });
    
    // Show brief notification when template settings change
    function showTemplateNotice(message) {
        var $notice = $('<div class="pfg-template-notice">' + escapeHtml(message) + '</div>');
        $('.pfg-template-grid').after($notice);
        setTimeout(function() {
            $notice.fadeOut(300, function() { $(this).remove(); });
        }, 2500);
    }
    
    // Range slider value update
    $('.pfg-range input[type="range"]').on('input', function() {
        var suffix = $(this).data('suffix') || '';
        $(this).siblings('.pfg-range-value').text(this.value + suffix);
    });
    
    // Device toggle for responsive columns
    $('.pfg-device-btn').on('click', function(e) {
        // Desktop click (normal behavior)
        var $wrapper = $(this).closest('.pfg-responsive-columns');
        $wrapper.find('.pfg-device-btn').removeClass('active');
        $(this).addClass('active');
    });
    
    // Tab switching
    $('.pfg-tab').on('click', function() {
        var tabId = $(this).data('tab');
        
        $('.pfg-tab').removeClass('active');
        $(this).addClass('active');
        
        $('.pfg-tab-content').removeClass('active');
        $('#' + tabId).addClass('active');
    });
    
    // Layout type conditional options
    function updateLayoutOptions() {
        var layout = $('#pfg-layout').val();
        
        $('.pfg-layout-option').each(function() {
            var allowedLayouts = $(this).data('layouts').split(',');
            if (allowedLayouts.includes(layout)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
    
    // Initial state
    updateLayoutOptions();
    
    // On layout change
    $('#pfg-layout').on('change', updateLayoutOptions);
    

    

    
    // Watermark toggle
    $('#pfg-watermark-enabled').on('change', function() {
        if ($(this).is(':checked')) {
            $('.pfg-watermark-options').show();
            updateWatermarkType();
        } else {
            $('.pfg-watermark-options').hide();
        }
    });
    
    // Watermark type change
    $('#pfg-watermark-type').on('change', function() {
        updateWatermarkType();
    });
    
    function updateWatermarkType() {
        var type = $('#pfg-watermark-type').val();
        if (type === 'text') {
            $('.pfg-watermark-text-options').show();
            $('.pfg-watermark-image-options').hide();
        } else {
            $('.pfg-watermark-text-options').hide();
            $('.pfg-watermark-image-options').show();
        }
    }
    
    // Watermark image upload
    $('#pfg-upload-watermark').on('click', function(e) {
        e.preventDefault();
        var mediaUploader = wp.media({
            title: 'Select Watermark Image',
            button: { text: 'Use as Watermark' },
            multiple: false,
            library: { type: 'image' }
        });
        
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#pfg-watermark-image-url').val(attachment.url);
        });
        
        mediaUploader.open();
    });
});

/* Extracted from page-filters.php */
jQuery(document).ready(function($) {
    
    // ============================================
    // Helper Functions for Live Updates
    // ============================================
    
    // Generate slug from name - Unicode-aware to support Japanese, Chinese, etc.
    function generateSlug(name) {
        // Use Unicode-aware lowercase (works in modern browsers)
        var slug = name.toLowerCase();
        
        // Replace anything that's not a letter, number, or allowed punctuation with dash
        // \p{L} matches any letter, \p{N} matches any number (Unicode-aware)
        try {
            // Modern browsers support Unicode property escapes
            slug = slug.replace(/[^\p{L}\p{N}]+/gu, '-');
        } catch (e) {
            // Fallback for older browsers: keep basic ASCII + common Unicode ranges
            slug = slug.replace(/[^a-z0-9\u3000-\u303f\u3040-\u309f\u30a0-\u30ff\u4e00-\u9faf\u0600-\u06ff\u0400-\u04ff]+/gi, '-');
        }
        
        // Remove leading/trailing dashes and collapse multiple dashes
        slug = slug.replace(/-+/g, '-').replace(/^-|-$/g, '');
        
        return slug || 'filter'; // Fallback if empty
    }
    
    // Get all filters data from table
    function getFiltersData() {
        var filters = [];
        $('#pfg-filters-list tbody tr').each(function() {
            var $row = $(this);
            filters.push({
                id: $row.data('id'),
                name: $row.find('.pfg-editable-name').val(),
                parent: $row.data('parent') || '',
                color: $row.find('.pfg-row-color').val() || '#94a3b8'
            });
        });
        return filters;
    }
    
    // Build hierarchy tree from flat filters array
    // Added safety checks to prevent infinite recursion from circular references
    function buildTree(filters, parentId, visited, depth) {
        parentId = parentId || '';
        visited = visited || {};
        depth = depth || 0;
        
        // Safety: prevent infinite recursion with max depth limit
        if (depth > 10) {
            return [];
        }
        
        var children = [];
        filters.forEach(function(filter) {
            // Skip if already visited (circular reference detection)
            if (visited[filter.id]) {
                return;
            }
            
            if ((filter.parent || '') === parentId) {
                // Skip if filter is its own parent
                if (filter.id === filter.parent) {
                    return;
                }
                
                var node = $.extend({}, filter);
                var newVisited = $.extend({}, visited);
                newVisited[filter.id] = true;
                node.children = buildTree(filters, filter.id, newVisited, depth + 1);
                children.push(node);
            }
        });
        return children;
    }
    
    // Render hierarchy tree to HTML
    function renderTreeHTML(tree, depth) {
        depth = depth || 0;
        var html = '';
        tree.forEach(function(filter) {
            var indent = depth > 0 ? ' style="margin-left: ' + (depth * 16) + 'px"' : '';
            var prefix = depth > 0 ? '<span class="pfg-tree-line">└</span> ' : '';
            var color = filter.color || '#94a3b8';
            
            html += '<div class="pfg-tree-item" data-id="' + filter.id + '"' + indent + '>';
            html += prefix;
            html += '<span class="pfg-tree-dot" style="background:' + color + '"></span>';
            html += '<span class="pfg-tree-name">' + $('<div>').text(filter.name).html() + '</span>';
            html += '</div>';
            
            if (filter.children && filter.children.length > 0) {
                html += renderTreeHTML(filter.children, depth + 1);
            }
        });
        return html;
    }
    
    // Rebuild the hierarchy chart
    function rebuildHierarchy() {
        var filters = getFiltersData();
        var tree = buildTree(filters, '');
        var html = renderTreeHTML(tree, 0);
        
        if (html) {
            $('.pfg-hierarchy-tree').html(html);
            $('.pfg-hierarchy-chart').show();
        } else {
            $('.pfg-hierarchy-chart').hide();
        }
    }
    
    // Update all parent dropdowns with current filter names
    function updateParentDropdowns() {
        var filters = getFiltersData();
        
        // For each parent dropdown, update the option text
        $('.pfg-parent-select').each(function() {
            var $select = $(this);
            var currentFilterId = $select.closest('.pfg-filter-row').data('id');
            
            $select.find('option').each(function() {
                var $option = $(this);
                var optionId = $option.val();
                
                if (optionId) {
                    // Find the filter with this ID
                    for (var i = 0; i < filters.length; i++) {
                        if (filters[i].id === optionId) {
                            $option.text(filters[i].name);
                            break;
                        }
                    }
                }
            });
        });
    }
    
    // Update slug display for a filter (when name changes, auto-generate slug)
    function updateSlugDisplay($row, newName) {
        var newSlug = generateSlug(newName);
        var $slugInput = $row.find('.pfg-editable-slug');
        var currentFilterId = String($row.data('id'));
        
        // Check if this slug already exists in other filters
        var existingSlugs = [];
        $('.pfg-editable-slug').each(function() {
            var $input = $(this);
            var filterId = String($input.closest('.pfg-filter-row').data('id'));
            if (filterId !== currentFilterId) {
                existingSlugs.push($input.val());
            }
        });
        
        // Generate unique slug if duplicate exists
        if (existingSlugs.indexOf(newSlug) > -1) {
            var counter = 2;
            var baseSlug = newSlug;
            while (existingSlugs.indexOf(newSlug) > -1) {
                newSlug = baseSlug + '-' + counter;
                counter++;
            }
        }
        
        $slugInput.val(newSlug);
        $slugInput.data('original', newSlug);
    }
    
    // ============================================
    // Event Handlers
    // ============================================
    
    // Initialize sortable table
    if ($.fn.sortable) {
        $('#pfg-filters-list tbody').sortable({
            handle: '.pfg-drag-handle',
            placeholder: 'ui-sortable-placeholder',
            axis: 'y',
            helper: function(e, tr) {
                var $helper = tr.clone();
                $helper.children('td').each(function(index) {
                    $(this).width(tr.children('td').eq(index).width());
                });
                return $helper;
            },
            update: function() {
                var order = [];
                $('#pfg-filters-list tbody tr').each(function() {
                    order.push($(this).data('id'));
                });
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'pfg_reorder_filters',
                        security: $('#pfg_filter_nonce').val(),
                        order: order
                    },
                    success: function() {
                        rebuildHierarchy();
                    }
                });
            }
        });
    }
    
    // Search filters
    $('#pfg-filter-search').on('input', function() {
        var query = $(this).val().toLowerCase();
        $('.pfg-filter-row').each(function() {
            var name = $(this).find('.pfg-editable-name').val().toLowerCase();
            $(this).toggle(name.indexOf(query) > -1);
        });
    });
    
    // Save name on blur - with live updates
    $('.pfg-editable-name').on('blur', function() {
        var $input = $(this);
        var $row = $input.closest('.pfg-filter-row');
        var filterId = $row.data('id');
        var newName = $input.val();
        
        // Immediately update slug display (generates unique slug if needed)
        updateSlugDisplay($row, newName);
        
        // Get the newly generated slug
        var newSlug = $row.find('.pfg-editable-slug').val();
        
        // Immediately update parent dropdowns
        updateParentDropdowns();
        
        // Immediately rebuild hierarchy
        rebuildHierarchy();
        
        // Save name to server
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'pfg_update_filter',
                security: $('#pfg_filter_nonce').val(),
                filter_id: filterId,
                name: newName
            }
        });
        
        // Also save slug to server (since server no longer auto-generates)
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'pfg_update_filter_slug',
                security: $('#pfg_filter_nonce').val(),
                filter_id: filterId,
                slug: newSlug
            }
        });
    });
    
    // Save slug on blur - with duplicate detection
    $('.pfg-editable-slug').on('blur', function() {
        var $input = $(this);
        var $row = $input.closest('.pfg-filter-row');
        var filterId = $row.data('id');
        var newSlug = $input.val().trim();
        var originalSlug = $input.data('original');
        
        // Generate proper slug format
        newSlug = generateSlug(newSlug);
        $input.val(newSlug);
        
        // Check for duplicates
        var isDuplicate = false;
        var currentFilterId = String(filterId);
        $('.pfg-editable-slug').each(function() {
            var $otherInput = $(this);
            var otherId = String($otherInput.closest('.pfg-filter-row').data('id'));
            if (otherId !== currentFilterId && $otherInput.val() === newSlug) {
                isDuplicate = true;
                return false;
            }
        });
        
        if (isDuplicate) {
            // Generate unique slug
            newSlug = generateUniqueSlug(newSlug, filterId);
            $input.val(newSlug);
            $input.addClass('pfg-slug-warning');
            setTimeout(function() { $input.removeClass('pfg-slug-warning'); }, 2000);
        }
        
        // Only save if changed
        if (newSlug !== originalSlug) {
            $input.data('original', newSlug);
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'pfg_update_filter_slug',
                    security: $('#pfg_filter_nonce').val(),
                    filter_id: filterId,
                    slug: newSlug
                }
            });
        }
    });
    
    // Generate unique slug by appending number suffix
    function generateUniqueSlug(baseSlug, currentFilterId) {
        var existingSlugs = [];
        var currentId = String(currentFilterId);
        $('.pfg-editable-slug').each(function() {
            var $input = $(this);
            var filterId = String($input.closest('.pfg-filter-row').data('id'));
            if (filterId !== currentId) {
                existingSlugs.push($input.val());
            }
        });
        
        var counter = 2;
        var newSlug = baseSlug;
        while (existingSlugs.indexOf(newSlug) > -1) {
            newSlug = baseSlug + '-' + counter;
            counter++;
        }
        return newSlug;
    }
    
    // Save parent on change - with live updates
    $('.pfg-parent-select').on('change', function() {
        var $row = $(this).closest('.pfg-filter-row');
        var filterId = $row.data('id');
        var parentId = $(this).val();
        
        // Update row data attribute
        $row.data('parent', parentId);
        $row.attr('data-parent', parentId);
        
        // Immediately rebuild hierarchy
        rebuildHierarchy();
        
        // Save to server
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'pfg_update_filter_parent',
                security: $('#pfg_filter_nonce').val(),
                filter_id: filterId,
                parent_id: parentId
            }
        });
    });
    
    // Save color on change - with live updates
    $('.pfg-row-color').on('change', function() {
        var $row = $(this).closest('.pfg-filter-row');
        var filterId = $row.data('id');
        var color = $(this).val();
        
        // Update the visible color label
        $(this).siblings('.pfg-color-label').css('background-color', color);
        
        // Immediately rebuild hierarchy to update dot color
        rebuildHierarchy();
        
        // Save to server
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'pfg_update_filter_color',
                security: $('#pfg_filter_nonce').val(),
                filter_id: filterId,
                color: color
            }
        });
    });
    
    // Update add form color label on change
    $('#add-filter-color').on('change input', function() {
        $(this).siblings('.pfg-color-label').css('background-color', $(this).val());
    });
    
    // Delete filter - with live updates
    $('.pfg-btn-delete').on('click', function() {
        var $row = $(this).closest('.pfg-filter-row');
        var filterId = $row.data('id');
        var filterName = $row.find('.pfg-editable-name').val();
        
        if (confirm(''+pfgAdmin.i18n.delete_filter+' "' + filterName + '"?')) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'pfg_delete_filter',
                    security: $('#pfg_filter_nonce').val(),
                    filter_id: filterId
                },
                success: function(response) {
                    if (response.success) {
                        $row.fadeOut(200, function() { 
                            $(this).remove();
                            
                            // Update count
                            var count = $('.pfg-filter-row').length;
                            $('.pfg-stat-number').text(count);
                            
                            // Remove from parent dropdowns
                            $('.pfg-parent-select option[value="' + filterId + '"]').remove();
                            
                            // Rebuild hierarchy
                            rebuildHierarchy();
                            
                            // Hide hierarchy chart if no filters left
                            if (count === 0) {
                                $('.pfg-hierarchy-chart').hide();
                            }
                        });
                    }
                }
            });
        }
    });
    
    // Delete all filters with confirmation
    $('#pfg-delete-all-filters').on('click', function() {
        var filterCount = $('#pfg-filters-list tbody tr').length;
        
        if (confirm(''+pfgAdmin.i18n.delete_all_filters+'\n\n' + filterCount + ' '+pfgAdmin.i18n.filters_deleted+'')) {
            var $btn = $(this);
            var originalText = $btn.html();
            
            $btn.html('<span class="dashicons dashicons-update spin"></span>').prop('disabled', true);
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'pfg_delete_all_filters',
                    security: $('#pfg_filter_nonce').val()
                },
                success: function(response) {
                    if (response.success) {
                        // Reload page to show empty state
                        window.location.reload();
                    } else {
                        alert(response.data.message || pfgAdmin.i18n.failed_delete);
                        $btn.html(originalText).prop('disabled', false);
                    }
                },
                error: function() {
                    alert(pfgAdmin.i18n.server_error);
                    $btn.html(originalText).prop('disabled', false);
                }
            });
        }
    });
    
    // Add new filter
    $('#pfg-add-filter-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $btn = $form.find('button[type="submit"]');
        var originalText = $btn.html();
        
        $btn.html('<span class="dashicons dashicons-update spin"></span> '+pfgAdmin.i18n.adding+'');
        $btn.prop('disabled', true);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'pfg_add_filter',
                security: $('#pfg_filter_nonce').val(),
                name: $form.find('[name="filter_name"]').val(),
                parent_id: $form.find('[name="parent_id"]').val(),
                color: $form.find('[name="filter_color"]').val()
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data || pfgAdmin.i18n.error_adding);
                    $btn.html(originalText);
                    $btn.prop('disabled', false);
                }
            },
            error: function() {
                alert(pfgAdmin.i18n.error_adding);
                $btn.html(originalText);
                $btn.prop('disabled', false);
            }
        });
    });

});

/* Extracted from page-docs.php */
jQuery(document).ready(function($) {
    // Smooth scroll for docs nav
    $('.pfg-doc-link').on('click', function(e) {
        e.preventDefault();
        var target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 50
            }, 500);
            $('.pfg-doc-link').removeClass('active');
            $(this).addClass('active');
        }
    });
    
    // Update active nav on scroll
    $(window).on('scroll', function() {
        var scrollPos = $(window).scrollTop() + 100;
        
        $('.pfg-doc-section').each(function() {
            var top = $(this).offset().top;
            var bottom = top + $(this).outerHeight();
            var id = $(this).attr('id');
            
            if (scrollPos >= top && scrollPos < bottom) {
                $('.pfg-doc-link').removeClass('active');
                $('.pfg-doc-link[href="#' + id + '"]').addClass('active');
            }
        });
    });
});

/* Extracted from meta-box-images.php */
jQuery(document).ready(function($) {

    });

/* Extracted from meta-box-images.php */
jQuery(document).ready(function($) {
        // Defer sort setup until master array and pagination are ready
        setTimeout(function() {
            var $settingsSelect = $('select[name="pfg_settings[sort_order]"]');
            var $imagesSelect = $('#pfg-sort-order-images');
            
            // Store original order for "custom" restore
            var originalOrder = [];
            if (typeof window.pfgGetMasterImages === 'function') {
                var master = window.pfgGetMasterImages();
                for (var i = 0; i < master.length; i++) {
                    originalOrder.push(parseInt(master[i].id, 10));
                }
            }
            
            function sortMasterImages(order) {
                if (typeof window.pfgGetMasterImages !== 'function') return;
                var master = window.pfgGetMasterImages();
                if (!master.length) return;
                
                switch(order) {
                    case 'title_asc':
                        master.sort(function(a, b) {
                            var tA = (a.title || '').toLowerCase();
                            var tB = (b.title || '').toLowerCase();
                            return tA.localeCompare(tB);
                        });
                        break;
                    case 'title_desc':
                        master.sort(function(a, b) {
                            var tA = (a.title || '').toLowerCase();
                            var tB = (b.title || '').toLowerCase();
                            return tB.localeCompare(tA);
                        });
                        break;
                    case 'date_newest':
                        master.sort(function(a, b) {
                            return parseInt(b.id, 10) - parseInt(a.id, 10);
                        });
                        break;
                    case 'date_oldest':
                        master.sort(function(a, b) {
                            return parseInt(a.id, 10) - parseInt(b.id, 10);
                        });
                        break;
                    case 'random':
                        for (var i = master.length - 1; i > 0; i--) {
                            var j = Math.floor(Math.random() * (i + 1));
                            var temp = master[i]; master[i] = master[j]; master[j] = temp;
                        }
                        break;
                    case 'custom':
                    default:
                        master.sort(function(a, b) {
                            var idA = parseInt(a.id, 10);
                            var idB = parseInt(b.id, 10);
                            var indexA = originalOrder.indexOf(idA);
                            var indexB = originalOrder.indexOf(idB);
                            if (indexA === -1) indexA = 99999;
                            if (indexB === -1) indexB = 99999;
                            return indexA - indexB;
                        });
                        break;
                }
                
                // Save sorted order to DB via AJAX, then reload page 1
                var $grid = $('#pfg-image-grid');
                var adminNonce = ''+pfgAdmin.nonce+'';
                var galleryId = pfgAdmin.galleryId;
                
                $grid.addClass('pfg-loading');
                
                // Save all images as a single chunk
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'pfg_save_images_chunk',
                        nonce: adminNonce,
                        gallery_id: galleryId,
                        chunk_index: 0,
                        total_chunks: 1,
                        images: JSON.stringify(master)
                    },
                    success: function() {
                        // Now reload page 1 from server (DB now has sorted order)
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'pfg_get_admin_images_page',
                                nonce: adminNonce,
                                gallery_id: galleryId,
                                page: 1,
                                per_page: 50
                            },
                            success: function(response) {
                                if (response.success) {
                                    $grid.html(response.data.html);
                                    // Update pagination UI if visible
                                    if ($('#pfg-pagination-controls').length) {
                                        $('#pfg-showing-start').text(response.data.showing_start || 1);
                                        $('#pfg-showing-end').text(response.data.showing_end || master.length);
                                        $('#pfg-total-count').text(response.data.total_images || master.length);
                                        $('#pfg-page-input').val(1);
                                    }
                                    // Refresh sortable
                                    if ($.fn.sortable && $grid.data('ui-sortable')) {
                                        $grid.sortable('refresh');
                                    }
                                }
                                $grid.removeClass('pfg-loading');
                            },
                            error: function() {
                                $grid.removeClass('pfg-loading');
                            }
                        });
                    },
                    error: function() {
                        // Fallback: just sort visible DOM elements
                        var $items = $grid.find('.pfg-image-item').detach().toArray();
                        if ($items.length) {
                            var idOrder = master.map(function(m) { return parseInt(m.id, 10); });
                            $items.sort(function(a, b) {
                                return idOrder.indexOf($(a).data('id')) - idOrder.indexOf($(b).data('id'));
                            });
                            $.each($items, function(i, item) { $grid.append(item); });
                        }
                        $grid.removeClass('pfg-loading');
                    }
                });
                
            }
            
            // Sort order change handlers
            $imagesSelect.on('change', function() {
                var val = $(this).val();
                $settingsSelect.val(val);
                $settingsSelect.find('option').prop('selected', false);
                $settingsSelect.find('option[value="' + val + '"]').prop('selected', true);
                sortMasterImages(val);
            });
            
            $settingsSelect.on('change', function() {
                var val = $(this).val();
                $imagesSelect.val(val);
                sortMasterImages(val);
            });
            
            // CRITICAL: Sync sort_order to settings select just before form submission
            $('form#post').on('submit', function() {
                var currentVal = $imagesSelect.val();
                if (currentVal && $settingsSelect.length) {
                    $settingsSelect.val(currentVal);
                    $settingsSelect.find('option').prop('selected', false);
                    $settingsSelect.find('option[value="' + currentVal + '"]').prop('selected', true);
                }
            });
        }, 500); // Wait for master array initialization
    });

/* Extracted from meta-box-images.php */
jQuery(document).ready(function($) {
    var currentImageItem = null;
    var currentImageIndex = 0;
    var galleryId = pfgAdmin.galleryId;
    var originalImageData = null; // Store original image for revert functionality
    
    // ========================================
    // PAGINATION CONFIGURATION
    // ========================================
    var PAGINATION_THRESHOLD = 50; // Show pagination when images exceed this
    var IMAGES_PER_PAGE = 50;
    var paginationCurrentPage = 1;
    var paginationTotalPages = 1;
    var paginationTotalImages = 0;
    var paginationLoading = false;
    
    // ========================================
    // MASTER IMAGES ARRAY
    // ========================================
    // This holds ALL images for the gallery, regardless of pagination
    // The DOM only shows the current page, but this array is the source of truth
    var masterImagesArray = [];
    
    // Initialize masterImagesArray from JSON textarea or DOM
    var initialJsonData = $('#pfg-images-json').val();
    if (initialJsonData && initialJsonData !== '' && initialJsonData !== '[]') {
        try {
            masterImagesArray = JSON.parse(initialJsonData);
        } catch(e) {
            masterImagesArray = [];
        }
    }
    
    // If no JSON data, populate from DOM (backward compatibility)
    if (masterImagesArray.length === 0) {
        $('.pfg-image-item:not(.pfg-product-preview-item)').each(function() {
            var $item = $(this);
            var imageData = {
                id: parseInt($item.data('id'), 10) || parseInt($item.find('input[name$="[id]"]').val(), 10),
                title: $item.find('input[name$="[title]"]').val() || '',
                alt: $item.find('input[name$="[alt]"]').val() || '',
                description: $item.find('input[name$="[description]"]').val() || '',
                link: $item.find('input[name$="[link]"]').val() || '',
                type: $item.find('input[name$="[type]"]').val() || 'image',
                filters: $item.find('input[name$="[filters]"]').val() || '',
                original_id: $item.find('input[name$="[original_id]"]').val() || ''
            };
            if (imageData.id) {
                masterImagesArray.push(imageData);
            }
        });
    }
    
    // Update pagination info
    paginationTotalImages = masterImagesArray.length;
    paginationTotalPages = Math.max(1, Math.ceil(paginationTotalImages / IMAGES_PER_PAGE));
    
    // Expose masterImagesArray globally for sort handler
    window.pfgGetMasterImages = function() { return masterImagesArray; };
    
    // Sync current page DOM changes to masterImagesArray
    function syncCurrentPageToMaster() {
        $('.pfg-image-item:not(.pfg-product-preview-item)').each(function() {
            var $item = $(this);
            var imageId = parseInt($item.data('id'), 10);
            
            // Find this image in master array
            for (var i = 0; i < masterImagesArray.length; i++) {
                if (parseInt(masterImagesArray[i].id, 10) === imageId) {
                    // Update from hidden inputs - explicit undefined check to allow empty strings
                    var newTitle = $item.find('input[name$="[title]"]').val();
                    if (newTitle !== undefined) masterImagesArray[i].title = newTitle;
                    
                    var newAlt = $item.find('input[name$="[alt]"]').val();
                    if (newAlt !== undefined) masterImagesArray[i].alt = newAlt;
                    
                    var newDesc = $item.find('input[name$="[description]"]').val();
                    if (newDesc !== undefined) masterImagesArray[i].description = newDesc;
                    
                    var newLink = $item.find('input[name$="[link]"]').val();
                    if (newLink !== undefined) masterImagesArray[i].link = newLink;
                    
                    var newType = $item.find('input[name$="[type]"]').val();
                    if (newType !== undefined) masterImagesArray[i].type = newType;
                    
                    var newFilters = $item.find('input[name$="[filters]"]').val();
                    if (newFilters !== undefined) masterImagesArray[i].filters = newFilters;
                    break;
                }
            }
        });
    }
    
    // Reorder master array based on new order IDs
    function reorderMasterArray(newOrderIds) {
        if (!Array.isArray(newOrderIds) || newOrderIds.length === 0) return;
        
        // Normalize IDs to integers
        var normalizedNewOrderIds = newOrderIds.map(function(id) {
            return parseInt(id, 10);
        });
        
        // Create lookup map
        var idMap = {};
        masterImagesArray.forEach(function(img) {
            idMap[parseInt(img.id, 10)] = img;
        });
        
        var newMasterArray = [];
        normalizedNewOrderIds.forEach(function(id) {
            if (idMap[id]) {
                newMasterArray.push(idMap[id]);
                delete idMap[id];
            }
        });
        
        // Add remaining images (from other pages)
        Object.keys(idMap).forEach(function(key) {
            newMasterArray.push(idMap[key]);
        });
        
        masterImagesArray = newMasterArray;
    }
    
    // Remove image from master array
    function removeImageFromMaster(imageId) {
        var normalizedId = parseInt(imageId, 10);
        masterImagesArray = masterImagesArray.filter(function(img) {
            return parseInt(img.id, 10) !== normalizedId;
        });
        paginationTotalImages = masterImagesArray.length;
        paginationTotalPages = Math.max(1, Math.ceil(paginationTotalImages / IMAGES_PER_PAGE));
    }
    
    // Expose functions globally
    window.pfgReorderMasterArray = reorderMasterArray;
    window.pfgRemoveImageFromMaster = removeImageFromMaster;
    window.pfgSyncCurrentPageToMaster = syncCurrentPageToMaster;
    // Note: pfgUpdatePaginationUI and pfgMarkImagesModified are exposed after their definitions below
    
    // ========================================
    // PAGINATION FUNCTIONS
    // ========================================
    
    // Update pagination UI based on current state
    function updatePaginationUI() {
        var $controls = $('#pfg-pagination-controls');
        
        // Show/hide based on threshold
        if (masterImagesArray.length > PAGINATION_THRESHOLD) {
            $controls.show();
            
            // Update counts
            var start = ((paginationCurrentPage - 1) * IMAGES_PER_PAGE) + 1;
            var end = Math.min(paginationCurrentPage * IMAGES_PER_PAGE, paginationTotalImages);
            
            $('#pfg-page-start').text(start);
            $('#pfg-page-end').text(end);
            $('#pfg-total-images').text(paginationTotalImages);
            
            // Update button states
            $('#pfg-page-prev').prop('disabled', paginationCurrentPage <= 1);
            $('#pfg-page-next').prop('disabled', paginationCurrentPage >= paginationTotalPages);
            
            // Render page numbers
            var $pageNumbers = $('#pfg-page-numbers');
            $pageNumbers.empty();
            
            // Show up to 5 page numbers centered around current page
            var startPage = Math.max(1, paginationCurrentPage - 2);
            var endPage = Math.min(paginationTotalPages, startPage + 4);
            if (endPage - startPage < 4) {
                startPage = Math.max(1, endPage - 4);
            }
            
            for (var i = startPage; i <= endPage; i++) {
                var $btn = $('<button type="button" class="pfg-page-num" style="min-width: 32px; padding: 6px 10px; border: 1px solid #e2e8f0; background: ' + (i === paginationCurrentPage ? '#3858e9' : '#fff') + '; color: ' + (i === paginationCurrentPage ? '#fff' : '#475569') + '; border-radius: 4px; cursor: pointer; font-weight: 500;">' + i + '</button>');
                $btn.data('page', i);
                $pageNumbers.append($btn);
            }
        } else {
            $controls.hide();
        }
    }
    
    // Render images for current page from masterImagesArray
    function renderCurrentPage() {
        if (paginationLoading) return;
        
        // Sync current DOM changes before switching
        syncCurrentPageToMaster();
        
        var $grid = $('#pfg-image-grid');
        var $loading = $('.pfg-pagination-loading');
        
        paginationLoading = true;
        $loading.show();
        
        // Calculate slice
        var start = (paginationCurrentPage - 1) * IMAGES_PER_PAGE;
        var end = start + IMAGES_PER_PAGE;
        var pageImages = masterImagesArray.slice(start, end);
        
        // Build HTML for this page
        var html = '';
        if (pageImages.length === 0) {
            html = '<div class="pfg-no-images"><span class="dashicons dashicons-format-gallery"></span><p>'+pfgAdmin.i18n.no_images+'</p></div>';
        } else {
            pageImages.forEach(function(image, idx) {
                var actualIndex = start + idx;
                var filters = image.filters || '';
                var imageType = image.type || 'image';
                
                html += '<div class="pfg-image-item" data-id="' + image.id + '" data-index="' + actualIndex + '">';
                html += '<label class="pfg-image-checkbox" style="position: absolute; top: 8px; left: 8px; z-index: 10;">';
                html += '<input type="checkbox" class="pfg-image-select" style="width: 18px; height: 18px; cursor: pointer;">';
                html += '</label>';
                
                // Type badge for video/url
                if (imageType === 'video' || imageType === 'url') {
                    var badgeClass = 'pfg-image-type-badge';
                    var badgeIcon = 'dashicons-external';
                    if (imageType === 'video') {
                        if (image.link && image.link.indexOf('youtube') !== -1) {
                            badgeClass += ' pfg-badge-youtube';
                            badgeIcon = 'dashicons-youtube';
                        } else if (image.link && image.link.indexOf('vimeo') !== -1) {
                            badgeClass += ' pfg-badge-vimeo';
                            badgeIcon = 'dashicons-video-alt3';
                        } else {
                            badgeClass += ' pfg-badge-video';
                            badgeIcon = 'dashicons-video-alt3';
                        }
                    }
                    html += '<div class="' + badgeClass + '"><span class="dashicons ' + badgeIcon + '"></span></div>';
                }
                
                // Use AJAX to get thumbnail URL later, for now use placeholder
                html += '<img src="" alt="" class="pfg-image-thumb" data-image-id="' + image.id + '" loading="lazy">';
                
                html += '<div class="pfg-image-actions">';
                html += '<button type="button" class="pfg-image-action pfg-image-edit" title="Edit"><span class="dashicons dashicons-edit"></span></button>';
                html += '<button type="button" class="pfg-image-action pfg-image-delete" title="Delete"><span class="dashicons dashicons-trash"></span></button>';
                html += '</div>';
                
                html += '<div class="pfg-image-info">';
                html += '<p class="pfg-image-title">' + escapeHtml(image.title || 'Untitled') + '</p>';
                html += '</div>';
                
                // Hidden inputs
                html += '<input type="hidden" name="pfg_images[' + actualIndex + '][id]" value="' + escapeHtml(String(image.id)) + '">';
                html += '<input type="hidden" name="pfg_images[' + actualIndex + '][title]" value="' + escapeHtml(image.title || '') + '">';
                html += '<input type="hidden" name="pfg_images[' + actualIndex + '][alt]" value="' + escapeHtml(image.alt || '') + '">';
                html += '<input type="hidden" name="pfg_images[' + actualIndex + '][description]" value="' + escapeHtml(image.description || '') + '">';
                html += '<input type="hidden" name="pfg_images[' + actualIndex + '][link]" value="' + escapeHtml(image.link || '') + '">';
                html += '<input type="hidden" name="pfg_images[' + actualIndex + '][type]" value="' + escapeHtml(image.type || 'image') + '">';
                html += '<input type="hidden" name="pfg_images[' + actualIndex + '][filters]" value="' + escapeHtml(filters) + '">';
                html += '<input type="hidden" name="pfg_images[' + actualIndex + '][original_id]" value="' + (image.original_id || image.id) + '">';
                
                html += '</div>';
            });
        }
        
        $grid.html(html);
        
        // Load thumbnails via AJAX
        loadThumbnails(pageImages);
        
        // Re-initialize Sortable
        if (window.PFGAdmin && typeof window.PFGAdmin.initSortable === 'function') {
            window.PFGAdmin.initSortable();
        }
        
        paginationLoading = false;
        $loading.hide();
        
        updatePaginationUI();
    }
    
    // Load thumbnails for displayed images
    function loadThumbnails(images) {
        if (!images || images.length === 0) return;
        
        var ids = images.map(function(img) { return img.id; });
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'pfg_get_thumbnails',
                nonce: ''+pfgAdmin.nonce+'',
                image_ids: ids
            },
            success: function(response) {
                if (response.success && response.data.thumbnails) {
                    $.each(response.data.thumbnails, function(id, url) {
                        $('img.pfg-image-thumb[data-image-id="' + id + '"]').attr('src', url);
                    });
                }
            }
        });
    }
    
    // Handle page navigation
    function goToPage(page) {
        if (page < 1 || page > paginationTotalPages || page === paginationCurrentPage) return;
        
        paginationCurrentPage = page;
        renderCurrentPage();
    }
    
    // Pagination event handlers
    $('#pfg-page-prev').on('click', function() {
        goToPage(paginationCurrentPage - 1);
    });
    
    $('#pfg-page-next').on('click', function() {
        goToPage(paginationCurrentPage + 1);
    });
    
    $(document).on('click', '.pfg-page-num', function() {
        goToPage($(this).data('page'));
    });
    
    // Initialize pagination on load
    paginationTotalImages = masterImagesArray.length;
    paginationTotalPages = Math.max(1, Math.ceil(paginationTotalImages / IMAGES_PER_PAGE));
    updatePaginationUI();
    
    // ========================================
    // CHUNKED SAVE CONFIGURATION
    // ========================================
    var CHUNK_SIZE = 50;           // Images per chunk
    var CHUNK_THRESHOLD = 100;     // Use chunked save above this count
    var structurallyModified = false; // Add/delete/reorder - requires full save
    var metadataModified = false;     // Title/description/filters - can use standard save
    var chunkedSaveInProgress = false;
    var chunkedSaveCompleted = false;
    
    // Mark images as structurally modified (add/delete/reorder)
    // This requires full chunked save for large galleries
    function markStructurallyModified() {
        structurallyModified = true;
    }
    
    // Mark metadata as modified (title/description/filters)
    // This can use standard save even for large galleries
    function markMetadataModified() {
        metadataModified = true;
    }
    
    // Legacy function for compatibility with pfg-admin.js
    function markImagesModified() {
        markStructurallyModified();
    }
    
    // Expose to global scope for pfg-admin.js integration
    window.pfgMarkImagesModified = markStructurallyModified;
    window.pfgUpdatePaginationUI = updatePaginationUI;
    
    // Get all image data as array - uses masterImagesArray for pagination support
    function getAllImagesData() {
        // First sync any DOM changes to master array
        syncCurrentPageToMaster();
        
        // Return the master array (contains ALL images, not just current page)
        return masterImagesArray.map(function(img) {
            return {
                id: img.id,
                title: img.title || '',
                alt: img.alt || '',
                description: img.description || '',
                link: img.link || '',
                type: img.type || 'image',
                filters: img.filters || '',
                original_id: img.original_id || ''
            };
        });
    }
    
    // Split array into chunks
    function chunkArray(array, size) {
        var chunks = [];
        for (var i = 0; i < array.length; i += size) {
            chunks.push(array.slice(i, i + size));
        }
        return chunks;
    }
    
    // Show progress modal
    function showProgressModal() {
        if ($('#pfg-save-progress-modal').length === 0) {
            $('body').append(
                '<div id="pfg-save-progress-modal">' +
                    '<div class="pfg-progress-content">' +
                        '<div class="pfg-progress-icon"><span class="dashicons dashicons-update-alt"></span></div>' +
                        '<h3>'+pfgAdmin.i18n.saving_gallery+'</h3>' +
                        '<div class="pfg-progress-bar"><div class="pfg-progress-fill"></div></div>' +
                        '<p class="pfg-progress-text">'+pfgAdmin.i18n.preparing+'</p>' +
                    '</div>' +
                '</div>'
            );
        }
        $('#pfg-save-progress-modal').fadeIn(200);
    }
    
    // Update progress
    function updateProgress(current, total, message) {
        var percent = Math.round((current / total) * 100);
        $('#pfg-save-progress-modal .pfg-progress-fill').css('width', percent + '%');
        $('#pfg-save-progress-modal .pfg-progress-text').text(message || (''+pfgAdmin.i18n.saving_images+' ' + current + '/' + total));
    }
    
    // Hide progress modal
    function hideProgressModal() {
        $('#pfg-save-progress-modal').fadeOut(200);
    }
    
    // Show error in progress modal
    function showProgressError(message) {
        $('#pfg-save-progress-modal .pfg-progress-icon .dashicons')
            .removeClass('dashicons-update-alt')
            .addClass('dashicons-warning');
        $('#pfg-save-progress-modal .pfg-progress-text').text(message);
        $('#pfg-save-progress-modal .pfg-progress-content').append(
            '<button type="button" class="button pfg-progress-close" style="margin-top: 15px;">'+pfgAdmin.i18n.close+'</button>'
        );
    }
    
    // Close error modal
    $(document).on('click', '.pfg-progress-close', function() {
        hideProgressModal();
        chunkedSaveInProgress = false;
    });
    
    // Save images in chunks
    async function saveImagesChunked(imagesData) {
        var chunks = chunkArray(imagesData, CHUNK_SIZE);
        var totalChunks = chunks.length;
        
        for (var i = 0; i < chunks.length; i++) {
            updateProgress(i * CHUNK_SIZE, imagesData.length, 
                ''+pfgAdmin.i18n.saving_images+' ' + 
                Math.min((i + 1) * CHUNK_SIZE, imagesData.length) + '/' + imagesData.length);
            
            try {
                await saveChunk(chunks[i], i, totalChunks);
            } catch (error) {
                throw error;
            }
        }
        
        return true;
    }
    
    // Save single chunk
    function saveChunk(chunk, chunkIndex, totalChunks) {
        return new Promise(function(resolve, reject) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'pfg_save_images_chunk',
                    nonce: ''+pfgAdmin.nonce+'',
                    gallery_id: galleryId,
                    chunk_index: chunkIndex,
                    total_chunks: totalChunks,
                    images: JSON.stringify(chunk)
                },
                success: function(response) {
                    if (response.success) {
                        resolve(response.data);
                    } else {
                        var errorMsg = response.data ? response.data.message : pfgAdmin.i18n.save_failed;
                        reject(errorMsg);
                    }
                },
                error: function(xhr, status, error) {
                    var errorMsg = pfgAdmin.i18n.network_error;
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.data && response.data.message) {
                            errorMsg = response.data.message;
                        }
                    } catch(e) {}
                    reject(errorMsg);
                }
            });
        });
    }
    
    // Form submit handler with chunked save support
    $('form#post').on('submit', function(e) {
        var imagesData = getAllImagesData();
        var imageCount = imagesData.length;
        
        // If chunked save already completed, just mark and let form submit
        if (chunkedSaveCompleted) {
            $('#pfg-images-json').val('__CHUNKED_SAVE__');
            return true;
        }
        
        // Check if any modifications were made
        var anyModification = structurallyModified || metadataModified;
        
        // CRITICAL FIX: For large galleries (over threshold), we MUST remove hidden image inputs
        // BEFORE form submit to avoid exceeding PHP's max_input_vars limit (typically 1000).
        // With 440 images × 8 inputs = 3500+ inputs, which truncates settings data!
        if (imageCount > CHUNK_THRESHOLD) {
            // Remove all hidden pfg_images inputs to stay under max_input_vars
            $('input[name^="pfg_images["]').remove();
        }
        
        // If nothing was modified, skip image saving entirely
        if (!anyModification) {
            $('#pfg-images-json').val('__UNCHANGED__');
            return true;
        }
        
        // For smaller galleries or metadata-only changes, use standard JSON save
        // Only use chunked save for STRUCTURAL changes on LARGE galleries
        if (imageCount <= CHUNK_THRESHOLD || !structurallyModified) {
            // Standard JSON save - fast for metadata changes
            $('#pfg-images-json').val(JSON.stringify(imagesData));
            return true;
        }
        
        // Large gallery with structural changes - use chunked save
        
        // Prevent form submit - we'll submit after chunked save
        if (!chunkedSaveInProgress) {
            e.preventDefault();
            chunkedSaveInProgress = true;
            
            showProgressModal();
            updateProgress(0, imageCount, pfgAdmin.i18n.starting_save);
            
            saveImagesChunked(imagesData)
                .then(function() {
                    updateProgress(imageCount, imageCount, pfgAdmin.i18n.complete_save);
                    chunkedSaveCompleted = true;
                    
                    // CRITICAL FIX: Remove all hidden pfg_images inputs from DOM to avoid
                    // exceeding PHP's max_input_vars limit (which would truncate settings data)
                    $('input[name^="pfg_images["]').remove();
                    
                    // Now submit the form normally
                    setTimeout(function() {
                        $('form#post').submit();
                    }, 500);
                })
                .catch(function(error) {
                    showProgressError(error);
                    chunkedSaveInProgress = false;
                });
            
            return false;
        }
        
        return true;
    });
    
    // Get all image items
    function getAllImageItems() {
        return $('.pfg-image-item:not(.pfg-product-preview-item)');
    }
    
    // Update navigation buttons visibility and counter
    function updateNavigation() {
        var allItems = getAllImageItems();
        var total = allItems.length;
        
        if (total <= 1) {
            $('.pfg-modal-prev, .pfg-modal-next').hide();
            $('.pfg-modal-counter').text('');
        } else {
            $('.pfg-modal-prev').show().prop('disabled', currentImageIndex <= 0);
            $('.pfg-modal-next').show().prop('disabled', currentImageIndex >= total - 1);
            $('.pfg-modal-counter').text('(' + (currentImageIndex + 1) + ' / ' + total + ')');
        }
    }
    
    // Open modal for a specific image item
    function openModalForItem(imageItem) {
        currentImageItem = imageItem;
        var allItems = getAllImageItems();
        currentImageIndex = allItems.index(imageItem);
        
        // Get image URL from thumbnail
        var imgSrc = currentImageItem.find('.pfg-image-thumb').attr('src');
        var imageId = currentImageItem.find('input[name$="[id]"]').val() || '';
        
        // Store original image data for revert functionality
        originalImageData = {
            id: imageId,
            src: imgSrc
        };
        
        // Get values from hidden inputs
        var title = currentImageItem.find('input[name$="[title]"]').val() || '';
        var alt = currentImageItem.find('input[name$="[alt]"]').val() || '';
        var description = currentImageItem.find('input[name$="[description]"]').val() || '';
        var link = currentImageItem.find('input[name$="[link]"]').val() || '';
        var type = currentImageItem.find('input[name$="[type]"]').val() || 'image';
        var filtersStr = currentImageItem.find('input[name$="[filters]"]').val() || '';
        var filters = filtersStr ? filtersStr.split(',') : [];
        var productId = currentImageItem.find('input[name$="[product_id]"]').val() || '';
        var productName = currentImageItem.find('input[name$="[product_name]"]').val() || '';

        
        // Populate modal
        $('#pfg-modal-image').attr('src', imgSrc);
        $('#pfg-modal-title').val(title);
        $('#pfg-modal-alt').val(alt);
        $('#pfg-modal-description').val(description);
        $('#pfg-modal-type').val(type).trigger('change');
        $('#pfg-modal-link').val(link);
        
        // Populate product search (if exists)
        $('#pfg-modal-product-id').val(productId);
        $('#pfg-modal-product-search').val(productName).css('border-color', productId ? '#10b981' : '');
        $('#pfg-product-results').hide().html('');
        
        // Check if we should show revert button (original_id differs from current id)
        var originalId = currentImageItem.find('input[name$="[original_id]"]').val() || imageId;
        if (originalId && originalId !== imageId) {
            // Get original image URL
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'pfg_get_attachment_url',
                    attachment_id: originalId,
                    nonce: ''+pfgAdmin.nonce+''
                },
                success: function(response) {
                    if (response.success && response.data.url) {
                        originalImageData = {
                            id: originalId,
                            src: response.data.url
                        };
                        $('#pfg-revert-thumb').show();
                        $('#pfg-fetch-thumb-status').text(pfgAdmin.i18n.video_thumb_in_use).css('color', '#64748b').show();
                    }
                }
            });
        } else {
            // Store current as original
            originalImageData = {
                id: imageId,
                src: imgSrc
            };
            $('#pfg-revert-thumb').hide();
            $('#pfg-fetch-thumb-status').hide().text('');
        }
        
        // Reset and check filter checkboxes
        $('#pfg-modal-filters input[type="checkbox"]').prop('checked', false);
        filters.forEach(function(filterId) {
            $('#pfg-modal-filters input[value="' + filterId.trim() + '"]').prop('checked', true);
        });
        
        // Update navigation
        updateNavigation();
        
        // Show modal
        $('#pfg-image-modal').fadeIn(200);
    }
    
    // Navigate to previous/next image
    function navigateToImage(direction) {
        // Save current changes first
        saveCurrentChanges();
        
        var allItems = getAllImageItems();
        var newIndex = currentImageIndex + direction;
        
        if (newIndex >= 0 && newIndex < allItems.length) {
            var newItem = allItems.eq(newIndex);
            openModalForItem(newItem);
        }
    }
    
    // Save changes to current image item
    function saveCurrentChanges() {
        if (!currentImageItem) return;
        
        var title = $('#pfg-modal-title').val();
        var alt = $('#pfg-modal-alt').val();
        var description = $('#pfg-modal-description').val();
        var type = $('#pfg-modal-type').val();
        var link = $('#pfg-modal-link').val();

        if (type === 'image') {
            link = '';
            $('#pfg-modal-link').val('');
        }
        
        // Get selected filters
        var filters = [];
        $('#pfg-modal-filters input[type="checkbox"]:checked').each(function() {
            filters.push($(this).val());
        });
        
        // Update hidden inputs
        currentImageItem.find('input[name$="[title]"]').val(title);
        currentImageItem.find('input[name$="[alt]"]').val(alt);
        currentImageItem.find('input[name$="[description]"]').val(description);
        currentImageItem.find('input[name$="[type]"]').val(type);
        currentImageItem.find('input[name$="[link]"]').val(link);
        currentImageItem.find('input[name$="[filters]"]').val(filters.join(','));
        
        // Mark metadata as modified for smart save
        markMetadataModified();
        

        
        // Update visible title
        currentImageItem.find('.pfg-image-title').text(title || 'Untitled');
        
        // Update filter tags display with color dots and hierarchy connector
        var filterTagsHtml = '';
        filters.forEach(function(filterId) {
            var checkbox = $('#pfg-modal-filters input[value="' + filterId + '"]');
            var $label = checkbox.closest('label');
            var filterName = $label.find('.pfg-tree-filter-name').text().trim() || $label.text().trim();
            var filterColor = $label.data('color') || '#94a3b8';
            var isChild = $label.data('parent') ? true : false;
            var connectorHtml = isChild ? '<span class="pfg-tag-connector">└</span>' : '';
            filterTagsHtml += '<span class="pfg-image-filter-tag">' + connectorHtml + '<span class="pfg-tag-dot" style="background-color: ' + escapeHtml(filterColor) + ';"></span>' + escapeHtml(filterName) + '</span>';
        });
        
        var filtersContainer = currentImageItem.find('.pfg-image-filters');
        if (filtersContainer.length) {
            filtersContainer.html(filterTagsHtml);
        } else if (filterTagsHtml) {
            currentImageItem.find('.pfg-image-info').append('<div class="pfg-image-filters">' + filterTagsHtml + '</div>');
        }
        
        // Update type badge (video/url indicator)
        var existingBadge = currentImageItem.find('.pfg-image-type-badge');
        if (type === 'video' || type === 'url') {
            var badgeClass = 'pfg-image-type-badge';
            var iconClass, titleText;
            
            if (type === 'video' && link) {
                // Detect YouTube or Vimeo
                if (link.indexOf('youtube.com') !== -1 || link.indexOf('youtu.be') !== -1) {
                    badgeClass += ' pfg-badge-youtube';
                    iconClass = 'dashicons-youtube';
                    titleText = pfgAdmin.i18n.youtube_video;
                } else if (link.indexOf('vimeo.com') !== -1) {
                    badgeClass += ' pfg-badge-vimeo';
                    iconClass = 'dashicons-video-alt3';
                    titleText = pfgAdmin.i18n.vimeo_video;
                } else {
                    badgeClass += ' pfg-badge-video';
                    iconClass = 'dashicons-video-alt3';
                    titleText = pfgAdmin.i18n.video_lightbox;
                }
            } else {
                iconClass = 'dashicons-external';
                titleText = pfgAdmin.i18n.external_link;
            }
            
            var badgeHtml = '<div class="' + badgeClass + '" title="' + titleText + '"><span class="dashicons ' + iconClass + '"></span></div>';
            
            if (existingBadge.length) {
                existingBadge.replaceWith(badgeHtml);
            } else {
                currentImageItem.find('.pfg-image-checkbox').after(badgeHtml);
            }
        } else {
            // Remove badge for regular images
            existingBadge.remove();
        }
    }
    
    // Open modal on edit click
    $(document).on('click', '.pfg-image-edit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var imageItem = $(this).closest('.pfg-image-item');
        openModalForItem(imageItem);
    });
    
    // Previous button click
    $(document).on('click', '.pfg-modal-prev:not(:disabled)', function() {
        navigateToImage(-1);
    });
    
    // Next button click
    $(document).on('click', '.pfg-modal-next:not(:disabled)', function() {
        navigateToImage(1);
    });
    
    // Close modal
    $(document).on('click', '.pfg-modal-close, .pfg-modal-cancel', function() {
        $('#pfg-image-modal').fadeOut(200);
        currentImageItem = null;
    });
    
    // Close on backdrop click
    $(document).on('click', '#pfg-image-modal', function(e) {
        if ($(e.target).is('#pfg-image-modal')) {
            $('#pfg-image-modal').fadeOut(200);
            currentImageItem = null;
        }
    });
    
    // Keyboard navigation (ESC to close, Arrow keys to navigate)
    $(document).on('keydown', function(e) {
        if (!$('#pfg-image-modal').is(':visible')) return;
        
        // Don't navigate when typing in input fields
        var activeEl = document.activeElement;
        var isInputFocused = activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA');
        
        if (e.key === 'Escape') {
            $('#pfg-image-modal').fadeOut(200);
            currentImageItem = null;
        } else if (e.key === 'ArrowLeft' && !isInputFocused) {
            navigateToImage(-1);
        } else if (e.key === 'ArrowRight' && !isInputFocused) {
            navigateToImage(1);
        }
    });
    
    // Save changes
    $(document).on('click', '.pfg-modal-save', function() {
        saveCurrentChanges();
        
        // Close modal
        $('#pfg-image-modal').fadeOut(200);
        currentImageItem = null;
    });
    
    // Delete image
    $(document).on('click', '.pfg-image-delete', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (confirm(pfgAdmin.i18n.remove_image)) {
            var $item = $(this).closest('.pfg-image-item');
            var imageId = $item.data('id');
            
            // Remove from master array BEFORE DOM removal (element won't exist after .remove())
            removeImageFromMaster(imageId);
            
            $item.fadeOut(200, function() {
                $(this).remove();
                reindexImages();
                markStructurallyModified(); // Deletion is a structural change
                
                // Show empty state if no images left
                if ($('.pfg-image-item').length === 0) {
                    $('#pfg-image-grid').html('<div class="pfg-no-images"><span class="dashicons dashicons-format-gallery"></span><p>'+pfgAdmin.i18n.no_images+'</p></div>');
                }
            });
        }
    });
    
    // Reindex image inputs after deletion
    function reindexImages() {
        $('.pfg-image-item').each(function(index) {
            $(this).attr('data-index', index);
            $(this).find('input').each(function() {
                var name = $(this).attr('name');
                if (name) {
                    $(this).attr('name', name.replace(/\[\d+\]/, '[' + index + ']'));
                }
            });
        });
    }
    
    // Link type toggle - show/hide URL field based on selection
    $(document).on('change', '#pfg-modal-type', function() {
        var type = $(this).val();
        var $urlRow = $('.pfg-link-url-row');
        var $hint = $('.pfg-url-hint');
        var $upgradeHint = $('.pfg-upgrade-hint');
        
        if (type === 'image') {
            $urlRow.hide();
            $hint.text('');
            $upgradeHint.hide();
        } else if (type === 'video') {
            $urlRow.show();
            $hint.text(pfgAdmin.i18n.paste_video_url);
            $upgradeHint.show();
        } else if (type === 'url') {
            $urlRow.show();
            $hint.text(pfgAdmin.i18n.opens_new_tab);
            $upgradeHint.hide();
        }
    });
    
    // Revert to original image button click
    $(document).on('click', '#pfg-revert-thumb', function() {
        if (!originalImageData) return;
        
        var $btn = $(this);
        var currentThumbId = currentImageItem ? currentImageItem.find('input[name$="[id]"]').val() : null;
        
        // If current thumbnail is different from original, delete it from media library
        if (currentThumbId && currentThumbId !== originalImageData.id) {
            $btn.prop('disabled', true).text(pfgAdmin.i18n.reverting);
            
            // Delete the fetched thumbnail
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'pfg_delete_video_thumbnail',
                    nonce: ''+pfgAdmin.nonce+'',
                    attachment_id: currentThumbId
                },
                complete: function() {
                    // Restore original image regardless of delete result
                    restoreOriginalImage();
                    $btn.prop('disabled', false).text(pfgAdmin.i18n.revert_to_original);
                }
            });
        } else {
            restoreOriginalImage();
        }
        
        function restoreOriginalImage() {
            // Restore original image
            $('#pfg-modal-image').attr('src', originalImageData.src);
            
            if (currentImageItem) {
                currentImageItem.find('input[name$="[id]"]').val(originalImageData.id);
                currentImageItem.find('input[name$="[original_id]"]').val(originalImageData.id); // Reset original_id too
                currentImageItem.find('.pfg-image-thumb').attr('src', originalImageData.src);
                currentImageItem.attr('data-id', originalImageData.id);
            }
            
            // Hide revert button and update status
            $btn.hide();
            $('#pfg-fetch-thumb-status').text(pfgAdmin.i18n.reverted_to_original).css('color', '#64748b').show();
        }
    });
    
    // =====================
    // Bulk Selection Logic
    // =====================
    
    // Show/hide bulk actions bar based on image count
    function updateBulkActionsBar() {
        var imageCount = $('.pfg-image-item').length;
        if (imageCount > 0) {
            $('#pfg-bulk-actions').css('display', 'flex');
        } else {
            $('#pfg-bulk-actions').hide();
        }
    }
    
    // Update selected count
    function updateSelectedCount() {
        var count = $('.pfg-image-select:checked').length;
        $('#pfg-selected-num').text(count);
        
        if (count > 0) {
            $('.pfg-delete-selected').show();
        } else {
            $('.pfg-delete-selected').hide();
        }
        
        // Update select all checkbox state
        var totalImages = $('.pfg-image-select').length;
        $('#pfg-select-all').prop('checked', count === totalImages && totalImages > 0);
        $('#pfg-select-all').prop('indeterminate', count > 0 && count < totalImages);
    }
    
    // Individual image checkbox change
    $(document).on('change', '.pfg-image-select', function() {
        var $item = $(this).closest('.pfg-image-item');
        if ($(this).is(':checked')) {
            $item.addClass('selected');
        } else {
            $item.removeClass('selected');
        }
        updateSelectedCount();
    });
    
    // Select All checkbox
    $(document).on('change', '#pfg-select-all', function() {
        var isChecked = $(this).is(':checked');
        $('.pfg-image-select').prop('checked', isChecked).trigger('change');
    });
    
    // Delete Selected button
    $(document).on('click', '.pfg-delete-selected', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var selectedCount = $('.pfg-image-select:checked').length;
        if (selectedCount === 0) return;
        
        var confirmMsg = pfgAdmin.i18n.confirmRemoveImages.replace('%s', selectedCount);
        
        if (confirm(confirmMsg)) {
            $('.pfg-image-select:checked').each(function() {
                var $item = $(this).closest('.pfg-image-item');
                var imageId = $item.data('id');
                
                // Remove from master array BEFORE removing from DOM
                if (typeof removeImageFromMaster === 'function') {
                    removeImageFromMaster(imageId);
                }
                
                $item.remove();
            });
            
            reindexImages();
            updateSelectedCount();
            updateBulkActionsBar();
            markStructurallyModified(); // Bulk delete is a structural change
            
            // Update pagination after deletion
            if (typeof updatePaginationUI === 'function') {
                updatePaginationUI();
            }
            
            // Check if no images left
            if ($('.pfg-image-item').length === 0 && masterImagesArray.length === 0) {
                $('#pfg-image-grid').html('<div class="pfg-no-images"><span class="dashicons dashicons-format-gallery"></span><p>'+pfgAdmin.i18n.no_images+'</p></div>');
                // Hide pagination when no images
                $('#pfg-pagination-controls').hide();
            }
        }
    });
    // Initialize bulk actions bar on page load
    updateBulkActionsBar();
});

/* Extracted from meta-box-images.php */
jQuery(document).ready(function($) {
    // Handle collapse toggle clicks for filter groups (both old and new styles)
    // Use mousedown instead of click to prevent label checkbox toggle interference
    $(document).on('mousedown', '.pfg-collapse-toggle, .pfg-tree-toggle', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $group = $(this).closest('.pfg-filter-collapsible-group');
        var isExpanded = $group.attr('data-expanded') === 'true';
        
        // Toggle state
        $group.attr('data-expanded', isExpanded ? 'false' : 'true');
    });
    
    // Bulk selection functionality
    var $bulkActions = $('#pfg-bulk-actions');
    var $imageGrid = $('#pfg-image-grid');
    var $selectAllCheckbox = $('#pfg-select-all');
    var $selectedCount = $('#pfg-selected-num');
    var $deleteBtn = $('.pfg-delete-selected');
    var $bulkFiltersDropdown = $('.pfg-bulk-filters-dropdown');
    var $bulkFiltersMenu = $('.pfg-bulk-filters-menu');
    
    // Update selection count and show/hide bulk actions
    function updateSelectionUI() {
        var $selected = $imageGrid.find('.pfg-image-select:checked');
        var count = $selected.length;
        
        $selectedCount.text(count);
        
        // Keep bar visible, only show/hide action buttons
        if (count > 0) {
            $deleteBtn.show();
            $bulkFiltersDropdown.show();
        } else {
            $deleteBtn.hide();
            $bulkFiltersDropdown.hide();
            $bulkFiltersMenu.hide();
        }
        
        // Update select all checkbox state
        var totalCheckboxes = $imageGrid.find('.pfg-image-select').length;
        $selectAllCheckbox.prop('checked', count > 0 && count === totalCheckboxes);
    }
    
    // Image checkbox change
    $(document).on('change', '.pfg-image-select', function() {
        updateSelectionUI();
    });
    
    // Select all checkbox
    $selectAllCheckbox.on('change', function() {
        var isChecked = $(this).prop('checked');
        $imageGrid.find('.pfg-image-select').prop('checked', isChecked);
        updateSelectionUI();
    });
    
    // Toggle bulk filters menu
    $(document).on('click', '.pfg-bulk-filters-btn', function(e) {
        e.stopPropagation();
        $bulkFiltersMenu.toggle();
    });
    
    // Close menu when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.pfg-bulk-filters-dropdown').length) {
            $bulkFiltersMenu.hide();
        }
    });
    
    // Cancel button
    $(document).on('click', '.pfg-cancel-bulk-filters', function() {
        $bulkFiltersMenu.hide();
        // Reset checkboxes
        $('.pfg-bulk-filter-checkbox').prop('checked', false);
    });
    
    // Apply bulk filters
    $(document).on('click', '.pfg-apply-bulk-filters', function() {
        var mode = $('#pfg-bulk-filter-mode').val();
        var selectedFilters = [];
        
        $('.pfg-bulk-filter-checkbox:checked').each(function() {
            selectedFilters.push($(this).val());
        });
        
        if (selectedFilters.length === 0) {
            alert('Please select at least one filter');
            return;
        }
        
        var $selectedItems = $imageGrid.find('.pfg-image-select:checked').closest('.pfg-image-item');
        var appliedCount = 0;
        
        $selectedItems.each(function() {
            var $item = $(this);
            var $filtersInput = $item.find('input[name$="[filters]"]');
            
            if ($filtersInput.length) {
                var currentFilters = $filtersInput.val();
                var filterArray = currentFilters ? currentFilters.split(',').filter(function(f) { return f; }) : [];
                
                if (mode === 'replace') {
                    // Replace all filters
                    filterArray = selectedFilters.slice();
                } else if (mode === 'remove') {
                    // Remove selected filters
                    filterArray = filterArray.filter(function(f) {
                        return selectedFilters.indexOf(f) === -1;
                    });
                } else {
                    // Add mode (default) - add new filters
                    selectedFilters.forEach(function(f) {
                        if (filterArray.indexOf(f) === -1) {
                            filterArray.push(f);
                        }
                    });
                }
                
                $filtersInput.val(filterArray.join(','));
                appliedCount++;
                
                // Update visual filter tags
                var $filterTagsContainer = $item.find('.pfg-image-filters');
                $filterTagsContainer.empty(); // Clear existing tags
                
                if (filterArray.length > 0) {
                    if ($filterTagsContainer.length === 0) {
                        $item.find('.pfg-image-info').append('<div class="pfg-image-filters"></div>');
                        $filterTagsContainer = $item.find('.pfg-image-filters');
                    }
                    // Note: Would need filter names lookup for accurate tags, using IDs for now
                    filterArray.forEach(function(filterId) {
                        var $checkbox = $('.pfg-bulk-filter-checkbox[value="' + filterId + '"]');
                        var filterName = $checkbox.closest('label').find('span:last').text() || filterId;
                        $filterTagsContainer.append('<span class="pfg-image-filter-tag">' + escapeHtml(filterName) + '</span>');
                    });
                }
            }
        });
        
        // Close menu and reset filter checkboxes (keep image selection)
        $bulkFiltersMenu.hide();
        $('.pfg-bulk-filter-checkbox').prop('checked', false);
        
        // Mark images as modified for chunked save
        if (typeof window.pfgMarkImagesModified === 'function') {
            window.pfgMarkImagesModified();
        }
        
        // Show success message
        var modeText = mode === 'replace' ? 'Filters replaced on' :
                       mode === 'remove' ? 'Filters removed from' :
                       'Filters added to';
        alert(modeText + ' ' + appliedCount + ' images!');
    });
});

