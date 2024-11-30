import { Query } from "./Query.js";

export class  DB_Article extends Query {
    constructor() {
        super();
    }

    elements() {
        this.id = document.getElementsByName("equipmentId[]");
        this.name=document.getElementsByName("equipmentName[]");
    }
    async insertQuery(index) {
        await this.request(`
            INSERT INTO bu_article
            SET 
                companyId =  ${login.companyId},
                price =      ${this.price[index].value},
                customerId = ${db_customer.id.value??0},
                articleId  = ${this.articleId}            
        `); 
    }


    async insert() {
        this.insertQuery();
        await this.get();
        this.id.value=this.data.lastId;
    }


}
