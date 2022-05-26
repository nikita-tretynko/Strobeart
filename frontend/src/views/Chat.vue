<template>
    <MainLayout :is-show-header=!showJobDetailsMob >
          <twilio-chat></twilio-chat>
        </MainLayout>
</template>

<script>

import MainLayout from "../layouts/MainLayout";
import {errorMessage} from "../services/messages";
import {mapMutations} from "vuex";
import TwilioChat from "../components/TwilioChat";

export default {
    name: 'Chat',
    components: {
        MainLayout,
        TwilioChat
    },
    data() {
        return {
        }
    },
    mounted() {
    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
        ]),
        isMobile() {
            return window.innerWidth <= 992;
        },
    },
    computed: {
        showJobDetailsMob() {
            return this.isMobile()
        },
    }
}
</script>
<style lang="scss" scoped>
</style>
