document.addEventListener("DOMContentLoaded", function () {
    let map = L.map('map').setView([22.3545449, 73.1293971], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    let userMarker = L.marker([22.3545449, 73.1293971]).addTo(map)
        .bindPopup("📍 You are here!")
        .openPopup();

    let destinationCoords = [22.449210182881238, 73.35563556885175];
    let destinationMarker = L.marker(destinationCoords).addTo(map)
        .bindPopup("🚩 ITMB SLS Baroda University")
        .openPopup();

    let tracking = false;
    let trackingInterval;

    let pathCoordinates = [[22.3545449, 73.1293971]];
    let polyline = L.polyline(pathCoordinates, { color: 'blue' }).addTo(map);

    let start = [73.1293971, 22.3545449];
    let end = [73.35563556885175, 22.449210182881238];
    let routeCoordinates = [];

    fetch(`https://router.project-osrm.org/route/v1/driving/${start.join(',')};${end.join(',')}?overview=full&geometries=geojson`)
        .then(res => res.json())
        .then(data => {
            routeCoordinates = data.routes[0].geometry.coordinates.map(coord => [coord[1], coord[0]]);
            let fixedPolyline = L.polyline(routeCoordinates, { color: 'red' }).addTo(map);
            map.fitBounds(fixedPolyline.getBounds());
        });

    function updatePolyline() {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                let lat = position.coords.latitude;
                let lng = position.coords.longitude;

                pathCoordinates.push([lat, lng]);
                polyline.setLatLngs(pathCoordinates);
            },
            (error) => console.error("Polyline update error:", error),
            { enableHighAccuracy: true, maximumAge: 0, timeout: 10000 }
        );
    }

    setInterval(updatePolyline, 1000);

    function startSharingLocation() {
        if (!navigator.geolocation) {
            alert("Geolocation is not supported by your browser.");
            return;
        }

        if (!tracking) {
            tracking = true;
            document.getElementById("status").innerText = "Tracking started... 📡";
            updateLocation();
            trackingInterval = setInterval(updateLocation, 1000);
        }
    }

    function updateLocation() {
        let systemTime = new Date().toLocaleTimeString();

        navigator.geolocation.getCurrentPosition(
            (position) => {
                let lat = position.coords.latitude;
                let lng = position.coords.longitude;

                userMarker.setLatLng([lat, lng])
                    .bindPopup(`📍 Live Location <br><b>Time:</b> ${systemTime}`)
                    .openPopup();

                map.setView([lat, lng], 15);

                document.getElementById("status").innerHTML =
                    `<b>Latitude:</b> ${lat}, <b>Longitude:</b> ${lng} <br> 
                     <b>System Time:</b> ${systemTime}`;

                // 🔵 Live Distance Calculation
                let lastCoord = pathCoordinates[pathCoordinates.length - 1];
                let liveDistance = getDistanceFromLatLonInKm(lastCoord[0], lastCoord[1], lat, lng);
                let totalLiveDistance = pathCoordinates.reduce((total, curr, index) => {
                    if (index === 0) return 0;
                    return total + getDistanceFromLatLonInKm(
                        pathCoordinates[index - 1][0], pathCoordinates[index - 1][1],
                        curr[0], curr[1]
                    );
                }, 0);
                document.getElementById("liveDistance").innerHTML = `<b>📏 Live Distance:</b> ${totalLiveDistance.toFixed(2)} km`;

                // 🕒 ETA Calculation using OSRM
                fetch(`https://router.project-osrm.org/route/v1/driving/${lng},${lat};${end.join(',')}?overview=false`)
                    .then(res => res.json())
                    .then(data => {
                        const route = data.routes[0];
                        const durationInMinutes = (route.duration / 60).toFixed(1);
                        const distanceInKm = (route.distance / 1000).toFixed(2);
                        document.getElementById("eta").innerHTML = `<b>🕒 ETA:</b> ${durationInMinutes} minutes<br><b>📍 Remaining Distance:</b> ${distanceInKm} km`;
                    })
                    .catch(err => console.error("Error fetching ETA:", err));

                console.log(`Updated Location: ${lat}, ${lng} at ${systemTime}`);

                sendData();
                function sendData() {
                    var xhr = new XMLHttpRequest();
                    xhr.open("GET", "insert_data.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            console.log("Response:", xhr.responseText);
                        }
                    };
                    xhr.send('lat=' + lat + '&long=' + lng + '&timestamp=' + systemTime);
                }

                fetch('/update_location', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ lat, lng, time: systemTime })
                })
                    .then(response => response.json())
                    .then(data => console.log("✅ Location updated:", data))
                    .catch(error => console.error("❌ Error updating location:", error));
            },
            (error) => {
                document.getElementById("status").innerHTML = `<b>Error:</b> ${error.message}`;
                console.error("Error getting location:", error);
            },
            { enableHighAccuracy: true, maximumAge: 0, timeout: 10000 }
        );
    }

    function stopSharingLocation() {
        if (tracking) {
            clearInterval(trackingInterval);
            tracking = false;

            let totalRedLineDistance = 0;
            for (let i = 1; i < routeCoordinates.length; i++) {
                totalRedLineDistance += getDistanceFromLatLonInKm(
                    routeCoordinates[i - 1][0], routeCoordinates[i - 1][1],
                    routeCoordinates[i][0], routeCoordinates[i][1]
                );
            }

            let totalKm = totalRedLineDistance.toFixed(2);

            document.getElementById("status").innerHTML =
                `🛑 Location tracking stopped.<br>
                 <b>📏 Red Path Distance:</b> ${totalKm} km`;

            alert(`🛑 Tracking stopped.\n📏 Red Path Distance: ${totalKm} km`);
        }
    }

    function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    document.getElementById("startTracking").addEventListener("click", startSharingLocation);
    document.getElementById("stopTracking").addEventListener("click", stopSharingLocation);
});
