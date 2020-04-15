<template>
    <widget>
        <div class="md-layout">
            <div class="md-layout-item md-subheader">
                Mobile Provider
            </div>
            <div class="md-layout-item md-subheader n-font">
                <img src="http://micropowermanager.com/storage/icons/vodacom.png"
                style="max-height: 35px;">
            </div>
        </div>
        <hr class="hr-d">
        <div class="md-layout">
            <div class="md-layout-item md-subheader">
                Originator Conversation Id
            </div>
            <div class="md-layout-item md-subheader n-font">
                {{ot.originator_conversation_id}}
            </div>
        </div>
        <hr class="hr-d">
        <div class="md-layout">
            <div class="md-layout-item md-subheader">
                Conversation Id
            </div>
            <div class="md-layout-item md-subheader n-font">
                {{ot.conversation_id}}
            </div>
        </div>
        <hr class="hr-d">
        <div class="md-layout">
            <div class="md-layout-item md-subheader">
                Mpesa Receipt
            </div>
            <div class="md-layout-item md-subheader n-font">
                {{ot.mpesa_receipt}}
            </div>
        </div>
        <hr class="hr-d">
        <div class="md-layout">
            <div class="md-layout-item md-subheader">
                Transaction ID
            </div>
            <div class="md-layout-item md-subheader n-font">
                {{ot.transaction_id}}
            </div>
        </div>
    </widget>

</template>

<script>
    export default {
        name: "VodacomTransactionDetail",
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
        /* transition: margin-left .3s cubic-bezier(.4,0,.2,1); */
        /* will-change: margin-left; */
        background-color: rgba(0,0,0,0.12);
    }
</style>
