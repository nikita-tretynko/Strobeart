<template>
    <div class="section_page pb-60">
        <div>
            <div class="title">Work Timer</div>
            <div class="text pt-20">As soon as you are ready to begin working on the file press start.
                When you are ready press stop and upload the edited file.
            </div>
        </div>

        <div class="timer_section">
            <vue-ellipse-progress
                :progress=progress_timer*progress_timer_procent
                :angle="90"
                color="#C4C4C4"
                :size="305"
                :thickness="10"
                :legend="false"
            >
                <p class="timer" slot="legend-caption">
                    <span>{{ workTime.minutes < 10 ? '0' + workTime.minutes : workTime.minutes }}</span>
                    :
                    <span>{{ workTime.seconds < 10 ? '0' + workTime.seconds : workTime.seconds }}</span>
                </p>

            </vue-ellipse-progress>
        </div>
        <div class="box_f">
            <button class="btn-mc3 w110" :class="{'noActive':add_time_job||work_image.add_time||image.decline}" data-bs-toggle="modal"
                    data-bs-target="#addTimeModal">Add Time
            </button>
            <button class="btn-mc3 w110" :class="{'noActive':!stop_timer}" @click="stopTimer">Stop</button>
        </div>
        <div class="modal fade " id="addTimeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Time</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Please make sure You want to add extra time. Your commission will be reduced.
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <button type="button" class="btn-mc3 w110" @click="noAddTime" data-bs-dismiss="modal">No
                        </button>
                        <button type="button" class="btn-mc3 w110" @click="addTime">Yes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import MainLayout from "../layouts/MainLayout";
import {mapMutations} from 'vuex';
import {errorMessage} from "../services/messages";
import {VueEllipseProgress} from "vue-ellipse-progress";
import {Modal} from 'bootstrap'
import JobWorkStepsEnum from "../enums/JobWorkStepsEnum";
import WorkedImagesStatusEnum from "../enums/WorkedImagesStatusEnum";


