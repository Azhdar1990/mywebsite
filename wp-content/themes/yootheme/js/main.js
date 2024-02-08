import UIkit from 'uikit';
import { $$, addClass, hasClass, isRtl, once, ready, swap } from 'uikit-util';
import { Header, Sticky } from './header';

UIkit.component('Header', Header);
UIkit.mixin(Sticky, 'sticky');

if (isRtl) {
    const mixin = {
        created() {
            this.$props.pos = swap(this.$props.pos, 'left', 'right');
        },
    };

    UIkit.mixin(mixin, 'drop');
    UIkit.mixin(mixin, 'tooltip');
}

once(document, 'uikit:init', () => {
    const { $theme: { i18n = {} } = {} } = window;
    for (const component in i18n) {
        UIkit.mixin({ i18n: i18n[component] }, component);
    }
});

ready(() => {
    const { $load = [], $theme = {} } = window;

    function load(stack, config) {
        stack.length && stack.shift()(config, () => load(stack, config));
    }

    load($load, $theme);

    // Check for :has() support
    if (!CSS.supports('selector(html:has(body))')) {
        for (const el of $$('.tm-page > * > .uk-section-overlap:last-child')) {
            addClass(el.parentElement, 'tm-has-section-overlap');

            if (hasClass(el, 'uk-section-overlap-flip')) {
                addClass(el.parentElement, 'tm-has-section-overlap-flip');
            }
        }

        for (const el of $$('.tm-page > * > .uk-position-z-index-negative')) {
            addClass(el.parentElement, 'tm-has-z-index-negative');
        }
    }
});
