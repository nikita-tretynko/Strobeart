<template>
        <div>
<!--            <SetTypeUser @typeUser="typeUser"  v-if="!is_type_user"></SetTypeUser>-->
            <HomePage v-if="is_type_user && this.user.type_user === this.editor"></HomePage>
            <BusinessHomePage v-if="is_type_user && this.user.type_user === this.business"/>
        </div>
</template>

<script>

import HomePage from "../components/HomePage";
import BusinessHomePage from "@/views/BusinessHomePage";
import MainLayout from "../layouts/MainLayout";
import SetTypeUser from "../components/SetTypeUser";
import {errorMessage} from "../services/messages";
import {mapMutations} from "vuex";
import TypeUserEnum from "@/enums/TypeUserEnum";

export default {
    name: 'Home',
    components: {
        MainLayout,
        SetTypeUser,
        HomePage,
        BusinessHomePage,
    },
    data() {
        return {
            user:{},
            is_type_user:true,
            business: TypeUserEnum.BUSINESS,
            editor: TypeUserEnum.EDITOR,
        }
    },
    mounted() {
        this.getUser()
    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
            'setUser',
        ]),
        typeUser(data) {
            this.is_type_user = data;
        },
        async getUser() {
            try {
                this.showLoader();
                const response = await this.$http.getAuth(`${this.$http.apiUrl()}user`);
                this.user = response?.data?.data|| {}
                this.is_type_user = response?.data?.data.type_user||false
                this.setUser(response?.data?.data || {})
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();
        },
    },
    computed: {}
}
</script>
<style lang="scss" scoped>
</style>
