// import { DB_TimeEquipmentList } from "./DB_TimeEquipmentList.js";
// import { EquipmentList } from "./EquipmentList.js";
import { Query } from "./Query.js";

export class TimeEquipmentInput { // extends DB_TimeEquipmentList {
    timeIds;
    articleId;
    price;
    projectJobId;

    constructor() {
        this.load();
    }

    elements() {
        this.timeIds=document.getElementsByName("timeEquipmentId[]");
        this.articleIds=document.getElementsByName("equipmentId[]");
        this.price=document.getElementsByName("equipmentPrice[]");
        this.projectJobId= document.getElementsByName("projectJobId")[0];
    }

    save() {
        this.elements();
        this.delete();
        this.insert();
    }

    async delete() {
        let ids=this.timeIds;
        if (ids.length == 0) return;
        let idList = Array.from(ids)
            .map(input => input.value)
            .filter(e => +e) 
            .join(',');

        let request=new Query(`
            DELETE FROM bu_time_equipment 
            WHERE 
                companyId=${+login.companyId} 
                AND projectJobId = ${+this.projectJobId.value}
                AND id NOT IN (${idList});
        `);
        await request.get();
    }

    inMarks(date) {
        return date?`'${date}'`:'NULL';
    }

    async insert()  {
        let timeIds=this.timeIds;
        let articleIds=this.articleIds;
        let price=this.price;

        let from=calendar.newEntry.start;
        let to  =calendar.newEntry.end;


        let q=`
        INSERT INTO bu_time_equipment(
            companyId, 
            projectJobId,
            articleId,
            price,
            vat,
            bu_time_equipment.from,
            bu_time_equipment.to,
            bu_time_equipment.status
        )`;
        
        let v="";
        // db_project.id.value
        // db_projectEdit.input.projectJobId.value
        // +db_projectEdit.input.projectJobId.value
        for (let i = 0;i<articleIds.length;i++ ) {
            if (+timeIds[i].value) continue;             // Entry already there
            if (!+this.articleIds[i].value) continue;    // No Article choosen
            if (v) v+=","
            v+= `
            VALUES (
                ${+login.companyId},
                ${+this.projectJobId.value}, 
                ${+articleIds[i].value},
                ${+price[i].value},
                0,
                ${this.inMarks(from)},
                ${this.inMarks(to)},
                2
            )
        `;
        }
        if (!v) return;

        let request=new Query(q+v);
        await request.get();
    }

    async load() {
        this.elements();
        // +db_projectEdit.input.projectJobId.value
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
                AND te.projectJobId = ${+this.projectJobId.value}
            ORDER BY a.name;`);

        this.data=await p.get();
        
        this.render();
        // this.listContainer.classList.remove("d-none");
    }

    render() {
        equipmentList.removeInputField();
        for (let dataset of this.data) {
            this.addInputField(dataset);
        }
    }

// .toFixed(2)
    addInputField(dataset) {
        let newContainer = document.createElement("div");
        newContainer.classList.add("input-container");
        newContainer.classList.add("equipment");
        newContainer.innerHTML=/*html*/`
            <input type="hidden" name="timeEquipmentId[]"  value="${dataset.id}">
            <input type="hidden" name="equipmentId[]" value="${dataset.articleId}">
            <input type="hidden" name="equipmentPrice[]" value="${dataset.price}">
            <input type="text" name="equipmentName[]"  placeholder="Was bringst du mit" value="${dataset.name}">
            <button class="small" type="button" onmousedown="equipmentList.setWindow(event)">&#128315;</button>
            <div class="right">${dataset.price} €</div>

            <div  id="popup" class="relative mb-16px d-none">
                <div class="blocker"></div>
                <div id="equipment-list" class="popup-list"></div>
            </div>

        `; 

        document.getElementById("popup").insertAdjacentElement("beforebegin", newContainer);
        equipmentList.moveElements(newContainer.firstElementChild); // ich brauche hier erstmal nur das input field
        equipmentList.addInputEvent() ;
        

    }
}