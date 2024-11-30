import {Query} from "./Query.js" 

export class DB_EquipmentPrice extends Query {
    constructor() {
        super();
    }

    elements() {
        this.articleId = projectPrice.articleId;
        this.price=document.getElementsByName("price-name")[0];
        this.id=document.getElementsByName("eventId")[0];
        this.city=document.getElementsByName("place")[0];
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

    async selectQuery() {
        let j=job.data.find(e => e.id==job.newEntry.id);
        this.articleId=projectPrice.articleId??j.articleId??0;

        await this.request(`
            SELECT id 
            FROM bu_equipment_price ep
            WHERE ep.companyId =  ${login.companyId}
            AND   ep.price =      ${this.price.value}
            AND   ep.customerId = ${db_customer.id.value??0}
            AND   ep.articleId  = ${this.articleId}            
        `); 
    }



    async insert() {
        this.selectQuery();
        await this.get();
        if (this.data[0]?.id) return;
        
        // this.data[0].id=0;

        this.insertQuery();
        await this.get();
        this.id.value=this.data.lastId;
    }


}
