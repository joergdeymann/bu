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
        
        // standart Employee view
        // other    would be Company View

        // tagessatz,
        // tagessatz_offday
        // arbeitszeit für einen Tag
        // ueberstunden_satz

        // Achtung jobId -> id umbenannt
        // CREATE MATERIALIZED VIEW job_hierarchy_view AS
        // WITH RECURSIVE job_hierarchy AS (
        //     SELECT 
        //         id,
        //         color,
        //         father
        //     FROM bu_job_definition
        //     WHERE father IS NULL
        
        //     UNION ALL
        
        //     SELECT 
        //         jd.id,
        //         root.color,
        //         jd.father
        //     FROM bu_job_definition jd
        //     INNER JOIN job_hierarchy root
        //         ON jd.father = root.id
        // )
        // SELECT * FROM job_hierarchy;
        
        // Diesen code dann später einmalig auisführen bei ersten Load
        // if exists
        // CREATE TEMPORARY TABLE job_hierarchy_temp AS
        // WITH RECURSIVE job_hierarchy AS (
        //     SELECT id, color, father
        //     FROM bu_job_definition
        //     WHERE father IS NULL
        //     UNION ALL
        //     SELECT jd.id, root.color, jd.father
        //     FROM bu_job_definition jd
        //     INNER JOIN job_hierarchy root ON jd.father = root.id
        // )
        // SELECT * FROM job_hierarchy;        

        
        // WITH RECURSIVE job_hierarchy AS (
        //     -- Start bei der aktuellen Job-Definition
        //     SELECT 
        //         id,
        //         color,
        //         father
        //     FROM bu_job_definition
        //     WHERE father IS NULL -- Wähle nur die Wurzelknoten
            
        //     UNION ALL
            
        //     -- Rekursiver Schritt: Suche alle Kinder
        //     SELECT 
        //         jd.id,
        //         root.color, -- Behalte die Farbe des Wurzelknotens
        //         jd.father
        //     FROM bu_job_definition jd
        //     INNER JOIN job_hierarchy root
        //         ON jd.father = root.id
        // )



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
                p.name AS projectName,
                a.ort AS city,
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
                LEFT JOIN bu_adresse a
                    ON a.recnum = p.addressId
                
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


        // this.request=new Query(`
        //     SELECT 
        //         w.recnum as id,
        //         w.projekt_recnum,
        //         left(w.start,10) as start, 
        //         left(w.ende,10) as end, 
        //         left(w.anfahrt,10) as arrival,
        //         left(w.abfahrt,10) as departure,
        //         w.unterkunft_recnum, 
        //         w.unterkunft_start, 
        //         w.unterkunft_ende, 
        //         w.info,
        //         w.color,
        //         p.name as projectName,
        //         a.ort as city
        //     FROM bu_projekt_arbeiter w 
        //     LEFT JOIN bu_projekt as p
        //         ON p.recnum = w.projekt_recnum
        //     LEFT JOIN bu_adresse as a
        //         ON a.recnum = p.location_recnum
        //     WHERE w.firma_recnum=${login.companyId}
        //     AND w.mitarbeiter_recnum =${login.userId}
        //     AND (
        //         (w.start between "${from}" and "${to}") OR 
        //         (w.ende  between "${from}" and "${to}") OR
        //         (w.anfahrt between "${from}" and "${to}") OR 
        //         (w.abfahrt  between "${from}" and "${to}")
        //     ) 
        //     ORDER BY w.start`
        // );
    }
    // mitarbeiter_recnum ist hier sinnlos, da es eine eigene Date dafür gibt
    // AND w.mitarbeiter_recnum = ${login.userId}

    async get() {
        this.data=await this.request.get();
        return this.data;
    }

}
