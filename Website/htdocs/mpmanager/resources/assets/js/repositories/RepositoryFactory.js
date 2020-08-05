import AssetRepository from './AssetRepository'
import TariffRepository from './TariffRepository'
import TicketRepository from './TicketRepository'
import MaintenanceRepository from './MaintenanceRepository'
import SmsRepository from './SmsRepository'
import MiniGridRepository from './MiniGridRepository'
import CityRepository from './CityRepository'
import ConnectionTypeRepository from './ConnectionTypeRepository'
import ConnectionGroupsRepository from './ConnectionGroupsRepository'

import RestrictionRepository from './RestrictionRepository'
import MappingRepository from './MappingRepository'
import ClusterRepository from './ClusterRepository'
import UserRepository from './UserRepository'
import MeterRepository from './MeterRepository'
import PersonRepository from './PersonRepository'
import AuthenticationRepository from './AuthenticationRepository'
import TransactionRepository from './TransactionRepository'
import RevenueRepository from './RevenueRepository'
import AgentRepository from './AgentRepository'
import CountryRespository from './CountryRespository'
import AgentCommissionRepository from './AgentCommissionRepository'
import AgentAssignedApplianceRepository from './AgentAssignedApplianceRepository'
import AgentBalanceHistoryRepository from './AgentBalanceHistoryRepository'
import AgentChargeRepository from './AgentChargeRepository'
import AgentSoldApplianceRepository from './AgentSoldApplianceRepository'
import AgentTransactionRepository from './AgentTransactionRepository'
import TicketCommentRepository from './TicketCommentRepository'
import TicketTrelloRepository from './TicketTrelloRepository'
import TicketUserRepository from './TicketUserRepository'
import TicketLabelRepository from './TicketLabelRepository'
import AgentReceiptRepository from './AgentReceiptRepository'

const repositories = {
    'asset': AssetRepository,
    'tariff': TariffRepository,
    'ticket': TicketRepository,
    'maintenance': MaintenanceRepository,
    'sms': SmsRepository,
    'minigrid': MiniGridRepository,
    'city': CityRepository,
    'map': MappingRepository,
    'user': UserRepository,
    'cluster': ClusterRepository,
    'connectionTypes': ConnectionTypeRepository,
    'connectionGroups': ConnectionGroupsRepository,
    'restriction': RestrictionRepository,
    'meter': MeterRepository,
    'person': PersonRepository,
    'authentication': AuthenticationRepository,
    'transaction': TransactionRepository,
    'revenue': RevenueRepository,
    'ticketComment': TicketCommentRepository,
    'ticketTrello': TicketTrelloRepository,
    'ticketUser': TicketUserRepository,
    'ticketLabel': TicketLabelRepository,
    'agent': AgentRepository,
    'country': CountryRespository,
    'commission': AgentCommissionRepository,
    'assignedAppliance': AgentAssignedApplianceRepository,
    'balanceHistory': AgentBalanceHistoryRepository,
    'balanceCharge': AgentChargeRepository,
    'soldAppliance': AgentSoldApplianceRepository,
    'agentTransactions': AgentTransactionRepository,
    'agentReceipt':AgentReceiptRepository
}

export default {
    get: name => repositories[name]
}
