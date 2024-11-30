import {Query} from "./Query.js" 

export class DB_Project extends Query {
    constructor() {
        super();
    }

    elements() {
        this.id=document.getElementsByName("projectId")[0];
        this.name=document.getElementsByName("eventName")[0];
        this.city=document.getElementsByName("place")[0];
        this.importanttext=document.getElementsByName("importanttext")[0];
    }

    async insertQuery() {
        this.request(`
            INSERT INTO bu_project 
                SET 
                    start ="${calendar.newEntry.start}",
                    end = "${calendar.newEntry.end}",
                    addressId=${db_address.id.value},
                    setup="${calendar.newEntry.arrival}",
                    dismantling="${calendar.newEntry.departure}",
                    createDate="${new Date().toISOString()}",
                    name="${this.name.value}",
                    companyId=${login.companyId},
                    info = "${this.importanttext.value}",
                    customerId  = ${db_customer.id.value}

            
        `); 
    }

    async updateQuery() {
        await this.request(`
            UPDATE bu_project 
            SET 
                start ="${calendar.newEntry.start}",
                end = "${calendar.newEntry.end}",
                addressId=${db_address.id.value},
                setup="${calendar.newEntry.arrival}",
                dismantling="${calendar.newEntry.departure}",
                createDate="${new Date().toISOString}",
                name="${this.eventName.value}",
                companyId=${login.companyId},
                info = "${this.importanttext.value}"
            
            WHERE id = ${this.id.value};
        `); 
    }


}
