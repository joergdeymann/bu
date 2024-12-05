import {Query} from "./Query.js" 

export class DB_TimeJob extends Query {
    constructor() {
        super();
    }

    elements() {
        this.id=document.getElementsByName("timeJobId");
        this.text=document.getElementsByName("importantText")[0];
        this.invoiceText=document.getElementsByName("invoiceText")[0];
    }

    async insertQuery() {
        this.request(`
            INSERT INTO bu_time_job 
            SET 
                bu_time_job.companyId=${+login.companyId},
                bu_time_job.projectJobId=${+db_projectJob.id.value},
                bu_time_job.jobDefinitionId=${+job.newEntry.id},
                bu_time_job.from = ${this.inMarks(calendar.newEntry.start)},           
                bu_time_job.to = ${this.inMarks(calendar.newEntry.end)},
                bu_time_job.text = '${this.text.value}',
                bu_time_job.invoiceText = '${this.invoiceText.value}',
                bu_time_job.status = 2 
        `); 

        // --vat standart null,= take from customer
    }


    async updateQuery() {
        await this.request(`
            UPDATE bu_time_job
            SET 
                bu_time_job.companyId=${+login.companyId},
                bu_time_job.projectJobId=${+db_projectJob.id.value},
                bu_time_job.jobDefinitionId=${+job.newEntry.id},
                bu_time_job.bu_time_job.from = ${this.inMarks(calendar.newEntry.start)},           
                bu_time_job.bu_time_job.to = ${this.inMarks(calendar.newEntry.end)},
                bu_time_job.text = '${this.text.value}',
                bu_time_job.invoiceText = '${this.invoiceText.value}',
                bu_time_job.status = 2 
            
            WHERE id = ${this.id.value};
        `); 
    }

}
