<template>
    <UploadFilesForm></UploadFilesForm>
</template>

<script>

import UploadFilesForm from "../components/UploadFilesForm";
import MainLayout from "../layouts/MainLayout";
import {errorMessage} from "../services/messages";
import {mapMutations} from "vuex";

export default {
    name: 'UploadFiles',
    components: {
        MainLayout,
        UploadFilesForm
    },
    data() {
        return {
            active:1,
        }
    },
    mounted() {
    },

    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
        ]),
        onActive(data) {
            this.active = data;
        },
    },
    computed: {

    }
}
</script>
<style lang="scss" scoped>

@media only screen and (max-width: 992px) {

}
</style>
