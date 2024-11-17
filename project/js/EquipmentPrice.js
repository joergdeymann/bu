import { Query } from './Query.js';

export class EquipmentPrice {
    constructor() {
    }

    async load() {
        let projectId=0;

        let p=new Query(`
            SELECT 
                COALESCE(eq.netto, eqp.netto, art.netto) AS netto,
                COALESCE(eq.mwst, eqp.mwst, art.mwst) AS mwst,
                eq.netto as eq_netto,
                eqp.netto as customer_netto,
                art.netto as art_netto,
                eq.mwst as eq_mwst,
                eqp.mwst as customer_mwst,
                art.mwst as art_mwst
            FROM 
                bu_artikel art
            LEFT JOIN 
                bu_equipment_price eqp 
                ON eqp.articleId = art.recnum 
                    AND eqp.customerId = ${customerList.id}
            LEFT JOIN 
                bu_project_equipment eq 
                ON eq.articleId = art.recnum 
                    AND eq.projectId=${projectId} 
                    AND eq.companyId=${login.companyId} 

            WHERE 
                art.recnum = ${this.equipment.recnum} and art.auftraggeber = ${login.companyId};
        `);
        this.data=await p.get();
    }

    XgetPrice(equipment) {
        this.equipment=equipment;
        this.load().then(e => {
            return this.data[0].netto;
        });
        console.log("Hier sollte man nicht hinkommen");
        
        // return this.data[0].netto;
    }
    async getPrice(equipment) {
        this.equipment=equipment;
        await this.load();
        return (this.data[0]?.netto || equipment.art_netto) + " €"; // !! Prüfen warum Bei Andy kein Datensatz gefunden wurde SELECT eingebnm

    }
}
    
