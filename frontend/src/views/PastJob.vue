<template>
       <main-layout :is-show-header=false>
           <div class="box_page">
               <div class="title_header">Past Jobs</div>
               <div class="pt-4">
                   <div class="input_box">
                       <input class="icon_input_search style_input1" v-model="search_key" @keyup="searchJob"
                              type="text">
                       <img class="filter_icon" src="@/assets/icons/filter.svg" alt="icon-approved.svg"/>
                       <img class="search_icon" src="@/assets/icons/search.svg" alt="">
                   </div>
               </div>
               <div class="jobs_box">
                   <div class="item_job" @click="sheduleFile(job)" v-for="job in jobs_search">
                       <div class="item_j">
                           <div class="box_i">
                               <div class="box_image">
                                   <img class="job_image" v-lazy="getJobImages(job)[0]"
                                        alt="icon-approved.svg"/>
                               </div>
                               <div class="ms-2 box_i2">
                                   <div class="description_item_j">
                                       <div v-if="job.style_guide"><span class="text_bold">Style Guide:</span>
                                           {{ job.style_guide }}
                                       </div>
                                       <div><span class="text_bold">Files:</span>
                                           {{ getJobImages(job).length }}
                                       </div>
                                       <div><span class="text_bold">Editing Level:</span> {{
                                               job.editing_level
                                           }}
                                       </div>
                                       <div><span class="text_bold">Acceptance:</span>
                                               {{ acceptance(job) }}%
                                       </div>
                                       <div><span class="text_bold">Time Frame:</span>
                                               {{ timerFrame(job) }} min/avg
                                       </div>

                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </main-layout>
</template>

<script>

import MainLayout from "../layouts/MainLayout";
import {errorMessage} from "../services/messages";
import {mapMutations} from "vuex";
import TypeUserEnum from "@/enums/TypeUserEnum";

export default {
    name: 'PastJob',
    components: {
        MainLayout,
    },
    data() {
        return {
            past_jobs:{},
            search_key:null,
            jobs_search: {},
            is_type_user: null,
            business: TypeUserEnum.BUSINESS,
            editor: TypeUserEnum.EDITOR,
        }
    },
    mounted() {
        this.pastJobs()
    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
        ]),
        getJobImages(job) {
            if (job?.images || null) {
                return Object.values(job?.images).map(key => key.src_cropped??key.src);
            }
            return [];
        },
        async pastJobs() {
            try {
                this.showLoader();
                const response = await this.$http.getAuth(`${this.$http.apiUrl()}past-job`);
                this.past_jobs = response?.data?.data?.past_jobs|| {}
                this.jobs_search = response?.data?.data?.past_jobs|| {}
                this.is_type_user = response?.data?.data?.user?.type_user||null
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();
        },
        sheduleFile(job) {
            if (this.is_type_user == this.editor) {
                return
            }
            this.$router.push('/job/' + job.id + '/upload-files/');
        },
        acceptance(job) {
            return 100 - (job.images_decline.length * 100) / job.images.length
        },
        timerFrame(job) {
            return parseInt((job.finished_worked_images_sum_sum_timers / job.images.length) / 60)
        },
        searchJob(event) {
            let search_text = (event.target.value).toUpperCase()
            if (search_text && search_text.length > 1) {
                this.jobs_search = this.past_jobs.filter(job => (job.style_guide).toUpperCase().includes(search_text));
            } else {
                this.jobs_search = this.past_jobs
            }
        },
    },
    computed: {}
}
</script>
<style lang="scss" scoped>
.box_page{
    padding: 70px 15px 120px 15px;
}
.title_header{
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    line-height: 22px;
    display: flex;
    align-items: center;
    text-transform: capitalize;
    color: #494949;
    padding-left: 10px;
}
.jobs_box {
    padding-top: 20px;
    gap: 8px;
    display: flex;
    flex-direction: column;
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
.description_item_j .text_bold {
    font-weight: bold;
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
.box_i{
    display: flex;
    width: 88%;
}
.input_box {
    position: relative;
}
.icon_input_search {
    padding-left: 40px;
    padding-right: 40px;
}
.style_input1 {
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
    padding-left: 40px;
}
.filter_icon {
    position: absolute;
    right: 16px;
    top: 10px;
    cursor: pointer;
}
.search_icon {
    position: absolute;
    left: 16px;
    top: 10px;
    cursor: pointer;
}
</style>
