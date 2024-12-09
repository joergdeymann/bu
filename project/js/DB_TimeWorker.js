import {Query} from "./Query.js" 

export class DB_TimeWorker extends Query {
    constructor() {
        super();
    }

    elements() {
        this.id=document.getElementsByName("timeWorkerId");
        this.price=document.getElementsByName("price-name");
        this.housingAddressId=document.getElementsByName("housingAddressId");
    }

    // housing Entries -> (vielleich new Address())
    // price is 0, becaus of from Customer 
    // name comes from address no need to save
    // Enable add new Housing
    // info and re_info is in job or elsewhere :)
    // !!! lumpsum = is missing
    // !!! distance = lumpsum distance
    // !!! runs     = lumpsum count of drives
    // Fehlende:
    // lumpsum
    // distance
    // runs
    // dayrateOffday
    // worktime -> gehört in customer rein 
    // overtimePrice
    // doubletimePrice aufnehmen: Doppelter Tagessatz
    // color is a nice to have, ca be set, but i would it place near the calendar 
    // a colorpicker, Price, Ivoice information
    // if you click on the in the headline of the Jobs is a good idea




    async insertQuery() {
        this.request(`
            INSERT INTO bu_time_worker 
            SET 
                companyId=${+login.companyId},
                projectJobId=${+db_projectJob.id.value},
                employeeId=${+login.userId},
                bu_time_worker.start =${this.inMarks(calendar.newEntry.start)},           
                bu_time_worker.end = ${this.inMarks(calendar.newEntry.end)},
                arrival = ${this.inMarks(calendar.newEntry.arrival)},           
                departure = ${this.inMarks(calendar.newEntry.departure)},
                housingAddressId = ${+(this.housingAddressId?.value??0)},
                housingPrice = 0,
                housingStart = NULL, 
                housingEnd = NULL,
                dayrate = ${+(this.price.value??0)},
                lumpsum =0
        `); 

        // --vat standart null,= take from customer
    }

    async updateQuery() {
        
        await this.request(`
            UPDATE bu_time_worker
            SET 
                companyId=${+login.companyId},
            
            WHERE id = ${this.id.value};
        `); 
    }

}