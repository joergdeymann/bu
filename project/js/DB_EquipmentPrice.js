import {Query} from "./Query.js" 

export class DB_EquipmentPrice extends Query {
    id;
    articleId;
    customerId;
    price;

    constructor() {
        super();
    }

    elements() {
    }    

    async insertQuery() {
        await this.request(`
            INSERT INTO bu_equipment_price
            SET 
                companyId =  ${login.companyId},
                price =      ${this.price.value},
                customerId = ${this.customerId.value??0},
                articleId  = ${this.articleId.value}            
        `); 
    }

    async updateQuery() {
        await this.request(`
            UPDATE bu_equipment_price a
            SET 
                companyId =  ${login.companyId},
                price =      ${this.price.value},
                customerId = ${this.customerId.value??0},
                articleId  = ${this.articleId.value}            

            WHERE a.id = ${this.id.value};
        `); 
    }

}
