import { Query } from './Query.js';

export class EquipmentPrice {
    constructor() {
    }

    async load() {
        let projectJobId=0;

        let p=new Query(`
            SELECT 
                COALESCE(eq.price, eqp.price, art.price) AS netto,
                COALESCE(eq.vat, eqp.vat, art.vat) AS mwst,
                
                eq.price as eq_netto,
                eqp.price as customer_netto,
                art.price as art_netto,
                
                eq.vat as eq_mwst,
                eqp.vat as customer_mwst,
                art.vat as art_mwst
            FROM 
                bu_article art
            LEFT JOIN 
                bu_equipment_price eqp 
                ON eqp.articleId = art.id 
                AND eqp.customerId = ${customerList.id}
            LEFT JOIN 
                bu_time_equipment eq 
                ON eq.articleId = art.id 
                AND eq.projectJobId=${projectJobId} 
                AND eq.companyId=${login.companyId} 

            WHERE 
                art.id = ${this.equipment.id} and art.companyId = ${login.companyId};
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
    
