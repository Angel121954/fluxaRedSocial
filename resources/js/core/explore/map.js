/**
 * map.js — Mapa de desarrolladores con Leaflet + MarkerCluster
 * Se activa solo cuando el tab "Mapa" está visible.
 * @vite('resources/js/core/explore/map.js')
 */

import L from 'leaflet';
import 'leaflet.markercluster';

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('dev-map');
    if (!container) return;

    let map = null;
    let markerClusterGroup = null;

    async function initMap() {
        if (map) return;

        map = L.map('dev-map', {
            center: [-8.78, -55.49],
            zoom: 4,
            zoomControl: true,
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 18,
        }).addTo(map);

        markerClusterGroup = L.markerClusterGroup({
            maxClusterRadius: 50,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
        });

        try {
            const response = await fetch('/api/map/users', {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });

            if (!response.ok) throw new Error('Error al cargar usuarios');

            const users = await response.json();
            const bounds = [];

            users.forEach((user) => {
                const lat = parseFloat(user.latitude);
                const lng = parseFloat(user.longitude);
                if (isNaN(lat) || isNaN(lng)) return;

                const marker = L.marker([lat, lng]);

                const popupContent = `
                    <div class="map-popup">
                        <img src="${user.avatar}" alt="" class="map-popup-avatar" loading="lazy" />
                        <div class="map-popup-info">
                            <a href="${user.url}" class="map-popup-name">${user.name}</a>
                            <span class="map-popup-handle">@${user.username}</span>
                        </div>
                    </div>
                `;

                marker.bindPopup(popupContent, {
                    closeButton: true,
                    maxWidth: 280,
                    className: '',
                });

                markerClusterGroup.addLayer(marker);
                bounds.push([lat, lng]);
            });

            map.addLayer(markerClusterGroup);

            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [30, 30], maxZoom: 12 });
            }

            renderNearbyDevs(users);
        } catch (error) {
            console.error('Error cargando mapa:', error);
        }
    }

    function renderNearbyDevs(users) {
        const sidebar = document.getElementById('nearby-devs');
        if (!sidebar || users.length === 0) return;

        const devs = users.slice(0, 3);

        sidebar.innerHTML = devs
            .map(
                (user) => `
                <div class="nearby-dev">
                    <img src="${user.avatar}" alt="" class="nearby-dev-avatar" loading="lazy" />
                    <div class="nearby-dev-info">
                        <a href="${user.url}" class="nearby-dev-name">${user.name}</a>
                        <span class="nearby-dev-location">@${user.username}</span>
                    </div>
                </div>
            `,
            )
            .join('');
    }

    function destroyMap() {
        if (map) {
            map.remove();
            map = null;
            markerClusterGroup = null;
        }

        const sidebar = document.getElementById('nearby-devs');
        if (sidebar) sidebar.innerHTML = '';
    }

    // Inicializar cuando el tab de mapa se activa
    const mapTab = document.querySelector('.feed-tab[data-tab="map"]');
    if (mapTab) {
        mapTab.addEventListener('click', () => {
            // Inicializar en el siguiente frame para que el DOM del mapa esté visible
            requestAnimationFrame(() => {
                initMap();
                map.invalidateSize();
            });
        });
    }

    // Si el mapa ya está visible al cargar (p.ej. ruta directa /explore/map)
    if (container.offsetParent !== null) {
        initMap();
    }

    // Destruir al cambiar a otro tab (excepto si el propio tab mapa ya está activo)
    document.querySelectorAll('.feed-tab:not([data-tab="map"])').forEach((tab) => {
        tab.addEventListener('click', () => {
            destroyMap();
        });
    });

    // Reinicializar si el contenedor cambia de visibility (pestañas de navegador)
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden && map) {
            map.invalidateSize();
        }
    });
});
