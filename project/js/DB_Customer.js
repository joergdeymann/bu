import {Query} from "./Query.js" 

export class DB_Customer extends Query {
    constructor() {
        super();
        this.name=document.getElementsByName("customerName")[0];
        this.city=document.getElementsByName("customerId")[0];
    }

    async query() {
        if (this.data && this.data.id) return;
        this.request(`
            INSERT INTO bu_customer 
                SET 
                    name = "${this.name.value}",
                    companyId=${login.companyId}

            
        `); 
    }


    async insert() {
        await this.query();
        await this.get();

        console.log(this.data);
        this.data={
            name:       this.name.value,
            companyId:  login.companyId
        };
    }

    async update() {
        if (!this.data.id) return;
        await this.request(`
            UPDATE bu_project 
            SET 
                name="${this.name.value}",
            
            WHERE id = ${this.data.id};
        `); 
        this.data={
            id:+this.data.lastId,
            name: this.name.value
        };
    }


    getById(id) {
        if (this.data == null) return null;
        return this.data.find(e => e.id == id);
    }

}
