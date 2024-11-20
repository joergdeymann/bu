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

        this.request=new Query(`
            SELECT 
                w.recnum as id,
                w.projekt_recnum,
                left(w.start,10) as start, 
                left(w.ende,10) as end, 
                left(w.anfahrt,10) as arrival,
                left(w.abfahrt,10) as departure,
                w.unterkunft_recnum, 
                w.unterkunft_start, 
                w.unterkunft_ende, 
                w.info,
                w.color,
                p.name as projectName,
                a.ort as city
            FROM bu_projekt_arbeiter w 
            LEFT JOIN bu_projekt as p
                ON p.recnum = w.projekt_recnum
            LEFT JOIN bu_adresse as a
                ON a.recnum = p.location_recnum
            WHERE w.firma_recnum=${login.companyId}
            AND (
                (w.start between "${from}" and "${to}") OR 
                (w.ende  between "${from}" and "${to}") OR
                (w.anfahrt between "${from}" and "${to}") OR 
                (w.abfahrt  between "${from}" and "${to}")
            ) 
            ORDER BY w.start`
        );
    }
    // mitarbeiter_recnum ist hier sinnlos, da es eine eigene Date dafür gibt
    // AND w.mitarbeiter_recnum = ${login.userId}

    async get() {
        this.data=await this.request.get();
        return this.data;
    }

}
