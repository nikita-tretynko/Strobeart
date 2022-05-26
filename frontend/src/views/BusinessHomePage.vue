<template>
    <MainLayout>
        <div class="container body_active">
            <div class="row m_b_14" :class="window_desktop ? 'm_t_51' : 'm_t_34'">
                <div class="col-12 text-start">
                    <div class="inbox main_text">
                        Inbox
                    </div>
                </div>
            </div>
            <div class="row" :class="window_desktop ? 'm_b_82' : 'm_b_45'">
                <router-link  :to="{ name: 'ChatsList'}" class="col-12 text-start text-decoration-none align-content-center cp">
                    <div
                        class="message_box white_background message_text main_text d-flex align-items-center text-truncate">
                        <svg width="29" height="22" viewBox="0 0 29 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="2" y="1" width="25.8667" height="20" rx="4" stroke="#494949" stroke-width="2"/>
                            <path d="M1.3667 4.7666L14.5667 12.4666L28.5 4.7666" stroke="#494949" stroke-width="2"/>
                        </svg>
                        You have <ChatsMessageCount></ChatsMessageCount> new messages.
                    </div>
                </router-link>
            </div>

            <div class="row m_b_14">
                <div class="col-12 text-start main_text title_mobile">
                    Your Active Jobs
                </div>
            </div>

            <div class="row" :class="window_desktop ? 'm_b_196' : 'm_b_103'">
                <div class="col-12 col-lg-6">
                    <div class="active_jobs_row" v-if="jobs.length">
                        <div
                            class="card_job"
                            :class="{card_job_s:isDoneJob(job)}"
                            v-if="jobs.length"
                            v-for="(job, index) in jobs"
                            :key="'job-card-' + index">
                            <div class="img_div"    @click="goToApproval(job)">
                                <img
                                    :src="mainPhoto(job)"
                                    class="card-img-top"
                                    :alt="'job-image-' + index">

                                <div class="name_div main_text text-center">
                                    {{ job.style_guide }}
                                </div>
                            </div>

                            <div class="loader_line">
                                <div class="done" :style="'width: ' + getProcentDoneJob(job) + '%;'"></div>
                            </div>

                            <div class="card_body main_text text-center">
                                {{ getFilesReady(job)}}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6 text-end">
                    <button @click="goAddNewJob" class="add_button">
                        <div class="icon" v-if="window_desktop">
                            <svg width="80" height="80" viewBox="0 0 80 80" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <line x1="39.5" y1="4.5" x2="39.5" y2="75.5" stroke="#494949" stroke-width="9"
                                      stroke-linecap="round"/>
                                <line x1="75.5" y1="39.5" x2="4.5" y2="39.5" stroke="#494949" stroke-width="9"
                                      stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div class="button_label main_text inbox" :class="window_desktop ? 'm_t_46' : ''">
                            Add New Job
                        </div>
                        <div class="mobile_icon" v-if="!window_desktop">
                            <svg width="20" height="19" viewBox="0 0 20 19" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <line x1="9.82642" y1="1.25" x2="9.82642" y2="17.75" stroke="#494949" stroke-width="2.5"
                                      stroke-linecap="round"/>
                                <line x1="18.4561" y1="9.25" x2="1.73252" y2="9.25" stroke="#494949" stroke-width="2.5"
                                      stroke-linecap="round"/>
                            </svg>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script>
import {mapMutations, mapGetters} from 'vuex';
import MainLayout from "@/layouts/MainLayout";
import TypeUserEnum from "@/enums/TypeUserEnum";
import ChatsMessageCount from "../components/ChatsMessageCount";
import {errorMessage} from "../services/messages";

