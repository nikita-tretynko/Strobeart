<template>
    <div class="item_sign_in">
        <div class="sign_in">
            <div class="app_logo">
                <img class="" src="@/assets/icons/app_logo.svg" alt="icon-approved.svg"/>
            </div>
            <div class="montserrat form_sign_in mb-4">
                <div class="login">Login</div>
                <div class="mb-4">
                    <input type="email" autocomplete="off" v-model="email" @keypress="keypressEmail"
                           :class="{ 'is-invalid': email_is_valid }" class="form-control" id="emailSignUp"
                           placeholder="e-mail">
                    <div id="emailSignUpFeedback" class="invalid-feedback">{{ email_is_valid }}</div>
                </div>
                <div class="">
                    <input type="password" @keypress="keypressPassword" :class="{ 'is-invalid': password_is_valid }"
                           v-model="password" class="form-control" placeholder="password">
                    <div class="invalid-feedback">{{ password_is_valid }}</div>
                </div>
                <div @click="forgotPassword" class="mobile_link style_link justify-content-end">forgot password</div>
                <div class="desc_link_form">
                   <div>
                       <router-link class="style_link" :to="{ name: 'Registration' }">I don’t have an account yet.</router-link>
                           </div>
                    <div @click="forgotPassword" class="style_link">Forgot Password</div>
                </div>
            </div>
            <div class="mb-4 box_btn">
                <div class="btn-c2" :class="{ 'not-active-btn':!active_btn}" @click="signIn">Sign in</div>

                <div class="mobile_link justify-content-center pt-28" >
                    <router-link class="style_link" :to="{ name: 'Registration' }">I don’t have an account yet.</router-link>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import {mapMutations} from 'vuex';
import {errorMessage} from "../services/messages";
import {VueTelInput} from 'vue-tel-input';
import RegistrationEnum from "../enums/RegistrationEnum";
import LoginEnum from "../enums/LoginEnum";

export default {
    name: "SignIn",
    components: {
        VueTelInput,
    },
    data() {
        return {
            email: '',
            password: null,
            valid_email: false,
            error_valid: false,
            email_is_valid: false,
            password_is_valid: false,
        };
    },
    mounted() {
    },
    computed: {
        active_btn() {
            return (
                 this.email && this.password
            );
        }
    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
            'setUser',
            'setLogged',
        ]),
        forgotPassword(){
            this.$emit('signIn',
                LoginEnum.FORGOT_PASSWORD,
            )
        },
        keypressEmail() {
            this.email_is_valid = false
        },
        keypressPassword() {
            this.password_is_valid = false
        },

        async signIn() {
        if (this.formValidate()){
            return
        }
            try {
                this.showLoader();
                const response = await this.$http.post(`${this.$http.apiUrl()}login`, {
                    'email': this.email,
                    'password':this.password
                });
                if (response && response.data && response.data.data && response.data.data.token) {
                    const token = response.data.data.token;
                    localStorage.setItem('strobeart_jwt', token);
                    this.setLogged(true);
                    if(response.data.data.user){
                        localStorage.setItem('strobeart_user', JSON.stringify(response.data.data.user))
                        this.setUser(response?.data?.data?.user || {})
                    }
                }
                this.$router.push({to: '/', name: "Home"}).then();
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();
        },
        isValidEmail(email) {
            const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]{1,25}\.)+[a-zA-Z]{2,6}))$/;
            this.valid_email = !re.test(email);
        },
        clearValid() {
            this.valid_email = false;
            this.error_valid = false;
            this.email_is_valid = false;
            this.password_is_valid = false;
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
            if (!this.password) {
                this.password_is_valid = 'This field is required';
                error_form=true
            }
            if (this.password<8){
                this.password_is_valid = 'Password needs to be at least 8 characters';
                error_form=true
            }
            return  error_form;
        },
    }
}
</script>

<style lang="scss" scoped>

.login{
    width: 100%;
    font-family: Montserrat;
    font-style: normal;
    font-weight: 600;
    font-size: 24px;
    line-height: 44px;
    color: #000000;
    text-align: center;
    padding-bottom: 43px;
}

.sign_in {
    // height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.montserrat .form-control{
    padding: 15px 40px;
}
.app_logo {
    display: none;
    padding-top: 80px;
    padding-bottom: 25px;
}

hr {
    color: #494949;
    opacity: unset;
    text-align: center;
    margin: 51px 80px 68px 80px;
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

.item_sign_in {
    display: flex;
    justify-content: center;
    margin-top: 179px;
}

.montserrat {
    width: 550px;
}

.type_user_radio {
    display: block;
}

.type_user_select {
    display: none;
}

#phoneSignUp {
    padding: 8px 7px;
}

#phoneSignUp:focus, .vue-tel-input:focus-within {
    border-color: #cccccc;
    box-shadow: none;
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
.mob_sign_in {
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
    padding-top: 68px;
    padding-bottom: 100px;
}
.mobile_link{
    display: none;
}
@media only screen and (max-width: 992px) {
    .mobile_link{
        display: flex;
        width: 100%;
        padding-top: 12px;
    }
    .login{
        display: none;
    }
    .type_user_radio {
        display: none;
    }
    .type_user_select {
        display: block;
    }
    .item_sign_in {
        margin-top: 0px;
    }
    .montserrat {
        width: 100%;
    }
    .item_sign_in
    {
        display: inherit;
    }
    .form_sign_in {
        padding: 0 45px 0 45px;
    }
    hr {
        margin: 39px 80px 32px 80px;
    }
    .mob_sign_in {
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
    .sign_in{
        height: 100vh;
    }

}
.pt-28{
    padding-top: 28px;
}
</style>
