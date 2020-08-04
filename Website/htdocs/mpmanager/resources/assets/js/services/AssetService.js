import Repository from '../repositories/RepositoryFactory'
import { EventBus } from '../shared/eventbus'
import { Paginator } from '../classes/paginator'
import { ErrorHandler } from '../Helpers/ErrorHander'

export class AssetService {
    constructor () {
        this.repository = Repository.get('asset')
        this.list = []
        this.asset = {
            id: null,
            name: null,
            price: null,
            updated_at: null,
            edit: false,
        }
        this.paginator = new Paginator(resources.assets.list)

    }

    fromJson(data) {
        this.id = data.id
        this.name = data.name
        this.price = data.price
        this.updated_at = data.updated_at
        return this
    }


    updateList(data) {
        console.log('update lists')
        this.list = data.map(a => {
            return {
                id: a.id,
                name: a.name,
                price: a.price,
                updated_at: a.updated_at,
                edit: false,
            }
        })


    }

    async createAsset() {
        try {
            let response = await this.repository.create(this.asset);
            if (response.status === 200 || response.status === 201) {
                this.asset.id = response.data.data.id;
                this.asset.name = response.data.data.name;
                this.asset.updated_at = response.data.data.updated_at;
                EventBus.$emit('assetTypeAdded', this.asset);
            } else {
                return new ErrorHandler(response.error, 'http', response.status);
            }
        } catch (e) {
            return new ErrorHandler(e, 'http');
        }

    }

    async updateAsset(asset) {
        try {
            let response = await this.repository.update(asset.id, asset);
            if (response.status === 200 || response.status === 201) {
                return response;
            } else {
                new ErrorHandler(response.error, 'http', response.status);
            }

        } catch (e) {
            return new ErrorHandler(e, 'http');
        }

    }

    async deleteAsset(asset) {
        try {
            let response = await this.repository.delete(asset.id);
            if (response.status === 200 || response.status === 201) {
                return response;
            } else {
                new ErrorHandler(response.error, 'http', response.status);
            }
            return response;
        } catch (e) {
            return new ErrorHandler(e, 'http');
        }

    }
}