export default {
    props:['work_job','work_image','image'],
    name: "JobEditorTimerStep2",
    components: {
        VueEllipseProgress
    },
    data() {
        return {
            time_sec_15min: 900,
            time_sec_30min: 1800,
            stop_timer_sec: 900,
            progress_timer_procent: 0.11111111,
            progress_timer: 0,
            timer: 0,
            interval_work_time: null,
            interval_update_time:null,
            workTime: {
                'hours': 0,
                'minutes': 0,
                'seconds': 0
            },
            stop_timer: false,
            start_timer: true,
            modalAddTime: null,
            add_time_job: null,
        };
    },
    mounted() {
        this.timer = this.time_sec_15min
        this.updateTimer()
        this.modalAddTime = new Modal(document.getElementById('addTimeModal'))
        this.startTimer();
    },
    computed: {},
    destroyed() {
        if (this.modalAddTime) {
            this.modalAddTime = null
        }
        if (this.interval_update_time){
            clearInterval(this.interval_update_time)
        }
    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
        ]),
        checkTimeMeeting2(secs2) {
            let secs = secs2;
            this.workTime.hours = Math.floor(secs / 3600);
            secs = secs % 3600;
            this.workTime.minutes = Math.floor(secs / 60);
            this.workTime.seconds = secs % 60;
        },
        async updateTimer() {
            try {
                const response = await this.$http.postAuth(`${this.$http.apiUrl()}update_time_work_image`, {
                    'timer': this.progress_timer,
                     'work_image_id':this.work_image?.id||null,
                });
                const work_img = response?.data?.data||{}

                if(work_img?.timer&&work_img?.add_time){
                    this.progress_timer = work_img?.timer
                    this.time30Min()
                }
                if(work_img?.timer&&!work_img?.add_time){
                    this.progress_timer = work_img?.timer
                    this.time15Min()
                }
              const status_work = response?.data?.data?.status
                if (status_work===WorkedImagesStatusEnum.END_TIME){
                    this.endTimeC()
                }
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                console.log(message)
            }
        },
        endTimeC(){
            clearInterval(this.interval_work_time)
            clearInterval(this.interval_update_time)
            this.$emit('jobEditorStep2',
                JobWorkStepsEnum.JOB_WORK_STEP_3)
        },
        async stopTimer() {
            this.stop_timer = false
            this.start_timer = true

            try {
                this.showLoader();
                await this.$http.postAuth(`${this.$http.apiUrl()}update_time_work_image`, {
                    'timer': this.progress_timer,
                    'work_image_id':this.work_image?.id||null,
                    'status':WorkedImagesStatusEnum.END_TIME
                });
              this.endTimeC()
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();
        },
        noAddTime() {

        },
        time30Min() {
            this.stop_timer_sec = this.time_sec_30min;
            this.timer = this.time_sec_30min - this.progress_timer;
            this.progress_timer_procent = 0.05555556
        },
        time15Min() {
            this.stop_timer_sec = this.time_sec_15min;
            this.timer = this.time_sec_15min - this.progress_timer;
            this.progress_timer_procent = 0.11111111
        },
        async addTime() {
            try {
                this.showLoader();
                await this.$http.postAuth(`${this.$http.apiUrl()}update_time_work_image`, {
                    'timer': this.progress_timer,
                    'work_image_id':this.work_image?.id||null,
                    'add_time':true
                });
                this.time30Min();
                this.add_time_job = true;
                this.modalAddTime.hide()
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();
        },
        startTimer() {
            this.interval_work_time = setInterval(() => {
                if (this.progress_timer === this.stop_timer_sec - 1) {
                    clearInterval(this.interval_work_time)
                    this.stopTimer();
                }
                this.checkTimeMeeting2(--this.timer)
                ++this.progress_timer
            }, 1000);
            this.stop_timer = true
            this.start_timer = false
        },
        clearTimer() {
            clearInterval(this.interval_work_time)
            this.progress_timer = 0
            this.timer = null
            this.interval_work_time = null
            this.workTime = {
                'hours': 0,
                'minutes': 0,
                'seconds': 0
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

.pb-60 {
    padding-bottom: 60px;
}

.section_page {
    padding-top: 102px;
}

.box_f {
    display: flex;
    justify-content: center;
    gap: 7px;
}

.items_b {
    display: flex;
    justify-content: center;
    flex-direction: column;
    text-align: center;
    cursor: pointer;
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
    max-width: 550px;
}

.text_2 {
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    line-height: 44px;
    color: #000000;
}

.opacity {
    opacity: 0.5;
}

.noActive {
    cursor: not-allowed;
    pointer-events: none;
    opacity: 0.5;
}

.btn-mc3:hover, .btn-mc2:hover {
    background: #D8C3AF;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.25);
    border-radius: 100px;
    color: #FFFFFF;
}

.w110 {
    width: 110px;
}

.timer_box {
    width: 304px;
    height: 304px;
    border: 10px solid rgba(156, 171, 194, 0.35);
    box-sizing: border-box;
    border-radius: 50%;
    position: relative;
}

.timer_section {
    display: flex;
    justify-content: center;
    padding-top: 150px;
    padding-bottom: 100px;
}

.timer {
    font-style: normal;
    font-weight: 200;
    font-size: 50px;
    line-height: 61px;
    color: #000000;
    top: 110px;
    left: 69px;
}

.modal-header, .modal-footer {
    border-bottom: 0px solid #dee2e6;
    border-top: 0px solid #dee2e6;
}

@media only screen and (max-width: 992px) {
    .section_page {
        padding: 0 16px 60px 16px;
        padding-top: 50px;
    }
    .title {
        font-size: 18px;
        line-height: 22px;
    }
    .text {
        font-size: 12.5px;
        line-height: 20px;
    }
    .timer_section {
        //padding-top: 106px;
        //padding-bottom: 106px;
        padding-top: 90px;
        padding-bottom: 80px;
    }
}

</style>
