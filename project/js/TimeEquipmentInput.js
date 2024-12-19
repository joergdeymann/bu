// import { DB_TimeEquipmentList } from "./DB_TimeEquipmentList.js";
// import { EquipmentList } from "./EquipmentList.js";
import { Query } from "./Query.js";

export class TimeEquipmentInput { // extends DB_TimeEquipmentList {

    constructor() {
        this.load();
    }

    save() {
        this.delete();
        this.insert();
    }

    async delete() {
        let ids=document.getElementsByName("timeEquipmentId[]");
        if (ids.length == 0) return;
        let idList = Array.from(ids).map(input => input.value).join(',');

        let request=new Query(`
            DELETE FROM bu_time_equipment 
            WHERE 
                companyId=${+login.companyId} 
                AND projectJobId = ${+projectEdit.data[0].projectJobId}
                AND id NOT IN (${idList});
        `);
        await request.get();
    }

    inMarks(date) {
        return date?`'${date}'`:'NULL';
    }

    async insert()  {
        let timeIds=document.getElementsByName("timeEquipmentId[]");
        let articleIds=document.getElementsByName("equipmentId[]");
        let price=document.getElementsByName("equipmentPrice[]");

        let from=calendar.newEntry.start;
        let to  =calendar.newEntry.end;


        let q=`
        INSERT INTO bu_time_equipment(
            companyId, 
            projectJobId,
            artcleId,
            price,
            vat,
            from,
            to,
            status
        )`;
        
        let v="";

        for (i = 0;i<articleIds.length;i++ ) {
            if (timeIds.value) continue;
            if (v) v+=","
            v+= `
            VALUES (
                ${+login.companyId},
                ${+projectEdit.data[0].projectJobId},
                ${+articleIds[i].value},
                ${+price[i].value},
                0,
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