<template>
    <div class="item_body">
        <input id="fileUpload" type="file" accept="image/*" @change="onFileChange" multiple hidden>

        <div class="row" v-if="images.length">
            <div v-if="images.length" v-for="(image, index) in images" :key="'image-' + index" class="col-4 col-sm-3 col-md-2 image_col">
                <div class="item_image">
                    <img :src="image.src" :alt="'image-' + index">

                    <button class="delete_image_button" @click="removeImage(index)">
                        <span><b>X</b></span>
                    </button>
                </div>
            </div>

            <div class="col-4 col-sm-3 col-md-2 text-center image_col">
                <button
                    v-if="!window_desktop"
                    class="text-center item_image"
                    @click="$emit('upload_modal')">
                    <svg width="130" height="130" viewBox="0 0 130 130" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M97.1427 48.7502H97.5489C100.745 48.7507 103.909 49.3818 106.861 50.6073C109.812 51.8329 112.493 53.6287 114.75 55.8921C119.313 60.4662 121.876 66.6638 121.876 73.1252C121.876 79.5866 119.313 85.7842 114.75 90.3583C112.493 92.6216 109.812 94.4175 106.861 95.643C103.909 96.8686 100.745 97.4997 97.5489 97.5002H81.3395V89.3752H97.5489C101.733 89.188 105.684 87.3942 108.578 84.3672C111.473 81.3402 113.088 77.3134 113.088 73.1252C113.088 68.937 111.473 64.9101 108.578 61.8831C105.684 58.8562 101.733 57.0623 97.5489 56.8752H90.1227L89.1233 49.9121C88.5136 45.5495 86.4967 41.5052 83.379 38.3934C80.2613 35.2816 76.2132 33.2723 71.8496 32.6708C67.4833 32.0744 63.042 32.9269 59.207 35.0976C55.3719 37.2683 52.355 40.6373 50.6189 44.6877L47.9133 50.8789L41.3402 49.3433C39.8377 48.9706 38.2974 48.7716 36.7496 48.7502C31.3708 48.7502 26.2114 50.8871 22.4171 54.6977C19.5893 57.541 17.6653 61.1569 16.8868 65.0906C16.1084 69.0244 16.5103 73.1005 18.042 76.8065C19.5737 80.5126 22.1669 83.683 25.4955 85.9193C28.8241 88.1556 32.7395 89.358 36.7496 89.3752H57.0133V97.5002H36.7496C32.7251 97.5364 28.7392 96.7145 25.0574 95.0893C21.3755 93.4642 18.0823 91.073 15.3971 88.0752C11.3371 83.5538 8.83118 77.8514 8.24636 71.8029C7.66154 65.7544 9.02849 59.6775 12.1471 54.4621C14.2126 51.0067 16.9888 48.0297 20.2917 45.7283C23.5946 43.4269 27.3489 41.8536 31.3058 41.1127C35.2546 40.3814 39.3252 40.4871 43.2333 41.4458C45.6959 35.8072 49.9276 31.1238 55.2885 28.104C60.6494 25.0842 66.8479 23.8922 72.9464 24.7083C79.0475 25.5339 84.7122 28.3288 89.0796 32.6683C93.4469 37.0078 96.2781 42.6545 97.1427 48.7502ZM83.6308 80.0558L72.9546 69.3877V113.555H64.8783V69.6802L54.4946 80.0639L48.7502 74.3114L66.1864 56.8752H71.9389L89.3752 74.3114L83.6308 80.0558Z" fill="#494949"/>
                    </svg>

                    <div class="text-center description_mobile main_text">
                        Upload Files
                    </div>
                </button>

                <button
                    v-else
                    class="text-center item_image"
                    @click="chooseFiles">
                    <svg width="130" height="130" viewBox="0 0 130 130" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M97.1427 48.7502H97.5489C100.745 48.7507 103.909 49.3818 106.861 50.6073C109.812 51.8329 112.493 53.6287 114.75 55.8921C119.313 60.4662 121.876 66.6638 121.876 73.1252C121.876 79.5866 119.313 85.7842 114.75 90.3583C112.493 92.6216 109.812 94.4175 106.861 95.643C103.909 96.8686 100.745 97.4997 97.5489 97.5002H81.3395V89.3752H97.5489C101.733 89.188 105.684 87.3942 108.578 84.3672C111.473 81.3402 113.088 77.3134 113.088 73.1252C113.088 68.937 111.473 64.9101 108.578 61.8831C105.684 58.8562 101.733 57.0623 97.5489 56.8752H90.1227L89.1233 49.9121C88.5136 45.5495 86.4967 41.5052 83.379 38.3934C80.2613 35.2816 76.2132 33.2723 71.8496 32.6708C67.4833 32.0744 63.042 32.9269 59.207 35.0976C55.3719 37.2683 52.355 40.6373 50.6189 44.6877L47.9133 50.8789L41.3402 49.3433C39.8377 48.9706 38.2974 48.7716 36.7496 48.7502C31.3708 48.7502 26.2114 50.8871 22.4171 54.6977C19.5893 57.541 17.6653 61.1569 16.8868 65.0906C16.1084 69.0244 16.5103 73.1005 18.042 76.8065C19.5737 80.5126 22.1669 83.683 25.4955 85.9193C28.8241 88.1556 32.7395 89.358 36.7496 89.3752H57.0133V97.5002H36.7496C32.7251 97.5364 28.7392 96.7145 25.0574 95.0893C21.3755 93.4642 18.0823 91.073 15.3971 88.0752C11.3371 83.5538 8.83118 77.8514 8.24636 71.8029C7.66154 65.7544 9.02849 59.6775 12.1471 54.4621C14.2126 51.0067 16.9888 48.0297 20.2917 45.7283C23.5946 43.4269 27.3489 41.8536 31.3058 41.1127C35.2546 40.3814 39.3252 40.4871 43.2333 41.4458C45.6959 35.8072 49.9276 31.1238 55.2885 28.104C60.6494 25.0842 66.8479 23.8922 72.9464 24.7083C79.0475 25.5339 84.7122 28.3288 89.0796 32.6683C93.4469 37.0078 96.2781 42.6545 97.1427 48.7502ZM83.6308 80.0558L72.9546 69.3877V113.555H64.8783V69.6802L54.4946 80.0639L48.7502 74.3114L66.1864 56.8752H71.9389L89.3752 74.3114L83.6308 80.0558Z" fill="#494949"/>
                    </svg>

                    <div class="text-center description_mobile main_text">
                        Upload Files
                    </div>
                </button>
            </div>
        </div>

        <div class="row" v-if="!images.length">
            <div class="col-12 text-center">
                <button
                    v-if="!window_desktop"
                    class="text-center upload_button item_image"
                    @click="$emit('upload_modal')">
                    <svg width="130" height="130" viewBox="0 0 130 130" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M97.1427 48.7502H97.5489C100.745 48.7507 103.909 49.3818 106.861 50.6073C109.812 51.8329 112.493 53.6287 114.75 55.8921C119.313 60.4662 121.876 66.6638 121.876 73.1252C121.876 79.5866 119.313 85.7842 114.75 90.3583C112.493 92.6216 109.812 94.4175 106.861 95.643C103.909 96.8686 100.745 97.4997 97.5489 97.5002H81.3395V89.3752H97.5489C101.733 89.188 105.684 87.3942 108.578 84.3672C111.473 81.3402 113.088 77.3134 113.088 73.1252C113.088 68.937 111.473 64.9101 108.578 61.8831C105.684 58.8562 101.733 57.0623 97.5489 56.8752H90.1227L89.1233 49.9121C88.5136 45.5495 86.4967 41.5052 83.379 38.3934C80.2613 35.2816 76.2132 33.2723 71.8496 32.6708C67.4833 32.0744 63.042 32.9269 59.207 35.0976C55.3719 37.2683 52.355 40.6373 50.6189 44.6877L47.9133 50.8789L41.3402 49.3433C39.8377 48.9706 38.2974 48.7716 36.7496 48.7502C31.3708 48.7502 26.2114 50.8871 22.4171 54.6977C19.5893 57.541 17.6653 61.1569 16.8868 65.0906C16.1084 69.0244 16.5103 73.1005 18.042 76.8065C19.5737 80.5126 22.1669 83.683 25.4955 85.9193C28.8241 88.1556 32.7395 89.358 36.7496 89.3752H57.0133V97.5002H36.7496C32.7251 97.5364 28.7392 96.7145 25.0574 95.0893C21.3755 93.4642 18.0823 91.073 15.3971 88.0752C11.3371 83.5538 8.83118 77.8514 8.24636 71.8029C7.66154 65.7544 9.02849 59.6775 12.1471 54.4621C14.2126 51.0067 16.9888 48.0297 20.2917 45.7283C23.5946 43.4269 27.3489 41.8536 31.3058 41.1127C35.2546 40.3814 39.3252 40.4871 43.2333 41.4458C45.6959 35.8072 49.9276 31.1238 55.2885 28.104C60.6494 25.0842 66.8479 23.8922 72.9464 24.7083C79.0475 25.5339 84.7122 28.3288 89.0796 32.6683C93.4469 37.0078 96.2781 42.6545 97.1427 48.7502ZM83.6308 80.0558L72.9546 69.3877V113.555H64.8783V69.6802L54.4946 80.0639L48.7502 74.3114L66.1864 56.8752H71.9389L89.3752 74.3114L83.6308 80.0558Z" fill="#494949"/>
                    </svg>

                    <div class="text-center description_mobile main_text">
                        Upload Files
                    </div>
                </button>

                <button
                    v-else
                    class="text-center upload_button item_image"
                    @click="chooseFiles">
                    <svg width="130" height="130" viewBox="0 0 130 130" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M97.1427 48.7502H97.5489C100.745 48.7507 103.909 49.3818 106.861 50.6073C109.812 51.8329 112.493 53.6287 114.75 55.8921C119.313 60.4662 121.876 66.6638 121.876 73.1252C121.876 79.5866 119.313 85.7842 114.75 90.3583C112.493 92.6216 109.812 94.4175 106.861 95.643C103.909 96.8686 100.745 97.4997 97.5489 97.5002H81.3395V89.3752H97.5489C101.733 89.188 105.684 87.3942 108.578 84.3672C111.473 81.3402 113.088 77.3134 113.088 73.1252C113.088 68.937 111.473 64.9101 108.578 61.8831C105.684 58.8562 101.733 57.0623 97.5489 56.8752H90.1227L89.1233 49.9121C88.5136 45.5495 86.4967 41.5052 83.379 38.3934C80.2613 35.2816 76.2132 33.2723 71.8496 32.6708C67.4833 32.0744 63.042 32.9269 59.207 35.0976C55.3719 37.2683 52.355 40.6373 50.6189 44.6877L47.9133 50.8789L41.3402 49.3433C39.8377 48.9706 38.2974 48.7716 36.7496 48.7502C31.3708 48.7502 26.2114 50.8871 22.4171 54.6977C19.5893 57.541 17.6653 61.1569 16.8868 65.0906C16.1084 69.0244 16.5103 73.1005 18.042 76.8065C19.5737 80.5126 22.1669 83.683 25.4955 85.9193C28.8241 88.1556 32.7395 89.358 36.7496 89.3752H57.0133V97.5002H36.7496C32.7251 97.5364 28.7392 96.7145 25.0574 95.0893C21.3755 93.4642 18.0823 91.073 15.3971 88.0752C11.3371 83.5538 8.83118 77.8514 8.24636 71.8029C7.66154 65.7544 9.02849 59.6775 12.1471 54.4621C14.2126 51.0067 16.9888 48.0297 20.2917 45.7283C23.5946 43.4269 27.3489 41.8536 31.3058 41.1127C35.2546 40.3814 39.3252 40.4871 43.2333 41.4458C45.6959 35.8072 49.9276 31.1238 55.2885 28.104C60.6494 25.0842 66.8479 23.8922 72.9464 24.7083C79.0475 25.5339 84.7122 28.3288 89.0796 32.6683C93.4469 37.0078 96.2781 42.6545 97.1427 48.7502ZM83.6308 80.0558L72.9546 69.3877V113.555H64.8783V69.6802L54.4946 80.0639L48.7502 74.3114L66.1864 56.8752H71.9389L89.3752 74.3114L83.6308 80.0558Z" fill="#494949"/>
                    </svg>

                    <div class="text-center description_mobile main_text">
                        Upload Files
                    </div>
                </button>
            </div>
        </div>

        <div class="d-flex justify-content-center gap-5 m_t_86 m_b_126" v-if="window_desktop">
            <div class="text-center">
                <button @click="goHome"
                        class="text-center custom_button white_background main_text">
                    Cancel
                </button>
            </div>
            <div class="text-center">
                <button
                    class="text-center custom_button braun_background white_text"
                    :class="images.length ? '' : 'disabled_button'"
                    :disabled="!images.length"
                    @click="$emit('next-tab')">
                    Next
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'AddFile',
    components: {
    },
    props: [
        'images',
        'files'
    ],
    data() {
        return {
            sizeW: null,
            window_desktop: true,
        }
    },
    created() {
        window.addEventListener('resize', this.updateWidth);
        this.updateWidth();
    },
    beforeDestroy() {
        window.removeEventListener("resize", this.updateWidth);
    },
    methods: {
        updateWidth() {
            this.sizeW = document.documentElement.clientWidth;
        },
        goHome() {
            this.$router.push({to: '/business-homepage', name: "BusinessHomePage"});
        },
        onFileChange(e) {
            this.createImages(e.target.files || e.dataTransfer.files);
        },
        createImages(files) {
            [...files].forEach(file => {
                const reader = new FileReader();
                this.files.push(file)
                reader.onload = e => this.images.push({
                    src: e.target.result,
                    name: file.name,
                });
                reader.readAsDataURL(file);
            });
        },
        removeImage(index) {
            this.images.splice(index, 1);
            this.files.splice(index, 1);

        },
        chooseFiles: function() {
            document.getElementById("fileUpload").click()
        },
        nextTab() {
            this.active_tab = this.active_tab++;
        }
    },
    computed: {
    },
    watch: {
        sizeW(val) {
            this.window_desktop = val > 992;
        },
    }
}

