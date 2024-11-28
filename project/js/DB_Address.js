import {Query} from "./Query.js" 

export class DB_Address extends Query {
    constructor() {
        super();
        this.name=document.getElementsByName("eventName")[0];
        this.city=document.getElementsByName("place")[0];
    }

    async query() {
        if (this.data && this.data.id) return;
        this.request(`
            INSERT INTO bu_adresse 
            SET 
                name = "${this.name.value}",
                ort = "${this.city.value}",
                location = 1
            
        `); 
    }
    async insert() {
        await this.query();
        await this.get();

        console.log(this.data);
        this.data={
            id:+this.data.lastId,
            name: this.name.value,
            city: this.city.value
        };
    }

    async update() {
        if (!this.data.id) return;
        await this.request(`
            UPDATE bu_adresse a
            SET 
                a.name = "${this.name.value}",
                a.ort = "${this.city.value}"
            
            WHERE a.recnum = this.data.id;
        `); 
        this.data={
            id:+this.data.lastId,
            name: this.name.value,
            city: this.city.value 
        };
    }


    getById(id) {
        if (this.data == null) return null;
        return this.data.find(e => e.id == id);
    }

}