document.addEventListener("DOMContentLoaded", function () {
    let map = L.map('map').setView([22.3251, 73.1975], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    fetch('/app.json')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            let route = data.predefinedRoute;
            let coordinates = route.map(stop => [stop.lat, stop.lng]);

            // Add polyline for predefined route
            L.polyline(coordinates, { color: 'blue', weight: 4 }).addTo(map);

            // Add markers for each stop
            route.forEach(stop => {
                L.marker([stop.lat, stop.lng])
                    .addTo(map)
                    .bindPopup(stop.name);
            });

            let busMarker = L.marker(coordinates[0]).addTo(map);

            window.startTracking = function () {
                let index = 0;
                let interval = setInterval(() => {
                    if (index < coordinates.length) {
                        busMarker.setLatLng(coordinates[index]);
                        map.panTo(coordinates[index]);
                        index++;
                    } else {
                        clearInterval(interval);
                    }
                }, 3000);
            };
        })
        .catch(error => {
            console.error("Error loading route data:", error);
            alert("Failed to load route data. Check console for details.");
        });
});
