<template>
    <MainLayout :vh_header="true" :is-show-header=!showJobDetailsMob>
        <div class="container_page">
            <div class="title_page">Upload File/s</div>
            <div class="select_socialite">
                <div class="title_2 pr-50">Upload to</div>
                <div>
                    <select
                        class="form-select"
                        v-model="platform"
                        @change="selectPlatform(platform)"
                        id="platforms_items">
                        <option selected disabled hidden value="">Choose a platform...</option>
                        <option v-for="(item, index) in platforms_items" :key="index" :value="item">{{ item }}</option>
                    </select>
                    <div id="fileFormatFeedback" class="invalid-feedback">
                        Please choose a file format.
                    </div>
                </div>
            </div>
            <Instagram v-if="platform==='Instagram'"></Instagram>
            <Shopify v-if="platform==='Shopify'"></Shopify>
        </div>
    </MainLayout>
</template>

<script>

import MainLayout from "../layouts/MainLayout";
import {errorMessage} from "../services/messages";
import {mapMutations} from "vuex";
import Instagram from "./FileSchedule/Instagram";
import Shopify from "./FileSchedule/Shopify";

export default {
    name: 'UploadFilesForm',
    components: {
        MainLayout,
        Instagram,
        Shopify
    },
    data() {
        return {
            platform: 'Instagram',
            platforms_items: [
                'Instagram',
                'Facebook',
                'Shopify',
                'Amazon',
                'WordPress'
            ],
        }
    },
    mounted() {
        this.platform = this.$route.query.platform ?? 'Instagram'
    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
        ]),
        selectPlatform(platform) {
            this.$router.push({query: {platform: platform}})
        },
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
.pr-50 {
    padding-right: 50px;
}

.container_page {
    padding: 70px 12px;
}

.title_page {
    font-style: normal;
    font-weight: 600;
    font-size: 35px;
    line-height: 44px;
    color: #494949;
}

.form-select {
    border-radius: 19px;
    color: rgba(74, 74, 74, 0.6);
    border-color: rgba(74, 74, 74, 0.6);
    overflow: hidden;
    text-overflow: ellipsis;
    width: 348px;
}

.form-select:focus {
    border-color: rgba(74, 74, 74, 0.6);
}

.select_socialite {
    padding-top: 70px;
    width: 100%;
    display: flex;
    justify-content: end;
    align-items: center;
}

.title_2 {
    font-style: normal;
    font-weight: 600;
    font-size: 20px;
    line-height: 24px;
    color: #494949;
}

.title_s {
    font-style: normal;
    font-weight: 600;
    font-size: 24px;
    line-height: 44px;
    color: #494949;
}


@media only screen and (max-width: 992px) {
    .w_text {
        width: 100%;
    }
    .title_page {
        font-size: 18px;
        line-height: 22px;
    }
    .form-select {
        width: 200px;
    }
    .title_2 {
        font-weight: 400;
        font-size: 13px;
        line-height: 18px;
    }
    .select_socialite {
        justify-content: center;
    }
}
</style>
