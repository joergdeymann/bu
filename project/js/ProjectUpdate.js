import { Win } from './Win.js';

export class ProjectUpdate {
    async msg(text) {
        await this.showPopup(`<center>${text}</center>`);
    }

    async showPopup(text=null) {
        const w=new Win();
    
        w.addStyle(
            {
                width: "150px",
                height: "150px" ,
                "max-width":"50w",
                "max-height":"50vw",
                background: "linear-gradient(to top right,rgb(0, 168, 99),rgba(0, 230, 160,1))",
                display: "flex",
                "justify-content": "center",
                "align-items":"center",
                "font-size": "1.5rem",
                transform: "translate(0, 200vh)",  
            }
        )

        if (text == null) {
            text="<center>Erfolgreich geändert</center>";
        }

        w.setContent(text);
        await w.start("BottomToCenter");
        await w.start("opacity");    
        
    }
    
    save() {
        this.saveAll();
    }

    async saveAll() {
        this.saveSetup();

        if (db_project.isFullProject) {
            // The Project itself, either the project (or the time_project later)
            // if (!db_project.data?.id ) await db_project.insert();
            // else if (db_project.isFullProject) db_project.update();

        } else {
            let p=[];
            p.push(this.saveCustomer());
            p.push(this.saveEventAdress());
            await Promise.all(p);

            await this.saveProject();
            await this.saveProjectJob();

            p=[];
            p.push(this.saveTimeEquipmentList());
            p.push(this.saveTimeWorker());
            p.push(this.saveTimeJob());
            p.push(this.saveProjectNew()); // Customer Price
            this.saveDayrate(p);
            this.saveStandart(p);
            
            await Promise.all(p);
            this.showPopup();
        }
    } 


    saveSetup() {

    }

    async saveCustomer() {
        if (!db_customer.data?.id && db_customer.name) return await db_customer.insert(); 
        else return await db_customer.update();
    }

    async saveEventAdress() {
        if (!db_address.input.id.value)  return await db_address.insert(); 
        else return await db_address.update();
    }
    
    /**
     * All ProjectData
     * 
     * The Project itself, either the project (or the time_project later)
     */
    async saveProject() {
        if (!db_project.input.id.value ) return await db_project.insert();
        else return await db_project.update();    
    }

    /**
     * All Information for the Job
     * CompanyId and ProjectId
     * its for handling more JOBs for one Project
     */
    async saveProjectJob() {
        // I think this has nor really need but its implemented somewhere 
        // Type of Job 
        if (!db_projectJob.input.id.value ) return await db_projectJob.insert();
        else return await db_projectJob.update();
    }

    async saveTimeEquipmentList() {
        // Quick and Dirty DB
        // Add all equipments
        db_timeEquipmentList.clear();
        db_timeEquipmentList.addAll();
        return await db_timeEquipmentList.insertAll();
    }

    /**
     * Add Data for the User / Worker
     * housing AdressId, Price, ans so on
     */
    async saveTimeWorker() {
        if (!db_timeWorker.input.id ) return await db_timeWorker.insert();
        else return await db_timeWorker.update();
    }

    /**
     * from, to , arrival and departure,jobDefinitionID, CompanyId ProjectJobId
     * hier sollte auch rein ArticleId für den Preis des Jobhs, 
     *    da die ArticleId für die Rechnug Relevant ist
     *    der Preis in der ArtikelID eindeutig ist, das ist ja nicht 100 % mit der Jobart eingabe 
     */
    async saveTimeJob() {
        //Save Time Job
        if (!db_timeJob.data?.id ) return await db_timeJob.insert();
        else return await db_timeJob.update();
    }
    
    async saveProjectNew() {
        // Save bu_customerprice: 
        // Contents of Group, Function, Dayrate, Overtime, and Offdayrate 
        if (!if_projectNew.dataset?.id) return await if_projectNew.insert();
        else return await if_projectNew.update();
    }

    saveDayrate(p) {
        // If Dayrate Change is selected update the Prices to new Value
        // new Creation happened already
        if (project.isDayrateAll() && project.isDayrateVisible()) {
            let d=if_projectNew.dataset;
            p.push(db_dayrate.updatePrice(+d.articleIdDayrate,+d.drPrice));
            p.push(db_dayrate.updatePrice(+d.articleIdOffday,+d.offPrice));
            p.push(db_dayrate.updatePrice(+d.articleIdOvertime,+d.otPrice));
        }
    }


    saveStandart(p) {
        // Standart Value: Set the standart when choosing the Customer or the JobDefinition
        if (project.isStandard()) {
            if (project.isOnlyCustomer()) {
                p.push(if_projectNew.updateNewCustomerStandard(if_projectNew.dataset.customerId));
            } else {
                p.push(if_projectNew.updateNewCustomerStandard(0));
            }
        }
    }

}
