import Repository from '../repositories/RepositoryFactory'
import {ErrorHandler} from "../Helpers/ErrorHander";

export class MiniGridService {
    constructor() {
        this.repository = Repository.get('minigrid');
        this.miniGrids = [];
    }

    async getMiniGrids() {
        try {

            let response = await this.repository.list();

            if (response.status === 200 || response.status === 201) {
                this.miniGrids = response.data.data;

                return this.miniGrids;
            } else {
                return new ErrorHandler(response.error, 'http', response.status);
            }
        } catch (e) {
            return new ErrorHandler(e, 'http');
        }
    }
}
