export class Query {
    __promise = null;
    // promiseList = [];
    __filename = "";
    __element = null;
    isLoading = false;
    data = [];
    headers=[];
    id=0;
    input={
        id: {
            value:0
        }
    }
    

    constructor(query) {
        this.setFilename();
        this.setHeaders();

        if (query) {
            this.request(query);
        }
        this.elements();

    }
    
    elements() {
    }

    // init() {
    //     setFilename();
    //     // this.promiseList = Null
    // }
    setHeaders(headers) {
        if (typeof headers === "String") {
            if (headers.toUpperCase() == "HTML") {
                this.headers = {
                    'Content-Type': 'text/html', 
                    'Accept': 'text/html, application/json'
                };
                return;
            }       

            if (headers.toUpperCase() == "JSON") {
                this.headers = {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                };
                return;
            }       
        }

        if (typeof headers === "object" && headers !== null && !Array.isArray(headers)) { 
            this.headers=headers;
            return;            
        }

        this.headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };

    }

    setFilename(filename='./php/request.php') {
        this.__filename = filename;
    }

    addHeader(header) {
        Object.assign(this.headers, header);
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
        if (this.__element) this.__element.innerHTML = html;
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
            await this.__promise;
            this.__promise=null;
        }
        if (debug) console.log(this.data);
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
                headers: this.headers, 
                body: JSON.stringify(keyvalues)
            });
            const contentType=response.headers.get('Content-Type')
            if (contentType.includes("application/json")) this.data = await response.json();
            else if (contentType.includes("text/html")) this.data = await response.text();
            else throw new Error("Falscher Content Type:"+contentType);

            if (this.data.error) {
                throw new Error("Fehler:"+this.data.error);
            }

            return this.data; // Rückgabe der Daten aus dem PHP-Skript

        } catch (error) {
            
            console.error(
                `Fehler im PHP-Skript ${this.__filename}:\n`, 
                error,
                "\nRequest:\n",
                keyvalues,
                "\nHeaders:",
                this.headers
            );
            console.trace("Trace");
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

    /**
     * Sets the Date in Marks if defined 
     * else null
     * date=0            -> date = 'NULL'
     * date=""           -> date = 'NULL'
     * date="2020-10-01" -> date ='"2020-10-01"'
     */
    inMarks(date) {
        return date?`'${date}'`:'NULL';
    }
    
    

    getById(id) {
        if (this.data == null) return null;
        return this.data.find(e => e.id == id);
    }

    get isLoaded() {
        if (this.input.id instanceof Element) return !!(+this.input.id.value);
        return !!(+this.id);
    }

    setId() {
        if (this.input.id instanceof Element) this.input.id.value=this.data.lastId;
        this.id=this.data.lastId;
    }

    async insert() {
        if(this.isLoaded) return;
        await this.insertQuery();
        await this.get();
        this.setId();
    }

    async update() {
        if (!this.isLoaded) return;
        await this.updateQuery();
        await this.get();        
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
