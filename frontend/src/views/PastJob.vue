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
                   <template v-if="isEmptyObject(jobs_search)">
                   <div class="item_job"  v-for="job in jobs_search">
                       <div class="item_j">
                           <div class="box_i">
                               <div @click="sheduleFile(job)" class="box_image">
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
                                       <template v-if="is_type_user===business">
                                           <div v-if="!isEmptyObject(job.review)" @click="openReviewModal(job)"
                                                class="leave-review">
                                               Leave Review
                                           </div>
                                           <div class="rating_job" v-else>
                                               <star-rating
                                                   v-model="job.review.rating"
                                                   :show-rating="false"
                                                   v-bind:star-size="14"
                                                   inactive-color="#D1D2D3"
                                                   active-color="#D8C3AF"
                                                   :read-only="true"
                                               >
                                               </star-rating>
                                           </div>
                                       </template>

                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
                   </template>
                   <div v-else class="empty_jobs">Here will be your jobs.</div>
               </div>
           </div>
           <div class="modal" id="reviewModal" tabindex="-1">
               <div class="modal-dialog modal-dialog-centered">
                   <div class="modal-content">
                       <div class="modal-header">
                           <h5 class="modal-title review_title">Tell everyone how was it to work with
                               {{ add_review.editor_name }}</h5>
                       </div>
                       <div class="modal-body">
                           <div class="box_body">
                               <star-rating
                                   v-model="add_review.rating"
                                   :show-rating="false"
                                   v-bind:star-size="22"
                                   inactive-color="#D1D2D3"
                                   active-color="#D8C3AF"
                               >
                               </star-rating>
                               <textarea rows="4" v-model="add_review.message" placeholder="Leave a review..."></textarea>
                           </div>
                       </div>
                       <div class="modal-footer">
                           <button class="btn_modal_t1" data-bs-dismiss="modal">Cancel</button>
                           <button class="btn_modal_t2" @click="postReview">Post</button>
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
import {Modal} from "bootstrap";
import StarRating from 'vue-star-rating'

export default {
    name: 'PastJob',
    components: {
        MainLayout,
        StarRating
    },
    data() {
        return {
            past_jobs:{},
            search_key:null,
            jobs_search: {},
            is_type_user: null,
            business: TypeUserEnum.BUSINESS,
            editor: TypeUserEnum.EDITOR,
            reviewModal: null,
            add_review: {
                editor_name: '',
                rating: 0,
                editor_id: null,
                image_job_id: null,
                message: '',
            }
        }
    },
    destroyed() {
        if (this.reviewModal) {
            this.reviewModal.dispose()
        }
    },
    mounted() {
        this.pastJobs()
        this.reviewModal = new Modal(document.getElementById('reviewModal'))

    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
        ]),
        isEmptyObject(obj) {
            if (obj) {
                return Object.keys(obj).length !== 0;
            }
            return 0;
        },
        async postReview() {
            if (!this.add_review.rating) {
                errorMessage('Rating is required')
                return
            }
            try {
                this.showLoader();
                await this.$http.postAuth(`${this.$http.apiUrl()}create-review`, {
                    'to_user_id': this.add_review.editor_id,
                    'job_image_id': this.add_review.image_job_id,
                    'message': this.add_review.message,
                    'rating': this.add_review.rating,
                });
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            await this.pastJobs()
            this.hideLoader();
            this.clearAddReview();
            this.reviewModal.hide();
        },
        openReviewModal(job) {
            this.add_review.editor_id = job?.user_work?.user_id || null
            this.add_review.image_job_id = job.id
            this.add_review.editor_name = job?.user_work?.user?.first_name || ''
            this.reviewModal.show();
        },
        clearAddReview() {
            this.add_review.rating = 0;
            this.add_review.editor_id = null;
            this.add_review.image_job_id = null;
            this.add_review.message = '';
        },
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
.empty_jobs{
    padding-top: 100px;
    text-align: center;
    font-style: normal;
    font-weight: 500;
    font-size: 18px;
    line-height: 22px;
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
.leave-review {
    font-style: normal;
    font-weight: 400;
    font-size: 14px;
    display: flex;
    align-items: center;
    text-decoration-line: underline;
    color: #494949;
}

.review_title {
    padding: 15px 0;
    text-align: center;
}
.box_body {
    background: #f4f5f5;
    border-radius: 3px;
    padding: 16px 30px;
}

.box_body textarea {
    border: 0px;
    resize: vertical;
    width: 100%;
    background: #f4f5f5;
    margin-top: 15px;
    font-style: normal;
    font-weight: 400;
    font-size: 18px;
    line-height: 24px;
    color: rgba(73, 73, 73, 0.65);
}
.rating_job {
    display: flex;
}

.btn_modal_t1 {
    border: 0.5px solid #494949;
    box-sizing: border-box;
    border-radius: 100px;
    background: white;
    height: 47px;
    width: 148px;
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #494949;
}

.btn_modal_t2 {
    background: #D8C3AF;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    border-radius: 100px;
    color: white;
    height: 47px;
    width: 148px;
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.modal-footer {
    border-top: none;
    display: flex;
    justify-content: center;
    padding-bottom: 30px;
    gap: 15px;
}
</style>
