import {Query} from "./Query.js" 


export class DB_CustomerPrice extends Query {
    constructor() {
        super();
    }

    id= {value:null};


    currentId=null;
    filter = null;

    get dataset() {
        if (this.currentId == null) return null;
        return this.data.filter(e => e.id==this.currentId)[0];
    }

    filterCustomer(customerId=0) {
        return this.filter=this.data.filter(e => e.customerId == customerId);
    }

    filterJob(jobDefinitionId=0) {
        return this.filter=this.data.filter(e => e.jobDefinitionId == jobDefinitionId);
    }

    filterCustomerAndJob(customerId=0,jobDefinitionId=0) {        
        return this.filter=this.data.filter(e => (e.customerId == customerId) && (e.jobDefinitionId == jobDefinitionId));
    }

    clearFilter() {
        this.filter=this.data.map(item => ({ ...item }));
    }


    async selectQuery() {
        this.request(`
            SELECT 
                cp.customerId, 
                cp.articleIdDayrate,
                cp.articleIdOffday,
                cp.articleIdOvertime,
                cp.jobDefinitionId,
                cp.name  AS cpName,
                
                dr.name  AS drName,
                dr.price AS drPrice,

                off.name AS offName,
                off.price AS offPrice,

                ot.name AS otName,
                ot.price AS otPrice


            FROM       
                bu_customerprice cp
            LEFT JOIN bu_article dr
                on dr.id = cp.articleIdDayrate
            LEFT JOIN bu_article off
                on off.id = cp.articleIdOffday
            LEFT JOIN bu_article ot
                on ot.id = cp.articleIdOvertime


            WHERE 
                cp.companyID = 14
            ORDER BY
                cp.customerId,
                dr.name;
        `)
    }


    // Diese umänderen gehören so nicht hier rein
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
            WHERE id = ${this.id.value};
        `); 
    }

}

