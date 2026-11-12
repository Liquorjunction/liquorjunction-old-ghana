
/* // mobile-menu */

$(function () {
    const body = $('body');
    const mobileMenu = $('.mobile-menu');
    const mobileMenuBody = mobileMenu.children('.mobile-menu__body');

    if (mobileMenu.length) {
        const open = function() {
            const bodyWidth = body.width();
            body.css('overflow', 'hidden');
            body.css('paddingRight', (body.width() - bodyWidth) + 'px');

            mobileMenu.addClass('mobile-menu--open');
        };
        const close = function() {
            body.css('overflow', 'auto');
            body.css('paddingRight', '');

            mobileMenu.removeClass('mobile-menu--open');
        };

        $('.mobile-header__menu-button').on('click', function() {
            open();
        });
        $('.mobile-menu__backdrop, .mobile-menu__close').on('click', function() {
            close();
        });
    }

    const panelsStack = [];
    let currentPanel = mobileMenuBody.children('.mobile-menu__panel');

    mobileMenu.on('click', '[data-mobile-menu-trigger]', function(event) {
        const trigger = $(this);
        const item = trigger.closest('[data-mobile-menu-item]');
        let panel = item.data('panel');

        if (!panel) {
            panel = item.children('[data-mobile-menu-panel]').children('.mobile-menu__panel');

            if (panel.length) {
                mobileMenuBody.append(panel);
                item.data('panel', panel);
                panel.width(); // force reflow
            }
        }

        if (panel && panel.length) {
            event.preventDefault();

            panelsStack.push(currentPanel);
            currentPanel.addClass('mobile-menu__panel--hide');

            panel.removeClass('mobile-menu__panel--hidden');
            currentPanel = panel;
        }
    });
    mobileMenu.on('click', '.mobile-menu__panel-back', function() {
        currentPanel.addClass('mobile-menu__panel--hidden');
        currentPanel = panelsStack.pop();
        currentPanel.removeClass('mobile-menu__panel--hide');
    });
});