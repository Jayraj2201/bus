document.addEventListener("DOMContentLoaded", function () {
    let map = L.map('map').setView([22.3251, 73.1975], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    let userMarker = L.marker([22.3251, 73.1975]).addTo(map)
        .bindPopup("📍 You are here!")
        .openPopup();

    let tracking = false;
    let trackingInterval;

    // 🔹 Fixed polyline from given location to Vadodara City Bus Stand
    let routeCoordinates = [
        [22.3545449, 73.1293971],  // Starting Point
        [22.3072, 73.1812]         // Vadodara City Bus Stand
    ];

    let routePolyline = L.polyline(routeCoordinates, { color: 'blue' }).addTo(map);
    map.fitBounds(routePolyline.getBounds());  // Adjust map to fit the polyline

    function startSharingLocation() {
        if (!navigator.geolocation) {
            alert("Geolocation is not supported by your browser.");
            return;
        }

        if (!tracking) {
            tracking = true;
            document.getElementById("status").innerText = "Tracking started... 📡";
            updateLocation();
            trackingInterval = setInterval(updateLocation, 1000); // Update every 1 sec
        }
    }

    function updateLocation() {
        let systemTime = new Date().toLocaleTimeString(); // Get system time

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

                console.log(`Updated Location: ${lat}, ${lng} at ${systemTime}`);

                sendData();
                function sendData() {
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "insert_data.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            console.log("Response:", xhr.responseText);
                        }
                    };
                    xhr.send('lat='+lat+ '&long='+lng+ '&timestamp='+systemTime);
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
            { enableHighAccuracy: true, maximumAge: 0, timeout: 10000}
        );
    }

    function stopSharingLocation() {
        if (tracking) {
            clearInterval(trackingInterval);
            tracking = false;
            document.getElementById("status").innerText = "🛑 Location tracking stopped.";
            alert("🛑 Location tracking stopped.");
        }
    }

    document.getElementById("startTracking").addEventListener("click", startSharingLocation);
    document.getElementById("stopTracking").addEventListener("click", stopSharingLocation);
});
