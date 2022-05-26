<template>
    <div>
        <MainLayout :is-show-header=!showJobDetailsMob>
            <div class="container_page">
                <JobDetails :job-details=job_details_d v-if="showJobDetailsMob"></JobDetails>
                <div v-if="!showJobDetailsMob" id="box_container_id" class="box_container">
                    <div class="">
                        <div class="pl-14 text-st1">Inbox</div>
                        <div class="pt-4 cp" @click="goToChats">
                            <div class="inbox">
                                <div class="me-4">
                                    <img class="" src="@/assets/icons/mail.svg" alt="icon-approved.svg"/>
                                </div>
                                <div class="inbox_text">You have <span class="count_message"><ChatsMessageCount></ChatsMessageCount></span> new messages.
                                </div>
                            </div>
                        </div>
                        <div class="pt-5 text-st1">Active Jobs</div>
                        <div class="pt-4">
                            <div class="input_box">
                                <input class="icon_input_search style_input1" v-model="search_key" @keyup="searchJob"
                                       type="text">
                                <img class="filter_icon" src="@/assets/icons/filter.svg" alt="icon-approved.svg"/>
                                <img class="search_icon" src="@/assets/icons/search.svg" alt="">
                            </div>
                        </div>
                        <div class="pt-4">
                            <div class="items_jobs">
                                <div v-for="(job, index) in jobs_search" ref="refJob" :key="index">
                                    <div class="item_j"  v-if="job!==job_details_d">
                                        <div class="box_i" @click="jobDetails(job,index)">
                                            <div class="box_image">
                                                <img class="job_image" v-lazy="getJobImages(job)[0]"
                                                     alt="icon-approved.svg"/>
                                            </div>
                                            <div class="ms-2 box_i2">
                                                <div class="title_item_job">{{ job.style_guide }}</div>
                                                <div class="description_item_j">
                                                    <div><span class="text_bold">Files:</span>
                                                        {{ getJobImages(job).length }}
                                                    </div>
                                                    <div><span class="text_bold">Editing Level:</span> {{
                                                            job.editing_level
                                                        }}
                                                    </div>
                                                    <div v-bind:class="{ text_red: diffDate(job.due_date)<3 }">
                                                        {{ diffDate(job.due_date) }} days left to complition
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bookmark" ref="bookmark" @click.prevent="bookmark(job,index)">
                                            <div v-if="!isMyWorkJob(job)"  ref="bookmark_img" class="img_flag_0"></div>
                                            <div v-else  ref="bookmark_img" class="img_flag_1"></div>
                                        </div>
                                    </div>
                                    <JobDetails @declineJob="declineJob" :index=index :job-details=job_details_d
                                                v-if="job===job_details_d&&!showJobDetailsMob"></JobDetails>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    </div>
</template>

<script>
import JobEditorTimerStep2 from "./JobEditorTimerStep2";
import {mapGetters, mapMutations} from 'vuex';
import {errorMessage} from "../services/messages";
import MainLayout from "../layouts/MainLayout";
import JobDetails from "./JobDetails";
import moment from 'moment-timezone'
import ChatsMessageCount from "../components/ChatsMessageCount";

export default {
    name: "HomePage",
    components: {
        MainLayout,
        JobDetails,
        JobEditorTimerStep2,
        ChatsMessageCount
    },
    data() {
        return {
            test1: null,
            search_key: null,
            job_details_d: {},
            jobs: {},
            jobs_search: {},
        };
    },
    mounted() {
        this.getJobs();
    },
    computed: {
        ...mapGetters([
            'getUser',
        ]),
        showJobDetailsMob() {
            return this.isMobile() && this.isJobDetails()
        },

    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
        ]),
        goToChats(){
            this.$router.push({name: 'ChatsList'}).then();
        },
        isMyWorkJob(job) {
            return job?.user_work?.user_id === this.getUser?.id
        },
       async bookmark(job,index) {
        },
        jobDetailsDel() {
            this.job_details_d = null
            ++this.test1
        },
        declineJob(data) {
            this.$refs.refJob[data].remove()
            this.job_details_d = {}
        },
        diffDate(date) {
            let end_job = moment(date).tz(Intl.DateTimeFormat().resolvedOptions().timeZone);
            let now_date = moment().tz(Intl.DateTimeFormat().resolvedOptions().timeZone).format("YYYY-MM-DD");
            let dif_finish_job = end_job.diff(now_date, 'days')
            if (dif_finish_job < 0) {
                return 0;
            }

            return dif_finish_job;
        },
        getJobImages(jobs) {
            if (jobs?.images || null) {
                return Object.values(jobs?.images).map(key => key.src_cropped??key.src);
            }
            return [];
        },
        searchJob(event) {
            let search_text = (event.target.value).toUpperCase()
            if (search_text && search_text.length > 1) {
                this.jobs_search = this.jobs.filter(job => (job.style_guide).toUpperCase().includes(search_text));
            } else {
                this.jobs_search = this.jobs
            }
        },
        dateJob(date) {
            return moment(date).format('MMM DD, YYYY')
        },
        async getJobs() {
            try {
                this.showLoader();
                const response = await this.$http.getAuth(`${this.$http.apiUrl()}jobs`);
                this.jobs = response?.data?.data || {}
                this.jobs_search = response?.data?.data || {}
                this.job_details_d = {}
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();
        },
        jobDetails(job1, index) {
            console.log(job1)
            this.job_details_d = job1
        },
        isMobile() {
            return window.innerWidth <= 992;
        },
        isJobDetails() {
            return !!Object.keys(this.job_details_d).length
        }
    },
}
</script>

