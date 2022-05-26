<template>
    <div class="chat_page">
        <div class="container_chat">
            <div class="header_chat" v-if="user_guest">
                <div class="guest_avatar">
                    <img v-if="getGuestAvatarUser()" class="guest_avatar" :src="getGuestAvatarUser()" alt="">
                    <img v-else class="guest_avatar" src='@/assets/images/sbcf-default-avatar.png' alt="">
                </div>
                <div class="ms-3 name_title">{{ user_guest.first_name }} {{ user_guest.last_name }}</div>
            </div>
            <div ref="messagesContainer" @scroll="onScrollChat" class="message-box">
                <div v-if="messages.length" v-for="message in messages" :key="message.id"
                     :class="{message_div_end:message.author==user.id}" class="message_div">
                    <div class="box_avatar" v-if="message.author!=user.id">
                        <img v-if="getAvatarUser(message.author)" class="avatar" :src=getAvatarUser(message.author)
                             alt="">
                        <img v-else class="avatar" src='@/assets/images/sbcf-default-avatar.png' alt="">
                    </div>
                    <div class="message_item" :class="{my_message:message.author==user.id}">
                        <template v-if="isImageChatMessage(message)">
                            <img v-if="getMessageContent(message)" :src="getMessageContent(message)"
                                 class="chat_image cp"
                                 preview=0 preview-text=""
                                 alt="chat image">
                        </template>
                        <template v-else>
                            <div class="">{{ getMessageContent(message) }}</div>
                        </template>
                    </div>
                </div>
            </div>
            <div class="input_box_s">
                <input type="file"
                       id="upload-file"
                       @change="saveImage"
                       accept="image/jpeg, image/gif, image/png"/>

                <img src="@/assets/icons/uil_camera-plus.svg" class="img_m chat" @click="loadFile"/>

                <input class="input_send_m" type="text" v-model="newMessage"
                       v-on:keyup.13="sendMessage" placeholder="Type...">
            </div>
        </div>
    </div>
</template>

<script>
import {mapGetters, mapMutations} from "vuex";
import {errorMessage} from "../services/messages";

const Chat = require('twilio-chat');

export default {
    name: "TwilioChat",
    components: {},
    data() {
        return {
            job_id: this.$route.params.id,
            user: {},
            chat_client: null,
            channel: null,
            connected: false,
            messages: [],
            newMessage: '',
            showMessages: false,
            users_chat: [],
            user_guest: {},
            auto_scroll: true,
        };
    },
    async mounted() {
        this.user = JSON.parse(localStorage.getItem('strobeart_user')) || {}
        this.connectClientWithUsername();
    },
    created() {
        this.$previewRefresh()
    },
    updated() {
        this.$previewRefresh()
        this.scrollToEnd();
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
        onScrollChat() {
            let content = this.$refs.messagesContainer;
            if ((content.scrollHeight - Math.abs(content.scrollTop)) === content.clientHeight) {
                this.auto_scroll = true;
            } else {
                this.auto_scroll = false;
            }
        },
        scrollToEnd: function () {
            let content = this.$refs.messagesContainer;
            if (this.auto_scroll) {
                content.scrollTop = content.scrollHeight
            }
        },

        getGuestAvatarUser() {
            return this.user_guest?.avatar?.url || null
        },
        getAvatarUser(user_id) {
            return this.users_chat[user_id]?.avatar?.url || null
        },
        connectClientWithUsername() {
            if (this.user?.id) {
                this.fetchAccessToken(this.user?.id, this.connectMessagingClient);
            } else {
                console.log('Error User')
            }
        },
        async fetchAccessToken(username, handler) {
            this.showLoader()
            try {
                const result = await this.$http.postAuth(`${this.$http.apiUrl()}access_token`, {
                    identity: this.user?.id,
                });
                const response = await this.$http.postAuth(`${this.$http.apiUrl()}chat`, {
                    job_id: this.job_id
                });
                if (response?.data?.data) {
                    let info = response.data.data;
                    info.forEach((element) => {
                        this.users_chat[element.id] = element;
                        if (element.id != this.user.id) {
                            this.user_guest = element
                        }
                    });
                }
                this.hideLoader()
                handler(result?.data?.data);
            } catch (e) {
                console.log(error);
            }
            this.hideLoader()
        },
        async identityUser() {
            const members = this.channel.getMembers();
            members.then((currentMembers) => {
                currentMembers.forEach(member => {
                    if (member.identity == this.user.id) {
                        this.channel.setAllMessagesConsumed()
                    }
                });
            });
        },
        async connectMessagingClient(token) {
            // Initialize the Chat messaging client
            let vm = this;
            this.chat_client = await Chat.Client.create(token);
            this.chat_client.on("tokenAboutToExpire", async () => {
                vm.refreshToken()
            });
            vm.updateConnectedUI();
            try {
                let channel_id = 'channel-' + this.job_id;
                this.channel = await this.chat_client.getChannelByUniqueName(channel_id);
                this.channel.on("messageAdded", message => {
                    this.messages.push(message);
                    this.identityUser()
                });
                await this.identityUser()
                this.channel.getUnconsumedMessagesCount().then((result) => {
                    console.log('res', result);
                });
                // console.log(this.channel.setAllMessagesConsumed());
                console.log(this.channel.lastConsumedMessageIndex);
                await this.fetchMessages()
            } catch (e) {
                console.log('await this.chat_client.getChannelByUniqueName(name); ERR:', e);
            }
        },
        async sendEmailUser() {
            this.showLoader();
            try {
                await this.$http.postAuth(`${this.$http.apiUrl()}send-email`, {
                    'email': this.user_guest?.email || '',
                    'text': this.newMessage,
                    'link': this.$route.path
                });
            } catch (e) {
            }
            this.hideLoader();
        },
        async sendMessage() {
            if (!this.newMessage) {
                return
            }
            const message_obj = {
                message: this.newMessage,
                url: null
            };
            this.channel.sendMessage(JSON.stringify(message_obj));
            await this.sendEmailUser();
            this.newMessage = "";
        },
        async fetchMessages() {
            try {
                const messageObj = await this.channel.getMessages();
                this.messages = messageObj.items;
            } catch (e) {
                console.error('await this.channel.getMessages() ERROR:', e);
            }
        },
        updateConnectedUI() {
            this.connected = true;
        },
        refreshToken() {
            this.fetchAccessToken(this.user?.id, this.setNewToken);
        },
        setNewToken(tokenResponse) {
            this.chat_client.updateToken(tokenResponse.token);
        },
        isImageChatMessage(message) {
            try {
                const messageBodyObj = JSON.parse(message.body);
                return !!(messageBodyObj && messageBodyObj.url);
            } catch (e) {
                return false;
            }
        },
        loadFile() {
            document.getElementById('upload-file').click();
        },
        async saveImage() {
            const formData = new FormData();
            formData.append('image', document.getElementById('upload-file').files[0]);
            formData.append('job_id', this.job_id);
            this.showLoader()
            try {
                const {data} = await this.$http.postAuth(
                    `${this.$http.apiUrl()}chat/image`,
                    formData,
                    this.$http.formDataHeader()
                );
                if (data?.data?.image?.url || null) {
                    const messageObj = {
                        message: null,
                        url: data.data.image.url
                    }
                    this.channel.sendMessage(JSON.stringify(messageObj));
                }
            } catch (e) {
                console.log('saveImage ERR:', e);
            }
            this.hideLoader()
        },
        getMessageContent(item) {
            try {
                const is_media = this.isImageChatMessage(item);
                const obj = JSON.parse(item.body);
                return !is_media ? obj['message'] : obj['url'];
            } catch (e) {
                return item.body;
            }
        },

    }

}
</script>

