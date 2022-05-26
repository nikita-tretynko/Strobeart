<template>
    <div>
        <MainLayout :is-show-header=!showJobDetailsMob>
            <div class="container_page">
                <JobEditorStep1
                    v-if="active===enums.JOB_WORK_STEP_1"
                    :job="job"
                    :index_img="index_image"
                    :image="image"
                    :work_job="work_job"
                    @workImage="workImage"
                    @jobEditorStep1='onActive'>
                </JobEditorStep1>
                <JobEditorTimerStep2
                    v-if="active===enums.JOB_WORK_STEP_2"
                    :work_job="work_job"
                    :image="image"
                    :work_image="work_image"
                    @jobEditorStep2='onActive'>
                </JobEditorTimerStep2>
                <JobEditorStep3 :work_image="work_image" @jobEditorStep3='onActive' v-if="active===enums.JOB_WORK_STEP_3"></JobEditorStep3>
                <JobEditorStep4
                    :work_image="work_image"
                    @jobEditorStep4='onActive'
                    @indexImage="indexImage"
                    v-if="active===enums.JOB_WORK_STEP_4">
                </JobEditorStep4>
            </div>
        </MainLayout>
    </div>
</template>

<script>
import JobEditorStep1 from "../components/JobEditorStep1";
import JobEditorTimerStep2 from "../components/JobEditorTimerStep2";
import JobEditorStep3 from "../components/JobEditorStep3";
import JobEditorStep4 from "../components/JobEditorStep4";
import {mapGetters, mapMutations} from 'vuex';
import {errorMessage} from "../services/messages";
import MainLayout from "../layouts/MainLayout";
import JobWorkStepsEnum from "../enums/JobWorkStepsEnum";
import moment from 'moment-timezone'
import JobWorkStatusEnum from "../enums/JobWorkStatusEnum";
import WorkedImagesStatusEnum from "../enums/WorkedImagesStatusEnum";

export default {
    name: "WorkingJob",
    components: {
        MainLayout,
        JobEditorTimerStep2,
        JobEditorStep1,
        JobEditorStep3,
        JobEditorStep4,
        JobWorkStepsEnum
    },
    data() {
        return {
            job:{},
            active: JobWorkStepsEnum.JOB_WORK_STEP_1,
            work_job:{},
            work_image:{},
            index_image:0,
            image:{},
        };
    },
    mounted() {
        this.getWorkJob()
    },
    computed: {
        showJobDetailsMob() {
            return this.isMobile()
        },
        enums() {
            return {
                [JobWorkStepsEnum.JOB_WORK_STEP_1]: JobWorkStepsEnum.JOB_WORK_STEP_1,
                [JobWorkStepsEnum.JOB_WORK_STEP_2]: JobWorkStepsEnum.JOB_WORK_STEP_2,
                [JobWorkStepsEnum.JOB_WORK_STEP_3]: JobWorkStepsEnum.JOB_WORK_STEP_3,
                [JobWorkStepsEnum.JOB_WORK_STEP_4]: JobWorkStepsEnum.JOB_WORK_STEP_4,
            }
        }
    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
        ]),
        workImage(data){
            this.work_image = data;
        },
        onActive(data) {
            this.active = data;
        },
        indexImage(data){
            this.index_image = data
            this.getWorkJob();
        },
        async getWorkJob() {
            try {
                this.showLoader();
                const response = await this.$http.getAuth(`${this.$http.apiUrl()}get-work-job/${this.$route.params.id}`);
                this.job = response?.data?.data?.job || {}
                this.work_job = response?.data?.data?.user_work_job||{}
                this.work_image = response?.data?.data?.work_image||{}
                this.index_image = response?.data?.data?.index_image||0
                this.image = response?.data?.data?.image||{}

                if (this.work_image.status===WorkedImagesStatusEnum.END_TIME){
                    this.active = JobWorkStepsEnum.JOB_WORK_STEP_3
                }
                if (this.work_image.status===WorkedImagesStatusEnum.START_TIME){
                    this.active = JobWorkStepsEnum.JOB_WORK_STEP_2
                }
                if (this.work_image.status===WorkedImagesStatusEnum.UPLOADED_FILE){
                    this.active = JobWorkStepsEnum.JOB_WORK_STEP_4
                }
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
                this.$router.push({path: `/`}).then();
            }
            this.hideLoader();
        },


        isMobile() {
            return window.innerWidth <= 992;
        },
    }
}
</script>

<style lang="scss" scoped>
.container_page {
    padding-top: 28px;
    padding-bottom: 60px;
}

@media only screen and (max-width: 992px) {
    .container_page {
        margin-top: 0px;
        display: inherit;
    }
    .container_page {
        padding-left: 15px;
        padding-right: 15px;
    }
}

</style>
