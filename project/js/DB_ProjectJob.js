import {Query} from "./Query.js" 

export class DB_ProjectJob extends Query {
    // request;
    // data=null;
    // isLoaded=false;


    constructor() {
        // this.request=new Query("SELECT * FROM `bu_job`  ORDER BY father; "); // where firma = 14 Eben welcher eingeloggt ist
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

        // super(`
        //     WITH RECURSIVE JobHierarch_temp AS (
        //         SELECT 
        //             job.id AS job_id,
        //             job.name AS job_name,
        //             job.color AS job_color,
        //             job.father AS direct_father,
        //             job.id AS ultimate_father_id,
        //             job.name AS ultimate_father_name,
        //             job.color AS ultimate_father_color
        //         FROM 
        //             bu_job job
        //         WHERE 
        //             job.companyId = ${login.companyId}                     
        //         UNION ALL
        //         SELECT 
        //             child.job_id,
        //             child.job_name,
        //             child.job_color,
        //             parent.father AS direct_father,
        //             parent.id AS ultimate_father_id,
        //             parent.name AS ultimate_father_name,
        //             parent.color AS ultimate_father_color
        //         FROM 
        //             bu_job parent
        //         INNER JOIN JobHierarchy child
        //             ON parent.id = child.direct_father
        //         WHERE 
        //             parent.companyId = ${login.companyId}               
        //     )
        //     SELECT DISTINCT
        //         jh.job_id AS id,
        //         jh.job_name AS name,
        //         jh.job_color AS color,
        //         jh.ultimate_father_id AS ultimateId,
        //         jh.ultimate_father_name AS ultimateName,
        //         jh.ultimate_father_color AS ultimateColor
        //     FROM 
        //         JobHierarchy jh
        //     WHERE 
        //         jh.direct_father IS NULL 
        //     ORDER BY 
        //         name;
        // `);
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