import AssetRepository from './AssetRepository'
import TariffRepository from './TariffRepository'
import TicketRepository from "./TicketRepository";
import MaintenanceRepository from "./MaintenanceRepository";
import SmsRepository from "./SmsRepository";
import MiniGridRepository from "./MiniGridRepository";
import CityRepository from "./CityRepository";
import MappingRepository from './MappingRepository'
import UserRepository from './UserRepository'
import ClusterRepository from './ClusterRepository'
const repositories = {
    'asset': AssetRepository,
    'tariff': TariffRepository,
    'ticket':TicketRepository,
    'maintenance':MaintenanceRepository,
    'sms':SmsRepository,
    'minigrid':MiniGridRepository,
    'city':CityRepository,
    'map':MappingRepository,
    'user':UserRepository,
    'cluster':ClusterRepository
};

export default {
    get: name => repositories[name]
}
