import {Query} from "./Query.js" 

export class DB_Project extends Query {
    constructor() {
        super();
        this.name=document.getElementsByName("eventName")[0];
        this.city=document.getElementsByName("place")[0];
    }

    async query() {
        if (this.data && this.data.id) return;
        this.request(`
            INSERT INTO bu_project 
                SET 
                    start ="${calendar.newEntry.start}",
                    end = "${calendar.newEntry.end}",
                    addressId=${db_address.data.id},
                    setup="${calendar.newEntry.arrival}",
                    dismantling="${calendar.newEntry.departure}",
                    createDate="${new Date().toISOString()}",
                    name="${document.getElementsByName("eventName")[0].value}",
                    companyId=${login.companyId},
                    info = "${document.getElementsByName("importanttext")[0].value}",
                    customerId  = db_customer.data.id

            
        `); 
    }


    async insert() {
        await this.query();
        await this.get();

        console.log(this.data);
        this.data={
            id:         +this.data.lastId,
            start:      calendar.newEntry.start,
            end:        calendar.newEntry.end,
            addressId:  db_address.data.id,
            setup:      calendar.newEntry.arrival,
            dismantling: calendar.newEntry.departure,
            createDate: new Date().toISOString,
            "name":     document.getElementsByName('eventName')[0].value,
            companyId:  login.companyId,
            info:       document.getElementsByName('importanttext')[0].value
        };
    }

    async update() {
        if (!this.data.id) return;
        await this.request(`
            UPDATE bu_project 
            SET 
                start ="${calendar.newEntry.start}",
                end = "${calendar.newEntry.end}",
                addressId=${db_address.data.id},
                setup="${calendar.newEntry.arrival}",
                dismantling="${calendar.newEntry.departure}",
                createDate="${new Date().toISOString}",
                name="${document.getElementsByName("eventName")[0].value}",
                companyId=${login.companyId},
                info = "${document.getElementsByName("importanttext")[0].value}"
            
            WHERE id = ${this.data.id};
        `); 
        this.data={
            id:+this.data.lastId,
            name: this.name.value,
            city: this.city.value 
        };
    }


    getById(id) {
        if (this.data == null) return null;
        return this.data.find(e => e.id == id);
    }

}
