import {Query} from "./Query.js" 

export class DB_Project extends Query {
    // request;
    // data=null;
    // isLoaded=false;


    constructor() {
        super();
    }

    <input type="text" name="name"  placeholder="Name der Veranstaltung">
    <input type="text" name="place" placeholder="Ort der Veranstaltung">

    async insert() {
        await this.request(`
            INSERT INTO bu_Project p
                SET (
                    p.from="${calendar.newEntry.from}",
                    p.to="${calendar.newEntry.to}",
                    p.arival="${calendar.newEntry.arrival}",
                    p.depature="${calendar.newEntry.departure}",
                    p.companyId=${login.companyId},
                    p.createDate="${new Date().toISOString}",
                    p.name="${document.getElementsByName["name"][0]}",  
                        //address.data.name steht unter adressId, welche erst noch eventuell gespüeichert werden muss




        `);
        
    }

    
    // async load(reload=true) {
    //     if (this.data!=null && !reload) return;
    //     this.data=await this.request.get();
    // }

    
    // getJob(id) {
    //     if (this.data == null) return null;
    //     return this.data.find(e => e.id == id);
    // }

    getRow(id) {
        if (this.data == null) return null;
        return this.data.find(e => e.id == id);
    }
    getById(id) {
        if (this.data == null) return null;
        return this.data.find(e => e.id == id);
    }

    getByFather(id) {
        return this.data.filter(e => e.father == id);
    }

    filterHeadlines() {
        let filter=this.data.filter(e => e.father == null);
        return filter;
    }

    


}