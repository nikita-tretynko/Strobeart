<template>
    <div class="item_type_user">
        <div class="type_user">
            <div class="app_logo">
                <img class="" src="@/assets/icons/app_logo.svg" alt="icon-approved.svg"/>
            </div>
            <div class="form_type_user">
                <div class="text_description text-center">Please select account type to finish registration </div>
                <div class="montserrat">
                    <div class="box_input">
                        <div class="mb-4 type_user_select">
                            <select class="form-select" v-model="type_user" aria-label="Default select example">
                                <option selected value="business">Business</option>
                                <option value="editor">Editor</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-4 box_btn">
                <div class="btn-c2" @click="updateUserType">Finish Sign up</div>
            </div>

        </div>
    </div>
</template>

<script>
import {mapMutations} from 'vuex';
import {errorMessage} from "../services/messages";
import RegistrationEnum from "../enums/RegistrationEnum";

export default {
    name: "SetTypeUser",
    components: {
    },
    data() {
        return {
            type_user: 'business',
        };
    },
    mounted() {
    },
    computed: {
    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
            'setUser'
        ]),

        async updateUserType() {
            try {
                this.showLoader();
                const response = await this.$http.postAuth(`${this.$http.apiUrl()}update_type_user`, {
                    'type_user': this.type_user,
                });
                this.setUser(response?.data?.data || {})
                localStorage.setItem('strobeart_user', JSON.stringify(response?.data?.data||{}))
                this.$emit('typeUser',
                    response?.data?.data.type_user||false
                )
                this.hideLoader();
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
        },
    }
}
</script>

<style lang="scss" scoped>
.text_description{
    padding-bottom: 46px;
    font-family: Montserrat;
    font-style: normal;
    font-weight: normal;
    font-size: 17px;
    line-height: 24px;
    color: #000000;
}
.text_title{
    width: 100%;
    font-family: Montserrat;
    font-style: normal;
    font-weight: 600;
    font-size: 24px;
    line-height: 44px;
    color: #000000;
    text-align: center;
    padding-bottom: 14px;
}

.type_user {
    // height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}


.app_logo {
    display: none;
    padding-top: 80px;
    padding-bottom: 25px;
}

.montserrat .form-control{
    padding: 15px 40px;
}
.item_type_user {
    display: flex;
    justify-content: center;
    margin-top: 179px;
}

.montserrat {
    width: 100%;
    display: flex;
    justify-content: center;
}
.box_input{
    width: 550px;
}

.desc_link_form{
    display: flex;
    justify-content: space-between;
    padding-top: 20px;
}
.mob_type_user {
    display: none;
}
.desc_link_form{
    display: flex;
    align-items: center;
}
.style_link{
    font-family: Montserrat;
    font-style: normal;
    font-weight: normal;
    font-size: 14px;
    line-height: 18px;
    color: #000000;
    opacity: 0.65;
    cursor: pointer;
    text-decoration-line: underline;
}
.box_btn{
    display: grid;
    justify-content: center;
    padding-top: 90px;
    padding-bottom: 100px;
}
.link{
    display: flex;
    width: 100%;
    padding-top: 27px;
}
@media only screen and (max-width: 992px) {
    .item_type_user {
        margin-top: 0px;
        display: inherit;
    }
    .montserrat {
        width: 100%;
    }
    .form_type_user {
        padding: 0 45px 0 45px;
    }
    .mob_type_user {
        display: flex;
        justify-content: center;
    }
    .app_logo{
        display: block;
    }
    .desc_link_form{
        display: none;
    }
    .box_btn{
        padding-top: 10px;
        padding-bottom: 40px;
    }
    .type_user{
        height: 100vh;
    }

}

</style>
