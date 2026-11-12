/**
 * DataTables Global State Manager
 * -------------------------------------------------------------
 * Automatically persists and restores:
 * 1. Pagination page number (e.g. Page 3, 5, etc.)
 * 2. Search input value
 * 3. Column sorting order
 * 4. Page length / records per page
 * 5. All custom filter inputs and selects (inside .card-body or [data-filter-container])
 *
 * Rules:
 * - State is PRESERVED when:
 *   - Returning from Show / View / Details page
 *   - Returning from Edit page (via Cancel button or form update redirect)
 *   - Using Breadcrumb navigation back to the list
 *   - Using browser Back / Forward buttons
 *
 * - State is RESET when:
 *   - Clicking any menu item in the left Sidebar (#aside)
 *   - Clicking any "Clear" / "Reset" button (.clear_button)
 */
(function(window, $) {
    'use strict';

    if (!$) return;

    // Helper: Collect all custom filter values from filter containers
    function collectCustomFilters() {
        var filters = {};
        var $containers = $('.card-body, [data-filter-container]');
        
        $containers.find('input, select').each(function() {
            var $el = $(this);
            var id = $el.attr('id') || $el.attr('name');
            var type = ($el.attr('type') || '').toLowerCase();
            
            // Skip buttons, submits, hidden tokens
            if (!id || type === 'button' || type === 'submit' || type === 'hidden') return;
            if (id === '_token' || id === 'action' || id === 'checkAll') return;

            if (type === 'checkbox' || type === 'radio') {
                filters[id] = $el.prop('checked');
            } else {
                filters[id] = $el.val();
            }
        });
        return filters;
    }

    // Helper: Restore custom filter values to DOM elements before DataTables queries
    function restoreCustomFilters(filters) {
        if (!filters || typeof filters !== 'object') return;

        $.each(filters, function(id, val) {
            var $el = $('#' + id);
            if (!$el.length) {
                $el = $('[name="' + id + '"]');
            }
            if ($el.length && val !== null && val !== undefined) {
                var type = ($el.attr('type') || '').toLowerCase();
                if (type === 'checkbox' || type === 'radio') {
                    $el.prop('checked', !!val);
                } else {
                    $el.val(val);
                }

                // Automatically keep corresponding hidden export inputs in sync
                var $export = $('#export_' + id);
                if ($export.length) {
                    $export.val(val ? String(val).toLowerCase() : '');
                }
            }
        });
    }

    // Enhance options ONLY during real table initialization
    function enhanceDataTableOptions(options) {
        var opts = $.extend({}, options);

        // Enable stateSave with sessionStorage (-1) by default unless explicitly disabled
        if (opts.stateSave === undefined) {
            opts.stateSave = true;
        }
        if (opts.stateDuration === undefined) {
            opts.stateDuration = -1; // sessionStorage: active per browser tab
        }

        var origSaveParams = opts.stateSaveParams;
        var origLoadParams = opts.stateLoadParams;

        // Save custom filters into DataTable state object
        opts.stateSaveParams = function(settings, data) {
            data.auto_custom_filters = collectCustomFilters();
            if (typeof origSaveParams === 'function') {
                origSaveParams.call(this, settings, data);
            }
        };

        // Restore custom filters from DataTable state object
        opts.stateLoadParams = function(settings, data) {
            if (data && data.auto_custom_filters) {
                restoreCustomFilters(data.auto_custom_filters);
            }
            if (typeof origLoadParams === 'function') {
                origLoadParams.call(this, settings, data);
            }
        };

        return opts;
    }

    // Intercept DataTables core initialization method ($.fn.dataTable)
    // Note: $.fn.DataTable internally calls $(this).dataTable(opts).api()
    if ($.fn && $.fn.dataTable) {
        var _originalDataTable = $.fn.dataTable;

        $.fn.dataTable = function(options) {
            // Case 1: If called with no arguments or empty object, it's an instance lookup (e.g. $('#label').DataTable())
            if (!options || $.isEmptyObject(options)) {
                return _originalDataTable.call(this, options);
            }

            // Case 2: If the table is already initialized and destroy is not requested, do not re-enhance
            var isAlreadyInit = false;
            this.each(function() {
                if ($.fn.dataTable.isDataTable && $.fn.dataTable.isDataTable(this)) {
                    isAlreadyInit = true;
                    return false;
                }
            });
            if (isAlreadyInit && !options.destroy && !options.bDestroy) {
                return _originalDataTable.call(this, options);
            }

            // Case 3: Initializing table -> inject state persistence
            var enhancedOptions = enhanceDataTableOptions(options);
            return _originalDataTable.call(this, enhancedOptions);
        };

        // Copy static methods and properties (isDataTable, ext, defaults, etc.)
        for (var prop in _originalDataTable) {
            if (_originalDataTable.hasOwnProperty(prop)) {
                $.fn.dataTable[prop] = _originalDataTable[prop];
            }
        }
    }

    // Helper: Clear DataTables storage
    function clearDataTablesStorage(targetPath) {
        try {
            for (var i = sessionStorage.length - 1; i >= 0; i--) {
                var key = sessionStorage.key(i);
                if (key && (key.indexOf('DataTables') !== -1 || key.indexOf('dt_') !== -1)) {
                    if (!targetPath || key.indexOf(targetPath) !== -1) {
                        sessionStorage.removeItem(key);
                    }
                }
            }
        } catch (err) {}
    }

    // Helper: Clear active DataTables on page
    function clearActiveTables() {
        if ($.fn && $.fn.dataTable) {
            $('table').each(function() {
                try {
                    if ($.fn.dataTable.isDataTable(this)) {
                        $(this).DataTable().state.clear();
                    }
                } catch (err) {}
            });
        }
    }

    // Clean up inline "onclick" attributes on Clear buttons to prevent premature page reload
    $(document).ready(function() {
        $('.clear_button a, .clear_button button, [data-action="clear-filter"]').removeAttr('onclick');
    });

    // 1. Sidebar Navigation Click Handler
    // When clicking any link in the sidebar menu (#aside), clear that module's state so it opens fresh
    $(document).on('click', '#aside a[href]', function(e) {
        var href = $(this).attr('href');
        if (!href || href === '#' || href.indexOf('javascript') === 0) return;

        try {
            var parser = document.createElement('a');
            parser.href = href;
            var targetPath = parser.pathname;

            // Clear saved state for the target page
            clearDataTablesStorage(targetPath);

            // If the user clicks the menu link for the CURRENT page they are already on:
            if (targetPath === window.location.pathname) {
                e.preventDefault();
                e.stopPropagation();

                clearActiveTables();
                clearDataTablesStorage(window.location.pathname);
                $('.card-body input:not([type="button"]):not([type="submit"]):not([type="hidden"]), .card-body select').val('');

                window.location.href = href;
            }
        } catch (err) {}
    });

    // 2. Global Clear / Reset Handler for any listing page (.clear_button)
    $(document).on('click', '.clear_button, .clear_button button, .clear_button a, [data-action="clear-filter"], #clear_filter_btn', function(e) {
        e.preventDefault();
        e.stopPropagation();

        // Clear active tables state
        clearActiveTables();

        // Clear sessionStorage for this page
        clearDataTablesStorage(window.location.pathname);

        // Clear all input and select elements in .card-body
        $('.card-body input:not([type="button"]):not([type="submit"]):not([type="hidden"]), .card-body select').val('');

        // Reload page cleanly without old query/state
        window.location.href = window.location.pathname;
    });

})(window, window.jQuery);