<style lang="scss" scoped>
.media-body {

}

#upload-file {
    display: none;
}

.input_box_s {
    display: flex;
    gap: 10px;
}

.box_avatar .avatar {
    width: 25px;
    height: 25px;
    border-radius: 50%;
    border: 0.3px solid #494949;
    box-sizing: border-box;
    margin-bottom: 6px;
}

.message_item {
    margin: 0 10px;
    background: rgba(216, 195, 175, 0.45);
    box-shadow: 0px 3px 3px rgba(0, 0, 0, 0.25);
    border-radius: 0px 10px 10px 10px;
    padding: 10px 25px;
    font-style: normal;
    font-weight: 400;
    font-size: 13px;
    line-height: 18px;
    color: #000000;
    display: inline-block;
}

.message_div_end {
    text-align: end;
}

.my_message {
    text-align: end;
    background: #F4F2F2;
    box-shadow: 0px 3px 3px rgba(0, 0, 0, 0.25);
    border-radius: 0px 10px 10px 10px;
}

.message_div {
    padding: 20px 0;
}

.input_send_m {
    background: #FFFFFF;
    border: 0.3px solid #494949;
    box-sizing: border-box;
    border-radius: 100px;
    height: 35px;
    width: 100%;
    padding: 18px;
}

.input_send_m::placeholder {
    font-style: normal;
    font-weight: 400;
    font-size: 13px;
    line-height: 18px;
    color: rgba(0, 0, 0, 0.35);
}

.chat_image {
    width: 100%;
    height: auto;
}

.chat_page {
    width: 100%;
    display: flex;
    justify-content: center;
    padding: 50px 30px 120px 30px;
}

.container_chat {
    width: 50%;
}

.message-box {
    overflow-y: scroll;
    height: 300px
    //height: calc(100vh - 400px);
}

.guest_avatar {
    border-radius: 50%;
    width: 45px;
    height: 45px;
}

.header_chat {
    padding: 30px 15px;
    display: flex;
    border-bottom: 0.5px solid #494949;
    margin-bottom: 50px;
}

.name_title {
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    line-height: 22px;
    color: #494949;
    display: flex;
    align-items: end;
}

@media only screen and (max-width: 992px) {
    .input_box_s {
        position: fixed;
        width: 84%;
        bottom: 99px;
        z-index: 9999;
        background: #f4f2f2;
    }
    .container_chat {
        width: 100%;
    }
    .message-box {
        overflow-x: hidden;
        height: calc(100vh - 363px);
    }
    .chat_page {
        padding: 20px 30px 0px 30px;
    }

}
</style>
