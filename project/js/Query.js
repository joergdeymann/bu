export class Query {
    __promise = null;
    // promiseList = [];
    __filename = "";
    __element = null;
    isLoading = false;
    data = [];

    constructor(query) {
        this.setFilename();

        if (query) {
            this.request(query);
        }
    }

    // init() {
    //     setFilename();
    //     // this.promiseList = Null
    // }

    setFilename(filename='./php/request.php') {
        this.__filename = filename;
    }

    setElement(elementId) { // element oder ElementID 
        // ++ abfrage ob Querystring 
        let element = null;
        if (elementId === undefined) return;
        if (typeof elementId === "object") element = elementId;
        if (typeof elementId === "string") {
            element = document.getElementById(elementId);
        }
        this.__element = element;
    }

    output(html=this.html) {
        this.__element.innerHTML = html;
    }

    getHTML(){
        return this.html;
    }

    __setParameter(query) {
        if (query) {
            query=query.replace(/\s+/g, " ").trim();
            console.log("request:",query); //DEBUG 
        } else {
            console.log("request: no Parameter"); //DEBUG 
        }
        return query;

    }
    // startet das Laden der Daten über fetchData
    async request(query) {
        if (this.isLoading) return; 

        query=this.__setParameter(query);
        this.isLoading = true; 
        const json = query ? { query: query } : {};
        this.__promise = this.__fetchData(json); 
        this.__promise.then(() => {
            this.isLoading = false;
            this.errorcheck(); 
        });
    }

    async get(query=null) {
        if (this.__promise) {
            await this.__promise;
        } else if (!this.data && query) {
            this.__promise = this.request(query);
            this.data= await this.__promise();
            this.__promise=null;
        }
        // console.log(this.data);
        // console.trace("Stack Trace:");
        return this.data;
    }

    async await(query=null) {
        await this.get(query);
    }
    // fetchData führt die Fetch-Anfrage durch
    async __fetchData(keyvalues = {}) {
        try {
            const response = await fetch(this.__filename, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(keyvalues)
            });
            this.data = await response.json();

            return this.data; // Rückgabe der Daten aus dem PHP-Skript

        } catch (error) {
            console.error("Fehler im PHP-Skript:", error);
            console.error("Request:\n" + keyvalues.query);
            throw error;
        }
    }

    errorcheck() {
        if (this.data.error) {
            let html=`
                <div style="display:flex;flex-direction:column;border: 1px solid red;background-color: rgba(200,66,66,1);width:90%;">
                    <div style="font-weight: 700;font-size:1.5rem;border-bottom:1px solid black">Request</div>
                    <div>${this.data.request}</div>

                    <div style="font-weight: 700;font-size:1.5rem;border-bottom:1px solid black">Fehler Text</div>
                    <div>${this.data.message}</div>

                    <div style="font-weight: 700;font-size:1.5rem;border-bottom:1px solid black">Fehler Kurz</div>
                    <div>${this.data.error} in Zeile ${this.data.line}</div>

                    <div style="font-weight: 700;font-size:1.5rem;border-bottom:1px solid black">Datei</div>
                    <div>${this.data.file}</div>

                </div>                
            `;
            
            let dbError = document.createElement("div"); // "div" statt "dbError" als Tag
            dbError.id = "dbError"; // Falls eine ID benötigt wird
            dbError.style="position:fixed;top:0;left:0;right:0;bottom:0;background-color: rgba(0,0,0,0.3);display:flex;justify-content:center;align-items:center;z-index:100;padding: 16px;";
            dbError.innerHTML = html; // HTML-Inhalt setzen
            
            // Neues Element als erstes in den Body einfügen
            document.body.insertBefore(dbError, document.body.firstChild);            

        }
    } 
}












/*
SYNTAX: 

let request = new Request("SELECT * from Datei");
oder 
let request = new Request();
request.request("SELECT * from Datei");

requets.get();
oder
await request.get();
console.log(request.data);
async function render(request) {
    await request.get();
    // request.data

}

function init() {
    const p = new Request();
    // p.setElement("content"); Duirekte zuweisung der Daten nicht möglich
    p.setFilename('./php/request.php');

    const query = "SELECT * FROM table" ; // Beispielhafter request
    p.request(query); // Startet den Ladeprozess

    // A Direkt
    render(p); 
}

*/
