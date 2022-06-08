<template>
    <div>
    <div class="header">
        <nav class="navbar navbar-expand-lg navbar-light ">
            <div class="container-fluid">
                <router-link class="navbar-brand logo" :to="{ name: 'Login' }">
                    <img class="logo_project" src="@/assets/icons/ST-logo_1.svg" alt="strobeart"/>
                </router-link>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false"
                        aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
                    <ul v-if="!this.isLogged" class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item pr-51">
                            <router-link class="nav-link" :to="{ name: 'Login' }">How it works</router-link>
                        </li>
                        <li class="nav-item pr-50">
                            <a class="nav-link" href="#">Prices</a>
                        </li>
                        <li class="nav-item pr-44">
                            <a class="nav-link" href="#">About</a>
                        </li>
                        <li class="nav-item">
                            <router-link active-class="active" class="nav-link" :to="{ name: 'Login' }">Sign In
                            </router-link>
                        </li>
                    </ul>
                    <ul v-if="this.isLogged" class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item pr-50">
                            <div class="nav-link disable_link">
                                <img class="mr-11" src="@/assets/icons/ph_sun-horizon-thin.svg" alt="icon-approved.svg"/>
                                Good Morning {{firstName}}!
                            </div>
                        </li>
                        <li class="nav-item pr-22">
                            <router-link class="nav-link" :to="{ name: 'Login' }">
                                <img class="" src="@/assets/icons/home_icon.svg" alt="icon-approved.svg"/>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ name: 'Profile' }">
                                <img v-if="avatar" class="avatar" :src=avatar alt="">
                                <img v-else class="avatar" src='@/assets/images/sbcf-default-avatar.png' alt="">
                            </router-link>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    <div v-if="this.isLogged&&isTypeUser" class="header_mobile">
        <div class="mr-27">
            <img v-if="avatar" class="avatar" :src=avatar alt="">
            <img v-else class="avatar" src='@/assets/images/sbcf-default-avatar.png' alt="">
        </div>
        <div class="header_inf">
            <div class="text_name">
                Good Morning {{firstName}}!
                <img class="ms-2" src="@/assets/icons/ph_sun-horizon-thin.svg" alt="icon-approved.svg"/>
            </div>
        </div>

    </div>
    </div>
</template>

<script>
import {mapGetters, mapMutations} from "vuex";

export default {
    name: "Header",
    data() {
        return {
            user: {},
        }
    },
    mounted() {
       this.user = JSON.parse(localStorage.getItem('strobeart_user'))
    },
    methods: {
        ...mapMutations([
            'setUpdateUserProfile'
        ]),
    },
    computed: {
        ...mapGetters([
            'isLogged',
            'getUser',
            'isUpdatedUserProfile'
        ]),
        firstName(){
            return this.user?.first_name||''
        },
        avatar(){
           return  this.user?.avatar?.url||null;
        },
        isTypeUser(){
            return  this.user?.type_user||null;
        }

    },
    watch: {
        isUpdatedUserProfile(newValue) {
            this.user = JSON.parse(localStorage.getItem('strobeart_user'))
            console.log(this.user)
            this.setUpdateUserProfile(false)
        }
    }
}
</script>

<style lang="scss" scoped>

.navbar-brand {
    padding: 0;
}

.navbar {
    padding: 0;
}

.header {
    padding: 18px 96px;
    position: fixed;
    width: 100%;
    z-index: 999;
    background: white;
}

@media only screen and (max-width: 992px) {
    .header {
        padding: 15px 15px;
    }
}

.navbar-brand.logo {
    font-family: Montserrat;
    font-style: normal;
    font-weight: 600;
    font-size: 24px;
    line-height: 44px;
    color: #000000;
}

.navbar-expand-lg .navbar-nav .nav-link {
    padding: 0;
}

.navbar-light .navbar-nav .nav-link {
    color: #494949;
}

.nav-item .nav-link {
    font-family: Montserrat;
    font-style: normal;
    font-weight: 500;
    font-size: 18px;
    line-height: 22px;
    color: #494949;
}

.navbar-nav .nav-item .nav-link:hover,
.navbar-light .navbar-nav .nav-link.active {
    color: #D8C3AF;
}
.pr-22{
    padding-right: 22.5px;
}
.mr-11{
    margin-right: 11px;
}
.mr-27{
    margin-right: 27px;
}
.pr-50 {
    padding-right: 50px;
}
.pr-44{
    padding-right: 44px;
}
.pr-51 {
    padding-right: 51px;
}
.navbar-nav{
    display: flex;
    align-items: center;
}
.avatar{
    border-radius: 50%;
    width: 45px;
    height: 45px;
    object-fit: cover;
}
.navbar-nav .nav-item .nav-link.disable_link:hover{
    color:#494949;
}
.navbar-nav .nav-item .nav-link.disable_link,text_name{
    font-family: Montserrat;
    font-style: normal;
    font-weight: 500;
    font-size: 16px;
    line-height: 20px;
    display: flex;
    align-items: center;
    text-transform: capitalize;
    color: #494949;
}
.header_mobile{
    display: none;
}
.header_inf{
    position: relative;
    top: 7px;
}
.logo_project{

}
@media only screen and (max-width: 992px) {
    .header {
        display: none;
    }
    .header_mobile{
        display: flex;
        padding: 50px 20px 20px 20px;
        justify-content: center;
    }
}
</style>
