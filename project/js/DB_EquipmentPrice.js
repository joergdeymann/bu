import {Query} from "./Query.js" 

export class DB_EquipmentPrice extends Query {
    constructor() {
        super();
    }

    async insertQuery() {
        await this.request(`
            INSERT INTO bu_equipment_price
            SET 
                companyId =  ${login.companyId},
                price =      ${this.price.value},
                customerId = ${db_customer.id.value??0},
                articleId  = ${this.articleId}            
        `); 
    }

    async updateQuery() {
        await this.request(`
            UPDATE bu_equipment_price a
            SET 
                companyId =  ${login.companyId},
                price =      ${this.price.value},
                customerId = ${db_customer.id.value??0},
                articleId  = ${this.articleId}            

            WHERE a.id = ${this.id.value};
        `); 
    }

}