<style lang="scss" scoped>

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

.container_page {
    //  display: flex;
    // justify-content: center;
    padding-top: 29px;
    //padding-bottom: 60px;
}

.pl-14 {
    padding-left: 14px;
}

.text-st1 {
    font-family: Montserrat;
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    line-height: 22px;
    display: flex;
    align-items: center;
    text-transform: capitalize;
    color: #494949;
}

.inbox {
    background: #FFFFFF;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.15);
    border-radius: 5px;
    display: flex;
    padding: 22px 36px;
    align-items: center;
}

.inbox_text {
    font-family: Montserrat;
    font-style: normal;
    font-weight: 400;
    font-size: 16px;
    line-height: 20px;
    color: #494949;
display: flex;
    gap:5px;
    .count_message {
        color: red;
    }
}

.style_input1 {
    font-family: Montserrat;
    font-style: normal;
    font-weight: 400;
    font-size: 16px;
    padding: 10px 13px;
    height: 35px;
    color: #494949;
    background: #FFFFFF;
    border: 0.3px solid #494949;
    box-sizing: border-box;
    border-radius: 100px;
    width: 100%;
}
.box_i{
    display: flex;
    width: 94%;
}
.box_i2{
    width: 80%;
}
.input_box {
    position: relative;
}

.search_icon {
    position: absolute;
    left: 16px;
    top: 10px;
    cursor: pointer;
}

.filter_icon {
    position: absolute;
    right: 16px;
    top: 10px;
    cursor: pointer;
}

.icon_input_search {
    padding-left: 40px;
    padding-right: 40px;
}

.item_j {
    position: relative;
    display: flex;
    padding: 8px 7px;
    background: #FFFFFF;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.15);
    border-radius: 5px;
    cursor: pointer;
}

.title_item_job {
    font-style: normal;
    font-weight: bold;
    font-size: 22px;
    line-height: 27px;
    letter-spacing: -0.02em;
    color: #494949;
    padding: 8px 0;
}

.description_item_j {
    font-style: normal;
    font-weight: 500;
    font-size: 13px;
    line-height: 18px;
    color: #494949;
}

.text_red {
    color: red;
}

.items_jobs {
    gap: 8px;
    display: flex;
    flex-direction: column;
    //height: 54vh;
    //overflow: auto;
}

.bookmark {
    position: absolute;
    top: 18px;
    right: 23px;
    cursor: pointer;
}

.description_item_j .text_bold {
    font-weight: bold;
}

.inbox, .input_box, .item_j {
    max-width: 50%;
}

.job_image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    filter: drop-shadow(0px 2px 2px rgba(0, 0, 0, 0.1));
    border-radius: 5px;
}

.box_image {
    width: 104px;
    height: 100px;
}
.bookmark img{
    width: 17px;
    height: 22px;
}
.bookmark {
    z-index: 998;
}
.img_flag_0{
    width: 17px;
    height: 22px;
    background: url("../assets/icons/bookmark-regular.svg");
}
.img_flag_1{
    width: 17px;
    height: 22px;
    background: url("../assets/icons/bookmark-regular2-filled.svg");
}
.non_click{
    pointer-events: none;
}
@media only screen and (min-width: 993px) {
    #box_container_id.box_container{
        padding-bottom: 85px;
    }
}
@media only screen and (max-width: 992px) {
    .container_page {
        margin-top: 0px;
        display: inherit;
    }
    .inbox {
        padding: 22px 20px;
    }
    .items_jobs{
        padding-bottom: 90px;
    }
    .app_logo {
        display: block;
    }
    .box_container {
        height: calc(100vh - 144px);
    }
    .inbox, .input_box, .item_j {
        width: 100%;
        max-width: 100%;
    }
    .container_page {
        padding-left: 15px;
        padding-right: 15px;
    }
    .box_i{
        display: flex;
        width: 88%;
    }
    .box_i2{
        width: 60%;
    }
}

</style>
