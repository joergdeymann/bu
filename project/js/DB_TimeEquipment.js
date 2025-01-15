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
        this.input={id:document.getElementsByName("timeEquipmentId[]")[this.index]}       
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
                bu_time_equipment.companyId=${+login.companyId},
                bu_time_equipment.projectJobId=${+db_projectJob.id},
                bu_time_equipment.articleId=${+this.articleId.value},
                bu_time_equipment.price=${+this.price.value},         
                bu_time_equipment.from =${this.inMarks(calendar.newEntry.start)},           
                bu_time_equipment.to = ${this.inMarks(calendar.newEntry.end)},
                bu_time_equipment.status = 2 
        `); 

        // --vat standart null,= take from customer
    }

    async updateQuery() {
        
        await this.request(`
            UPDATE bu_time_equipment  
            SET 
                bu_time_equipment.companyId=${+login.companyId},
                bu_time_equipment.projectJobId=${+db_projectJob.id},
                bu_time_equipment.articleId=${+this.articleId.value},
                bu_time_equipment.price=${+this.price.value},         
                bu_time_equipment.from =${this.inMarks(calendar.newEntry.start)},           
                bu_time_equipment.to = ${this.inMarks(calendar.newEntry.end)},
                bu_time_equipment.status = 2 
            
            WHERE id = ${this.input.id.value};
        `); 
    }

}
