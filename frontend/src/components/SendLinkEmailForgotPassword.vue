<template>
    <div class="item_forgot_password">
        <div class="forgot_password">
            <div class="app_logo">
                <img class="" src="@/assets/icons/app_logo.svg" alt="icon-approved.svg"/>
            </div>
            <div class="form_forgot_password">
                <div class="text_title">Forgot Password</div>
                <div class="text_description">Further instructions will be sent to e-mail {{email}}</div>
            </div>
            <div class="mb-4 box_btn">
                <div class="btn-c2" @click="homePage"> Back to Sign in</div>
            </div>
        </div>
    </div>
</template>

<script>
import {mapMutations} from 'vuex';
import LoginEnum from "../enums/LoginEnum";

export default {
    name: "SendLinkEmailForgotPassword",
    props: ['email'],
    components: {
    },
    data() {
        return {

        };
    },
    mounted() {
    },
    computed: {
    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
        ]),
         homePage() {
             this.$emit('signIn',
                 LoginEnum.LOGIN,
             )
        },
    }
}
</script>

<style lang="scss" scoped>
.item_forgot_password{
    padding-top: 170px;
}
.text_description{
    padding-bottom: 46px;
    font-family: Montserrat;
    font-style: normal;
    font-weight: normal;
    font-size: 17px;
    line-height: 24px;
    color: #000000;
    text-align: center;
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
.app_logo {
    display: none;
    padding-top: 80px;
    padding-bottom: 25px;
}
.montserrat .form-control{
    padding: 15px 40px;
}
.montserrat {
    width: 100%;
    display: flex;
    justify-content: center;
}
.box_input{
    width: 550px;
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
    .montserrat {
        width: 100%;
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
        display: flex;
        justify-content: space-between;
        height: 100vh;
        flex-direction: column;
        padding: 0 20px;
    }
    .item_forgot_password{
        padding-top: 10px;
    }
}

</style>
