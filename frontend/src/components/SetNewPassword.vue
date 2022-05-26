<template>
    <div class="item_forgot_password">
        <div class="forgot_password">
            <div class="app_logo">
                <img class="" src="@/assets/icons/app_logo.svg" alt="icon-approved.svg"/>
            </div>
            <div class="form_forgot_password">
                <div class="text_title">Password reset</div>
                <div class="montserrat">
                    <div class="box_input">
                        <div class="mb-4">
                            <input type="password" @keypress="keypressPassword" :class="{ 'is-invalid': password_is_valid }"
                                   v-model="password" class="form-control" placeholder="new password">
                            <div class="invalid-feedback">{{ password_is_valid }}</div>
                        </div>

                        <div class="mb-4">
                            <input type="password" @keypress="keypressConfirmPassword"
                                   :class="{ 'is-invalid': confirm_password_is_valid }" v-model="confirm_password"
                                   class="form-control" placeholder="repeat new password">
                            <div class="invalid-feedback">{{ confirm_password_is_valid }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-4 box_btn">
                <div class="btn-c2" :class="{ 'not-active-btn':!active_btn}" @click="newPassword">Reset password</div>

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
import LoginEnum from "../enums/LoginEnum";

export default {
    name: "SetNewPassword",
    components: {
    },
    data() {
        return {
            password: null,
            confirm_password: null,
            password_is_valid: false,
            confirm_password_is_valid: false,
        };
    },
    mounted() {
    },
    computed: {
        active_btn() {
            return (
                this.password&&this.confirm_password
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
        keypressPassword() {
            this.password_is_valid = false
        },
        keypressConfirmPassword() {
            this.confirm_password_is_valid = false
        },
        getTokenFromUrlQuery() {
            const queryString = window.location.search;
            const parameters = new URLSearchParams(queryString);
            return parameters.get('t');
        },
        async newPassword() {
            if (this.formValidate()){
                return
            }
            try {
                this.showLoader();
                const response = await this.$http.post(`${this.$http.apiUrl()}set_new_password`, {
                    'password': this.password,
                    'token':this.getTokenFromUrlQuery()
                });
                this.$emit('responseSetPassword',
                   'success_new_password',
                )
                this.hideLoader();
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
        },

        clearValid() {
            this.password_is_valid = false;
            this.confirm_password_is_valid = false;
        },
        formValidate(){
            let error_form =  false
            this.clearValid();
            if (!this.password) {
                this.password_is_valid = 'This field is required';
                error_form=true
            }
            if (!this.confirm_password) {
                this.confirm_password_is_valid = 'This field is required';
                error_form=true
            }
            if (this.password<8){
                this.password_is_valid = 'Password needs to be at least 8 characters';
                error_form=true
            }
            if (this.password&&this.confirm_password&&this.password!==this.confirm_password){
                this.confirm_password_is_valid = 'Please make sure your passwords match';
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
    padding-bottom: 19px;
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
