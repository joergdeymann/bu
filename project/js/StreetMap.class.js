/*
    1. Streetview
        - implement js
            streetViewInit();
        - setTag Element
            streetViewAddMap()
        - show streetView
            streetViewShow(Address)

*/

export class StreetMap {
    popup;

    setTag(element) {
        this.map = element;
    }

    setTagById(id) {
        this.map = document.getElementById(id);
    }

    setTagByName(name) {
        this.map = document.getElementsByName(name)[0];
    }

    setTagByQuery(query) {
        this.map = document.getQuerySelector(query);
    }

    addMapTag() {
        return `<div id="map"></div>`;
    }

    setPopup(popup) {
        this.popup=popup;
    }

    /**
     * Use completge Open Scource StreetView width Nominatim API 
     * ist not very close to the designed Position
     * its Free
     *  
     * @param {*} address 
     */
    streetViewShow(address) {
        // Nominatim API verwenden, um Koordinaten der Adresse zu erhalten
        let encodedAddress = encodeURIComponent(address);
        let url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodedAddress}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {

                    // Koordinaten extrahieren
                    const lat = parseFloat(data[0].lat);
                    const lon = parseFloat(data[0].lon);
                    console.log("Koordinaten:", lat, lon);
                    const googleMapsLink = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lon}`;
                    const appleMapsLink = `http://maps.apple.com/?daddr=${lat},${lon}`;

                    // Karte anzeigen und auf den Marker zentrieren
                    const map = L.map(this.map).setView([lat, lon], 15);

                    // OpenStreetMap TileLayer einfügen
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    if (!this.popup) this.popup=`<b>${address}</b>`;
                    // Marker für den Standort hinzufügen
                    L.marker([lat, lon]).addTo(map)
                        .bindPopup(`
                            ${this.popup}
                            <a href="${googleMapsLink}" target="_blank">Mit Google Maps navigieren</a><br>
                            <a href="${appleMapsLink}" target="_blank">Mit Apple Maps navigieren</a>                            
                        `)
                        .openPopup();
//                             <b>${address}</b>br>


                } else {
                    alert("Adresse konnte nicht gefunden werden.");
                }
            })
            .catch(error => {
                console.error("Fehler beim Geocoding:", error);
            });
    }

    streetViewInit() {
        // Leaflet CSS einfügen
        const leafletCSS = document.createElement('link');
        leafletCSS.rel = 'stylesheet';
        leafletCSS.href = 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css';
        document.head.appendChild(leafletCSS);

        // Leaflet JS einfügen
        const leafletScript = document.createElement('script');
        leafletScript.src = 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js';
        // leafletScript.onload = () => this.initializeMap(); // Initialisiere Karte, wenn das Script geladen ist
        document.body.appendChild(leafletScript);
    }



    streetViewExample() {
        this.streetViewInit();
        document.body.innerHTML += this.addMapTag() +
            `
        <button onclick="map.streetViewShow('Lipperring 36, 49733 Haren')">StreetView anzeigen</button>
        `;

        this.setTagById('map');
        this.map.style.width = "100%";
        this.map.style.height = "400px";
    }


    /**
     * Google Map
     * Just use 
     * documenmt.getElementById("yourTag").innerHTML=googleMapsShow("Your Address");
     * 
     * @param {*} address 
     * @returns 
     */
    googleMapsShow(address) {
        // URL-encode die Adresse für die Einbettung
        address = "Hoher Kamp,49733 Haren";
        const encodedAddress = encodeURIComponent(address);

        // const iframeURL = `https://www.google.com/maps?q=${encodedAddress}&output=embed`;
        const iframeURL = `https://www.google.com/maps/embed?origin=mfe&pb=!1m2!2m1!1s${encodedAddress}`;
        // Generiere den iframe-Code mit der übergebenen Adresse
        const iframeHTML = `
            <iframe
                src="${iframeURL}"
                width="100%"
                height="250"
                sandbox="allow-scripts allow-same-origin allow-forms allow-popups"
                style="border:0;border-radius: 10px;"
                allowfullscreen=""
                loading="lazy">
            </iframe>
        `;

        return iframeHTML;
    }

    googleMapsExample() {
        let address = "Hoher Kamp 12,49733 Haren";
        document.body.innerHTML += this.addMapTag();
        this.setTagById("map");
        this.map.innerHTML = googleMapShow(address);
    }


    /**
     * Use completge Open Scource StreetView width Nominatim API 
     * ist not very close to the designed Position
     * its Free
     *  
     * @param {*} address 
     */
    streetViewWithHereCoordinates(address) {
        // Nominatim API verwenden, um Koordinaten der Adresse zu erhalten
        let key = 'akQKY5WAqudavdLjHOty1ZRVmHjkGWZ_wqNtVCYveAc';

        let encodedAddress = encodeURIComponent(address);
        let url = `https://geocode.search.hereapi.com/v1/geocode?q=${encodedAddress}&apiKey=${key}`;

        fetch(url)
            .then((response) => {
                console.log("Antwort-Status:", response.status); // Debug: Status prüfen
                if (!response.ok) {
                    throw new Error(`HTTP-Fehler! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data && data.items.length > 0) {
                    // Koordinaten extrahieren
                    const lat = parseFloat(data.items[0].position.lat);  // 52.78503;  // parseFloat(data[0].lat);
                    const lon = parseFloat(data.items[0].position.lng);  // 7.2046;    // parseFloat(data[0].lon);
                    const googleMapsLink = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lon}`;
                    const appleMapsLink = `http://maps.apple.com/?daddr=${lat},${lon}`;

                    // Karte anzeigen und auf den Marker zentrieren
                    const map = L.map(this.map).setView([lat, lon], 15);

                    // OpenStreetMap TileLayer einfügen
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    if (!this.popup) this.popup=`<b>${address}</b>`;
                    // Marker für den Standort hinzufügen
                    L.marker([lat, lon]).addTo(map)
                        .bindPopup(`
                            ${this.popup}<br>
                             <a href="${googleMapsLink}" target="_blank">Mit Google Maps navigieren</a><br>
                             <a href="${appleMapsLink}" target="_blank">Mit Apple Maps navigieren</a>                            
                         `)
                        .openPopup();


                } else {
                    alert("Adresse konnte nicht gefunden werden.");
                }
            })
            .catch(error => {
                console.error("Fehler beim Geocoding:", error);
            });
    }


    streetViewExample2() {
        this.streetViewInit();
        document.body.innerHTML += this.addMapTag() +
            `
        <button onclick="map.streetViewWithHereCoordinates('Lipperring 36, 49733 Haren')">StreetView anzeigen</button>
        `;

        this.setTagById('map');
        this.map.style.width = "100%";
        this.map.style.height = "400px";
    }



}

// let map = new StreetMap();

// function init() {

//     map.streetViewExample2();
// }