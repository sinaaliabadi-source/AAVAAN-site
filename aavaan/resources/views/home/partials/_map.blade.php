{{-- ===== ۲. نقشه‌ی تراکم استعداد (D3 + مرز واقعی کشورها) ===== --}}
<section class="map-section">
    <div class="container">
        <h2 class="section-title">{{ __('home.map_title') }}</h2>
        <p class="section-sub">{{ __('home.map_subtitle') }}</p>

        <div
            id="talent-map"
            class="talent-map"
            data-geo="{{ asset('data/countries.geo.json') }}"
            data-api="{{ route('api.talent-density') }}"
            data-label-artists="{{ __('home.map_artists') }}"
        >
            <div class="talent-map__loading" data-map-loading>{{ __('home.map_loading') }}</div>
            <svg id="talent-map-svg" role="img" aria-label="{{ __('home.map_title') }}"></svg>
            <div class="talent-map__tooltip" data-map-tooltip hidden></div>
        </div>

        <p class="map-legend">{{ __('home.map_legend') }}</p>
    </div>
</section>

@push('styles')
<style>
.map-section { padding: 5rem 0; background: #fff; }
.talent-map {
    position: relative;
    max-width: 900px;
    margin: 0 auto;
    background:
        radial-gradient(circle at 50% 35%, rgba(31,42,68,.05), transparent 60%),
        var(--color-bg);
    border-radius: var(--radius);
    border: 1.5px solid rgba(31,42,68,.08);
    overflow: hidden;
}
.talent-map svg { display: block; width: 100%; height: auto; }
.talent-map__loading {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-muted);
    font-size: .95rem;
    pointer-events: none;
}
.talent-map .country { transition: fill .2s; }
.talent-map .pin {
    fill: var(--color-accent);
    fill-opacity: .78;
    stroke: #fff;
    stroke-width: 1;
    cursor: pointer;
    transition: fill-opacity .2s, stroke-width .2s;
}
.talent-map .pin:hover { fill: var(--color-coral, #D9724F); fill-opacity: .95; stroke-width: 1.6; }
.talent-map .pin-core { fill: var(--color-primary); pointer-events: none; }
.talent-map__tooltip {
    position: absolute;
    transform: translate(-50%, -130%);
    background: var(--color-primary);
    color: #fff;
    font-size: .82rem;
    font-weight: 600;
    padding: .35rem .7rem;
    border-radius: 6px;
    white-space: nowrap;
    pointer-events: none;
    box-shadow: 0 4px 12px rgba(0,0,0,.18);
    z-index: 5;
}
.talent-map__tooltip::after {
    content: '';
    position: absolute;
    left: 50%;
    top: 100%;
    transform: translateX(-50%);
    border: 5px solid transparent;
    border-top-color: var(--color-primary);
}
.map-legend {
    text-align: center;
    color: var(--color-muted);
    font-size: .88rem;
    margin-top: 1.5rem;
}
</style>
@endpush

@push('scripts')
<script src="https://d3js.org/d3.v7.min.js" defer></script>
<script defer>
(function () {
    function initTalentMap() {
        var container = document.getElementById('talent-map');
        if (!container || typeof d3 === 'undefined') return;

        var svgEl     = document.getElementById('talent-map-svg');
        var loadingEl = container.querySelector('[data-map-loading]');
        var tooltipEl = container.querySelector('[data-map-tooltip]');
        var geoUrl    = container.dataset.geo;
        var apiUrl    = container.dataset.api;
        var labelArtists = container.dataset.labelArtists || '';

        // اعداد فارسی برای نمایش
        function toFa(n) {
            return String(n).replace(/\d/g, function (d) {
                return '۰۱۲۳۴۵۶۷۸۹'[d];
            });
        }

        var width = 900, height = 620;
        var svg = d3.select(svgEl)
            .attr('viewBox', '0 0 ' + width + ' ' + height)
            .attr('preserveAspectRatio', 'xMidYMid meet');
        var gCountries = svg.append('g').attr('class', 'countries');
        var gPins      = svg.append('g').attr('class', 'pins');

        // محدوده‌ی نقشه: ایران و همسایه‌ها (ترکیه، عراق، ارمنستان، آذربایجان،
        // ترکمنستان، افغانستان، پاکستان، کشورهای حاشیه خلیج فارس)
        var viewport = {
            type: 'Feature',
            geometry: {
                type: 'Polygon',
                coordinates: [[[40, 23], [72, 23], [72, 42], [40, 42], [40, 23]]]
            }
        };

        var projection = d3.geoMercator();
        var path = d3.geoPath(projection);

        function isIran(d) {
            return d.id === 'IRN' || (d.properties && d.properties.name === 'Iran');
        }

        function done() { if (loadingEl) loadingEl.remove(); }

        d3.json(geoUrl).then(function (world) {
            projection.fitExtent([[20, 20], [width - 20, height - 20]], viewport);

            gCountries.selectAll('path')
                .data(world.features)
                .join('path')
                .attr('class', 'country')
                .attr('d', path)
                .attr('fill', function (d) { return isIran(d) ? 'rgba(31,42,68,.10)' : '#ece4d3'; })
                .attr('stroke', function (d) { return isIran(d) ? 'rgba(31,42,68,.55)' : 'rgba(31,42,68,.18)'; })
                .attr('stroke-width', function (d) { return isIran(d) ? 1.2 : 0.6; });

            done();

            // پین شهرها از endpoint واقعی
            d3.json(apiUrl).then(function (density) {
                if (!density || !density.cities || !density.cities.length) return;

                var max = density.max || d3.max(density.cities, function (c) { return c.count; }) || 1;
                var r = d3.scaleSqrt().domain([0, max]).range([3, 20]);

                var pins = gPins.selectAll('g.pin-group')
                    .data(density.cities)
                    .join('g')
                    .attr('class', 'pin-group')
                    .attr('transform', function (d) {
                        var p = projection([d.lng, d.lat]);
                        return p ? 'translate(' + p[0] + ',' + p[1] + ')' : 'translate(-100,-100)';
                    });

                pins.append('circle')
                    .attr('class', 'pin')
                    .attr('r', function (d) { return r(d.count); });

                pins.append('circle')
                    .attr('class', 'pin-core')
                    .attr('r', 1.6);

                pins.on('mousemove', function (event, d) {
                    if (!tooltipEl) return;
                    var rect = container.getBoundingClientRect();
                    tooltipEl.hidden = false;
                    tooltipEl.textContent = d.city + ' — ' + toFa(d.count) + ' ' + labelArtists;
                    tooltipEl.style.left = (event.clientX - rect.left) + 'px';
                    tooltipEl.style.top  = (event.clientY - rect.top) + 'px';
                }).on('mouseleave', function () {
                    if (tooltipEl) tooltipEl.hidden = true;
                });
            }).catch(function () { /* endpoint در دسترس نبود؛ نقشه بدون پین می‌ماند */ });
        }).catch(function () { done(); });
    }

    if (window.d3) {
        initTalentMap();
    } else {
        window.addEventListener('load', initTalentMap);
    }
})();
</script>
@endpush
