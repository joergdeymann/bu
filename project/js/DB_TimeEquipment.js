import {Query} from "./Query.js" 

export class DB_TimeEquipment extends Query {
    index;
    articleId;
    price;

    constructor(index=0) {
        super();
        this.index=index;
        this.elements();
    }

    elements() {
        if (this.index === null) return;
        //Arrays
        this.id=document.getElementsByName("timeEquipmentId[]")[this.index];       
        this.articleId=document.getElementsByName("equipmentId[]")[this.index];
        this.price=document.getElementsByName("equipmentPrice[]")[this.index];
    }

    get isFullProject() {
        return document.getElementsByClassName("full-project")[0].classList.contains("full");
    }

    async insertQuery() {
        this.request(`
            INSERT INTO bu_time_equipment 
            SET 
                companyId=${+login.companyId},
                projectJobId=${+db_projectJob.id.value},
                articleId=${+this.articleId.value},
                price=${+this.price.value},         
                bu_time_equipment.from ="${calendar.newEntry.start}",           
                bu_time_equipment.to = "${calendar.newEntry.end}",
                status = 2 
        `); 

        // --vat standart null,= take from customer
    }

    async updateQuery() {
        
        await this.request(`
            UPDATE bu_project 
            SET 
                companyId=${+login.companyId},
                projectJobId=${+db_projectJob.id.value},
                articleId=${+this.articleId.value},
                price=${+this.price.value},         
                bu_time_equipment.from ="${calendar.newEntry.start}",           
                bu_time_equipment.to = "${calendar.newEntry.end}",
                status = 2 
            
            WHERE id = ${this.id.value};
        `); 
    }

}
