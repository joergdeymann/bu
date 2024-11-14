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

        this.request=new Query(`
            SELECT 
                w.recnum as jobId,
                w.projekt_recnum,
                left(w.start,10) as start, 
                left(w.ende,10) as end, 
                left(w.anfahrt,10) as arrival,
                left(w.abfahrt,10) as departure,
                w.unterkunft_recnum, 
                w.unterkunft_start, 
                w.unterkunft_ende, 
                w.info,
                w.color

            FROM bu_projekt_arbeiter w 
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
    
    async get() {
        this.data=await this.request.get();
        return this.data;
    }

}
