<template>
    <div v-if="show_count">
        <div class="count_message_m" :class={red_count:count_unconsumed_messages}>{{count_unconsumed_messages}}</div>
    </div>
</template>

<script>
import {mapGetters, mapMutations} from "vuex";

const Chat = require('twilio-chat');

export default {
    name: "ChatMessageCount",
    components: {},
    data() {
        return {
            show_count:false,
            user: {},
            chat_client: null,
            channel: null,
            chats_list:{},
            count_unconsumed_messages:0,
        };
    },
    async mounted() {
        this.user = JSON.parse(localStorage.getItem('strobeart_user')) || {}
        this.connectClientWithUsername();
       await this.chatsList()
    },
    computed: {
        ...mapGetters([
            'getUser'
        ]),
    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
        ]),
        connectClientWithUsername() {
            if (this.user?.id) {
                this.fetchAccessToken(this.user?.id, this.connectMessagingClient);
            } else {
                console.log('Error User')
            }
        },
        async chatsList() {
            this.showLoader()
            try {
                const result = await this.$http.getAuth(`${this.$http.apiUrl()}chats-list`);
                this.chats_list = result?.data?.data
            } catch (e) {
                console.log(error);
            }
            this.hideLoader()
        },

        async fetchAccessToken(username, handler) {
            this.showLoader()
            try {
                const result = await this.$http.postAuth(`${this.$http.apiUrl()}access_token`, {
                    identity: this.user?.id,
                });
                this.hideLoader()
                handler(result?.data?.data);
            } catch (e) {
                console.log(error);
            }
            this.hideLoader()
        },
        async connectMessagingClient(token) {

            let vm = this;
            this.chat_client = await Chat.Client.create(token);
            this.chat_client.on("tokenAboutToExpire", async () => {
                vm.refreshToken()
            });
            try {
                for (const chat of this.chats_list) {
                    let channel_id = 'channel-' + chat.job_image_id;
                    this.channel = await this.chat_client.getChannelByUniqueName(channel_id);
                    await this.channel.getUnconsumedMessagesCount().then((result) => {
                        if (result===null){
                            this.channel.getMessagesCount().then((result2) => {
                                this.count_unconsumed_messages+=result2
                                console.log('chat',chat.job_image_id);
                                console.log('result-null',result2);
                            })
                        }else{
                            this.count_unconsumed_messages+=result??0
                            console.log('chat',chat.job_image_id);
                            console.log('result',result);
                        }

                    });
                }
            } catch (e) {
                console.log('await this.chat_client.getChannelByUniqueName(name); ERR:', e);
            }
            this.show_count=true
        },
        refreshToken() {
            this.fetchAccessToken(this.user?.id, this.setNewToken);
        },
        setNewToken(tokenResponse) {
            this.chat_client.updateToken(tokenResponse.token);
        },
    }

}
</script>

<style lang="scss" scoped>
.red_count {
    color: #dc3545;
}
.count_message_m{
    margin: 0 5px;
}
</style>
