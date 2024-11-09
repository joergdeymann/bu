import {Request} from "../js/Request.js" // Sollte aber groß sein

export class ProjectJobs  {
    request;
    json=null;


    constructor() {
        this.request=new Request("SELECT * FROM `bu_project_jobs`  ORDER BY father; "); // where firma = 14 Eben welcher eingeloggt ist
    }

    async load() {
        if (this.json==null) {
            this.json=await this.request.get();
        }
    }

    async getJobHeadlines() {
        await this.load();

        let filter=this.json.filter(e => e.father == null);
        return filter;
    }

    async getJobs(filterId) {
        await this.load();
        let filter=this.json.filter(e => e.father == filterId);
        return filter;
    }


    async renderJobHeadlines() {
        let html="";
        let jobs=await this.getJobHeadlines();

        for(let job of jobs) {
            html+=`<button type="button" onclick="calendar.getJobs(${job.recnum})" style="background:${job.color}">${job.name}</button>`;
        }
        return `<div class="buttons type">${html}</div>`;
    }

    async renderJobs(id) {
        let html="";
        let jobs=await this.getJobs(id);

        for(let job of jobs) {
            html+=`<button type="button" onclick="calendar.chooseJob(${job.recnum})" style="background:${job.color}">${job.name}</button>`;
        }
        return `<div class="buttons full">${html}</div>`;
    }
    
    getJob(id) {
        return this.json.find(e => e.recnum == id);
    }
}