import {Request} from "../js/Request.js" // Sollte aber groß sein

export class ProjectJobs  {
    request;


    constructor() {
        this.request=new Request("SELECT * FROM `bu_project_jobs`  ORDER BY father; "); // where firma = 14 Eben welcher eingeloggt ist
        this.request.getRequest();
    }

    async getJobHeadlines() {
        let json=await this.request.getData();
        let filter=json.filter(e => e.father == null);
        console.log(filter);
    }

    async getJobs(filterId) {
        let json=await request.getData();
        let filter=json.filter(e => e.father == filterId);
        console.log(filter);
    }
}