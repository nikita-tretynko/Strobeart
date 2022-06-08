<template>
    <div class="section_page pb-60">
        <div class="title padding_t">Files</div>
        <div class="text pt-20 padding_t">The files are ready to be downloaded.</div>
        <div class="d-flex justify-content-center mt-4">
            <div class="btn_dsg" @click="downloadStyleGuides">
                Download style guides
                <img class="info_btn-dsg" src="@/assets/icons/info.svg" alt=""/>
            </div>
        </div>
        <div class="pt-110 pb_images images">
            <div class="img">
                <img v-lazy=imageWorkPrev() alt=""/>
            </div>
            <div class="img_items_d">
                <div class="img_d im1"></div>
                <div class="img_d im2"></div>
            </div>
        </div>
        <div class="box_f">
            <div class="items_b" @click.prevent="downloadItem({url:imageWork(),
          label: 'example',})">
                <div class="pb-22 mpb-16"><img src="@/assets/icons/download.svg" alt=""/></div>
                <div class="text_2">download</div>
            </div>
            <div class="items_b opacity">
                <div class="pb-22"><img src="@/assets/icons/upload.svg" alt=""/></div>
                <div class="text_2">upload</div>
            </div>
        </div>
        <!--        <div class="d-flex justify-content-center pt-4">-->
        <!--            <button class="btn-mc3" @click="startWork">Start Work</button>-->
        <!--        </div>-->
        <div class="modal fade " id="timeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        Files have been downloaded. Timer will automatically start in 15 seconds
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import {mapMutations} from 'vuex';
import {errorMessage} from "../services/messages";
import axios from "axios";
import JobWorkStepsEnum from "../enums/JobWorkStepsEnum";
import {Modal} from "bootstrap";

