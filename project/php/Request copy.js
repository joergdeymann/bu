import {PHP} from "../js/PHP.js"
export class Request  {
    query;
    response;

    isLoading=false;


    constructor(query=null) {
        this.query=query;
        this.getRequest();
    } 

    getRequest() {
        if (this.isLoading) return; 
        this.isLoading=true;

        this.promise = new Promise((resolve)  => {



            setTimeout(() => {
                resolve({action:'boot'});
                this.response="Der Hinzugefügte Content";
                this.isLoading=false;
            },2000)
        });
        
    }




    async XgetRequest(query=this.query) {
        if (query == null) return null;
        if (this.loadPromise) return this.loadPromise;

        let php=new PHP('./php/request.php');
        let parameters = {
            query: query
        }

        this.loadPromise = php.get(parameters).then(data => {
            this.response = data;  // Speichert das Ergebnis
            
            return data;
        }).finally(() => {
            this.loadPromise = null;  // Setzt loadPromise zurück, wenn das Laden abgeschlossen ist
        });

        // this.response=await php.get(parameters);   
    }

    /**
     * 
     * Wait for load and get Data
     * 
     * @returns data
     */
    async getData() {
        if (this.loadPromise) {
            await this.loadPromise;
        }
        return this.response;
    }
    
}
/*
SYNTAX: 

let request = new Request("SELECT * from Datei");
request.getRequest();

let json=await request.getData();
console.log(json);
*/
