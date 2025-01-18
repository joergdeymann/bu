import { Query } from './Query.js';
import { DateTime } from './DateTime.class.js';
import { IF_ProjectNew } from './IF_ProjectNew.js';
import { ProjectPrice } from './ProjectPrice.js';

export class DB_ProjectEdit extends Query {
    // id=document.getElementsByName("timeWorkerId")[0];  // bu_time_worker.id

    elements() {
        
        this.input={
            id:document.getElementsByName("timeWorkerId")[0],
            timeWorkerId: document.getElementsByName("timeWorkerId")[0], // New 

            projectId: document.getElementsByName("projectId")[0],         //??
            projectJobId: document.getElementsByName("projectJobId")[0],   //??
            timeJobId: document.getElementsByName("timeJobId")[0],         //??
            date: document.getElementsByName("date")[0],                   //??
            housingAddressId: document.getElementsByName("housingAddressId")[0], //??
             

            // jobId: document.getElementById("jobId")[0],
            eventId: document.getElementsByName("eventId")[0],      //address <- time_worker.addressId
            eventName: document.getElementsByName("eventName")[0],  //address
            eventCity: document.getElementsByName("place")[0],      //addrress 

            jobDescription:document.getElementById("jobs").firstElementChild,
            // jobDescriptionId:document.getElementByName("job???DescriptionId")[0]

            
            start:document.getElementsByName("from")[0],
            end: document.getElementsByName("to")[0],
            arrival: document.getElementsByName("arrival")[0],
            departure: document.getElementsByName("departure")[0],

            customerId: document.getElementsByName("customerId")[0],
            customerName: document.getElementsByName("customerName")[0],

            dayratePrice:document.getElementsByName("price-name")[0],
            overtimePrice:document.getElementsByName("overtime-price")[0],
            offdayPrice:document.getElementsByName("offday-price")[0],

            overtimeYes: document.getElementsByName("set-overtime")[0],
            overtimeNo: document.getElementsByName("set-overtime")[1],

            timeEquipmentId:document.getElementsByName("timeEquipmenId"), // Multiple Data
            equipmentId:document.getElementsByName("equipmenId"),
            equipmentPrice:document.getElementsByName("equipmentPrice"),
            equipmentName:document.getElementsByName("equipmentName"),

            hotelYes: document.getElementsByName("hotel")[0],
            hotelNo: document.getElementsByName("hotel")[1],
            customerPayedYes: document.getElementsByName("customer-payed")[0],
            customerPayedNo: document.getElementsByName("customer-payed")[1],
            hotelId: document.getElementsByName("unterkunftId")[0],
            hotelName: document.getElementsByName("unterkunftName")[0],

            importantText: document.getElementsByName("importantText")[0],
            invoiceText: document.getElementsByName("invoiceText")[0],



            
        }

    }
    
    constructor(id) {
        super();
        // this.timeWorkerId=id; // Noch nötig ? aber nicht hier, da nur zum laden verwendet
    }

    async loadValues(id) {
        this.load(id);
        await this.get();   

    }

        
    fillNewForm() {
        this.elements();
        this.fillForm();
    }

    fillForm() {          
        let data=this.data[0];

        let datetime=new DateTime();

        this.input.timeWorkerId.value   =data.id; //Worker id

        this.input.projectId.value      =data.projectId;
        this.input.projectJobId.value   =data.projectJobId;
        this.input.timeJobId.value      =data.timeJobId;        

        this.input.eventId.value        =data.eventId;
        this.input.eventName.value      =data.eventName;
        this.input.eventCity.value      =data.eventCity??"";

        // JobDescription noch laden: this.inpiut.
        this.input.jobDescription.innerHTML =data.jobName;

        this.input.start.value          =datetime.getDate(data.start);
        this.input.end.value            =datetime.getDate(data.end);
        this.input.arrival.value        =datetime.getDate(data.arrival);
        this.input.departure.value      =datetime.getDate(data.departure);

        this.input.customerId.value     =data.customerId;
        this.input.customerName.value   =data.customerName;

        this.input.dayratePrice.value   =data.dayratePrice;
        this.input.overtimePrice.value  =data.overtimePrice;
        this.input.offdayPrice.value    =data.offdayPrice;

        // Hier das Equipment rein

        this.input.hotelId.value        =data.hotelId;
        this.input.hotelName.value      =data.hotelName??"";

        this.input.importantText.value  =data.text;       
        this.input.invoiceText.value    =data.invoiceText;
        


        project.toggleYesNo(this.input.overtimeYes,data.overtimePrice>0);
        project.toggleYesNo(this.input.hotelYes,data.housingAddressId>0);
        project.toggleYesNo(this.input.customerPayedYes,data.housingAddressId>0); // Gibt es noch nicht in der Datenbank !!!!


        job.newEntry.id          = data.id;    // time_WorkerId 
        job.newEntry.timeWorkerId= data.id;    // time_WorkerId 
        job.newEntry.jobId       = data.jobId; // JobDefinitionId 
        job.newEntry.name        = data.jobName;
        job.newEntry.color       = data.color;
        job.newEntry.projectName = data.eventName;
        job.newEntry.city        = data.eventCity;
        job.newEntry.start       = data.start;
        job.newEntry.end         = data.end;
        job.newEntry.arrival     = data.arrival;
        job.newEntry.departure   = data.departure;
        job.newEntry.rootColor   = data.rootColor; 
        // level: findout 
        // displayColor: find out


        
        
    }

