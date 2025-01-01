import { Query } from './Query.js';

export class DB_WorkPrice extends Query {
    data;


    
    constructor() {
        super();
    }

    async load() {
        this.request(`
            SELECT 
                overtime.price AS overtimePrice,
                job.price      AS jobPrice
            FROM 
                bu_job_definition jd
            LEFT JOIN
                bu_article job
                ON job.id = jd.articleId
            LEFT JOIN
                bu_article overtime
                ON overtime.id = job.connectedArticleId
            WHERE 
                jd.id = ${+calendar.newEntry.id}
        
        `);
        await this.get();
        this.fill();

    }

    fill() {
        let price=document.getElementsByName("price-name")[0];
        let oPrice=document.getElementsByName("overtime-price")[0];
        if (!price.value) {

        }
        if (!price.value) return;
        let data=this.data[0]??[];

        if (!data?.overtimePrice) data.overtimePrice=0;
        if (!data?.jobPrice) data.jobPrice=0;

        if (!oPrice.value) {
            oPrice.value=(data.overtimePrice*price.value/data.jobPrice).toFixed(2);
        }
        projectOvertime.handleFocusEvent(oPrice);
    }

}
