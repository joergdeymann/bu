import {Query} from "./Query.js" 

export class DB_Address extends Query {
    constructor() {
        super();
    }

    elements() {
        this.name=document.getElementsByName("eventName")[0];
        this.id=document.getElementsByName("eventId")[0];
        this.city=document.getElementsByName("place")[0];
    }

    async insertQuery() {
        this.request(`
            INSERT INTO bu_address 
            SET 
                name = "${this.name.value}",
                city = "${this.city.value}",
                companyId = ${login.companyId},
                location = 1
            
        `); 
    }

    async updateQuery() {
        await this.request(`
            UPDATE bu_address a
            SET 
                a.name = "${this.name.value}",
                a.city = "${this.city.value}"
            WHERE a.id = ${this.id.value};
        `); 
    }

}