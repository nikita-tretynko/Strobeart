<template>
    <div class="item_forgot_password">
        <div class="forgot_password">
            <div class="app_logo">
                <img class="" src="@/assets/icons/app_logo.svg" alt="icon-approved.svg"/>
            </div>
            <div class="form_forgot_password">
                <div class="text_title">Forgot Password</div>
                <div class="text_description">Please enter the email  you’d like your password reset information sent to </div>
                <div class="montserrat">
                    <div class="box_input">
                    <input type="email" autocomplete="off" v-model="email" @keypress="keypressEmail"
                           :class="{ 'is-invalid': email_is_valid }" class="form-control" id="emailSignUp"
                           placeholder="e-mail">
                    <div id="emailSignUpFeedback" class="invalid-feedback">{{ email_is_valid }}</div>
                    </div>
                </div>
            </div>
            <div class="mb-4 box_btn">
                <div class="btn-c2" :class="{ 'not-active-btn':!active_btn}" @click="resetPassword">Request reset link</div>

                <div class="link justify-content-center" >
                    <div class="style_link" @click="backToLogin">Back to login</div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import {mapMutations} from 'vuex';
import {errorMessage} from "../services/messages";
import {VueTelInput} from 'vue-tel-input';
import LoginEnum from "../enums/LoginEnum";

export default {
    name: "ForgotPassword",
    components: {
        VueTelInput,
    },
    data() {
        return {
            email: '',
            valid_email: false,
            error_valid: false,
            email_is_valid: false,
        };
    },
    mounted() {
    },
    computed: {
        active_btn() {
            return (
                this.email
            );
        }
    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
        ]),
        backToLogin(){
            this.$emit('forgotPassword',
                LoginEnum.LOGIN,
            )
        },
        keypressEmail() {
            this.email_is_valid = false
        },
        keypressPassword() {
            this.password_is_valid = false
        },
        async resetPassword() {
            if (this.formValidate()){
                return
            }
            try {
                this.showLoader();
                const response = await this.$http.post(`${this.$http.apiUrl()}reset_password`, {
                    'email': this.email,
                });
                this.$emit('sendEmail',
                    response?.data?.data?.email?.email,
                )
                this.$emit('forgotPassword',
                    LoginEnum.SEND_EMAIL_FORGOT_PASSWORD,
                )
                this.hideLoader();
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
        },
        isValidEmail(email) {
            const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]{1,25}\.)+[a-zA-Z]{2,6}))$/;
            this.valid_email = !re.test(email);
        },
        clearValid() {
            this.valid_email = false;
            this.error_valid = false;
            this.email_is_valid = false;
        },
        formValidate(){
            let error_form =  false
            this.clearValid();
            this.isValidEmail(this.email)
            if (!this.email) {
                this.email_is_valid = 'This field is required';
                error_form=true
            }
            if (this.email&&this.valid_email) {
                this.email_is_valid = 'Incorrect email format. Please try again'
                error_form=true
            }
            return  error_form;
        },
    }
}
</script>

<style lang="scss" scoped>
.text_description{
    padding-bottom: 46px;
    font-family: Montserrat;
    font-style: normal;
    font-weight: normal;
    font-size: 17px;
    line-height: 24px;
    color: #000000;
}
.text_title{
    width: 100%;
    font-family: Montserrat;
    font-style: normal;
    font-weight: 600;
    font-size: 24px;
    line-height: 44px;
    color: #000000;
    text-align: center;
    padding-bottom: 14px;
}

.forgot_password {
    // height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}


.app_logo {
    display: none;
    padding-top: 80px;
    padding-bottom: 25px;
}

input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Firefox */
input[type=number] {
    -moz-appearance: textfield;
}

.btn-c1 {
    .text {
        opacity: 0.5;
    }

    > div, img {
        color: #000000;
        background: #FFFFFF;
    }

    background: #FFFFFF;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    border-radius: 10px;
    padding: 15px 17px;
    cursor: pointer;
}

.error_valid {
    color: red;
    font-family: Montserrat;
    font-style: normal;
    font-weight: 400;
    font-size: 14px;
    text-align: center;
}
.montserrat .form-control{
    padding: 15px 40px;
}
.item_forgot_password {
    display: flex;
    justify-content: center;
    margin-top: 179px;
}

.montserrat {
    width: 100%;
    display: flex;
    justify-content: center;
}
.box_input{
    width: 550px;
}

.form-check-input:checked {
    background-color: #D8C3AF;
    border-color: #D8C3AF;
}

.desc_link_form{
    display: flex;
    justify-content: space-between;
    padding-top: 20px;
}
.mob_forgot_password {
    display: none;
}
.desc_link_form{
    display: flex;
    align-items: center;
}
.style_link{
    font-family: Montserrat;
    font-style: normal;
    font-weight: normal;
    font-size: 14px;
    line-height: 18px;
    color: #000000;
    opacity: 0.65;
    cursor: pointer;
    text-decoration-line: underline;
}
.box_btn{
    display: grid;
    justify-content: center;
    padding-top: 90px;
    padding-bottom: 100px;
}
.link{
    display: flex;
    width: 100%;
    padding-top: 27px;
}
@media only screen and (max-width: 992px) {

    .type_user_radio {
        display: none;
    }
    .type_user_select {
        display: block;
    }
    .item_forgot_password {
        margin-top: 0px;
    }
    .montserrat {
        width: 100%;
    }
    .item_forgot_password
    {
        display: inherit;
    }
    .form_forgot_password {
        padding: 0 45px 0 45px;
    }
    .mob_forgot_password {
        display: flex;
        justify-content: center;
    }
    .app_logo{
        display: block;
    }
    .desc_link_form{
        display: none;
    }
    .box_btn{
        padding-top: 10px;
        padding-bottom: 40px;
    }
    .forgot_password{
        height: 100vh;
    }

}

</style>
