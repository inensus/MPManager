<template>
    <div>
        <h2>Main Settings</h2>
        <div class="md-layout md-gutter">
            <div class="md-layout-item md-size-50">
                <md-field :class="{'md-invalid': errors.has($tc('words.title'))}">
                    <label>{{ $tc('words.title') }}</label>
                    <md-input :name="$tc('words.title')"
                              v-model="mainSettings.site_title"
                              :id="$tc('words.title')"
                              v-validate="'required|min:5'">

                    </md-input>
                    <span class="md-error">{{ errors.first($tc('words.title')) }}</span>
                </md-field>
            </div>
            <div class="md-layout-item md-size-50">
                <md-field :class="{'md-invalid': errors.has('Company Name')}">
                    <label>Company Name</label>
                    <md-input name="Company Name"
                              id="Company Name"
                              v-model="mainSettings.company_name"
                              v-validate="'required|min:5'"
                        ></md-input>
                    <span class="md-error">{{ errors.first('Company Name') }}</span>
                </md-field>
            </div>
        </div>
        <div class="md-layout md-gutter">
            <div class="md-layout-item md-size-30">
                <md-field>
                    <label for="currency">{{ $tc('words.currency') }}</label>
                    <md-select name="currency" id="currency"  v-model="mainSettings.currency">
                        <md-option disabled>Select Currency</md-option>
                        <md-option v-for="cur in currencyList" :key="cur.code" :value="cur.symbol">{{ cur.name }} - {{ cur.symbol }}</md-option>
                    </md-select>
                </md-field>
            </div>
            <div class="md-layout-item md-size-40">
                <md-field>
                    <label for="country">Country</label>
                    <md-select name="country" id="country" v-model="mainSettings.country" md-dense >
                        <md-option disabled>Select Country</md-option>
                        <md-option v-for="country in countryList" :key="country" :value="country">{{ country }}</md-option>
                    </md-select>
                </md-field>
            </div>
            <div class="md-layout-item md-size-30">
                <md-field>
                    <label for="language">Language</label>
                    <md-select name="language" id="language" v-model="mainSettings.language" md-dense >
                        <md-option disabled>Select Language</md-option>
                        <md-option v-for="(language,index) in languagesList" :key="index" :value="language">{{ language }}</md-option>
                    </md-select>
                </md-field>
            </div>
        </div>
        <div class="md-layout md-gutter">
            <div class="md-layout-item md-size-50">
                <md-field :class="{'md-invalid': errors.has('vat_energy')}">
                    <label for="vat_energy">VAT Energy</label>
                    <md-input
                    name="vat_energy"
                    id="vat_energy"
                    v-model="mainSettings.vat_energy"
                    type="number"
                    maxlength="9"
                    v-validate="'required|decimal:2|max:4'"
                    ></md-input>
                </md-field>
                <span class="md-error">{{ errors.first('vat_energy') }}</span>
            </div>
            <div class="md-layout-item md-size-50">
                <md-field :class="{'md-invalid': errors.has('vat_appliance')}">
                    <label for="vat_appliance">VAT Appliance</label>
                    <md-input
                        name="vat_appliance"
                        id="vat_appliance"
                        v-model="mainSettings.vat_appliance"
                        type="number"
                        maxlength="9"
                        v-validate="'required|decimal:2|max:4'"
                    ></md-input>
                </md-field>
                <span class="md-error">{{ errors.first('vat_appliance') }}</span>
            </div>


        </div>
        <md-progress-bar v-if="progress" md-mode="indeterminate"></md-progress-bar>
    </div>
</template>

<script>
import { MainSettingsService } from '../../services/MainSettingsService'
import { CurrencyListService } from '../../services/CurrencyListService'
import { LanguagesService } from '../../services/LanguagesService'
import { CountryListService } from '../../services/CountryListService'

export default {
    name: 'MainSettings',

    data(){
        return{
            mainSettingsService: new MainSettingsService(),
            currencyListService: new CurrencyListService(),
            languagesService: new LanguagesService(),
            countryListService: new CountryListService(),
            mainSettings: [],
            currencyList:{},
            languagesList:[],
            countryList:{},
            progress:false,
        }

    },
    mounted () {
        this.getMainSettings()
        this.getCurrencyList()
        this.getLanguagesList()
        this.getCountryList()
    },
    methods:{
        async getCurrencyList(){
            try {
                this.currencyList = await this.currencyListService.list()
            }catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        async getCountryList(){
            try {
                this.countryList = await this.countryListService.list()
            }catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        async getLanguagesList(){
            try {
                this.languagesList = await this.languagesService.list()
            }catch (e) {
                this.alertNotify('error', e.message)
            }
        },
        async getMainSettings(){
            try {
                this.mainSettings = await this.mainSettingsService.list()
            }catch (e) {
                this.alertNotify('error', e.message)
            }

        },
        async updateMainSettings(){
            this.progress = true
            let validator = await this.$validator.validateAll()
            if (!validator) {
                return
            }
            try {
                await this.mainSettingsService.update(this.mainSettings)
                this.updateStoreStates(this.mainSettings)

            }catch (e) {
                this.alertNotify('error', e.message)
            }
            this.progress = false
        },

        updateStoreStates(mainSettings){
            document.title = mainSettings.site_title
            this.$i18n.locale = mainSettings.language
            this.$store.state.mSettings = mainSettings
        },
        alertNotify (type, message) {
            this.$notify({
                group: 'notify',
                type: type,
                title: type + ' !',
                text: message
            })
        },
    }
}
</script>

<style scoped>

</style>
