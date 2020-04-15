<template>
    <div class="content-field">
        <div class="header">
            <h1 class="title">MicroPowerManager</h1>
            <h5 class="subtitle">Forgot Password</h5>
            <div class="title-divider">&nbsp;</div>

        </div>
        <div class="content">
            <form class="md-layout" @submit.prevent="validateUser" data-vv-scope="form-forgot">
                <md-card class="md-layout-item">
                    <md-card-header>
                        <div class="">
                            <div class="subtitle">Please insert your email address that you use in system.</div>
                        </div>
                    </md-card-header>

                    <md-card-content>
                        <md-field :class="{'md-invalid': errors.has('form-forgot.email')}">
                            <label>Email</label>
                            <md-input
                                type="email"
                                name="email"
                                id="email"
                                autocomplete="email"
                                v-model="form.email"
                                :disabled="sending"
                                :v-validate="'required|email'"
                            />
                            <span class="md-error">{{ errors.first('form-forgot.email') }}</span>
                        </md-field>

                    </md-card-content>

                    <md-progress-bar md-mode="indeterminate" v-if="sending"/>

                    <md-card-actions>
                        <md-button type="submit" class="md-primary btn-log" :disabled="sending">Send</md-button>
                    </md-card-actions>
                </md-card>
            </form>

        </div>
    </div>
</template>

<script>

    import {Admin} from "../../classes/admin";

    export default {
        name: 'ForgotPassword',
        data: () => ({

            form: {
                email: null,
                password: null
            },

            sending: false,
            adminService: new Admin()
        }),
        methods: {

            validateUser() {
                this.$validator.validateAll("form-forgot").then(result => {
                    if (result) {
                        this.sending = true;
                        this.saveUser();
                    }
                    if (!result) {
                    }
                });
            },
            async saveUser() {
                try {
                    let response = await this.adminService.sendEmail(this.form.email);

                    if (response.status_code === 200) {
                        this.alertNotify("success", "New password has sended to your email.");
                        setTimeout(() => {
                            this.$router.push('/')
                        }, 1500)
                    } else {

                        this.alertNotify("error", response.message.email);
                    }
                    this.sending = false;
                } catch (error) {

                    this.alertNotify("error", error);
                    this.sending = false;
                }

            },
            alertNotify(type, message) {
                this.$notify({
                    group: "notify",
                    type: type,
                    title: type + " !",
                    text: message
                });
            },
        }

    }
</script>

<style lang="css"></style>

