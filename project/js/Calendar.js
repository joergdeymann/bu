import {ExtDate} from './ExtDate.js'

export class Calendar extends ExtDate {
    constructor(dateString) {
        super(dateString);
    }

    /**
     * Set the Date we are now
     * @param dateString - A date that can ghet converted to date 
     */
    set(dateString="") {
        this.setDate(dateString);
    };

    /**
     * next Month of the Calendar
     */
    next() {
        this.date.AddMonth();
    }

    /**
     * previous Month Of Calendar
     */
    prev() {
        this.date.SubMonth();
    }

}