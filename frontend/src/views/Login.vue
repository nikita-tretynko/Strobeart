<template>
    <MainLayout>
        <div>
            <SignIn @signIn='onActive' v-if="active===enums.LOGIN"></SignIn>
            <ForgotPassword  @forgotPassword='onActive' @sendEmail='emailForgotPassword' v-if="active===enums.FORGOT_PASSWORD"></ForgotPassword>
            <SendLinkEmailForgotPassword @signIn='onActive' :email="email" v-if="active===enums.SEND_EMAIL_FORGOT_PASSWORD"></SendLinkEmailForgotPassword>
        </div>
    </MainLayout>
</template>

<script>

import SignIn from "../components/SignIn";
import ForgotPassword from "../components/ForgotPassword";
import MainLayout from "../layouts/MainLayout";
import LoginEnum from "../enums/LoginEnum";
import SendLinkEmailForgotPassword from "../components/SendLinkEmailForgotPassword";

export default {
    name: 'Login',
    components: {
        MainLayout,
        SignIn,
        ForgotPassword,
        SendLinkEmailForgotPassword,
    },
    data() {
        return {
            active: LoginEnum.LOGIN,
            email:null,
        }
    },

    mounted() {
       this.isLogin()
    },
    methods: {
        isLogin(){
           if (localStorage.getItem('strobeart_jwt')&&localStorage.getItem('strobeart_user')){
               this.$router.push({to: '/', name: "Home"}).then();
           }
        },
        onActive(data) {
            this.active = data;
        },
        emailForgotPassword(data) {
            this.email = data;
        }
    },
    computed: {
enums(){
    return {
        [LoginEnum.LOGIN]: LoginEnum.LOGIN,
        [LoginEnum.FORGOT_PASSWORD]: LoginEnum.FORGOT_PASSWORD,
        [LoginEnum.SEND_EMAIL_FORGOT_PASSWORD]:LoginEnum.SEND_EMAIL_FORGOT_PASSWORD
    }
}
    }
}
</script>
<style lang="scss" scoped>

</style>
