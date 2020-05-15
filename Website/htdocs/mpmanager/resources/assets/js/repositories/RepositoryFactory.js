import AssetRepository from './AssetRepository'
import TariffRepository from './TariffRepository'
import TicketRepository from "./TicketRepository";
import MaintenanceRepository from "./MaintenanceRepository";
import SmsRepository from "./SmsRepository";
import MiniGridRepository from "./MiniGridRepository";
import CityRepository from "./CityRepository";
import RestrictionRepository from './RestrictionRepository'
const repositories = {
    'asset': AssetRepository,
    'tariff': TariffRepository,
    'ticket':TicketRepository,
    'maintenance':MaintenanceRepository,
    'sms':SmsRepository,
    'minigrid':MiniGridRepository,
    'city':CityRepository,
    'restriction':RestrictionRepository
};

export default {
    get: name => repositories[name]
}
