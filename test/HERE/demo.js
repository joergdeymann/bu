/**
 * Moves the map to display over Berlin
 *
 * @param  {H.Map} map      A HERE Map instance within the application
 */
// function moveMapToBerlin(map) {
//   map.setCenter({ lat: 52.5159, lng: 13.3777 });
//   map.setZoom(14);
// }
function moveMapToBerlin(map) {
  map.setCenter({ lat: 52.78503, lng: 7.2046 });
  map.setZoom(14);
  text=`
  <div style="font-family: Arial, sans-serif;width: 200px;">
      <h3 style="color: balck;">Berlin</h3>
      <p>Die Hauptstadt von <b>Deutschland</b>.<br>
      <a href="https://www.visitberlin.de/" target="_blank">Weitere Informationen</a>
      </p>
  </div>
  
  `

  // addMarker(52.78503, 7.2046, text);
  addMarker(52.78503, 7.2046, text);

}

/**
 * Boilerplate map initialization code starts below:
 */

//Step 1: initialize communication with the platform
// In your own code, replace variable apikey with your own apikey


/***********************
//
//********* please set apiKey here *************/
window.apiKey = "akQKY5WAqudavdLjHOty1ZRVmHjkGWZ_wqNtVCYveAc";
//
/***********************/


var alertWarningUi = document.getElementById('alert-warning');

if (!apiKey) {
  alertWarningUi.style.display = 'block';
} else {
  alertWarningUi.style.display = 'none';
}

var platform = new H.service.Platform({
  apikey: window.apiKey
});

var defaultLayers = platform.createDefaultLayers();

//Step 2: initialize a map - this map is centered over Europe
var map = new H.Map(document.getElementById('map'),
  defaultLayers.vector.normal.map, {
  center: { lat: 50, lng: 5 },
  zoom: 4,
  pixelRatio: window.devicePixelRatio || 1
});
// add a resize listener to make sure that the map occupies the whole container
window.addEventListener('resize', () => map.getViewPort().resize());

//Step 3: make the map interactive
// MapEvents enables the event system
// Behavior implements default interactions for pan/zoom (also on mobile touch environments)
var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));

// Create the default UI components
var ui = H.ui.UI.createDefault(map, defaultLayers);

// Now use the map as required...
window.onload = function () {
  moveMapToBerlin(map);
}



const addCustomMarker = (lat, lng, text) => {
  const marker = new H.map.Marker({ lat, lng });
  map.addObject(marker);

  const popup = document.createElement('div');
  popup.className = 'custom-popup';
  popup.innerHTML = text;

  // Position des Popups anpassen
  marker.addEventListener('tap', () => {
    const markerPos = map.geoToScreen(marker.getGeometry());
    popup.style.left = `${markerPos.x}px`;
    popup.style.top = `${markerPos.y}px`;

    // Popup zur Karte hinzufügen
    document.body.appendChild(popup);
  });

  // Entfernen bei Klick irgendwo anders
  map.addEventListener('tap', () => {
    if (popup.parentNode) {
      popup.parentNode.removeChild(popup);
    }
  });

};


const addMarker = (lat, lng, text) => {
  const marker = new H.map.Marker({ lat, lng });
  map.addObject(marker);


  map.setCenter({ lat, lng });
  // Optional: Popup hinzufügen
  marker.setData(text);
  marker.addEventListener('tap', event => {
      const bubble = new H.ui.InfoBubble(event.target.getGeometry(), {
          content: event.target.getData()
      });
    
      bubble.setOffset({ x: 100, y: -40 });
      ui.addBubble(bubble);
  });
};