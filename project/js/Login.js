import { Query } from './Query.js';

export class Login {
    userName=null;
    userId=null;
    companyName=null;
    companyId=null;

    constructor() {
        // this.getSession();
    }

    async getSession() {
        // document.location.replace("./php/login.php");
        let filename='./php/login.php';
        let session=new Query(); // Query ohne Parameter
        session.setFilename(filename);
        session.addHeader({credentials: 'include'});
        // session.setHeaders("HTML");
        // session.request(document.location.pathname);
        session.request();
        let json=await session.get();
        if (json.error) {
            console.log(json.error);
        }
        if (json.html) {
            // document.location.assign("https://www.example.com");
            // console.log(document.location.hash);  // "#sektion1"
            // console.log(document.location.search); // "?id=123&name=abc"
            // console.log(document.location.pathname); // "/pfad/zur/seite"
            // console.log(document.location.port); // "8080" (falls vorhanden)
            // console.log(document.location.hostname); // "www.example.com"
            // console.log(document.location.host); // Der Hostname und Port der URL (z. B. www.example.com:8080)
            // console.log(document.location.protocol); // "https:"
            // console.log(document.location.href); // Gibt die vollständige URL aus


            document.location.replace(filename);
            return false;
        }


        this.userName=json.userName;
        this.userId=+json.userId;
        this.companyId=+json.companyId;
        // this.companyName=json.companyName;
        return true;
    }
}