export default {
    name: "JobEditorStep1",
    props: ['job', 'work_job', 'index_img','image'],
    components: {},
    data() {
        return {
            work_image: {},
            downloadTimer: null,
            attempts: 15,
            downloadToken: null,
            timer_start_work: null,
            update_timer_start_work: null,
            start_timer: 0,
            timeModal: null,
            style_guide_files:[]
        };
    },
    destroyed() {
        if (this.timer_start_work) {
            clearInterval(this.timer_start_work)
        }
        if (this.update_timer_start_work) {
            clearInterval(this.update_timer_start_work)
        }
        if (this.timeModal) {
            document.body.style.overflow = "scroll";
            document.querySelector('body').classList.remove('modal-open');
            this.timeModal.dispose()
        }

    },
    updated() {
        if (this.getWorkJobTimer) {
            this.timeModal.show()
            this.start_timer = (+this.getWorkJobTimer);
            this.timerStartWork();
            this.saveTimer();

        }
    },
    mounted() {
        this.timeModal = new Modal(document.getElementById('timeModal'))
        this.getUserStyleGuide()
    },
    computed: {
        getJobImages() {
            return this.job?.images || []
        },
        getWorkJobTimer() {
            return this.work_job?.timer || null
        },
    },
    created() {
        window.addEventListener('beforeunload', this.handler)
    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
        ]),
        async getUserStyleGuide() {
            try {
                this.showLoader();
                const response = await this.$http.getAuth(`${this.$http.apiUrl()}profile/get-file-job/`+this.$route.params.id);
                this.style_guide_files = response?.data?.data|| [];
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();
        },
        handler: function handler(event) {
            if (this.getWorkJobTimer && this.start_timer) {
                this.updateTimerWorkJob()
            }
        },
        downloadStyleGuides(){
            this.style_guide_files.map(async element => {
                if (element) {
                    try {
                        const response = await axios.get(element.file_url, {responseType: "blob"});
                        const blob = new Blob([response.data], {type: response.data.type});
                        const link = document.createElement("a");
                        link.href = window.URL.createObjectURL(blob);
                        link.download = element.file_name;
                        link.click();
                        window.URL.revokeObjectURL(link.href);
                    } catch (e) {
                        const message = e?.response?.data?.error?.message || 'ERROR';
                        errorMessage(message)
                    }
                }
            })
        },
        async updateTimerWorkJob() {
            try {
                this.showLoader();
                const response = await this.$http.postAuth(`${this.$http.apiUrl()}update-time-download`, {
                    work_job_id: this.work_job.id,
                    timer: this.start_timer
                });
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();
        },
        imageWorkPrev() {
            return (this.image?.src_cropped ?? this.image?.src) || ''
        },
        imageWork() {
            return this.image?.src || ''
        },
        async startWork() {
            try {
                this.showLoader();
                const response = await this.$http.postAuth(`${this.$http.apiUrl()}create-work-image`, {
                    work_job: this.work_job.id,
                    number_file: this.index_img,
                    image_id: this.image?.id || null,
                    image_jobs_id: this.job.id
                });
                this.work_image = response?.data?.data || {}
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();
            this.$emit('jobEditorStep1',
                JobWorkStepsEnum.JOB_WORK_STEP_2)
            this.$emit('workImage',
                this.work_image)
        },
        getCookie(name) {
            let parts = document.cookie.split(name + "=");
            if (parts.length === 2)
                return parts.pop().split(";").shift();
        },
        expireCookie(cName) {
            document.cookie = encodeURIComponent(cName) + "=deleted; expires=" + new Date(0).toUTCString();
        },
        setFormToken() {
            this.downloadToken = new Date().getTime();
            document.getElementById("downloadToken").value = this.downloadToken;
            return this.downloadToken;
        },
        unblockSubmit() {
            console.log("auto", "pointer");
            window.clearInterval(this.downloadTimer);
            this.expireCookie("downloadToken");
            this.attempts = 300;
        },
        blockResubmit() {
            // this.downloadToken = this.setFormToken();
            this.downloadToken = 123;
            console.log(this.downloadToken)
            console.log("wait", "wait");
            this.downloadTimer = window.setInterval(() => {
                let token = this.getCookie("downloadToken");
                if ((token == this.downloadToken) || (this.attempts === 0)) {
                    this.unblockSubmit();
                }
                this.attempts--;
            }, 1000);

        },
        timerStartWork() {
            if (!this.timer_start_work) {
                this.timer_start_work = window.setInterval(() => {
                    if (this.start_timer >= 15) {
                        this.timeModal.hide()
                        clearInterval(this.update_timer_start_work)
                        clearInterval(this.timer_start_work)
                        this.startWork()
                    }
                    ++this.start_timer
                }, 1000);
            }
        },
        saveTimer() {
            if (!this.update_timer_start_work) {
                this.update_timer_start_work = window.setInterval(() => {
                    this.updateTimerWorkJob()
                }, 3000);
            }
        },
        async downloadItem({url, label}) {
            this.timeModal.show()
            this.timerStartWork();
            this.saveTimer();
            try {
                const response = await axios.get(url, {responseType: "blob"});
                const blob = new Blob([response.data], {type: response.data.type});
                const link = document.createElement("a");
                link.href = window.URL.createObjectURL(blob);
                link.download = label;
                link.click();
                window.URL.revokeObjectURL(link.href);
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
        },
        async homePage() {
            this.$router.push({to: '/login', name: "Login"}).then();
        },
    }
}
</script>

<style lang="scss" scoped>
.pt-20 {
    padding-top: 20px;
}

.pt-110 {
    padding-top: 110px;
}

.pb-112, .pb_images {
    padding-bottom: 112px;
}

.pb-22 {
    padding-bottom: 22px;
}

.pb-60 {
    padding-bottom: 60px;
}

.section_page {
    padding-top: 102px;
}

.box_f {
    display: flex;
    justify-content: center;
    gap: 40px;
}

.items_b {
    display: flex;
    justify-content: center;
    flex-direction: column;
    text-align: center;
    cursor: pointer;
    width: 130px;
}

.title {
    font-style: normal;
    font-weight: 600;
    font-size: 35px;
    line-height: 44px;
    color: #494949;
}

.text {
    font-style: normal;
    font-weight: normal;
    font-size: 17px;
    line-height: 24px;
    color: #494949;
}

.text_2 {
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    line-height: 44px;
    color: #000000;
}

.images {
    display: flex;
    justify-content: center;
    padding-left: 34px;
}

.img_d {
    background: #F4F4F3;
    filter: drop-shadow(0px 2px 2px rgba(0, 0, 0, 0.2));
    border-radius: 5px;
    width: 370px;
    height: 354px;
}

.img {
    //background: url("../assets/images/test_img.png");
    background-size: cover;
    filter: drop-shadow(0px 2px 2px rgba(0, 0, 0, 0.2));
    border-radius: 5px;
    width: 370px;
    height: 354px;
    z-index: 1;
}

.img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    filter: drop-shadow(0px 2px 2px rgba(0, 0, 0, 0.2));
    border-radius: 5px;
}

.img_items_d {
    filter: drop-shadow(0px 2px 2px rgba(0, 0, 0, 0.2));
    border-radius: 5px;
    width: 370px;
    height: 354px;
    position: absolute;
}

.im1 {
    position: absolute;
    left: -44px;
    top: -44px;
}

.im2 {
    position: absolute;
    top: -22px;
    left: -22px;
}

.opacity {
    opacity: 0.5;
}

.btn-mc3:hover {
    background: #D8C3AF;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.25);
    border-radius: 100px;
    color: #FFFFFF;
}
.btn_dsg{
    cursor: pointer;
    height: 60px;
    width: 239px;
    font-style: normal;
    font-weight: 500;
    font-size: 14px;
    line-height: 17px;
    display: flex;
    align-items: center;
   justify-content: center;
    color: #494949;
    background: #FFFFFF;
    border: 0.5px solid #494949;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.25);
    border-radius: 100px;
    position: relative;
}
.info_btn-dsg{
    position: absolute;
    right: 17px;
    top: 9px;
}

@media only screen and (max-width: 992px) {
    .title {
        font-size: 18px;
        line-height: 22px;
    }
    .text {
        font-size: 12.5px;
        line-height: 20px;
    }
    .img {
        // background: url("../assets/images/test_img.png");
        filter: drop-shadow(0px 2px 2px rgba(0, 0, 0, 0.2));
        border-radius: 5px;
        width: 284px;
        height: 272px;
        z-index: 1;
        background-size: cover;
    }
    .img_items_d {
        filter: drop-shadow(0px 2px 2px rgba(0, 0, 0, 0.2));
        border-radius: 5px;
        width: 284px;
        height: 272px;
        position: absolute;
    }
    .img_d {
        background: #F4F4F3;
        filter: drop-shadow(0px 2px 2px rgba(0, 0, 0, 0.2));
        border-radius: 5px;
        width: 284px;
        height: 272px;
    }
    .im1 {
        position: absolute;
        left: -34px;
        top: -34px;
    }
    .im2 {
        position: absolute;
        top: -17px;
        left: -17px;
    }
    .section_page {
        padding-top: 50px;
        padding-bottom: 60px;
    }
    .pb_images {
        padding-bottom: 90px;
        padding-top: 100px;
    }
    .padding_t {
        padding: 0 19px;
    }
    .text_2 {
        font-style: normal;
        font-weight: 500;
        font-size: 15px;
        line-height: 18px;
        color: #000000;
    }
    .mpb-16 {
        padding-bottom: 16px;
    }
    .box_f {
        gap: 0;
    }

    .items_b {
        width: 120px;
    }
}

</style>
