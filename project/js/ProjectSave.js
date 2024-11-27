export class ProjectSaver {

    async showPopup() {
        const w=new Windows();
        window.w = w;
    
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
                transform: "translate(0, 200vh) !important",
    
            }
        )

        w.setContent("<center>Erfolgreich gespeichert</center>");
        await w.start("BottomToCenter");
        await w.start("opacity");    
    
    
        // await new Promise(e => setTimeout(e,5));
        // await w.start();
        // w.addAnimation("opacity")
        // await w.start(); 
        // w.hide();   
    }
    
    saveSetup() {

    }

    /**
     * All ProjectData
     */
    saveProject() {

    }

    /**
     * All Information for the Job
     * CompanyId and ProjectId
     * its for handling more JOBs for one Project
     */
    saveProjectJob() {


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


}
