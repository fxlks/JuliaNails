/**
 * Portfolio Filter Gallery - Lightbox
 * Vanilla JS - No jQuery dependency
 * 
 * @package Portfolio_Filter_Gallery
 * @version 2.0.0
 */

(function() {
    'use strict';

    /**
     * Lightbox class
     */
    class PFGLightbox {
        constructor() {
            this.isOpen = false;
            this.currentIndex = 0;
            this.items = [];
            this.galleryGroup = null;
            this.touchStartX = 0;
            this.touchEndX = 0;
            
            this.createLightbox();
            this.bindEvents();
        }

        /**
         * Create lightbox DOM structure
         */
        createLightbox() {
            // Safe access to i18n strings with fallbacks
            const i18n = (typeof pfgData !== 'undefined' && pfgData.i18n) ? pfgData.i18n : {};
            const closeText = i18n.close || 'Close';
            const prevText = i18n.prev || 'Previous';
            const nextText = i18n.next || 'Next';
            
            const html = `
                <div class="pfg-lightbox" role="dialog" aria-modal="true" aria-label="Image lightbox">
                    <button class="pfg-lightbox-close" aria-label="${closeText}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 6L6 18M6 6l12 12"/>
                        </svg>
                    </button>
                    <span class="pfg-lightbox-counter"></span>
                    <button class="pfg-lightbox-nav pfg-lightbox-nav--prev" aria-label="${prevText}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 18l-6-6 6-6"/>
                        </svg>
                    </button>
                    <button class="pfg-lightbox-nav pfg-lightbox-nav--next" aria-label="${nextText}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 18l6-6-6-6"/>
                        </svg>
                    </button>
                    <div class="pfg-lightbox-content">
                        <img class="pfg-lightbox-image" src="" alt="">
                        <div class="pfg-lightbox-video" style="display: none;"></div>
                    </div>
                    <div class="pfg-lightbox-caption">
                        <h3 class="pfg-lightbox-title"></h3>
                        <p class="pfg-lightbox-description"></p>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', html);
            
            this.lightbox = document.querySelector('.pfg-lightbox');
            this.closeBtn = this.lightbox.querySelector('.pfg-lightbox-close');
            this.prevBtn = this.lightbox.querySelector('.pfg-lightbox-nav--prev');
            this.nextBtn = this.lightbox.querySelector('.pfg-lightbox-nav--next');
            this.counter = this.lightbox.querySelector('.pfg-lightbox-counter');
            this.content = this.lightbox.querySelector('.pfg-lightbox-content');
            this.image = this.lightbox.querySelector('.pfg-lightbox-image');
            this.video = this.lightbox.querySelector('.pfg-lightbox-video');
            this.title = this.lightbox.querySelector('.pfg-lightbox-title');
            this.description = this.lightbox.querySelector('.pfg-lightbox-description');
        }

        /**
         * Bind event listeners
         */
        bindEvents() {
            // Click on lightbox trigger links
            document.addEventListener('click', (e) => {
                const link = e.target.closest('[data-lightbox]');
                if (link) {
                    e.preventDefault();
                    this.open(link);
                }
            });

            // Close button
            this.closeBtn.addEventListener('click', () => this.close());

            // Navigation
            this.prevBtn.addEventListener('click', () => this.prev());
            this.nextBtn.addEventListener('click', () => this.next());

            // Click outside to close
            this.lightbox.addEventListener('click', (e) => {
                if (e.target === this.lightbox) {
                    this.close();
                }
            });

            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (!this.isOpen) return;

                switch (e.key) {
                    case 'Escape':
                        this.close();
                        break;
                    case 'ArrowLeft':
                        this.prev();
                        break;
                    case 'ArrowRight':
                        this.next();
                        break;
                }
            });

            // Touch swipe support
            this.lightbox.addEventListener('touchstart', (e) => {
                this.touchStartX = e.changedTouches[0].screenX;
            });

            this.lightbox.addEventListener('touchend', (e) => {
                this.touchEndX = e.changedTouches[0].screenX;
                this.handleSwipe();
            });
        }

        /**
         * Handle touch swipe
         */
        handleSwipe() {
            const threshold = 50;
            const diff = this.touchStartX - this.touchEndX;

            if (Math.abs(diff) < threshold) return;

            if (diff > 0) {
                this.next();
            } else {
                this.prev();
            }
        }

        /**
         * Open lightbox
         */
        open(trigger) {
            this.galleryGroup = trigger.dataset.lightbox;
            
            // Get all items in this gallery group
            const allItems = Array.from(document.querySelectorAll(`[data-lightbox="${this.galleryGroup}"]`));
            
            // Filter to only visible items (not hidden by filters or pagination)
            // Check if parent .pfg-item has hidden classes
            this.items = allItems.filter(item => {
                const pfgItem = item.closest('.pfg-item');
                // Include if no parent pfg-item (standalone) or parent is not hidden
                if (!pfgItem) return true;
                return !pfgItem.classList.contains('pfg-item--hidden') && 
                       !pfgItem.classList.contains('pfg-item--hiding') &&
                       !pfgItem.classList.contains('pfg-item--paginated-hidden');
            });
            
            // Find current index within visible items
            this.currentIndex = this.items.indexOf(trigger);
            
            // If trigger not in visible items (edge case), use first item
            if (this.currentIndex === -1) {
                this.currentIndex = 0;
            }

            // Show lightbox
            this.lightbox.classList.add('pfg-lightbox--open');
            this.isOpen = true;
            
            // Prevent body scroll with scrollbar width compensation
            const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
            document.documentElement.style.setProperty('--pfg-scrollbar-width', scrollbarWidth + 'px');
            document.body.classList.add('pfg-lightbox-open');

            // Load current item
            this.loadItem(this.currentIndex);

            // Update navigation visibility
            this.updateNavigation();

            // Focus management
            this.closeBtn.focus();
        }

        /**
         * Close lightbox
         */
        close() {
            this.lightbox.classList.remove('pfg-lightbox--open', 'pfg-lightbox--loaded');
            this.isOpen = false;
            
            // Restore body scroll
            document.body.classList.remove('pfg-lightbox-open');
            document.documentElement.style.removeProperty('--pfg-scrollbar-width');

            // Clear video if playing
            this.video.innerHTML = '';
            this.video.style.display = 'none';
            this.image.style.display = '';
        }

        /**
         * Go to previous item
         */
        prev() {
            if (this.currentIndex > 0) {
                this.currentIndex--;
                this.loadItem(this.currentIndex);
                this.updateNavigation();
            }
        }

        /**
         * Go to next item
         */
        next() {
            if (this.currentIndex < this.items.length - 1) {
                this.currentIndex++;
                this.loadItem(this.currentIndex);
                this.updateNavigation();
            }
        }

        /**
         * Load item by index
         */
        loadItem(index) {
            const item = this.items[index];
            if (!item) return;

            const isVideo = item.dataset.type === 'video';
            const src = item.href;
            const titleText = item.dataset.title || '';
            const descText = item.dataset.description || '';

            // Get gallery wrapper settings
            const wrapper = item.closest('.pfg-gallery-wrapper');
            const showTitle = wrapper ? wrapper.dataset.lightboxTitle !== 'false' : true;
            const showDesc = wrapper ? wrapper.dataset.lightboxDescription === 'true' : false;

            // Show loading state
            this.lightbox.classList.remove('pfg-lightbox--loaded');
            this.lightbox.classList.add('pfg-lightbox--loading');

            if (isVideo) {
                this.loadVideo(src);
            } else {
                this.loadImage(src);
            }

            // Update caption based on settings
            this.title.innerHTML = showTitle ? titleText : '';
            this.description.innerHTML = showDesc ? descText : '';
            
            // Hide caption container if both are empty
            const captionEl = this.lightbox.querySelector('.pfg-lightbox-caption');
            if (captionEl) {
                const hasContent = (showTitle && titleText) || (showDesc && descText);
                captionEl.style.display = hasContent ? '' : 'none';
            }

            // Update counter
            this.counter.textContent = `${index + 1} / ${this.items.length}`;
        }

        /**
         * Load image
         */
        loadImage(src) {
            // Clear any playing video first
            this.video.innerHTML = '';
            this.video.style.display = 'none';
            this.image.style.display = '';

            const img = new Image();
            img.onload = () => {
                this.image.src = src;
                this.image.alt = this.title.textContent;
                this.lightbox.classList.remove('pfg-lightbox--loading');
                this.lightbox.classList.add('pfg-lightbox--loaded');
            };
            img.onerror = () => {
                this.lightbox.classList.remove('pfg-lightbox--loading');
            };
            img.src = src;
        }

        /**
         * Load video
         */
        loadVideo(url) {
            // Hide image and clear its src to free memory
            this.image.style.display = 'none';
            this.image.src = '';
            
            // Clear any previous video iframe before loading new one
            this.video.innerHTML = '';
            this.video.style.display = '';

            const embedUrl = this.getVideoEmbedUrl(url);
            
            if (embedUrl) {
                this.video.innerHTML = `<iframe src="${embedUrl}" allowfullscreen allow="autoplay"></iframe>`;
            }

            this.lightbox.classList.remove('pfg-lightbox--loading');
            this.lightbox.classList.add('pfg-lightbox--loaded');
        }

        /**
         * Get video embed URL
         */
        getVideoEmbedUrl(url) {
            // YouTube
            const youtubeMatch = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/);
            if (youtubeMatch) {
                return `https://www.youtube.com/embed/${youtubeMatch[1]}?autoplay=1`;
            }

            // Vimeo
            const vimeoMatch = url.match(/vimeo\.com\/(?:.*\/)?(\d+)/);
            if (vimeoMatch) {
                return `https://player.vimeo.com/video/${vimeoMatch[1]}?autoplay=1`;
            }

            return null;
        }

        /**
         * Update navigation button visibility
         */
        updateNavigation() {
            this.prevBtn.style.display = this.currentIndex > 0 ? '' : 'none';
            this.nextBtn.style.display = this.currentIndex < this.items.length - 1 ? '' : 'none';

            // Hide counter if single item
            this.counter.style.display = this.items.length > 1 ? '' : 'none';
        }
    }

    // Initialize lightbox when DOM is ready
    function initLightbox() {
        // Check if any lightbox triggers exist
        if (document.querySelector('[data-lightbox]')) {
            new PFGLightbox();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLightbox);
    } else {
        initLightbox();
    }

    // Expose to global scope
    window.PFGLightbox = PFGLightbox;

})();
