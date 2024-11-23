import { Query } from './Query.js';
/**
 * 
 * Prepares for the Session a Recursive color Table
 */
export class JobHierachy {
    request; 
    data;

    
    constructor() {
        this.load()
        this.get()
    }

    load(date) {
        // CREATE MATERIALIZED VIEW job_hierarchy_view AS WITH RECURSIVE job_hierarchy AS (
        // does not work for MariaDB


        this.request=new Query(`
            DROP  TABLE IF EXISTS job_hierarchy;
            CREATE  TABLE job_hierarchy AS
            WITH RECURSIVE job_hierarchy AS (
                SELECT id, color, father
                FROM bu_job_definition
                WHERE father IS NULL
                UNION ALL
                SELECT jd.id, root.color, jd.father
                FROM bu_job_definition jd
                INNER JOIN job_hierarchy root ON jd.father = root.id
            )
            SELECT * FROM job_hierarchy;     
        `);

    }

    async get() {
        this.data=await this.request.get();
        return this.data;
    }

    async await() {
        this.data=await this.request.get();
        return
    }

}
