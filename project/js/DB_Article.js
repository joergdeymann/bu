import { Query } from "./Query.js";

export class  DB_Article extends Query {
    

    constructor() {
        super();
    }

    elements() {
        // this.price = document.getElementsByName("newArticlePrice")[0];
        // this.name=document.getElementsByName("newArticletName")[0];
        // this.text=document.getElementsByName("newArticletText")[0];
    }

    // vat        = ${company.data.vat}
    // customerId = ${+db_customer?.id.value??0},

    async insertQuery() {
        await this.request(`
            INSERT INTO bu_article
            SET 
                companyId =  ${+login.companyId},
                price =      ${+this.price.value},
                bu_article.name     = '${this.name.value}',
                bu_article.usage    = 1,
                re_text    = '${this.text.value}'


        `); 
    }


    async insert() {
        this.insertQuery();
        await this.get();
        this.id.value=this.data.lastId;
    }


}
