import {ExtDate} from './ExtDate.js';
import {StreetMap} from './StreetMap.class.js';


export class EventFrame {
    datetime=new ExtDate();
    streetmap=new StreetMap();
    constructor() {
        this.streetmap.streetViewInit();
    }
    

    toggleEventElement(element,state=null) {
        let parent=element.closest(".event-frame");

        if (state == null) {
            parent.classList.toggle("collapse");
        } else {
            parent.classList.toggle("collapse",state);
        }
    }


    showMap(element,id) {
        // if (!setup.maps) return;

        let parent=element.closest(".event-frame");
        let map=parent.querySelector(".map-frame");
        if (parent.classList.contains("collapse")) return;

        if (map.childElementCount !== 0) return;

        let event=calendar.timeline.data.find(e => e.id==id);
        let address=`${event.street}, ${event.postcode} ${event.city}`;
        this.streetmap.setTag(map);
        this.streetmap.setPopup(`
            <div style="font-size:10px;"><b style="font-size=12px;">${event.projectName}</b><br>${event.street}<br>${event.postcode} ${event.city}</div>
        `);
        this.streetmap.streetViewWithHereCoordinates(address);
        // this.loadStreetView(map,address);
        // map.innerHTML=this.generateGoogleMapsIframe(address);
    }

    toggleEvent(e,id) {
        this.toggleEventElement(e.target);
        this.showMap(e.target,id);

    }   


    renderList() {
        for (let e of calendar.timeline.data) {
            document.getElementById("events").innerHTML += this.render(e);
        }
    }

    getDate(date) {
        return this.datetime.getGermanDate(date);
    }

    scrollTo(id) {
        let element=document.getElementById(`#event${id}`);
        element.classList.remove("collapse");
        let offset=40;
        setTimeout(e => {
            const elementPosition = element.getBoundingClientRect().top; // Abstand vom aktuellen Viewport
            const offsetPosition = elementPosition + window.scrollY - offset; 
    
            window.scrollTo({ top:offsetPosition, behavior: "smooth" });
    
        },500);

    }


    render(projectEvent) {
        // address=projectEventList.getById(projectEvent.)
        let style=`style="border-color:${projectEvent.color}"`;
        
        let html=/*html*/`
        <div id="#event${projectEvent.id}" class="event-frame collapse" ${style}>
        <header ${style}>
            <img src="./img/saw.svg">
            <span>${projectEvent.projectName}</span>
            <img class="open" src="./img/pfeil-rechts.png" onmousedown="eventFrame.toggleEvent(event,${projectEvent.id})">
        </header>
        <section>
            <i>Datum:</i> 
            <p>${this.getDate(projectEvent.start)} - ${this.getDate(projectEvent.end)}</p>

            <i>Anfahrt:</i> 
            <p>${this.getDate(projectEvent.arrival)}</p>
            
            <i>Abfahrt:</i> 
            <p>${this.getDate(projectEvent.departure)}</p>
            
            <i>Ort:</i>
            <p>${projectEvent.city}</p>
            
            <i>Kunde:</i>
            <p>${projectEvent.customerName}</p>

            <i>Job:</i>
            <p>${projectEvent.jobName??"kein Job angegeben"}</p>

            <i>Hinweise:</i>
            <p>${projectEvent.text}</p>

            <i>Rechnungstext:</i>
            <p>${projectEvent.invoiceText}</p>
            
            <i>Anreise:</i>
            <p>${projectEvent.projectName}
                <br>${projectEvent.street}
                <br>${projectEvent.postcode} ${projectEvent.city}
                <br><br>
                <div class="map-frame"></div>
                
            </p>    
        </section>
        </div>
        `;
        return html;


    }
}

// <!-- ${this.generateGoogleMapsIframe(projectEvent.street+","+projectEvent.postcode+"+"+projectEvent.city)} -->