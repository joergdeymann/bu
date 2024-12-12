import { SelectionList } from './SelectionList.js';
import { Query } from './Query.js';

export class EventTimeList extends SelectionList {
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
        this.DBid="id";
        this.DBfield2="name";
        this.DBfield1="start";
        this.filter=this.DBfield2;
        this.classname="eventTimeList";
        this.new="Neues Event";
        
    }

    germanToIsoDate(germanDate) {
        const [day, month, year] = germanDate.split('.');
        return  new Date(`${year}-${month}-${day}`); // ISO-Format erstellen
    }

    async select(id) {
        let data=this.data.find(e => e.id==id);
        this.input.blur();
        this.input.value=data.name;
        this.inputId.value=data.id;
        this.input2.value=data.city;
        this.toggleWindow();
        calendar.date=this.germanToIsoDate(data.start);
        await calendar.renderCalendarAll();
        // eventFrame.clearList();
        // eventFrame.renderList();
        eventFrame.scrollTo(data.id);


    }

    async load() {
        // brauche einer der Ids um den ausgewählen Datensatz anzusteuern
        let p=new Query(`
            SELECT 
                tw.id,
                tw.projectJobId, 
                DATE_FORMAT(tw.start, '%d.%m.%Y') AS 'start',
                tw.end,
                a.name,
                a.city
            FROM 
                bu_time_worker tw
            LEFT JOIN 
                bu_project_job pj
            ON 
                pj.id=tw.projectJobId
            LEFT JOIN
                bu_project p
            ON 
                p.id=pj.projectId
            LEFT JOIN
                bu_address a
            ON  
                p.addressId = a.id
            WHERE 
                tw.companyId=${login.companyId} 
            ORDER BY 
                tw.start DESC
            LIMIT 200;
        `);

        this.data=await p.get();
        
        this.render();
        this.listContainer.classList.remove("d-none");
    }

    getById(id) {
        return this.data.find(e => e.id = id);
    }
            
}
