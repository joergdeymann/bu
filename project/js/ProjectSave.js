import { IF_ProjectNew } from './IF_ProjectNew.js';
import { Win } from './Win.js';

export class ProjectSave {
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
            text="<center>Erfolgreich gespeichert</center>";
        }

        w.setContent(text);
        await w.start("BottomToCenter");
        await w.start("opacity");    
        
    }
    
    save() {
        this.saveAll();
    }

    async saveAll() {
        // try{
            this.saveSetup();

            if (db_project.isFullProject) {
                // The Project itself, either the project (or the time_project later)
                if (!db_project.data?.id ) await db_project.insert();
                else if (db_project.isFullProject) db_project.update();

            } else {
                let p=[];
                // Hier abfragen ob der Kunde leer ist wenn leer dan muss die id im Listener zurückgesetzt werden Focus Lost
                // kommt die abfrage der Id vielleicht aus der Liste der Kunden ?
                // Wenn kunde.id =0 dann auch als 0 in dem bu_project speichern, Kunde nicht anlegen

                
                // Save customer
                if (!db_customer.data?.id && db_customer.name) p.push(db_customer.insert()); 
                else p.push(db_customer.update());

                // Save Event Address
                if (!db_address.data?.id)  p.push(db_address.insert()); // Event Addresse
                else p.push(db_address.update());
                await Promise.all(p);

                // The Project itself, either the project (or the time_project later)
                if (!db_project.data?.id ) await db_project.insert();
                else db_project.update();

                // else if (db_project.isFullProject) db_project.update();

                // I think this has nor really need but its implemented somewhere 
                // Type of Job
                if (!db_projectJob.data?.id ) await (db_projectJob.insert()); // only link, no update Das Falsche ?



                p=[];
                // Quick and Dirty DB
                // Add all equipments
                db_timeEquipmentList.clear();
                db_timeEquipmentList.addAll();
                p.push(db_timeEquipmentList.insertAll());

                // Add Data for the User / Worker 
                if (!db_timeWorker.data?.id ) p.push(db_timeWorker.insert());
                if (!db_timeJob.data?.id ) p.push(db_timeJob.insert());

                // Save bu_customerprice: 
                //  Contents of Group, Function, Dayrate, Overtime, and Offdayrate 
                if (!if_projectNew.dataset?.id) p.push(if_projectNew.insert());
                else p.push(if_projectNew.update());

                await Promise.all(p);
            }
        



            // await db_eventPrice.insert();
            // else 
            // if (calendar.fullProjectView) {
                // await db_project.updateProject(); //Falls in Projekt Ansicht
                // Sind dor ganz andere Felder
                // Ausserdem muss ich die Daten de sganzen Projektes laden, wenn ich in "Bearbeiten Modus bin"
                // dann sollte es klappen
                // im erstellen Modus wird immer ein Job angelegt
                // also es gibt folgende MODI:
                // 1. Erstellen Project und ProjectJob wird erstellt
                // 2. Erweitern Project wird belassen und geladen und ProjectJob wird neu erstellt
                // 3. änderen Project und ProjectJob kann verändert werden#

            // }

            // if (!db_projectJob.data?.id) await projectJob.saveProjectJob();
            // else projectJob.updateProjectJob();
    
            this.saveTimeJob();
            this.saveTimeEquipment();
            this.saveTimeEmplopyee();
            this.showPopup();
        // } catch (e){
        //     console.error("Fehler beim Speichern",e);
        // } 




    } 
    saveSetup() {

    }

    /**
     * All ProjectData
     */
    async saveProject() {

    }

    /**
     * All Information for the Job
     * CompanyId and ProjectId
     * its for handling more JOBs for one Project
     */
   async  saveProjectJob() {


    }

    /**
     * from, to , arrival and departure,jobDefinitionID, CompanyId ProjectJobId
     * hier sollte auch rein ArticleId für den Preis des Jobhs, 
     *    da die ArticleId für die Rechnug Relevant ist
     *    der Preis in der ArtikelID eindeutig ist, das ist ja nicht 100 % mit der Jobart eingabe 
     */
    saveTimeJob() {

    }
    
    /**
     * CompanyId,ProjectJobId, employeeID, start, end,m arrival,dfepature
     * housing AdressId, Price, ans so on
     */
    saveTimeWorker() {

    }

    /**
     * CompanyId, projectJobId, articleId,Price,from, to, info for the equipment (not Yet) 
     * 
     */
    saveTimeEquipment() {

    }

    saveNewCustomer() {
        
    }

    saveNewEquipment() {
        
    }

    /**
     *  CompanyId, articleId, customerId, Price if we have no Id
     */
    saveNewEquipmentPrice() {

    }
    saveTimeEmplopyee() {

    }


}
