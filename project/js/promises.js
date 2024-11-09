class myPromises {
    promise=null;
    promiseList=[];
    filename="";
    element=null;
    isloading=false;


    init() {
        // this.promiseList=Null
    }
    setFilename() {
        this.filename="datei.txt"
    }

    setElement(elementId) { // element oder ElementID
        let element=null;
        if (elementId === undefined) return;
        if (typeof elementId === "object") element=elementId;
        if (typeof elementId === "string") {
            // Auch Query könnte man hier noch mit einbinden
            element=document.getElementById(elementId);
        }
        this.element=element;

    }

    startLoad() {
        if (this.isLoading) return; 
        this.isLoading=true;

        this.promise = new Promise((resolve)  => {
            setTimeout(() => {
                resolve({action:'boot'});
                this.content="Der Hinzugefügte Content";
                this.isLoading=false;
            },2000)
        });
        
        this.promiseList.push(this.promise);
    }

    async do() {
        if (this.isLoading) {
            console.log("PromiseAll: Warte auf den Ladeprozess...");
            await Promise.all(this.promiseList);
        }


        if (this.element && this.content) {
            this.element.innerHTML = this.content;
        } else {
            console.error("PromiseAll: Element ist nicht gesetzt.");
        }
    }

    async doSingle() {
        if (!this.promise) {
            console.log("Single: Promise wurde noch nicht gestartet.");
            return;
        }

        console.log("Single: Warte auf den Ladeprozess...");
        const result = await this.promise;
        

        console.log("Single: Promise Result:", result)

        if (this.element && this.content) {
            this.element.innerHTML = this.content;
        } else {
            console.error("Single: Element ist nicht gesetzt.");
        }
    }
    
}

function init() {
    const p=new myPromises();
    p.setElement("content");
    p.setFilename();

    p.startLoad();

    // A Direkt
    p.doSingle();

    //B Indirekt
    // p.do();
}

