function geocodeAddress() {
    const address = document.getElementById('address').value;
    if (!address) {
        alert("Bitte eine Adresse eingeben.");
        return;
    }

    const apiKey = 'akQKY5WAqudavdLjHOty1ZRVmHjkGWZ_wqNtVCYveAc'; // Deinen API-Schlüssel hier einfügen
    const url = `https://api.geocode.earth/v1/search?text=${encodeURIComponent(address)}&api_key=${apiKey}`;

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP-Fehler: ${response.status} - ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("API-Antwort:", data);
            if (data.features && data.features.length > 0) {
                const [lon, lat] = data.features[0].geometry.coordinates;
                map.setView([lat, lon], 13);
                L.marker([lat, lon]).addTo(map)
                    .bindPopup(`<b>${address}</b><br>${lat}, ${lon}`).openPopup();
            } else {
                alert("Adresse nicht gefunden.");
            }
        })
        .catch(error => {
            console.error("Fehler bei der API-Anfrage:", error);
            alert(`Fehler: ${error.message}`);
        });
}
