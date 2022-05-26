<template>
    <div class="item_sign_up1">
        <div class="sign_up1">
            <div class="app_logo">
                <img class="" src="@/assets/icons/app_logo.svg" alt="icon-approved.svg"/>
            </div>
            <div class="montserrat form_sign_in">
                <div class="mb-4">
                    <input type="text" v-model="first_name" :class="{ 'is-invalid': first_name_is_valid&&!first_name }"
                           class="form-control" id="firstNameSignUp" placeholder="first name">
                    <div id="firstNameSignUpFeedback" class="invalid-feedback">{{ first_name_is_valid }}</div>
                </div>
                <div class="mb-4">
                    <input type="text" v-model="last_name" :class="{ 'is-invalid': last_name_is_valid&&!last_name  }"
                           class="form-control" id="lastNameSignUp" placeholder="last name">
                    <div id="lastNameSignUpFeedback" class="invalid-feedback">{{ last_name_is_valid }}</div>
                </div>
                <div class="mb-4">
                    <input type="email" v-model="email" @keypress="keypressEmail"
                           :class="{ 'is-invalid': email_is_valid }" class="form-control" id="emailSignUp"
                           placeholder="e-mail">
                    <div id="emailSignUpFeedback" class="invalid-feedback">{{ email_is_valid }}</div>
                </div>
                <div class="mb-4">
                    <vue-tel-input :class="{ 'is-invalid': phone_is_valid }" class="form-control" id="phoneSignUp"
                                   placeholder="phone number" v-model="phone" v-bind="props_tel"></vue-tel-input>
                    <!--                <input type="number" v-model="phone">-->
                    <div id="phoneSignUpFeedback" class="invalid-feedback">{{ phone_is_valid }}</div>

                </div>
                <div class="mb-4">
                    <input type="password" @keypress="keypressPassword" :class="{ 'is-invalid': password_is_valid }"
                           v-model="password" class="form-control" placeholder="password">
                    <div class="invalid-feedback">{{ password_is_valid }}</div>
                </div>

                <div class="mb-4">
                    <input type="password" @keypress="keypressConfirmPassword"
                           :class="{ 'is-invalid': confirm_password_is_valid }" v-model="confirm_password"
                           class="form-control" placeholder="confirm password">
                    <div class="invalid-feedback">{{ confirm_password_is_valid }}</div>
                </div>
                <div class="mb-4 radio_btn type_user_radio ms-5">
                    <div class="me-4">I am</div>
                    <div class="form-check me-3">
                        <input class="form-check-input" v-model="type_user" value="business" type="radio"
                               name="flexRadioDefault" id="flexRadioDefault1">
                        <label class="form-check-label" for="flexRadioDefault1">
                            a business owner
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" v-model="type_user" value="editor" type="radio"
                               name="flexRadioDefault" id="flexRadioDefault2"
                               checked>
                        <label class="form-check-label" for="flexRadioDefault2">
                            an editor
                        </label>
                    </div>
                    <div v-if="error_valid" class="error_valid">{{ error_valid }}</div>
                </div>

                <div class="mb-4 type_user_select">
                    <select class="form-select" v-model="type_user" aria-label="Default select example">
                        <option selected value="business">Business</option>
                        <option value="editor">Editor</option>
                    </select>
                </div>
                <hr>
                <div class="mb-4 btn-c1 d-flex align-items-center" @click="loginWithProvider('google')">
                    <div class="me-4">
                        <img src="@/assets/icons/google_logo.svg" alt="icon-approved.svg"/>
                    </div>
                    <div class="text">continue with google</div>
                </div>
                <div class="mb-4 btn-c1 d-flex align-items-center" @click="loginWithProvider('apple')">
                    <div class="me-4">
                        <img src="@/assets/icons/apple_logo_black.svg" alt="icon-approved.svg"/>
                    </div>
                    <div class="text">continue with apple</div>
                </div>
                <div class="d-flex justify-content-center mt-5 mb-4">
                    <div class="btn-c2" :class="{ 'not-active-btn':!active_btn}" @click="signUp">Sign up</div>
                </div>
                <div class="mob_sign_in mb-4">
                    <div>
                        <div class="w-214 d-flex justify-content-center">
                            <router-link :to="{ name: 'Login' }" class="link-s1">Already have an
                                account?
                                Sign in
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import RegistrationEnum from "../enums/RegistrationEnum";
import {mapMutations} from 'vuex';
import {errorMessage} from "../services/messages";
import {VueTelInput} from 'vue-tel-input';

