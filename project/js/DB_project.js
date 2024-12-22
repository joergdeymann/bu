import {Query} from "./Query.js" 

export class DB_Project extends Query {
    constructor() {
        super();
    }

    elements() {
        this.id=document.getElementsByName("projectId")[0];
        this.name=document.getElementsByName("eventName")[0];
        this.city=document.getElementsByName("place")[0];
        this.importanttext=document.getElementsByName("importantText")[0];
    }

    get isFullProject() {
        return document.getElementsByClassName("full-project")[0].classList.contains("full)");
    }

    async insertQuery() {
        //  bu_project.name="${this.name.value}", )
        this.request(`
            INSERT INTO bu_project 
                SET 
                    bu_project.start =${this.inMarks(calendar.newEntry.start)},           
                    bu_project.end = ${this.inMarks(calendar.newEntry.end)},              
                    bu_project.addressId=${db_address.id.value},              
                    bu_project.setup=${this.inMarks(calendar.newEntry.arrival)},          
                    bu_project.dismantling=${this.inMarks(calendar.newEntry.departure)},  
                    bu_project.createDate=${this.inMarks(new Date().toISOString())},      
                                       
                    bu_project.companyId=${login.companyId},                  
                    bu_project.info = "${this.importanttext.value}",          
                    bu_project.customerId  = ${db_customer.id.value}
        `); 
    }

    async updateQueryFull() {
        await this.request(`
            UPDATE bu_project 
            SET 
                bu_project.start =${this.inMarks(calendar.newEntry.start)},           
                bu_project.end = ${this.inMarks(calendar.newEntry.end)},              
                bu_project.setup=${this.inMarks(calendar.newEntry.arrival)},          
                bu_project.dismantling=${this.inMarks(calendar.newEntry.departure)},  
                                    
                bu_project.addressId=${db_address.id.value},              
                bu_project.companyId=${login.companyId},                  
                bu_project.info = "${this.importanttext.value}",          
                bu_project.customerId  = ${db_customer.id.value}
            WHERE id = ${this.id.value};
        `); 
    }

    async updateQueryProjectJob() {
        await this.request(`
            UPDATE bu_project 
            SET 
                bu_project.addressId=${db_address.id.value},                                                  
                bu_project.companyId=${login.companyId},                  
                bu_project.info = "${this.importanttext.value}",          
                bu_project.customerId  = ${db_customer.id.value} 
            WHERE id = ${this.id.value};
        `); 
    }
    
    async updateQuery() {
        await this.updateQueryFull();
        return;
        // this is for later
        if (this.isFullProject()) await this.updateQueryFull();
        else                      await this.updateQueryProjectJob();
    }


}
