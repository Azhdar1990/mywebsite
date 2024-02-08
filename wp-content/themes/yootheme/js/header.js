import {
    $,
    $$,
    addClass,
    before,
    css,
    hasAttr,
    hasClass,
    observeMutation,
    observeResize,
    offset,
    removeClass,
    toPx,
} from 'uikit-util';

export const Header = {
    async connected() {
        this.apply(hasAttr(this.$el, 'tm-header-transparent'));

        this.section = await getSection();

        this.apply(hasAttr(this.section, 'tm-header-transparent'));

        this.placeholder =
            hasClass(this.$el, 'tm-header-overlay') &&
            !hasClass(this.section, 'tm-header-transparent-no-placeholder');

        if (this.placeholder) {
            observeResize(this.$el, () => this.$emit());
        }

        this.$emit();
    },

    update: [
        {
            read() {
                return this.placeholder && this.$el.offsetHeight
                    ? { height: this.$el.offsetHeight }
                    : false;
            },

            write({ height }) {
                const placeholder =
                    $('.tm-header-placeholder', this.section) ||
                    before(
                        $('.uk-grid', this.section),
                        '<div class="tm-header-placeholder uk-margin-remove-adjacent">'
                    );

                css(placeholder, { height });
            },
        },
    ],
    methods: {
        apply(transparent) {
            if (!transparent || hasClass(this.$el, 'tm-header-overlay')) {
                return;
            }

            addClass(this.$el, 'tm-header-overlay');

            addClass(
                $$('.tm-headerbar-top, .tm-headerbar-bottom, .js-toolbar-transparent'),
                `uk-${css(this.section, '--uk-navbar-color')}`
            );

            removeClass($$('.tm-headerbar-top, .tm-headerbar-bottom'), 'tm-headerbar-default');
            removeClass($('.js-toolbar-transparent'), 'tm-toolbar-default');

            if (!$('[uk-sticky]', this.$el) || hasAttr(this.$el, 'tm-header-transparent')) {
                addClass($('.uk-navbar-container', this.$el), 'uk-navbar-transparent');
            }
        },
    },
};

export const Sticky = {
    async connected() {
        const header = this.$el.closest('[uk-header]');
        if (!header || hasAttr(header, 'tm-header-transparent')) {
            return;
        }

        this.section = await getSection();

        if (!this.section || !hasAttr(this.section, 'tm-header-transparent')) {
            return;
        }

        this.transparent = true;
        this.animation = 'uk-animation-slide-top';
        this.clsInactive = `uk-navbar-transparent uk-${css(this.section, '--uk-navbar-color')}`;

        if (!this.active) {
            this.setActive(false);
            preventTransition(this.selTarget);
        }
    },

    update: {
        read() {
            if (!this.transparent) {
                return;
            }

            return {
                start:
                    this.section.offsetHeight <= toPx('100vh')
                        ? offset(this.section).bottom
                        : offset(this.section).top + 300,
            };
        },

        events: ['resize'],
    },
};

let promise;
const getSection = () =>
    (promise =
        promise ||
        new Promise((resolve) => {
            const selector =
                '.tm-header ~ [class*="uk-section"], .tm-header ~ * > [class*="uk-section"]';

            let section = $(selector);
            if (section) {
                resolve(section);
            } else {
                observeMutation(
                    document.body,
                    (records, observer) => {
                        section = $(selector);
                        if (section) {
                            observer.disconnect();
                            resolve(section);
                        }
                    },
                    { childList: true, subtree: true }
                );
            }
        }));

function preventTransition(el) {
    css(el, 'transition', '0s');
    requestAnimationFrame(() => css(el, 'transition', ''));
}
