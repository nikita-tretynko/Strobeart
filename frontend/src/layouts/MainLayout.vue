<template>
    <div class="app-main-layout">
        <Header v-if="isShowHeader"/>
        <main class="app-content">
            <div class="app-page" :class="{vh_header:vh_header}">
                <slot></slot>
            </div>
        </main>
        <Footer/>
    </div>
</template>
<script>

import Header from "../components/Header";
import Footer from "../components/Footer";

export default {
    props: {
        isShowHeader: {
            default: true,
        },
        vh_header:null,
    },
    name: 'main-layout',
    data: () => ({
        isOpen: true
    }),
    components: {
        Header, Footer
    },
    mounted() {
    }
}
</script>
<style lang="scss" scoped>

body {
    display: flex;
    font-style: normal;
    flex-direction: column;
}

.app-main-layout {
    display: flex;
    flex-direction: column;
    width: 100%;
    max-width: 100%;
    min-height: 100vh;
}

main {
    flex-grow: 1;
}

footer {
    flex-grow: 0;
}
.app-page {
    min-height: calc(100vh - 472px);
    margin-top: 107px;
}

@media screen and (max-width: 992px) {
    .vh_header.app-page {
        min-height: calc(100vh - 205px);
    }
    .app-page {
        margin-top: 0px;
    }
}
</style>
