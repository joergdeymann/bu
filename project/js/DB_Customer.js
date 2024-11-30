import {Query} from "./Query.js" 

export class DB_Customer extends Query {
    constructor() {
        super();
    }

    elements() {
        this.name=document.getElementsByName("customerName")[0];
        this.id=document.getElementsByName("customerId")[0];
    }

    references() {
        this.customerId=login.companyId; // Beispiel(nicht in Benutzung)
    } 

    async insertQuery() {
        this.request(`
            INSERT INTO bu_customer 
                SET 
                    name = "${this.name.value}",
                    companyId=${login.companyId}

        `); 
    }

    async updateQuery() {
        await this.request(`
            UPDATE bu_customer 
            SET 
                name="${this.name.value}",
            WHERE id = ${this.id.value};
        `); 
    }

    async update() {
        if (customerList.data.firma == this.name.value) return;
        super.update();
    }

}