</script>

<style lang="scss" scoped>
$primary_font_family: 'Montserrat', sans-serif;

.main_text {
    color: #494949;
    font-style: normal;
    font-family: $primary_font_family;
}
.white_text {
    color: #FFFFFF;
    font-style: normal;
    font-family: $primary_font_family;
}
.title_desktop {
    font-weight: 600;
    font-size: 35px;
    line-height: 44px;
}
.description_desktop {
    font-weight: normal;
    font-size: 17px;
    line-height: 24px;
}
.description_mobile {
    font-weight: 500;
    font-size: 12.5px;
    line-height: 20px;
}
.item_body {
    width: 100%;
    min-height: 340px;
    padding: 11px;
}
.image_col {
    padding: 0 3px !important;

    button {
        border: none;
    }
}
.item_image {
    width: 102px;
    height: 102px;
    overflow: hidden;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3px;
    margin: 3px 0;
    box-shadow: 0 3px 3px rgba(0, 0, 0, 0.15) !important;
    box-sizing: border-box;
    border-radius: 5px;

    img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .delete_image_button {
        position: absolute;
        top: 0;
        right: 0;
        background: red;
        color: #FFFFFF;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        border: none;
        padding-bottom: 3px;

        span {
            position: absolute;
            top: 1px;
            right: 7px;
            font-style: normal;
        }
    }
}
.upload_button {
    width: 249px;
    height: 272px;
    background: #FFFFFF;
    border-radius: 5px;
    box-sizing: border-box;
    box-shadow: 0 3px 3px rgba(0, 0, 0, 0.15) !important;
    border: none;
    margin: 0 auto;
}
.modal-footer {
    justify-content: center;
}
.modal-body {
    width: 100%;
}
.close_modal {
    width: 120px;
    height: 50px;
    background: #FFFFFF;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.25);
    border-radius: 100px;
}
.in_center {
    margin: 0 auto;
}
.custom_button {
    width: 200px;
    height: 60px;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.25);
    border-radius: 100px;
}
.braun_background {
    background: #D8C3AF;
}
.white_background {
    background: #FFFFFF;
}
.disabled_button {
    opacity: 0.45;
}
.m_t_86 {
    margin-top: 86px;
}
.m_b_126 {
    margin-bottom: 126px;
}
</style>
