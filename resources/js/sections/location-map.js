import { AttributionControl, Map, Marker, setWorkerUrl } from 'maplibre-gl';
import workerUrl from 'maplibre-gl/dist/maplibre-gl-worker.mjs?url';
import 'maplibre-gl/dist/maplibre-gl.css';

const MAP_STYLES = {
    voyager: 'https://basemaps.cartocdn.com/gl/voyager-gl-style/style.json',
    positron: 'https://basemaps.cartocdn.com/gl/positron-gl-style/style.json',
    dark: 'https://basemaps.cartocdn.com/gl/dark-matter-gl-style/style.json',
};

function createMarkerElement(brandName, subtitle) {
    const wrap = document.createElement('div');
    wrap.className = 'umco-map-marker relative flex flex-col items-center cursor-pointer select-none';

    wrap.innerHTML = `
        <div class="mb-2 rounded-xl bg-[var(--kertas-terang)] px-3 py-1.5 text-center border border-[#1B5E42]/15 pointer-events-none" style="box-shadow: 0 8px 22px -6px rgba(15, 23, 42, 0.22);">
            <span class="block font-display font-bold text-xs tracking-tight text-[#161615] leading-tight">${brandName}</span>
            <span class="block text-[10px] font-medium text-[#1B5E42] leading-tight">${subtitle}</span>
        </div>
        <div class="relative flex items-center justify-center w-8 h-8">
            <span class="absolute inset-0 rounded-full bg-[#1B5E42]/28 animate-ping"></span>
            <span class="relative flex items-center justify-center w-6 h-6 rounded-full bg-[#1B5E42] border-[2.5px] border-white" style="box-shadow: 0 4px 12px rgba(27, 94, 66, 0.45);">
                <span class="w-2 h-2 rounded-full bg-white"></span>
            </span>
        </div>
    `;

    return wrap;
}

function sync3DExtrusion(map, enabled) {
    try {
        const layerId = 'umco-3d-buildings';
        if (!enabled) {
            if (map.getLayer(layerId)) {
                map.removeLayer(layerId);
            }
            return;
        }

        if (map.getLayer(layerId)) return;

        const style = map.getStyle();
        const sources = style?.sources || {};
        const vectorSourceKey = Object.keys(sources).find((k) => sources[k]?.type === 'vector');
        if (!vectorSourceKey) return;

        const hasBuildingLayer = (style.layers || []).some((l) => l['source-layer'] === 'building');
        if (!hasBuildingLayer) return;

        map.addLayer({
            id: layerId,
            source: vectorSourceKey,
            'source-layer': 'building',
            type: 'fill-extrusion',
            minzoom: 14,
            paint: {
                'fill-extrusion-color': '#D7E2D9',
                'fill-extrusion-height': ['coalesce', ['get', 'render_height'], 8],
                'fill-extrusion-base': ['coalesce', ['get', 'render_min_height'], 0],
                'fill-extrusion-opacity': 0.58,
            },
        });
    } catch {
        // Fallback aman jika style vektor tidak memuat layer bangunan
    }
}

export function init(el) {
    const canvasEl = el.querySelector('[data-map-canvas]');
    if (!canvasEl) return;

    const lat = Number(el.dataset.lat || '0.4999292');
    const lng = Number(el.dataset.lng || '101.4155284');
    const brandName = el.dataset.brand || 'UMCO House';
    const subtitle = el.dataset.subtitle || 'Kampus 2 UMRI Pekanbaru';
    const center = [lng, lat];

    let activeStyleKey = 'voyager';
    let is3d = false;
    let map = null;
    let marker = null;

    try {
        setWorkerUrl(workerUrl);

        map = new Map({
            container: canvasEl,
            style: MAP_STYLES.voyager,
            center,
            zoom: 16.2,
            pitch: 0,
            bearing: 0,
            scrollZoom: false,
            attributionControl: false,
        });

        map.addControl(new AttributionControl({ compact: true }), 'bottom-left');

        const markerEl = createMarkerElement(brandName, subtitle);
        markerEl.addEventListener('click', () => {
            map.flyTo({
                center,
                zoom: is3d ? 16.8 : 16.3,
                pitch: is3d ? 54 : 0,
                bearing: is3d ? -18 : 0,
                duration: 750,
            });
        });

        marker = new Marker({ element: markerEl, anchor: 'bottom' })
            .setLngLat(center)
            .addTo(map);
    } catch {
        el.dispatchEvent(new CustomEvent('umco:map-fallback', { bubbles: false }));
        return;
    }

    const onStyleChange = (e) => {
        const key = e.detail?.style;
        if (!map || !key || !MAP_STYLES[key] || key === activeStyleKey) return;
        activeStyleKey = key;
        map.setStyle(MAP_STYLES[key]);
        if (is3d) {
            map.once('style.load', () => sync3DExtrusion(map, true));
        }
    };

    const onToggle3D = (e) => {
        if (!map) return;
        is3d = typeof e.detail?.enabled === 'boolean' ? e.detail.enabled : !is3d;
        sync3DExtrusion(map, is3d);
        map.easeTo({
            pitch: is3d ? 54 : 0,
            bearing: is3d ? -18 : 0,
            zoom: is3d ? Math.max(map.getZoom(), 16.5) : 16.2,
            duration: 650,
        });
    };

    const onFocusCenter = () => {
        if (!map) return;
        map.flyTo({
            center,
            zoom: is3d ? 16.6 : 16.2,
            pitch: is3d ? 54 : 0,
            bearing: is3d ? -18 : 0,
            duration: 750,
        });
    };

    const onZoomIn = () => map?.zoomIn({ duration: 250 });
    const onZoomOut = () => map?.zoomOut({ duration: 250 });

    el.addEventListener('umco:map-style', onStyleChange);
    el.addEventListener('umco:map-3d', onToggle3D);
    el.addEventListener('umco:map-focus', onFocusCenter);
    el.addEventListener('umco:map-zoom-in', onZoomIn);
    el.addEventListener('umco:map-zoom-out', onZoomOut);

    return () => {
        el.removeEventListener('umco:map-style', onStyleChange);
        el.removeEventListener('umco:map-3d', onToggle3D);
        el.removeEventListener('umco:map-focus', onFocusCenter);
        el.removeEventListener('umco:map-zoom-in', onZoomIn);
        el.removeEventListener('umco:map-zoom-out', onZoomOut);
        marker?.remove();
        map?.remove();
    };
}
