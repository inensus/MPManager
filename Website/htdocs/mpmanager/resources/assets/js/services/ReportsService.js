import Repository from '../repositories/RepositoryFactory'
import {Paginator} from '../classes/paginator'
import {resources} from '../resources'
export class ReportsService {
    constructor() {
        this.repository = Repository.get('reports')
        this.report = {
            id: null,
            name: null,
            path: null,
            date: null,
            type: null,
        }
        this.list = []
        this.paginatorWeekly = new Paginator(resources.reports.weekly.list)
        this.paginatorMonthly = new Paginator(resources.reports.monthly.list)
    }


    updateList(reports) {
        this.list = []

        for (let index in reports) {
            let report = {
                id: reports[index].id,
                name: reports[index].name,
                path: reports[index].path,
                date: reports[index].date,
                type: reports[index].type,
            }
            this.list.push(report)
        }
        return this.list
    }

    exportReport(id, reference) {
        return this.repository.download(id, reference)
    }

    showAll() {
    }
}