    load(id=null) {
        // id=34; // 7 und 8 und 25 
        if (id) this.input.id.value=id;
        else if (!this.input.id.value) return;
        

        this.request=new Query(`
            SELECT 
                w.id,
                LEFT(w.start, 10) AS "start",
                LEFT(w.end, 10) AS "end",
                LEFT(w.arrival, 10) AS arrival,
                LEFT(w.departure, 10) AS departure,
                w.housingAddressId,
                w.housingStart,
                w.housingEnd,
                w.info,
                w.overtimePrice,
                w.color AS workerColor,
                w.projectJobId,
                w.customerPriceId,
                w.dayratePrice AS dayratePrice,
                w.offdayPrice AS offdayPrice,
                w.overtimePrice AS overtimePrice,
                jd.id AS jobId,
                jd.color AS color,
                jd.name  AS jobName,
                a.id AS eventId,
                a.name AS eventName,
                a.city AS eventCity,
                a.postcode AS postcode,
                a.street AS street,
                c.id AS customerId,
                c.name AS customerName,
                tj.id AS timeJobId,
                tj.text AS "text",
                tj.invoiceText AS invoiceText,
                p.id AS projectId,
                hotel.id as hotelId,
                hotel.name as hotelName,
                root_job.color AS rootColor 
            

            FROM bu_time_worker w
                LEFT JOIN bu_time_job tj
                    ON tj.projectJobId = w.projectJobId
                LEFT JOIN bu_job_definition jd
                    ON jd.id = tj.jobDefinitionId
                LEFT JOIN job_hierarchy root_job
                    ON jd.id = root_job.id        
                LEFT JOIN bu_project_job pj 
                    ON pj.id = w.projectJobId
                LEFT JOIN bu_project p
                    ON p.id = pj.projectId
                LEFT JOIN bu_address a
                    ON a.id = p.addressId
                LEFT JOIN bu_customer c
                    ON c.id = p.customerId
                LEFT JOIN bu_address hotel
                    ON hotel.id = w.housingAddressId
                LEFT JOIN bu_customerprice cp
                    ON cp.id = w.customerPriceId
            WHERE
                w.id = ${this.input.id.value}         
        `);

    }
    
    async get() {
        this.data=await this.request.get();
        return this.data;
    }

    getDateInRange() {
        return this.findMonthWithMostDaysInRange(this.data[0].start, this.data[0].end);
    }

    findMonthWithMostDaysInRange(startDate, endDate) {
        // Konvertiere die Eingaben in Date-Objekte
        const start = new Date(startDate);
        const end = new Date(endDate);
    
        // Prüfe, ob das Startdatum vor dem Enddatum liegt
        if (start > end) {
            return; // throw new Error("Das Startdatum muss vor dem Enddatum liegen.");
        }
    
        // Initialisiere ein Objekt zum Zählen der Tage in jedem Monat
        const daysCount = {};
    
        // Iteriere durch alle Tage im Bereich
        let current = new Date(start);
        while (current <= end) {
            // Hole Jahr und Monat
            const year = current.getFullYear();
            const month = current.getMonth() + 1; // Monat von 0-basiert auf 1-basiert ändern
            const key = `${year}-${String(month).padStart(2, "0")}`;
    
            // Erhöhe den Zähler für diesen Monat
            daysCount[key] = (daysCount[key] || 0) + 1;
    
            // Gehe zum nächsten Tag
            current.setDate(current.getDate() + 1);
        }
    
        // Finde den Monat mit den meisten Tagen
        let maxMonth = null;
        let maxDays = 0;
        for (const [month, days] of Object.entries(daysCount)) {
            if (days > maxDays) {
                maxMonth = month;
                maxDays = days;
            }
        }
    
        // Gib den Monat mit den meisten Tagen im Format YYYY-MM-01 zurück
        return `${maxMonth}-01`;
    }

    fillNewFormLast() {
        let data=this.data[0];
        if_projectNew.currentId=data.customerPriceId;
        projectPrice.showOverlay(this.input.offdayPrice);
        projectPrice.showOverlay(this.input.dayratePrice);
        projectPrice.showOverlay(this.input.overtimePrice);

        projectPrice.setDayrateCustomerName();
        project.setDayrateStandard(if_projectNew.dataset.standard);
        project.setDayrateCustomer(if_projectNew.dataset.customerId>0);
        project.setDayrateAll(false);
    }
}