export default {
    name: 'BusinessHomePage',
    components: {
        MainLayout,
        ChatsMessageCount
    },
    props: [],
    data() {
        return {
            sizeW: null,
            window_desktop: true,
            confirm_user_type: TypeUserEnum.BUSINESS,
            jobs: [],
            message_count: null,
            percent_done: 0,
            user_plan_name:null,
        }
    },
    created() {
        window.addEventListener('resize', this.updateWidth);
        this.updateWidth();
    },
    async mounted() {
        await this.testUser();
        await this.getJobs();
    },
    beforeDestroy() {
        window.removeEventListener("resize", this.updateWidth);
    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
        ]),
        mainPhoto(job){
           return job?.images[0]?.src_cropped||job?.images[0]?.src
        },
        getFilesReady(job) {
            let filter = job?.images?.filter(i => i.approval==1)
            let count_img = (job?.images?.length || 0) - filter.length;
            let count_finished_img = (job?.finished_worked_images?.length || 0) - filter.length;
            if (!count_img){
                return "All files approved"
            }
            return count_finished_img  + "/" + count_img + " files ready for approval"
        },
        getLengthImagesJob(job) {
            return job?.images?.length || 0
        },
        getCountFinishedImage(job) {
            return job?.finished_worked_images?.length || 0
        },
        getProcentDoneJob(job) {
            let filter = job?.images?.filter(i => i.approval==1)
            const countDoneJob = this.getCountFinishedImage(job) - filter.length;
            const countImagesJob = this.getLengthImagesJob(job) - filter.length;
            if (!countImagesJob){
                return 100
            }
            if (countDoneJob && countImagesJob) {
                return (countDoneJob / countImagesJob) * 100
            }
            return 0;
        },
        updateWidth() {
            this.sizeW = document.documentElement.clientWidth;
        },
        goHome() {
            this.$router.push({to: '/login', name: "Login"});
        },
        goAddNewJob() {
            if (!this.user_plan_name){
                errorMessage('Please purchase membership from Profile section to create a job.')
                return
            }
            this.$router.push({to: '/add-new-job', name: "AddNewJob"});
        },
        isDoneJob(job){
            if (this.getProcentDoneJob(job) === 100) {
               return true
            }
            return false
        },
        goToApproval(job) {
            if (job.status==='FINISHED'){
                this.$router.push('/job/'+ job.id + '/upload-files/');
                return
            }
            if (this.getProcentDoneJob(job) === 100) {
                this.$router.push('/file-approval/' + job.id);
            }
        },
        testUser() {
            this.user = JSON.parse(localStorage.getItem('strobeart_user'));

            if (!this.user || this.user.type_user !== this.confirm_user_type) {
                this.goHome();
            }
        },
        async getJobs() {
            this.showLoader();

            const result = await this.$http.getAuth(`${this.$http.apiUrl()}get-user-owned-jobs`);

            if (!result?.data?.success) {
                this.hideLoader();
                return errorMessage('OOPS... Something went wrong...');
            }

            this.jobs = result?.data?.data?.jobs || [];
            this.user_plan_name = result?.data?.data?.user?.plan?.plan || null;
            await this.percentDone();
            this.hideLoader();
        },
        percentDone() {
            if (this.jobs.length) {
            }
        },
    },
    computed: {
        ...mapGetters([
            'getUser'
        ]),
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

.body_active {
    height: 100%;
}

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

.inbox {
    font-weight: 600;
    font-size: 18px;
    line-height: 22px;
}

.title_desktop {
    font-weight: 600;
    font-size: 35px;
    line-height: 44px;
}

.title_mobile {
    font-weight: 600;
    font-size: 18px;
    line-height: 22px;
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

.message_text {
    font-weight: normal;
    font-size: 16px;
    line-height: 20px;
}

.item_body {
    width: 100%;
    min-height: 340px;
    padding: 11px;
}

.m_bt_16 {
    margin: 16px 0;
}

.time_button {
    width: 86px;
    height: 41px;
    border: 0.3px solid #494949;
    box-sizing: border-box;
    border-radius: 100px;
    padding: 10px;
    font-weight: 500;
    font-size: 12.5px;
    line-height: 20px;
}

.time_button .active {
    background: #D5C4B1;
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

.message_box {
    width: 414px;
    height: 65px;
    padding: 10px 36px;
    border-radius: 5px;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.15);

    svg {
        margin-right: 22px;
        min-width: 27.87px;
        min-height: 22px;
    }

}

.active_jobs_row {
    width: 100%;
    height: 300px;
    overflow: auto;
    white-space: nowrap;
}

.card_job {
    width: 249px !important;
    height: 272px !important;
    display: inline-block;
    padding: 12px;
    background-color: #FFFFFF;
    border-radius: 5px;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.15);
    margin-right: 7px;
}

.img_div {
    width: 225px;
    height: 217px;
    overflow: hidden;
    border-radius: 5px;
    margin-bottom: 12px;
    position: relative;

    img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
}

.name_div {
    text-transform: capitalize;
    overflow: hidden;
    text-overflow: ellipsis;
    width: 215px;
    font-weight: bold;
    font-size: 24px;
    line-height: 29px;
    position: absolute;
    top: 97px;
    left: 5px;
}

.loader_line {
    width: 170px;
    height: 7px;
    border: 0.2px solid #696969;
    box-sizing: border-box;
    border-radius: 10px;
    margin: 0 auto;
    overflow: hidden;
}

.done {
    height: 100%;
    background: #494949;
}
.card_job_s{
    cursor: pointer;
}
.card_body {
    width: 200px;
    height: 20px;
    margin: 5px auto;
    font-weight: normal;
    font-size: 10px;
    line-height: 12px;
}

.add_button {
    width: 249px;
    height: 272px;
    border: none;
    background: #FFFFFF;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.15);
    border-radius: 5px;
}

.in_center {
    display: flex;
    flex-direction: row;
    align-items: center;
}

.m_t_34 {
    margin-top: 34px;
}

.m_t_46 {
    margin-top: 46px;
}

.m_t_51 {
    margin-top: 51px;
}

.m_t_70 {
    margin-top: 70px;
}

.m_b_14 {
    margin-bottom: 14px;
}

.m_b_45 {
    margin-bottom: 45px;
}

.m_b_82 {
    margin-bottom: 82px;
}

.m_b_103 {
    margin-bottom: 103px;
}

.m_b_126 {
    margin-bottom: 126px;
}

.m_b_196 {
    margin-bottom: 196px;
}

@media only screen and (max-width: 992px) {
    .add_button {
        width: 100%;
        height: 65px;
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        padding: 0 30px;
        margin-top: 28px;
    }
    .message_box {
        width: 100%;
    }
}
</style>
