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

    findByDayrateId(dayrateId,customerId=null) {
        return this.data.find(e => e.articleIdDayrate == dayrateId && (!customerId || e.customerId === customerId));  
    }

    remove(id) {
        let index=this.data.findIndex(e => e.id === id);
        if (index !== -1) this.data.splice(index,1);
    }



    async selectQuery() {
        this.request(`
            SELECT 
                cp.id,
                cp.customerId, 
                cp.articleIdDayrate,
                cp.articleIdOffday,
                cp.articleIdOvertime,
                cp.jobDefinitionId,
                cp.name  AS cpName,
                cp.standard,

                
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
    // WHERE AND cp.jobDefinitionId = job.id?




    // Diese umänderen gehören so nicht hier rein
    async insertQuery() {
        let data=this.dataset;
        let customerId=0;
        if (data.dayrateCustomer) customerId=data.customerId;

        this.request(`
            INSERT INTO bu_customerprice 
                SET 
                    bu_customerprice.companyId=${login.companyId},
                    bu_customerprice.customerId=${customerId},
                    bu_customerprice.articleIdDayrate = ${data.articleIdDayrate},
                    bu_customerprice.articleIdOffday =  ${data.articleIdOffday},
                    bu_customerprice.articleIdOvertime = ${data.articleIdOvertime},
                    bu_customerprice.jobDefinitionId = ${data.jobDefinitionId},
                    bu_customerprice.standard = ${data.standart},
                    bu_customerprice.name = "${data.drName}";
        `); 
        await this.get();
    }

    async updateQuery() {
        let data=this.dataset;
        let customerId=0;
        if (data.dayrateCustomer) customerId=data.customerId;
        
        await this.request(`
            UPDATE bu_customerprice
                SET 
                    bu_customerprice.companyId=${login.companyId},
                    bu_customerprice.customerId=${customerId},
                    bu_customerprice.articleIdDayrate = ${data.articleIdDayrate},
                    bu_customerprice.articleIdOffday =  ${data.articleIdOffday},
                    bu_customerprice.articleIdOvertime = ${data.articleIdOvertime},
                    bu_customerprice.jobDefinitionId = ${data.jobDefinitionId},
                    bu_customerprice.standard = ${data.standart},
                    bu_customerprice.name = "${data.drName}"
            WHERE id = ${data.id};
        `); 
        await this.get();
    }



}

