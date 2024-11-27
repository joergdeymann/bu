import { SelectionList } from './SelectionList.js';
import { Query } from './Query.js';

export class EventList extends SelectionList {
    constructor() {
        super();
    }

    setElements() {
        this.list=document.getElementById("event-list");
        this.listContainer=this.list.parentElement;
        this.input=document.getElementsByName("eventName")[0];
        this.inputId=document.getElementsByName("eventId")[0];

        this.input2=document.getElementsByName("place")[0];

        this.headline="Veranstaltungen";
        this.DBid="recnum";
        this.DBfield1="name";
        this.DBfield2="ort";
        this.classname="eventList";
        this.new="Neues Event";
        
    }

    async load() {

        let p=new Query(`
            SELECT 
                recnum,
                name,
                ort

            FROM bu_adresse 
            WHERE 
                firmanr=${login.companyId} 
                AND location=2
            ORDER BY name;`);

        this.data=await p.get();
        
        this.render();
        this.listContainer.classList.remove("d-none");
    }
}
