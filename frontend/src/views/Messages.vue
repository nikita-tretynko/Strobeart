<template>
    <div>
        <MainLayout :vh_header="true" :is-show-header=false>
           Messages
        </MainLayout>
    </div>
</template>

<script>

import MainLayout from "../layouts/MainLayout";
import {errorMessage} from "../services/messages";
import {mapMutations} from "vuex";
import TypeUserEnum from "@/enums/TypeUserEnum";

export default {
    name: 'Messages',
    components: {
        MainLayout,
    },
    data() {
        return {
            user: {},
            is_type_user: true,
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
                this.user = response?.data?.data || {}
                this.is_type_user = response?.data?.data.type_user || false
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
@media only screen and (max-width: 992px) {
    .app-page.stp_1 {
        min-height: calc(100vh - 205px);
    }

}
</style>
