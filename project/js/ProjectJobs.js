import {Query} from "./Query.js" // Sollte aber groß sein

export class ProjectJobs  {
    request;
    data=null;


    constructor() {
        // this.request=new Query("SELECT * FROM `bu_job`  ORDER BY father; "); // where firma = 14 Eben welcher eingeloggt ist
        this.request=new Query(`
            WITH RECURSIVE JobHierarchy AS (
                SELECT 
                    job.id AS job_id,
                    job.name AS job_name,
                    job.color AS job_color,
                    job.father AS direct_father,
                    job.id AS ultimate_father_id,
                    job.name AS ultimate_father_name,
                    job.color AS ultimate_father_color
                FROM 
                    bu_job job
                UNION ALL
                SELECT 
                    child.job_id,
                    child.job_name,
                    child.job_color,
                    parent.father AS direct_father,
                    parent.id AS ultimate_father_id,
                    parent.name AS ultimate_father_name,
                    parent.color AS ultimate_father_color
                FROM 
                    bu_job parent
                INNER JOIN JobHierarchy child
                    ON parent.id = child.direct_father
            )
            SELECT DISTINCT
                jh.job_id AS id,
                jh.job_name AS name,
                jh.job_color AS color,
                jh.ultimate_father_id AS ultimateId,
                jh.ultimate_father_name AS ultimateName,
                jh.ultimate_father_color AS ultimateColor
            FROM 
                JobHierarchy jh
            WHERE 
                jh.direct_father IS NULL 
            ORDER BY 
                name;
        `);
    }

    async load(reload=true) {
        if (this.data!=null && !reload) return;
        this.data=await this.request.get();
    }

    async getJobHeadlines() {
        await this.load();

        // let filter=this.data.filter(e => e.father == null);

        let filter=this.data.filter(e => e.id == e.ultimateId);
        return filter;
    }

    async getJobs(filterId) {
        await this.load();
        let filter=this.data.filter(e => e.ultimateId == filterId && e.id != e.ultimateId);
        return filter;
    }


    async renderJobHeadlines() {
        let html="";
        let jobs=await this.getJobHeadlines();
        

        for(let job of jobs) {
            html+=`<button type="button" onclick="calendar.getJobs(${job.id})" style="background:${job.color}">${job.name}</button>`;
        }
        return `<div class="buttons type">${html}</div>`;
    }

    async renderJobs(id) {
        let html="";
        let jobs=await this.getJobs(id);

        for(let job of jobs) {
            html+=`<button type="button" onclick="calendar.chooseJob(${job.id})" style="background:${job.color}">${job.name}</button>`;
        }
        return `<div class="buttons full">${html}</div>`;
    }
    
    getJob(id) {
        return this.data.find(e => e.id == id);
    }

    get(id) {
        return this.data.find(e => e.id == id);
    }


}