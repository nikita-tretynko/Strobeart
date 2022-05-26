<template>
    <div class="sign_up2">
        <div class="height150 app_logo">
            <img class="" src="@/assets/icons/app_logo.svg" alt="icon-approved.svg"/>
        </div>
        <div class="montserrat form_sign_in mb-4">
            <div class="text_code">
                A text message with 6-digits verification
                code was just sent to (***) *** **{{phoneLastNum}}
            </div>
            <div class="mt-4">
                <input type="text" v-model="code" class="form-control" placeholder="enter the code">
            </div>
        </div>
        <div class="height150">
            <div class="d-flex justify-content-center pb-5">
                <div :class="{ 'not-active-btn':!active_btn}" class="btn-c2" @click="signUp2Next">Continue</div>
            </div>
        </div>
    </div>
</template>

<script>
import RegistrationEnum from "../enums/RegistrationEnum";
import {mapGetters, mapMutations} from 'vuex';
import {errorMessage} from "../services/messages";

export default {
    name: "SignUp2",
    data() {
        return {
            code: '',
            phone: null,
        };
    },
    mounted() {

    },
    computed: {
        ...mapGetters([
            'getPhone',
        ]),
        active_btn() {
            return (
                this.code
            );
        },
        phoneLastNum(){
            return this.phone.slice(-2)
        },
    },
    created() {
        this.phone = this.getPhone;
    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader'
        ]),
        closeToast(){
            this.toast = false
        },

        async signUp2Next() {
            if (!this.code){
                errorMessage('Please fill in all of the required fields')
                return
            }
            try {
                this.showLoader();
                const response = await this.$http.post(`${this.$http.apiUrl()}sms/verify`, {
                    'phone': this.phone,
                    'code': this.code,
                });
                this.hideLoader();

                this.$emit('signUp2Next',
                    RegistrationEnum.ONBOARDING_1,
                )
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }

        }
    }
}
</script>

<style lang="scss" scoped>
.app_logo {
    padding-top: 80px;
}

.form_sign_in {
    padding: 0 45px 0 45px;
}

.height150 {
    height: 150px;
}

.sign_up2 {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100vh;
}

.text_code {
    font-family: Montserrat;
    font-style: normal;
    font-weight: 500;
    font-size: 13px;
    line-height: 22px;
    text-align: center;
    text-transform: capitalize;
    color: #494949;
}
</style>
