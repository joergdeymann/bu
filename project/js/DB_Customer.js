import {Query} from "./Query.js" 

export class DB_Customer extends Query {
    constructor() {
        super();
    }

    elements() {
        this.input={id:document.getElementsByName("customerId")[0]};
        this.name=document.getElementsByName("customerName")[0];
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
                name="${this.name.value}"
            WHERE id = ${this.input.id.value};
        `); 
    }

    async update() {
        // not here, not really necessary : if (customerList.data.firma == this.name.value) return;
        super.update();
    }

}
