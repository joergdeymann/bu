import { Query } from './Query.js';

export class ProjectWorker {
    request; 
    data;

    
    constructor() {
    }

    load(date) {
        let from=date.getFullYear()+"-"+String(date.getMonth()+1).padStart(2, '0')+"-01";
        let endOfMonth = new Date(date.getFullYear(), date.getMonth() + 1, 0);
        let to=date.getFullYear()+"-"+String(date.getMonth()+1).padStart(2, '0')+"-"+String(endOfMonth.getDate());
        
        this.request=new Query(`
            SELECT 
                w.id,
                LEFT(w.start, 10) AS "start",
                LEFT(w.end, 10) AS "end",
                LEFT(w.arrival, 10) AS arrival,
                LEFT(w.departure, 10) AS departure,
                w.housingAddressId,
                w.housingStart,
                w.housingEnd,
                w.info,
                w.color AS workerColor,
                w.projectJobId,
                jd.color AS color,
                jd.name  AS jobName,
                a.name AS projectName,
                a.city AS city,
                root_job.color AS rootColor 

            FROM bu_time_worker w
                LEFT JOIN bu_time_job tj
                    ON tj.projectJobId = w.projectJobId
                LEFT JOIN bu_job_definition jd
                    ON jd.id = tj.jobDefinitionId
                LEFT JOIN job_hierarchy root_job
                    ON jd.id = root_job.id        
                LEFT JOIN bu_project_job pj 
                    ON pj.id = w.projectJobId
                LEFT JOIN bu_project p
                    ON p.id = pj.projectId
                LEFT JOIN bu_address a
                    ON a.id = p.addressId
                
            WHERE
                w.companyId = ${login.companyId} 
            AND 
                w.employeeId = ${login.userId}
            AND (
                    (LEFT(w.start,10)      BETWEEN "${from}" AND "${to}") OR 
                    (LEFT(w.end,10)        BETWEEN "${from}" AND "${to}") OR
                    (LEFT(w.arrival,10)    BETWEEN "${from}" AND "${to}") OR 
                    (LEFT(w.departure,10)  BETWEEN "${from}" AND "${to}")
                )
        
        `);

    }
    
    async get() {
        this.data=await this.request.get();
        return this.data;
    }

}
