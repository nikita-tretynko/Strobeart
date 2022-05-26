<template>
    <MainLayout>
        <div>
            <SignUp1 @typeUser="typeUser" @signUp1Next='onActive' v-if="active===enums.SIGN_UP1"></SignUp1>
            <SetTypeUser @typeUser="typeUser" v-if="!is_type_user&&active===enums.ONBOARDING_1"></SetTypeUser>
            <template v-if="is_type_user">
                <Onboarding1 @Onboarding1Next="onActive" v-if="active===enums.ONBOARDING_1"></Onboarding1>
                <Onboarding2 @Onboarding2Next="onActive" v-if="active===enums.ONBOARDING_2"></Onboarding2>
                <Onboarding3 v-if="active===enums.ONBOARDING_3"></Onboarding3>
            </template>
        </div>
    </MainLayout>
</template>

<script>

import RegistrationEnum from "../enums/RegistrationEnum";
import SignUp1 from "../components/SignUp1";
import SignUp2 from "../components/SignUp2";
import Onboarding1 from "../components/Onboarding1";
import Onboarding2 from "../components/Onboarding2";
import Onboarding3 from "../components/Onboarding3";
import MainLayout from "../layouts/MainLayout";
import TypeUserEnum from "@/enums/TypeUserEnum";
import SetTypeUser from "../components/SetTypeUser";
import {mapGetters} from "vuex";

export default {
    name: 'Home',
    components: {
        MainLayout,
        SignUp1,
        SignUp2,
        SetTypeUser,
        Onboarding1,
        Onboarding2,
        Onboarding3
    },
    data() {
        return {
            active: RegistrationEnum.SIGN_UP1,
            is_type_user: false,
        }
    },
    mounted() {
        this.isRegistrationSocial();
    },
    methods: {
        typeUser(data) {
            this.is_type_user = data;
        },
        onActive(data) {
            this.active = data;
        },
        isRegistrationSocial() {
            if (localStorage.getItem('strobeart_registration')) {
                this.active = RegistrationEnum.ONBOARDING_1
                localStorage.removeItem('strobeart_registration');
            }
        }
    },
    computed: {
        ...mapGetters([
            'getUser',
        ]),
        enums() {
            return {
                [RegistrationEnum.SIGN_UP2]: RegistrationEnum.SIGN_UP2,
                [RegistrationEnum.SIGN_UP1]: RegistrationEnum.SIGN_UP1,
                [RegistrationEnum.ONBOARDING_1]: RegistrationEnum.ONBOARDING_1,
                [RegistrationEnum.ONBOARDING_2]: RegistrationEnum.ONBOARDING_2,
                [RegistrationEnum.ONBOARDING_3]: RegistrationEnum.ONBOARDING_3
            }
        },
    }
}
</script>
<style lang="scss" scoped>

</style>