export default {
    name: "SignUp1",
    components: {
        VueTelInput,
    },
    data() {
        return {
            first_name: '',
            last_name: '',
            email: '',
            phone: '',
            type_user: 'business',
            password: null,
            confirm_password: null,
            valid_email: false,
            error_valid: false,
            first_name_is_valid: false,
            last_name_is_valid: false,
            email_is_valid: false,
            phone_is_valid: false,
            password_is_valid: false,
            confirm_password_is_valid: false,
            props_tel: {
                preferredCountries: ["US", "GB"],
                placeholder: "Enter your phone",
                mode: "international",
                inputOptions: {
                    showDialCode: true
                },
                disabledFormatting: false,
                wrapperClasses: "country-phone-input"
            }
        };
    },
    mounted() {
    },
    computed: {
        active_btn() {
            return (
                this.first_name && this.last_name && this.email && this.phone && this.password && this.confirm_password
            );
        }
    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
            'setUser',
            'setPhone',
            'setLogged',
        ]),
        keypressEmail() {
            this.email_is_valid = false
        },
        keypressConfirmPassword() {
            this.confirm_password_is_valid = false
        },
        keypressPassword() {
            this.password_is_valid = false
        },

        async loginWithProvider(provider) {
            try {
                this.showLoader();
                const response = await this.$http.get(`${this.$http.apiUrl()}auth/`+provider);
                window.location.href = response?.data?.data
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();
        },

        async signUp() {
            if (this.formValidate()) {
                return
            }
            try {
                this.showLoader();
                const response = await this.$http.post(`${this.$http.apiUrl()}registration`, {
                    'first_name': this.first_name,
                    'last_name': this.last_name,
                    'phone': this.phone.split(' ').join('').trim(),
                    'email': this.email,
                    'type_user': this.type_user,
                    'password': this.password
                });
                if (response && response.data && response.data.data && response.data.data.token) {
                    const token = response.data.data.token;
                    localStorage.setItem('strobeart_jwt', token);
                    this.setLogged(true);
                    if (response.data.data.user) {
                        localStorage.setItem('strobeart_user', JSON.stringify(response.data.data.user))
                        this.setUser(response?.data?.data?.user || {})
                    }
                    this.$emit('typeUser',
                        response?.data?.data?.user?.type_user||false
                    )
                }
                this.$emit('signUp1Next',
                    RegistrationEnum.ONBOARDING_1,
                )
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();
        },
        isValidEmail(email) {
            const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]{1,20}\.)+[a-zA-Z]{2,6}))$/;
            this.valid_email = !re.test(email);
        },
        isValidPassword(password) {
            const re =  /^(?=.*[a-z])(?=.*[A-Z])/;
            return !re.test(password);
        },
        clearValid() {
            this.valid_email = false;
            this.error_valid = false;
            this.first_name_is_valid = false;
            this.last_name_is_valid = false;
            this.email_is_valid = false;
            this.phone_is_valid = false;
            this.password_is_valid = false;
            this.confirm_password_is_valid = false;
        },
        formValidate() {
            let error_form = false
            this.clearValid();
            this.isValidEmail(this.email)
            if (!this.first_name) {
                this.first_name_is_valid = 'This field is required';
                error_form = true
            }
            if (!this.last_name) {
                this.last_name_is_valid = 'This field is required';
                error_form = true
            }
            if (!this.email) {
                this.email_is_valid = 'This field is required';
                error_form = true
            }
            if (this.email && this.valid_email) {
                this.email_is_valid = 'Incorrect email format. Please try again'
                error_form = true
            }
            if (this.phone.split('+').join('').trim().length < 10) {
                this.phone_is_valid = 'Phone number should contain at least 10 digits';
                error_form = true
            }
            if (this.phone.split(' ').join('').trim().length > 13) {
                this.phone_is_valid = 'Phone number can contain 12 digits maximum.'
                error_form = true
            }
            if (!this.password) {
                this.password_is_valid = 'This field is required';
                error_form = true
            }
            if (!this.confirm_password) {
                this.confirm_password_is_valid = 'This field is required';
                error_form = true
            }
            if (this.isValidPassword(this.password)){
                this.password_is_valid = 'The password must be uppercase and lowercase';
                error_form = true
            }
            if (this.password.length < 8) {
                this.password_is_valid = 'Password needs to be at least 8 characters';
                error_form = true
            }
            if (this.isValidPassword(this.password)&&this.password.length < 8) {
                this.password_is_valid = 'The password must be uppercase and lowercase. Password needs to be at least 8 characters';
                error_form = true
            }

            if (this.password && this.confirm_password && this.password !== this.confirm_password) {
                this.confirm_password_is_valid = 'Please make sure your passwords match';
                error_form = true
            }
            if (!this.type_user) {
                this.error_valid = 'Error'
                error_form = true
            }
            return error_form;
        },
    }
}
</script>
<style src="vue-tel-input/dist/vue-tel-input.css"></style>

<style lang="scss" scoped>
.w-214 {
    width: 214px;
}

.sign_up1 {
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

.item_sign_up1 {
    display: flex;
    justify-content: center;
    margin-top: 192px;
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

.radio_btn {
    font-family: Montserrat;
    font-style: normal;
    font-weight: normal;
    font-size: 16px;
    line-height: 20px;
    color: #494949;
    display: flex;
}

.mob_sign_in {
    display: none;
}
.form_sign_in{
    margin-bottom: 55px;
}
@media only screen and (max-width: 992px) {
    .type_user_radio {
        display: none;
    }
    .type_user_select {
        display: block;
    }
    .item_sign_up1 {
        margin-top: 0px;
    }
    .montserrat {
        width: 100%;
    }
    .item_sign_up1 {
        display: inherit;
    }
    .form_sign_in {
        padding: 0 45px 0 45px;
        margin-bottom: 5px;
    }
    hr {
        margin: 39px 80px 32px 80px;
    }
    .mob_sign_in {
        display: flex;
        justify-content: center;
    }
    .app_logo {
        display: block;
    }
}

</style>
