/**
 * Portfolio Filter Gallery - Core JavaScript
 * Vanilla JS - No jQuery dependency
 * Supports multi-filter selection and AND/OR logic
 *
 * @package Portfolio_Filter_Gallery
 * @version 2.1.0
 */

(function () {
  "use strict";

  /**
   * Gallery class for handling filtering and interactions
   */
  class PFGGallery {
    constructor(container) {
      this.container = container;
      this.galleryId = container.dataset.galleryId;
      this.grid = container.querySelector(".pfg-grid");
      this.filtersContainer = container.querySelector(".pfg-filters");
      this.filters = container.querySelectorAll(".pfg-filter");
      this.items = container.querySelectorAll(".pfg-item");
      this.searchInput = container.querySelector(".pfg-search-input");
      this.logicToggle = container.querySelector(".pfg-logic-toggle");

      // Multi-filter settings
      this.multiSelect = container.dataset.multiSelect === "true";
      this.filterLogic = container.dataset.filterLogic || "or"; // 'and' or 'or'
      this.activeFilters = new Set();
      this.searchTerm = "";

      // Deep linking settings
      this.deepLinking = container.dataset.deepLinking === "true";
      this.urlParam = container.dataset.urlParam || "filter";
      this.defaultFilter = container.dataset.defaultFilter || "";

      // Filter hierarchy (parent slug => array of child slugs)
      this.filterHierarchy = {};
      try {
        this.filterHierarchy = JSON.parse(
          container.dataset.filterHierarchy || "{}"
        );
      } catch (e) {
      }

      this.init();
    }

    init() {
      this.bindFilters();
      this.bindSearch();
      this.bindLogicToggle();
      this.bindCascadingDropdowns();
      this.initLazyLoading();
      this.assignStaggerDelays();
      this.initDeepLinking();
      this.initMasonryLayout();
      this.initPreloader();
    }

    /**
     * Initialize preloader - hide after images load
     */
    initPreloader() {
      // Check if all images are already loaded (cached)
      const images = Array.from(this.container.querySelectorAll('.pfg-grid img'));
      const allLoaded = images.every(img => img.complete && img.naturalHeight !== 0);
      
      if (allLoaded) {
        // Images already loaded, remove preloader immediately
        this.removePreloader();
        return;
      }
      
      // Wait for images to load
      this.waitForImagesMain().then(() => {
        this.removePreloader();
      });
      
      // Fallback: always hide preloader after 3 seconds max
      setTimeout(() => {
        this.removePreloader();
      }, 3000);
    }

    /**
     * Wait for images in main grid
     */
    waitForImagesMain() {
      const images = Array.from(this.container.querySelectorAll('.pfg-grid img'));
      const promises = images.map((img) => {
        if (img.complete) {
          return Promise.resolve();
        }
        return new Promise((resolve) => {
          img.addEventListener('load', resolve, { once: true });
          img.addEventListener('error', resolve, { once: true });
          // Timeout fallback
          setTimeout(resolve, 5000);
        });
      });
      return Promise.all(promises);
    }

    /**
     * Remove preloader and show gallery
     */
    removePreloader() {
      this.container.classList.remove('pfg-loading');
      this.container.classList.add('pfg-loaded');
    }

    /**
     * Initialize URL filter and default filter
     * URL param ALWAYS works, Deep Linking only controls URL updates on click
     */
    initDeepLinking() {
      // Check URL param first (highest priority, always works)
      const urlParams = new URLSearchParams(window.location.search);
      const urlFilter = urlParams.get(this.urlParam);

      if (urlFilter) {
        this.activateFilterBySlug(urlFilter);
        return;
      }

      // Otherwise, activate default filter if set
      if (this.defaultFilter) {
        this.activateFilterBySlug(this.defaultFilter);
      } else {
        // No URL filter and no default filter - activate "All" button
        const allBtn = this.container.querySelector('[data-filter="*"]');
        if (allBtn) {
          allBtn.classList.add("pfg-filter--active");
        }
      }
    }

    /**
     * Activate a filter by its slug
     */
    activateFilterBySlug(slug) {
      const filterBtn = this.container.querySelector(
        `.pfg-filter[data-filter="${slug}"]`
      );
      if (filterBtn) {
        this.setSingleFilter(slug, filterBtn);
        this.filterItems();
      }
    }

    /**
     * Update URL with current filter (deep linking)
     */
    updateUrl(filter) {
      if (!this.deepLinking) return;

      const url = new URL(window.location.href);

      if (filter === "*" || !filter) {
        url.searchParams.delete(this.urlParam);
      } else {
        url.searchParams.set(this.urlParam, filter);
      }

      window.history.replaceState({}, "", url.toString());
    }

    /**
     * Assign stagger delays to items for animation
     */
    assignStaggerDelays() {
      this.items.forEach((item, index) => {
        item.dataset.delay = (index % 6) + 1;
      });
    }

    /**
     * Bind filter button click events
     */
    bindFilters() {
      this.filters.forEach((filter) => {
        filter.addEventListener("click", (e) => {
          e.preventDefault();
          const filterValue = filter.dataset.filter;

          if (this.multiSelect && filterValue !== "*") {
            // Multi-select mode: toggle filter
            this.toggleFilter(filterValue, filter);
          } else {
            // Single-select mode or "All" clicked
            this.setSingleFilter(filterValue, filter);
          }

          this.filterItems();
        });
      });
    }

    /**
     * Toggle a filter in multi-select mode
     */
    toggleFilter(filter, button) {
      if (this.activeFilters.has(filter)) {
        this.activeFilters.delete(filter);
        button.classList.remove("pfg-filter--selected", "pfg-filter--active");
      } else {
        this.activeFilters.add(filter);
        button.classList.add("pfg-filter--selected", "pfg-filter--active");
      }

      // Update "All" button state
      const allBtn = this.container.querySelector('[data-filter="*"]');
      if (allBtn) {
        if (this.activeFilters.size === 0) {
          allBtn.classList.add("pfg-filter--active");
        } else {
          allBtn.classList.remove("pfg-filter--active");
        }
      }
    }

    /**
     * Set single active filter (clears others)
     */
    setSingleFilter(filter, button) {
      this.activeFilters.clear();

      if (filter !== "*") {
        this.activeFilters.add(filter);
      }

      // Update button states
      this.filters.forEach((btn) => {
        btn.classList.remove("pfg-filter--active", "pfg-filter--selected");
      });
      button.classList.add("pfg-filter--active");

      // Update URL for deep linking
      this.updateUrl(filter);
    }

    /**
     * Bind search input events
     */
    bindSearch() {
      if (!this.searchInput) return;

      let debounceTimer;
      this.searchInput.addEventListener("input", (e) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
          this.searchTerm = e.target.value.toLowerCase().trim();
          this.filterItems();
        }, 300);
      });
    }

    /**
     * Bind AND/OR logic toggle
     */
    bindLogicToggle() {
      if (!this.logicToggle) return;

      const buttons = this.logicToggle.querySelectorAll(".pfg-logic-btn");
      buttons.forEach((btn) => {
        btn.addEventListener("click", () => {
          this.filterLogic = btn.dataset.logic;
          
          // Update container dataset so AJAX picks up the current logic
          this.container.dataset.filterLogic = this.filterLogic;

          // Update button states
          buttons.forEach((b) => b.classList.remove("pfg-logic-btn--active"));
          btn.classList.add("pfg-logic-btn--active");

          this.filterItems();
        });
      });
    }

    /**
     * Bind cascading dropdown events (single-level hierarchy)
     */
    bindCascadingDropdowns() {
      const dropdownContainer = this.container.querySelector('.pfg-cascading-dropdowns');
      if (!dropdownContainer) return;

      const level1Select = dropdownContainer.querySelector('.pfg-level1-select');
      if (!level1Select) return;

      // Level 1 dropdown change
      level1Select.addEventListener('change', () => {
        const level1Value = level1Select.value;

        // Clear active filters and set new one
        this.activeFilters.clear();
        if (level1Value !== '*') {
          this.activeFilters.add(level1Value);
        }

        // Update URL if deep linking enabled
        if (this.deepLinking && level1Value !== '*') {
          this.updateUrl(level1Value);
        } else if (this.deepLinking) {
          this.updateUrl('*');
        }

        this.filterItems();
      });
    }

    /**
     * Filter gallery items with smooth animation
     * Uses masonry-aware repositioning for masonry layouts,
     * FLIP animation for other layouts
     */
    filterItems() {
      // Re-query items from DOM
      this.items = this.container.querySelectorAll(".pfg-item");
      
      // Check if masonry layout — needs different animation strategy
      const isMasonry = this.grid && this.grid.classList.contains("pfg-grid--masonry");
      
      if (isMasonry) {
        this._filterItemsMasonry();
      } else {
        this._filterItemsFLIP();
      }

      // Dispatch custom event for other scripts
      let visibleCount = 0;
      this.items.forEach((item) => {
        if (!item.classList.contains("pfg-item--hidden") && !item.classList.contains("pfg-item--hiding")) {
          visibleCount++;
        }
      });
      this.container.dispatchEvent(
        new CustomEvent("pfg:filtered", {
          bubbles: true,
          detail: {
            filters: Array.from(this.activeFilters),
            logic: this.filterLogic,
            search: this.searchTerm,
            visibleCount: visibleCount,
          },
        })
      );


    }

    /**
     * Masonry-aware filtering: hide/show items then recalculate all positions.
     * CSS transitions on left/top handle smooth repositioning of remaining items.
     */
    _filterItemsMasonry() {
      const itemsToShow = [];
      const itemsToHide = [];

      this.items.forEach((item) => {
        const matchesFilter = this.itemMatchesFilter(item);
        const matchesSearch = this.itemMatchesSearch(item);
        const wasHidden = item.classList.contains("pfg-item--hidden");

        if (matchesFilter && matchesSearch) {
          if (wasHidden) {
            itemsToShow.push(item);
          }
        } else if (!wasHidden) {
          itemsToHide.push(item);
        }
      });

      // Phase 1: Fade out items that need hiding
      itemsToHide.forEach((item) => {
        item.classList.remove("pfg-item--visible", "pfg-item--hidden");
        item.classList.add("pfg-item--hiding");
      });

      // Phase 2: After fade-out completes, collapse hidden items and recalculate layout
      setTimeout(() => {
        // Mark hidden items
        itemsToHide.forEach((item) => {
          item.classList.remove("pfg-item--hiding");
          item.classList.add("pfg-item--hidden");
          item.classList.remove("pfg-item--positioned");
        });

        // Prepare items to show (they'll be positioned by initMasonryLayout)
        itemsToShow.forEach((item) => {
          item.classList.remove("pfg-item--hidden", "pfg-item--hiding");
          item.classList.add("pfg-item--visible");
          item.classList.remove("pfg-item--positioned");
        });

        // Recalculate all masonry positions
        this.initMasonryLayout();

      }, 250); // Wait for hide (opacity) animation
    }

    /**
     * FLIP-based filtering for non-masonry layouts
     * FLIP = First, Last, Invert, Play - for smooth position animations
     */
    _filterItemsFLIP() {
      // FLIP Step 1: FIRST - Record current positions of ALL items
      const firstPositions = new Map();
      this.items.forEach((item) => {
        if (!item.classList.contains("pfg-item--hidden")) {
          const rect = item.getBoundingClientRect();
          firstPositions.set(item, { x: rect.left, y: rect.top });
        }
      });
      
      let visibleIndex = 0;
      const itemsToShow = [];
      const itemsToHide = [];
      const itemsToAnimate = [];

      this.items.forEach((item) => {
        const matchesFilter = this.itemMatchesFilter(item);
        const matchesSearch = this.itemMatchesSearch(item);
        const wasVisible = !item.classList.contains("pfg-item--hidden");

        if (matchesFilter && matchesSearch) {
          if (wasVisible) {
            itemsToAnimate.push(item);
          } else {
            itemsToShow.push({ item, index: visibleIndex });
          }
          visibleIndex++;
        } else if (wasVisible) {
          itemsToHide.push(item);
        }
      });

      itemsToHide.forEach((item) => {
        item.classList.remove("pfg-item--visible", "pfg-item--hidden");
        item.classList.add("pfg-item--hiding");
      });

      setTimeout(() => {
        itemsToHide.forEach((item) => {
          item.classList.remove("pfg-item--hiding");
          item.classList.add("pfg-item--hidden");
        });

        itemsToShow.forEach(({ item }) => {
          item.classList.remove("pfg-item--hidden", "pfg-item--hiding");
          item.style.opacity = "0";
          item.style.transform = "scale(0.9)";
          item.style.transition = "none";
        });

        void this.container.offsetHeight;

        const lastPositions = new Map();
        [...itemsToAnimate, ...itemsToShow.map(i => i.item)].forEach((item) => {
          const rect = item.getBoundingClientRect();
          lastPositions.set(item, { x: rect.left, y: rect.top });
        });

        itemsToAnimate.forEach((item) => {
          const first = firstPositions.get(item);
          const last = lastPositions.get(item);
          item.style.opacity = "1";
          
          if (first && last) {
            const deltaX = first.x - last.x;
            const deltaY = first.y - last.y;
            const didMove = Math.abs(deltaX) > 1 || Math.abs(deltaY) > 1;
            
            item.style.transition = "none";
            if (didMove) {
              item.style.transform = `translate(${deltaX}px, ${deltaY}px) scale(0.97)`;
            } else {
              item.style.transform = "scale(0.97)";
            }
            void item.offsetHeight;
            item.style.transition = "transform 0.35s cubic-bezier(0.4, 0, 0.2, 1)";
            item.style.transform = "translate(0, 0) scale(1)";
          } else {
            item.style.transition = "none";
            item.style.transform = "scale(0.97)";
            void item.offsetHeight;
            item.style.transition = "transform 0.35s cubic-bezier(0.4, 0, 0.2, 1)";
            item.style.transform = "scale(1)";
          }
        });

        itemsToShow.forEach(({ item, index }) => {
          const delay = index * 50;
          setTimeout(() => {
            item.style.transition = "opacity 0.3s ease-out, transform 0.3s ease-out";
            item.style.opacity = "1";
            item.style.transform = "scale(1)";
          }, delay);
        });

        setTimeout(() => {
          itemsToAnimate.forEach((item) => {
            item.style.transition = "";
            item.style.transform = "";
            item.style.opacity = "";
          });
          itemsToShow.forEach(({ item }) => {
            item.style.transition = "";
            item.style.transform = "";
            item.style.opacity = "";
            item.classList.add("pfg-item--visible");
          });
        }, 500);

      }, 280);
    }

    /**
     * Apply mosaic layout with FLIP animation for smooth repositioning
     */
    applyMosaicLayoutWithFLIP(firstPositions) {
      // Apply new layout (this sets new --pfg-x and --pfg-y values)
      this.initMasonryLayout();
      
      // FLIP Step 2 & 3: After layout applied, calculate inverse transforms
      // The CSS will handle the smooth animation via transition
    }

    /**
     * Check if item matches current filter(s)
     */
    itemMatchesFilter(item) {
      // No filters active = show all
      if (this.activeFilters.size === 0) return true;

      const itemFilters = this.getItemFilters(item);

      // Expand active filters to include their children
      const expandedFilters = this.expandFiltersWithChildren([
        ...this.activeFilters,
      ]);

      if (this.filterLogic === "and") {
        // AND logic: item must match ALL active filters (or their children)
        const result = [...this.activeFilters].every((filter) => {
          const filterAndChildren = [
            filter,
            ...(this.filterHierarchy[filter] || []),
          ];
          const matches = filterAndChildren.some((f) => itemFilters.includes(f));
          return matches;
        });
        return result;
      } else {
        // OR logic: item must match ANY active filter (or their children)
        return expandedFilters.some((filter) => itemFilters.includes(filter));
      }
    }

    /**
     * Expand filter list to include all child filters
     */
    expandFiltersWithChildren(filters) {
      const expanded = new Set(filters);
      filters.forEach((filter) => {
        if (this.filterHierarchy[filter]) {
          this.filterHierarchy[filter].forEach((child) => expanded.add(child));
        }
      });
      return [...expanded];
    }

    /**
     * Get all filter slugs for an item
     */
    getItemFilters(item) {
      const classes = Array.from(item.classList);
      return classes
        .filter((c) => c.startsWith("pfg-filter-"))
        .map((c) => c.replace("pfg-filter-", ""));
    }

    /**
     * Check if item matches search term
     */
    itemMatchesSearch(item) {
      if (!this.searchTerm) return true;

      const title = item.querySelector(".pfg-item-title");
      const alt = item.querySelector(".pfg-item-image")?.alt || "";

      const searchableText = (title?.textContent || "") + " " + alt;
      return searchableText.toLowerCase().includes(this.searchTerm);
    }

    /**
     * Show an item with smooth animation
     */
    showItem(item, index) {
      // Remove hidden class
      item.classList.remove("pfg-item--hidden");

      // Remove any previous visible class and re-add for animation
      item.classList.remove("pfg-item--visible");

      // Force reflow to restart animation
      void item.offsetWidth;

      // Add visible class with stagger delay for smooth sequential animation
      const delay = (index % 8) * 50; // 50ms stagger between items
      setTimeout(() => {
        item.classList.add("pfg-item--visible");
      }, delay);

      // Reset inline styles
      item.style.maxHeight = "";
      item.style.margin = "";
      item.style.padding = "";
    }

    /**
     * Hide an item with smooth animation
     */
    hideItem(item) {
      // Just add hidden class - CSS handles animation
      item.classList.add("pfg-item--hidden");
    }

    /**
     * Initialize lazy loading for images
     */
    initLazyLoading() {
      if ("loading" in HTMLImageElement.prototype) {
        // Native lazy loading supported
        return;
      }

      // Fallback for browsers without native lazy loading
      if ("IntersectionObserver" in window) {
        const imageObserver = new IntersectionObserver(
          (entries, observer) => {
            entries.forEach((entry) => {
              if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                  img.src = img.dataset.src;
                  img.removeAttribute("data-src");
                }
                observer.unobserve(img);
              }
            });
          },
          {
            rootMargin: "50px 0px",
          }
        );

        this.items.forEach((item) => {
          const img = item.querySelector("img[data-src]");
          if (img) {
            imageObserver.observe(img);
          }
        });
      }
    }

    /**
     * Initialize JS-positioned masonry layout
     * Masonry: horizontal fill, shortest-column-first (Pinterest-style)
     */
    initMasonryLayout() {
      const masonryGrid = this.grid?.classList.contains("pfg-grid--masonry");

      if (!masonryGrid) {
        return;
      }

      // Mark that this gallery uses masonry layout
      this.usesMosaicLayout = true;

      // Wait for all images to load before calculating layout
      this.waitForImages().then(() => {
        this.applyMasonryLayout();
      });

      // Re-apply on resize with debounce
      let resizeTimeout;
      window.addEventListener("resize", () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
          if (this.usesMosaicLayout) {
            this.applyMasonryLayout();
          }
        }, 200);
      });
    }

    /**
     * Wait for all images to load
     */
    waitForImages() {
      const images = Array.from(this.grid.querySelectorAll("img"));
      const promises = images.map((img) => {
        if (img.complete) {
          return Promise.resolve();
        }
        return new Promise((resolve) => {
          img.addEventListener("load", resolve, { once: true });
          img.addEventListener("error", resolve, { once: true });
        });
      });
      return Promise.all(promises);
    }

    /**
     * Apply masonry layout with absolute positioning
     * Places items horizontally (shortest-column-first) for left-to-right ordering
     */
    applyMasonryLayout() {
      if (!this.grid) return;

      const items = Array.from(
        this.grid.querySelectorAll(".pfg-item:not(.pfg-item--hidden)")
      );
      if (items.length === 0) {
        this.grid.style.height = "0px";
        return;
      }

      const containerWidth = this.grid.offsetWidth;
      const gap =
        parseInt(getComputedStyle(this.grid).getPropertyValue("--pfg-gap")) ||
        10;

      const w = window.innerWidth;
      const styles = getComputedStyle(this.grid);
      let cols;
      if (w >= 1200) {
        cols = parseInt(styles.getPropertyValue("--pfg-cols-xl")) || 4;
      } else if (w >= 992) {
        cols = parseInt(styles.getPropertyValue("--pfg-cols-lg")) || 3;
      } else if (w >= 768) {
        cols = parseInt(styles.getPropertyValue("--pfg-cols-md")) || 2;
      } else {
        cols = parseInt(styles.getPropertyValue("--pfg-cols-sm")) || 1;
      }
      
      const colWidth = (containerWidth - gap * (cols - 1)) / cols;

      const newItems = [];
      const itemData = [];

      items.forEach((item) => {
        const img = item.querySelector("img");
        const imgLink = item.querySelector(".pfg-item-link");

        const isNewItem = !item.classList.contains("pfg-item--positioned");

        if (isNewItem) {
          item.style.transition = "none";
          item.style.opacity = "0";
          item.style.transform = "scale(0.92)";
        }

        item.style.position = "absolute";
        item.style.width = colWidth + "px";

        if (imgLink) {
          imgLink.style.display = "block";
          imgLink.style.width = "100%";
          imgLink.style.height = "auto";
          imgLink.style.overflow = "hidden";
        }
        if (img) {
          img.style.width = "100%";
          img.style.height = "auto";
          img.style.objectFit = "";
        }

        if (isNewItem) {
          newItems.push(item);
        }

        itemData.push({
          item,
          colWidth,
        });
      });

      setTimeout(() => {
        const colHeights = new Array(cols).fill(0);

        itemData.forEach((data) => {
          const { item, colWidth: itemWidth } = data;

          if (item.classList.contains("pfg-item--hidden")) {
            return;
          }

          let itemHeight = Math.ceil(item.offsetHeight);
          if (itemHeight <= 0) {
            const img = item.querySelector("img");
            if (img && img.naturalWidth && img.naturalHeight) {
              itemHeight = Math.ceil(itemWidth / (img.naturalWidth / img.naturalHeight));
            } else {
              itemHeight = Math.ceil(itemWidth);
            }
          }

          // Shortest-column-first placement
          let bestCol = 0;
          for (let c = 1; c < cols; c++) {
            if (colHeights[c] < colHeights[bestCol]) {
              bestCol = c;
            }
          }

          const x = Math.round(bestCol * (colWidth + gap));
          const y = Math.round(colHeights[bestCol]);

          item.style.left = x + "px";
          item.style.top = y + "px";

          if (!item.classList.contains("pfg-item--positioned")) {
            void item.offsetHeight;
            item.classList.add("pfg-item--positioned");
          }

          colHeights[bestCol] = y + itemHeight + gap;
        });

        const maxHeight = Math.max(...colHeights, 0);
        this.grid.style.position = "relative";
        this.grid.style.height = Math.ceil(maxHeight) + "px";

        if (newItems.length > 0) {
          void this.grid.offsetHeight;

          newItems.forEach((item, i) => {
            const delay = i * 40;
            setTimeout(() => {
              item.style.transition = "opacity 0.35s ease-out, transform 0.35s ease-out";
              item.style.opacity = "1";
              item.style.transform = "scale(1)";

              setTimeout(() => {
                item.style.transition = "";
                item.style.opacity = "";
                item.style.transform = "";
              }, 400);
            }, delay);
          });
        }
      }, 50);
    }

    /**
     * Public method to re-apply mosaic layout
     */
    refreshMosaicLayout() {
      if (this.usesMosaicLayout) {
        // Update items reference
        this.items = this.container.querySelectorAll(".pfg-item");

        this.waitForImages().then(() => {
          this.initMasonryLayout();
        });
      }
    }

    /**
     * Public API: Set filter logic programmatically
     */
    setLogic(logic) {
      if (logic === "and" || logic === "or") {
        this.filterLogic = logic;
        this.filterItems();
      }
    }

    /**
     * Public API: Add a filter programmatically
     */
    addFilter(filter) {
      this.activeFilters.add(filter);
      this.filterItems();
    }

    /**
     * Public API: Clear all filters
     */
    clearFilters() {
      this.activeFilters.clear();
      this.filters.forEach((btn) => {
        btn.classList.remove("pfg-filter--active", "pfg-filter--selected");
      });
      const allBtn = this.container.querySelector('[data-filter="*"]');
      if (allBtn) allBtn.classList.add("pfg-filter--active");
      this.filterItems();
    }


  }



  /**
   * Initialize galleries when DOM is ready
   */
  function initGalleries() {
    const galleries = document.querySelectorAll(".pfg-gallery-wrapper");

    galleries.forEach((container) => {
      // Skip if already initialized
      if (container.dataset.pfgInitialized) return;

      container.pfgGallery = new PFGGallery(container);
      container.dataset.pfgInitialized = "true";
    });
  }

  // Initialize on DOM ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initGalleries);
  } else {
    initGalleries();
  }

  // Re-initialize for dynamically added content
  if (typeof MutationObserver !== "undefined") {
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        mutation.addedNodes.forEach((node) => {
          if (node.nodeType === 1) {
            if (node.classList?.contains("pfg-gallery-wrapper")) {
              node.pfgGallery = new PFGGallery(node);
            } else {
              const galleries = node.querySelectorAll?.(
                ".pfg-gallery-wrapper:not([data-pfg-initialized])"
              );
              galleries?.forEach((g) => {
                g.pfgGallery = new PFGGallery(g);
              });
            }
          }
        });
      });
    });

    observer.observe(document.body, {
      childList: true,
      subtree: true,
    });
  }

  // Expose to global scope for external use
  window.PFGGallery = PFGGallery;
  window.pfgInitGalleries = initGalleries;
})();
