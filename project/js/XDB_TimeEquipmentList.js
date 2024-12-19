import {DB_TimeEquipment} from "./DB_TimeEquipment.js" 

export class DB_TimeEquipmentList {
    promise=[];
    data=[]; // the id ist data.find( e =>  e.id)
    input;

    constructor() {
        this.input=document.getElementsByName("timeEquipmentId[]");
    }

    get len() {
        return this.input.length;
    }

    getIndex(id) {
        return this.data.findIndex( e =>  e.data.id == id);
    }

    add(index) {
        this.data.push(new DB_TimeEquipment(index));
    }
    
    remove(id) { // maybe over ID
        this.data.splice(getIndex(id),1);
    }
    
    clear() {
        this.data.length=0;
    }

    addAll() {
        for(let index = 0; index < this.len; index++) {
            this.add(index);
        }
    }


    async insertAll() {
        for (let equipment of this.data) {
            this.promise.push(equipment.insert());
            // this.promise.push(equipment.get());
        }
        await Promise.all(this.promise);
    }


    async load() {

        let p=new Query(`
            SELECT 
                te.id as id,
                te.price as price,
                te.from,
                a.id AS articleId,
                a.vat,
                a.name               
            FROM bu_time_equipment te 
            LEFT JOIN bu_article a 
                ON a.id = te.articleId
            WHERE 
                te.companyId=${+login.companyId} 
                AND te.projectJobId = ${+projectEdit.data[0].projectJobId}
            ORDER BY a.name;`);

        this.data=await p.get();
        
        this.render();
        // this.listContainer.classList.remove("d-none");
    }

    getById(id) {
        return this.data.find(e => e.id = id);
    }

    render() {
        foreach() 
    }

}


