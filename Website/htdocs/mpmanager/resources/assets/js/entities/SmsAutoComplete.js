export class SmsAutoComplete {
    constructor(id,stored, phone, display) {
        this.id = id
        this.stored = stored
        this.phone = phone
        this.display = display
    }

    toLowerCase(){
        return this.display.toLowerCase()
    }

    toString(){
        return this.toLowerCase()
    }
}
