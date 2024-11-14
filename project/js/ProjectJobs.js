import {Query} from "./Query.js" // Sollte aber groß sein

export class ProjectJobs  {
    request;
    data=null;


    constructor() {
        this.request=new Query("SELECT * FROM `bu_job`  ORDER BY father; "); // where firma = 14 Eben welcher eingeloggt ist
    }

    async load() {
        if (this.data==null) {
            this.data=await this.request.get();
        }
    }

    async getJobHeadlines() {
        await this.load();

        let filter=this.data.filter(e => e.father == null);
        return filter;
    }

    async getJobs(filterId) {
        await this.load();
        let filter=this.data.filter(e => e.father == filterId);
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