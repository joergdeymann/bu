import {Query} from "./Query.js" 
// Alles ändern Neuanlage
export class DB_ProjectJob extends Query {
    constructor() {
        super();
    }

    elements() {
        this.id = document.getElementsByName("projectJobId")[0];   // noch anlegen
        this.projectId=document.getElementsByName("projectId")[0];
        
        
    }

    get isFullProject() {
        return document.getElementsByClassName("full-project")[0].classList.contains("full)");
    }

    async insertQuery() {
        this.request(`
            INSERT INTO bu_project_job 
            SET 
                projectId = ${+this.projectId.value},
                companyId = ${+login.companyId}
                
            
        `); 
    }

    async updateQuery() {
        
        await this.request(`
            UPDATE bu_project_job 
            SET 
                projectId = ${+this.projectId.value}            
            WHERE id = ${+this.id.value};
        `); 
    }


}
