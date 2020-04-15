<template>
    <widget>
    <div class="md-layout">
        <div class="md-layout-item md-subheader">
            Mobile Provider
        </div>
        <div class="md-layout-item md-subheader n-font" >
            <img src="https://assets.airtel.in/static-assets/selfcare/images/airtel-logo_opt.webp"
             style="max-height: 35px;">
        </div>
    </div>
        <hr class="hr-d">
    <div class="md-layout">
        <div class="md-layout-item md-subheader">
            Transaction ID
        </div>
        <div class="md-layout-item md-subheader n-font">
           <span>{{ot.tr_id}}</span>
        </div>
    </div>
        <hr class="hr-d">
    <div class="md-layout">
        <div class="md-layout-item md-subheader">
            Transaction Identifier
        </div>
        <div class="md-layout-item md-subheader n-font">
            {{ot.trans_id}}
        </div>
    </div>
        <hr class="hr-d">
    <div class="md-layout ">
        <div class="md-layout-item md-subheader">
            Business Number
        </div>
        <div class="md-layout-item md-subheader n-font">
            {{ot.business_number}}
        </div>
    </div>
    </widget>

</template>

<script>
    export default {
        name: 'AirtelTransactionDetail',
        props: ['ot']
    }
</script>

<style scoped>
    .n-font{
        font-weight: 100!important;
    }
    .hr-d{
        height: 1pt;
        margin: auto;
        padding: 0;
        display: block;
        border: 0;
        background-color: rgba(0,0,0,0.12);
    }
</style>
