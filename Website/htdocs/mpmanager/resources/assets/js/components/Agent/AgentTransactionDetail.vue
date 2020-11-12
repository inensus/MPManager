<template>
    <div>
        <div class="md-layout">
            <div class="md-layout-item md-subheader">
                Agent Transaction
            </div>
            <div class="md-layout-item md-subheader n-font">
                <img class="logo" alt="logo" src="https://image.flaticon.com/icons/svg/99/99395.svg"
                     style="max-height: 35px;"/>
            </div>
        </div>
        <hr class="hr-d">
        <div class="md-layout">
            <div class="md-layout-item md-subheader">
                Name
            </div>
            <div class="md-layout-item md-subheader n-font">
                {{agentService.agent.name}}
            </div>
        </div>
        <hr class="hr-d">
        <div class="md-layout">
            <div class="md-layout-item md-subheader">
                Phone
            </div>
            <div class="md-layout-item md-subheader n-font">
                {{agentService.agent.phone}}
            </div>
        </div>
        <hr class="hr-d">
        <div class="md-layout">
            <div class="md-layout-item md-subheader">
                Email
            </div>
            <div class="md-layout-item md-subheader n-font">
                {{agentService.agent.email}}
            </div>
        </div>
        <hr class="hr-d">
        <div class="md-layout">
            <div class="md-layout-item md-subheader">
                Mini-Grid
            </div>
            <div class="md-layout-item md-subheader n-font">
                {{agentService.agent.miniGrid}}
            </div>
        </div>
        <hr class="hr-d">
        <div class="md-layout">
            <div class="md-layout-item md-subheader">
                Transaction ID
            </div>
            <div class="md-layout-item md-subheader n-font">
                {{ot.id}}
            </div>
        </div>
        <hr class="hr-d">
        <div class="md-layout">
            <div class="md-layout-item md-subheader">
                Token
            </div>
            <div class="md-layout-item md-subheader n-font">
                6664 8965 0265 8232 7729
            </div>
        </div>

    </div>
</template>

<script>


import { AgentService } from '../../services/AgentService'

export default {
    name: 'AgentTransactionDetail',
    props: ['ot'],
    data () {
        return {
            agentService: new AgentService()
        }
    },
    mounted () {
        this.getAgentDetail()
    },
    methods: {
        async getAgentDetail () {
            try {
                await this.agentService.getAgent(this.ot.agent_id)

            } catch (e) {
                this.alertNotify('error', e.message)
            }

        },
    }
}
</script>

<style scoped>
    .n-font {
        font-weight: 100 !important;
    }

    .hr-d {
        height: 1pt;
        margin: auto;
        padding: 0;
        display: block;
        border: 0;
        /* transition: margin-left .3s cubic-bezier(.4,0,.2,1); */
        /* will-change: margin-left; */
        background-color: rgba(0, 0, 0, 0.12);
    }
</style>
