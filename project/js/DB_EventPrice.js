import { DB_EquipmentPrice } from "./DB_EquipmentPrice.js";

export class DB_EventPrice extends DB_EquipmentPrice {
    constructor() {
        super();
    }

    elements() {
        // this.articleId = projectPrice.articleId;
        this.price=document.getElementsByName("price-name")[0]; 
    }


    get articleId() {
        let j=job.data.find(e => e.id==job.newEntry.id);
        this.articleId=projectPrice.articleId??j.articleId??0;
    } 

    async Xinsert() {
        this.getArticleId();
        this.selectQuery();
        await this.get();
        if (this.data[0]?.id) return;
        
        this.insertQuery();
        await this.get();
        this.id.value=this.data.lastId;
    }


}
