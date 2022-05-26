<template>
    <MainLayout :is-show-header=!showJobDetailsMob >
        <div class="page_list_channels">
            <div class="items_chat">
                <div class="title_page mb-3">Messages</div>
                <div class="box_search_i">
                    <input @keyup="keypressSearchChat" type="text" class="search_chat_inp"
                           placeholder="">
                    <img class="search_icon" src="@/assets/icons/search.svg" alt="">
                </div>
            <div v-for="chat in chats_list_filter" @click="goChat(chat.job_image_id)" class="channel_item">
                <div class="chat_item">
                    <div class="box_avatar">
                        <img v-if="getAvatarUser(chat)" class="avatar" :src=getAvatarUser(chat) alt="">
                        <img v-else class="avatar" src='@/assets/images/sbcf-default-avatar.png' alt="">
                    </div>
                    <div class="ps-3">
                        <div class=" user_title">
                            {{getUser(chat).first_name}}   {{getUser(chat).last_name}}
                            <div class="job_t">{{styleGuideJob(chat)}}</div>
                        </div>
                    </div>
                </div>
                <div class="date_box">
                    <div>{{getDateCreatedChat(chat.created_at)}}</div>
                </div>
                <div class="icon_a">
                    <img  src='@/assets/icons/ep_arrow-right.svg' alt="">
                </div>
            </div>
            </div>
        </div>
    </MainLayout>
</template>

<script>

import MainLayout from "../layouts/MainLayout";
import {errorMessage} from "../services/messages";
import moment from 'moment-timezone'
import {mapMutations} from "vuex";
const Chat = require('twilio-chat');

export default {
    name: 'ChatsList',
    components: {
        MainLayout,
    },
    data() {
        return {
            user: {},
            chats_list:{},
            chats_list_filter:{}
        }
    },
    mounted() {
        this.user = JSON.parse(localStorage.getItem('strobeart_user')) || {}
        this.chatsList();
    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
        ]),
        getDateCreatedChat(date) {
            return moment(date).format('MM/DD/yyyy')
        },
        styleGuideJob(chat){
            return chat?.job_image?.style_guide||''
        },
        isMobile() {
            return window.innerWidth <= 992;
        },
        goChat(job_id){
            this.$router.push({name: 'Chat',params:{id:job_id}}).then();
        },
        getAvatarUser(chat){
            if (this.user?.type_user=='business'){
                return chat?.user_editor?.avatar?.url||null
            }else {
                return chat?.user_business?.avatar?.url||null
            }
        },
        getUser(chat){
            if (this.user?.type_user=='business'){
                return chat?.user_editor||{}
            }else {
                return chat?.user_business||{}
            }
        },
        keypressSearchChat(event){
            let search_text = (event.target.value).toUpperCase()
            if (search_text && search_text.length > 1) {
                if (this.user?.type_user=='business'){
                    this.chats_list_filter = this.chats_list.filter(chat => (chat.user_editor.name).toUpperCase().includes(search_text));
                }else {
                    this.chats_list_filter = this.chats_list.filter(chat => (chat.user_business.name).toUpperCase().includes(search_text));
                }
            } else {
                this.chats_list_filter = this.chats_list
            }
        },
        async chatsList() {
            this.showLoader()
            try {
                const result = await this.$http.getAuth(`${this.$http.apiUrl()}chats-list`);
                this.chats_list = result?.data?.data
                this.chats_list_filter= result?.data?.data
            } catch (e) {
                console.log(error);
            }
            this.hideLoader()
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
.page_list_channels{
    width: 100%;
    display: flex;
    justify-content: center;
    padding: 30px 15px 120px 15px;
}
.box_avatar .avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    border: 0.3px solid #494949;
    box-sizing: border-box;
    margin-bottom: 6px;
}
.channel_item{
    display: flex;
    justify-content: space-between;
    border-bottom: 0.5px solid #494949;
    padding: 21px 10px;
    cursor: pointer;
}
.items_chat{
    width: 50%;
}
.user_title{
    font-style: normal;
    font-weight: 500;
    font-size: 15px;
    line-height: 18px;
    color: #494949;
}
.icon_a{
    display: flex;
    align-items: center;
}
.date_channel{
    font-style: normal;
    font-weight: 400;
    font-size: 13px;
    line-height: 18px;
    color: #494949;
}
.chat_item{
    display: flex;
    width: 100%;
}
.date_box{
    padding-right: 30px;
    display: flex;
    align-items: end;
    font-style: normal;
    font-weight: 400;
    font-size: 13px;
    line-height: 18px;
    color: #494949;
}
.job_t{
    display: flex;
    padding-top: 5px;
    justify-content: center;
    font-style: normal;
    font-weight: 400;
    font-size: 13px;
    line-height: 18px;
    color: #494949;
}
.box_search_i {
    position: relative;
}
.search_chat_inp {
    background: #FFFFFF;
    border: 0.3px solid #494949;
    box-sizing: border-box;
    border-radius: 100px;
    height: 41px;
    width: 100%;
    padding-left: 45px;
}
.search_icon {
    position: absolute;
    left: 16px;
    top: 14px;
    cursor: pointer;
}
.title_page{
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    line-height: 22px;
    color: #494949;
}
@media only screen and (max-width: 992px) {
    .items_chat{
        width: 100%;
    }
    .page_list_channels {
        padding: 70px 15px 120px 15px;
    }
}
</style>
