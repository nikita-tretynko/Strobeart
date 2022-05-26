<template>
    <div id="app_camera" class="container-fluid web-camera-container">
        <div class="row header_camera align-items-center justify-content-end">
            <button
                type="button"
                class="btn-close"
                @click="$emit('close-camera-modal')"
                aria-label="Close"></button>
        </div>

        <div class="row body_camera">
            <div v-show="isCameraOpen && isLoading" class="camera-loading">
                <Loader />
            </div>
            <div v-if="isCameraOpen" v-show="!isLoading" class="camera-box" :class="{ 'flash' : isShotPhoto }">
                <video ref="camera" :width="camera_width" :height="camera_height" autoplay></video>

                <canvas id="photoTaken" ref="canvas" :width="camera_width" :height="camera_height"></canvas>
            </div>
        </div>

        <div class="row footer_camera align-items-center justify-content-between">
            <div class="col-4 photo_preview text-center">
                <button
                    class="reload">
                    <svg v-if="!images.length" width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M23.625 9.33301H23.6446" stroke="#494949" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M27.7082 1.1665H7.2915C3.90876 1.1665 1.1665 3.90876 1.1665 7.2915V27.7082C1.1665 31.0909 3.90876 33.8332 7.2915 33.8332H27.7082C31.0909 33.8332 33.8332 31.0909 33.8332 27.7082V7.2915C33.8332 3.90876 31.0909 1.1665 27.7082 1.1665Z" stroke="#494949" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M1.1665 23.6247L9.33317 15.458C10.2643 14.562 11.3205 14.0903 12.3957 14.0903C13.4708 14.0903 14.5271 14.562 15.4582 15.458L25.6665 25.6663" stroke="#494949" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M21.583 21.5832L23.6247 19.5415C24.5558 18.6455 25.612 18.1738 26.6872 18.1738C27.7623 18.1738 28.8186 18.6455 29.7497 19.5415L33.833 23.6248" stroke="#494949" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <button v-else class="photo_preview_div" @click="$emit('close-camera-modal')">
                        <img :src="images[images.length - 1].src" alt="photo preview" class="img_photo_preview">
                    </button>
                </button>
            </div>
            <div class="col-4 text-center">
                <button
                    type="button"
                    class="button start_photo"
                    @click="takePhoto"
                    :disabled="!isCameraOpen">
                    <svg width="68" height="68" viewBox="0 0 68 68" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="34" cy="34" r="33" stroke="#494949" stroke-width="2"/>
                        <circle cx="34" cy="34" r="30" fill="#494949"/>
                    </svg>
                </button>
            </div>
            <div class="col-4 text-center">
                <button class="reload">
                    <svg width="36" height="34" viewBox="0 0 36 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M33.1651 13.75L31 17.5L28.8349 13.75H33.1651Z" fill="#494949" stroke="#494949" stroke-width="2.5"/>
                        <path d="M31 14C31 7.37258 25.1797 2 18 2C10.8203 2 5 7.37258 5 14" stroke="#494949" stroke-width="2.5"/>
                        <path d="M5 20C5 26.6274 10.8203 32 18 32C25.1797 32 31 26.6274 31 20" stroke="#494949" stroke-width="2.5"/>
                        <path d="M2.83494 20.25L5 16.5L7.16506 20.25H2.83494Z" fill="#494949" stroke="#494949" stroke-width="2.5"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>
<script>
import {mapMutations} from 'vuex';
import Loader from "@/components/Loader";

export default {
    name: 'AddFromTheCamera',
    components: {
        mapMutations,
        Loader
    },
    props: ['images'],
    data() {
        return {
            sizeW: null,
            window_desktop: true,
            camera_width: null,
            camera_height: null,
            isCameraOpen: false,
            isShotPhoto: false,
            isLoading: false,
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
        ...mapMutations([
            'showLoader',
            'hideLoader',
            'setUser',
        ]),
        updateWidth() {
            this.sizeW = document.documentElement.clientWidth;
            this.camera_width = this.sizeW - 24;
            this.camera_height = this.camera_width / 1.33;
        },
        toggleFlesh() {
            this.isShotPhoto = !this.isShotPhoto;
        },
        toggleCamera() {
            if(this.isCameraOpen) {
                this.stopCamera();
            } else {
                this.startCamera();
            }
        },
        startCamera() {
            this.showLoader();
            this.isCameraOpen = true;
            this.createCameraElement();
        },
        stopCamera() {
            this.isCameraOpen = false;
            this.isShotPhoto = false;
            this.stopCameraStream();
        },
        createCameraElement() {
            this.isLoading = true;

            const constraints = (window.constraints = {
                audio: false,
                video: true
            });

            navigator.mediaDevices
                .getUserMedia(constraints)
                .then(stream => {
                    this.isLoading = false;
                    this.$refs.camera.srcObject = stream;
                    this.hideLoader();
                })
                .catch(error => {
                    this.isLoading = false;
                    alert("May the browser didn't support or there is some errors.");
                });
            this.hideLoader();
        },
        stopCameraStream() {
            let tracks = this.$refs.camera.srcObject.getTracks();

            tracks.forEach(track => {
                track.stop();
            });
            console.log('STOP');
        },
        takePhoto() {
            this.isShotPhoto = true;

            const FLASH_TIMEOUT = 50;

            setTimeout(() => {
                this.isShotPhoto = false;
            }, FLASH_TIMEOUT);

            const context = this.$refs.canvas.getContext('2d');
            context.drawImage(this.$refs.camera, 0, 0, this.camera_width, this.camera_height);

            const canvas = document.getElementById("photoTaken").toDataURL("image/jpeg")
                .replace("image/jpeg", "image/octet-stream");

            this.images.push({src: canvas, name: this.createNewPhotoName()});
        },
        createNewPhotoName() {
            let result = '';
            let words  = '0123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
            let max_position = words.length - 1;
            for (let i = 0; i < 12; ++i) {
                let position = Math.floor(Math.random() * max_position);
                result = result + words.substring(position, position + 1);
            }
            return result + '.jpg';
        },
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
<style lang="scss" scope>
$primary_font_family: 'Montserrat', sans-serif;

#photoTaken {
    display: none;
}
.header_camera {
    min-height: 50px;
    padding: 0 25px;
}
.footer_camera {
    min-height: 96px;
    padding: 22px 0;
}
.body_camera {
    min-height: calc(100vh - 152px);
}
.start_photo {
    width: 70px;
    height: 70px;
    padding: 0;
    border: none !important;
}
.reload {
    padding: 0;
    border: none !important;
}
.photo_preview_div {
    width: 40px;
    height: 40px;
    overflow: hidden;
    padding: 0;
    border: 0.2px solid #696969;
    border-radius: 5px;
}
.img_photo_preview {
    width: 100%;
}
.no_flash {
    width: 24px;
    height: 24px;
    border: none;
}
.web-camera-container {
    //background-color: #ECF0F3;
}
@media only screen and (max-width: 992px) {
    .body_camera {
        min-height: calc(100vh - 250px);
    }

}
</style>
