/**
 * BrandGEO Nova tool — hand-written (no build step).
 *
 * Registers an Inertia page inside the Nova SPA that renders the branded
 * BrandGEO dashboard in an iframe sharing Nova's menu and chrome. The iframe
 * auto-resizes to its content height (the embedded page reports it via
 * postMessage), so there are no inner scrollbars — the page scrolls as one.
 *
 * The dashboard also follows Nova's light/dark theme: the initial scheme rides
 * along on the iframe URL (right on first paint, no flash), and later toggles
 * are pushed down via postMessage — reassigning src would reload the whole
 * dashboard, and its API data, on every theme switch.
 */
Nova.booting(function () {
    var isDark = function () {
        return document.documentElement.classList.contains('dark');
    };

    Nova.inertia('BrandGeoNovaTool', {
        name: 'BrandGeoNovaTool',
        props: {
            src: { type: String, required: true },
        },
        data: function () {
            return { height: window.innerHeight, dark: isDark() };
        },
        computed: {
            frameSrc: function () {
                return this.src
                    + (this.src.indexOf('?') === -1 ? '?' : '&')
                    + 'theme=' + (this.dark ? 'dark' : 'light');
            },
        },
        mounted: function () {
            var self = this;

            this.onMessage = function (event) {
                if (event.data && typeof event.data.brandgeoHeight === 'number') {
                    self.height = event.data.brandgeoHeight;
                }
            };
            window.addEventListener('message', this.onMessage);

            // Nova flips a `dark` class on <html> when the user switches theme
            // — mirror it into the iframe without reloading it.
            this.themeObserver = new MutationObserver(function () {
                var dark = isDark();

                if (dark === self.dark) {
                    return;
                }

                self.dark = dark;

                var frame = self.$el && self.$el.querySelector('iframe');

                if (frame && frame.contentWindow) {
                    frame.contentWindow.postMessage({ brandgeoTheme: dark ? 'dark' : 'light' }, '*');
                }
            });
            this.themeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
        },
        beforeUnmount: function () {
            window.removeEventListener('message', this.onMessage);

            if (this.themeObserver) {
                this.themeObserver.disconnect();
            }
        },
        render: function () {
            // Negative margins bleed over Nova's content padding so the
            // dashboard fills the content area edge to edge. The backdrop
            // matches the embedded page's own background in each theme.
            return window.Vue.h(
                'div',
                { style: 'margin:-1.5rem;overflow:hidden;background:' + (this.dark ? '#09090b' : '#fafafa') + ';' },
                [window.Vue.h('iframe', {
                    src: this.frameSrc,
                    scrolling: 'no',
                    style: 'width:100%;height:' + this.height + 'px;border:0;display:block;overflow:hidden;',
                })]
            );
        },
    });
});
