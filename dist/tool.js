/**
 * BrandGEO Nova tool — hand-written (no build step).
 *
 * Registers an Inertia page inside the Nova SPA that renders the branded
 * BrandGEO dashboard in an iframe sharing Nova's menu and chrome. The iframe
 * auto-resizes to its content height (the embedded page reports it via
 * postMessage), so there are no inner scrollbars — the page scrolls as one.
 */
Nova.booting(function () {
    Nova.inertia('BrandGeoNovaTool', {
        name: 'BrandGeoNovaTool',
        props: {
            src: { type: String, required: true },
        },
        data: function () {
            return { height: window.innerHeight };
        },
        mounted: function () {
            var self = this;
            this.onMessage = function (event) {
                if (event.data && typeof event.data.brandgeoHeight === 'number') {
                    self.height = event.data.brandgeoHeight;
                }
            };
            window.addEventListener('message', this.onMessage);
        },
        beforeUnmount: function () {
            window.removeEventListener('message', this.onMessage);
        },
        render: function () {
            // Negative margins bleed over Nova's content padding so the
            // dashboard fills the content area edge to edge.
            return window.Vue.h(
                'div',
                { style: 'margin:-1.5rem;overflow:hidden;background:#09090b;' },
                [window.Vue.h('iframe', {
                    src: this.src,
                    scrolling: 'no',
                    style: 'width:100%;height:' + this.height + 'px;border:0;display:block;overflow:hidden;',
                })]
            );
        },
    });
});
