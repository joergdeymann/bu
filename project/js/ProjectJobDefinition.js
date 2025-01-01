import { DB_ProjectJobDefinition } from "./DB_ProjectJobDefinition.js";

export class ProjectJobDefinition extends DB_ProjectJobDefinition {
    newEntry= {
        start:'',
        end:'',
        arrival:'',
        departure:'',
        color: '#FFA500',
        id:0,  
        name:"",
        ultimateColor:'#FFA500'   
    }
    
    constructor() {
        super();
        this.init();
    }


    init() {
        this.setElement("jobs"); //Output in HTML
    }

    async outputHeadlines() {
        //if (!this.data) 
        await this.get();
        let jobs=this.filterHeadlines();
        this.output(this.renderHeadlines(jobs));
    }

    async outputJobs(id) {
        console.log("outputJobs");
        // await this.get();
        let jobs=this.getByFather(id);
        this.output(this.renderJobs(jobs));
    }


    // filterJobs(filterId) {
    //     let filter=this.data.filter(e => e.ultimateId == filterId && e.id != e.ultimateId);
    //     return filter;
    // }


    renderHeadlines(jobs) {
        let html=``;
        for(let job of jobs) {
            html+=`<button type="button" onclick="job.outputJobs(${job.id})" style="background:${job.color}">${job.name}</button>`;
        }
        return `
            <h2>${this.newEntry.name}</h2>
            <div class="buttons type">${html}</div>
        `;
    }

    renderJobs(jobs) {
        let html="";
        for(let job of jobs) {
            html+=`<button type="button" onclick="job.chooseJob(${job.id})" style="background:${job.color}">${job.name}</button>`;
        }
        return `
            <h2>${this.newEntry.name}</h2>
            <div class="buttons full">${html}</div>
        `;
    }

    isChoosen() {
        return this.newEntry?.jobId>0??false;
    }

    displayJobByArticleId(articleId) {
        let jobId=this.chooseJobByArticleId(articleId);    
        if (jobId) this.chooseAndDisplayJob(jobId);
    }

    chooseJobByArticleId(articleId) {
        return this.data.find(e => e.articleId == articleId)?.id??null;
    } 

    chooseAndDisplayJob(id) {
        let job=this.getById(id);
        this.newEntry.color=job.color;
        // this.newEntry.id=id;
        this.newEntry.jobId=id;
        this.newEntry.name=job.name;
        this.outputHeadlines();
        calendar.display();
    }

    chooseJob(id) {
        this.chooseAndDisplayJob(id);
        projectPrice.clearDayrate();
    }

    reset() {
        this.newEntry = {
            ...this.newEntry,  // Die restlichen Attribute behalten
            start: '',
            end: '',
            arrival: '',
            departure: ''
        };        
        calendar.position=0;
        calender.display();      // this.renderCalendarAll();    
    }


}
