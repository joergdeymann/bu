import {Query} from "./Query.js" 

export class DB_ProjectJobDefinition extends Query {
    constructor() {
        super(`
            SELECT 
                job.id AS id,
                job.name AS name,
                job.color AS color,
                job.father AS father,
                job.articleId AS articleId
            FROM bu_job_definition job 
            WHERE job.companyId = ${login.companyId}
            ORDER BY job.father,job.name
        `);

    }

    
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