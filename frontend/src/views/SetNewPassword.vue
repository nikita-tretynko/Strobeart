<template>
    <MainLayout>
        <div>
            <SetNewPassword @responseSetPassword='onActive' v-if="active==='set_new_password'"></SetNewPassword>
            <SuccessSetNewPassword v-if="active==='success_new_password'"></SuccessSetNewPassword>
            <ErrorSetNewPassword :error-message="error" v-if="active==='error_set_new_password'"></ErrorSetNewPassword>
        </div>
    </MainLayout>
</template>

<script>
import SuccessSetNewPassword from "../components/SuccessSetNewPassword";
import ErrorSetNewPassword from "../components/ErrorSetNewPassword";
import SetNewPassword from "../components/SetNewPassword";
import MainLayout from "../layouts/MainLayout";
import LoginEnum from "../enums/LoginEnum";
import {errorMessage} from "../services/messages";

export default {
    name: 'SentNewPassword',
    components: {
        MainLayout,
        SetNewPassword,
        ErrorSetNewPassword,
        SuccessSetNewPassword
    },
    data() {
        return {
            active:null,
            error:null,
        }
    },
    mounted() {
        this.loadUserInfoByToken();
    },
    methods: {
        onActive(data) {
            this.active = data;
        },
        getTokenFromUrlQuery() {
            const queryString = window.location.search;
            const parameters = new URLSearchParams(queryString);
            return parameters.get('t');
        },
        async loadUserInfoByToken() {
            try {
                const token = this.getTokenFromUrlQuery();
                const request_data = {token};
                const response = await this.$http.post(`${this.$http.apiUrl()}reset-pass-info`, request_data)
                const user_info = response?.data?.data?.user || null;
                this.active ='set_new_password';
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'Oops... Something went wrong...';
                this.error = message;
                console.log(message)
                this.active = 'error_set_new_password';
            }

        },
    },
    computed: {
        enums() {
            return {
                [LoginEnum.LOGIN]: LoginEnum.LOGIN,
                [LoginEnum.FORGOT_PASSWORD]: LoginEnum.FORGOT_PASSWORD,
            }
        }
    }
}
</script>

<style lang="scss" scoped>

</style>
