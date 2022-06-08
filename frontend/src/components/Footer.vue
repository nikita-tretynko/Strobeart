<template>
    <div>
        <div class="footer">
            <div class="item">
                <div>
                    <div class="email_list">JOIN OUR EMAIL LIST</div>
                    <div class="form_join_email_list">
                        <input type="text" class="input_s form-control" placeholder="your email">
                        <button class="btn-c3">Sign Up</button>
                    </div>
                </div>
                <div>
                    <ul class="list_link">
                        <li>
                            <router-link :to="{ name: 'Home' }">Home</router-link>
                        </li>
                        <li>
                            <router-link :to="{ name: 'Home' }">How it Works</router-link>
                        </li>
                        <li>
                            <router-link :to="{ name: 'Home' }">Prices</router-link>
                        </li>
                        <li>
                            <router-link :to="{ name: 'Home' }">About</router-link>
                        </li>
                    </ul>
                </div>
                <div>
                    <router-link class="footer_sign_in" :to="{ name: 'Login' }">Sign In</router-link>
                    <div class="icons_social">
                        <img class="mr-16" src="@/assets/icons/instagram.png" alt="icon-approved.svg"/>
                        <img class="mr-16" src="@/assets/icons/facebook.png" alt="icon-approved.svg"/>
                        <img class="" src="@/assets/icons/twitter.png" alt="icon-approved.svg"/>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="this.isLogged&&isTypeUser" class="footer_mobile">
            <router-link :to="{ name: 'ChatsList' }">
                <img v-if="$route.name==='ChatsList'" class="mr-16" src="@/assets/icons/mail-mob_1.svg" alt="icon-approved.svg"/>
                <img v-else class="mr-16" src="@/assets/icons/mail-m.svg" alt="icon-approved.svg"/>
            </router-link>
            <router-link :to="{ name: 'PastJobs' }">
<!--                <img v-if="isTypeUser==='business'" class="mr-16" src="@/assets/icons/ic_round-add-a-photo.svg"-->
<!--                     alt="icon-approved.svg"/>-->
                <img v-if="$route.name==='PastJobs'" class="mr-16" src="@/assets/icons/list-filled.svg" alt="icon-approved.svg"/>
                <img v-else class="mr-16" src="@/assets/icons/list-m.svg" alt="icon-approved.svg"/>

            </router-link>
            <router-link :to="{ name: 'Home' }">
                <img v-if="$route.name==='Home'" class="mr-16" src="@/assets/icons/home-m.svg" alt="icon-approved.svg"/>
                <img v-else class="mr-16" src="@/assets/icons/home0.svg" alt="icon-approved.svg"/>
            </router-link>
            <router-link :to="{ name: 'Profile' }" active-link="active">
                <img v-if="$route.name==='Profile'" class="mr-16" src="@/assets/icons/Ppofile1.svg" alt="icon-approved.svg"/>
                <img v-else class="mr-16" src="@/assets/icons/user-m.svg" alt="icon-approved.svg"/>
            </router-link>
        </div>
    </div>
</template>

<script>
import MainLayout from "../layouts/MainLayout";
import {mapGetters, mapMutations} from "vuex";

export default {
    name: "Footer",
    components: {MainLayout},
    data() {
        return {
            user: {},
        }
    },
    mounted() {
        this.user = JSON.parse(localStorage.getItem('strobeart_user'))
    },
    computed: {
        ...mapGetters([
            'isLogged',
        ]),
        isTypeUser() {
            return this.user?.type_user || null;
        }
    },
}
</script>

<style lang="scss" scoped>
.footer .item {
    display: flex;
    justify-content: space-between;
}

.email_list {
    font-family: Montserrat;
    font-style: normal;
    font-weight: 500;
    font-size: 16px;
    line-height: 20px;
    display: flex;
    align-items: center;
    color: #AD967F;
    padding-bottom: 32px;
    padding-left: 20px;
}

.form_join_email_list {
    display: flex;
    gap: 27px;
}

.input_s {
    width: 443px;
    height: 46px;
    padding-left: 28px;
    background: #FFFFFF;
    border: 0.5px solid #D8C3AF;
    box-sizing: border-box;
    box-shadow: 0px 1px 4px rgba(216, 195, 175, 0.15);
    border-radius: 100px 0px 0px 100px;
}

input::placeholder {
    color: rgba(73, 73, 73, 0.8);
    font-family: Montserrat;
    font-style: normal;
    font-weight: normal;
    font-size: 17px;
    line-height: 24px;
}

.list_link li {
    list-style-type: none;
}

.list_link li a {
    font-family: Montserrat;
    font-style: normal;
    font-weight: 500;
    font-size: 18px;
    line-height: 29px;
    display: flex;
    text-decoration: none;
    align-items: center;
    color: #AD967F;
}

.mr-16 {
    margin-right: 16px;
}

.icons_social {
    padding-top: 27px;
}

.icons_social img {
    cursor: pointer;
}

.icons_social img:hover, .footer_sign_in:hover, .list_link li:hover {
    opacity: 0.5;
}

.footer_sign_in {
    font-family: Montserrat;
    font-style: normal;
    font-weight: 500;
    font-size: 18px;
    line-height: 22px;
    color: #AD967F;
    text-decoration: none;
}

.footer_mobile {
    display: none;
}

@media only screen and (max-width: 992px) {
    .footer {
        display: none;
    }
    .footer_mobile {
        position: fixed;
        bottom: 0px;
        padding: 26px 37px;
        left: 0px;
        right: 0px;
        margin-bottom: 0px;
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(25px);
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 9999;
    }
}

</style>
