<template>
    <div class="details_job">
        <div class="container">
            <div class="row">
                <div class="col-12 col-xl-4 order_2">
                    <div class="title m_title">Work Sample</div>
                    <div class="image pt18">
                        <carousel :per-page="1" :mouse-drag="true" :touch-drag="true">
                            <slide v-for="image in getJobImages(jobDetails)">
                                <img v-lazy="image" alt="">
                            </slide>
                        </carousel>

                    </div>
                </div>
                <div class="col-12 col-lg-4 order_1">
                    <div class="title">{{getBusinessName}}</div>
                    <div class="desc2 mt60">{{getBusinessBio}}
                    </div>
                    <div class="date_desc mt-4"><span
                        class="date_desc_t">Due Date </span>{{ dateJob(jobDetails.due_date) }}
                    </div>
                </div>
                <div class="col-12 col-lg-4 order_3">
                    <div class="title m_title mpt5">Requirements</div>
                    <div class="dt_row">
                        <div class="mt-4  info_box">
                            <div class="job_info_item">
                                <div class="title_2">Editing Level:</div>
                                <div class="desc2 title_3 msdt">{{ jobDetails.editing_level }}</div>
                            </div>
                            <div class="job_info_item">
                                <div class="title_2">Style Guide:</div>
                                <div class="desc2 title_3 msdt">{{ jobDetails.style_guide }}</div>
                            </div>
                            <div class="job_info_item">
                                <div class="title_2">Add Logo:</div>
                                <div class="desc2 title_3 msdt">{{ jobDetails.add_logo ? 'Yes' : 'No' }}</div>
                            </div>
                            <div class="job_info_item">
                                <div class="title_2">Background:</div>
                                <div class="desc2 title_3 msdt">{{ jobDetails.white_background ? 'White' : 'No' }}</div>
                            </div>
                            <div class="job_info_item">
                                <div class="title_2">Resolution:</div>
                                <div class="desc2 title_3 msdt">300 ppi</div>
                            </div>
                            <div class="job_info_item">
                                <div class="title_2">Color Profile:</div>
                                <div class="desc2 title_3 msdt">{{ jobDetails.color_profile }}</div>
                            </div>
                            <div class="job_info_item">
                                <div class="title_2">Size:</div>
                                <div class="desc2 title_3 msdt">300 x 300 px</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="order_4">
                    <span class="date_desc_t title m_title">Due Date</span>
                    <div class="date_desc_mob ms-4 order_4 title_3">{{ dateJob(jobDetails.due_date) }}</div>
                </div>
            </div>
        </div>
        <div class="btns_box">
            <button class="btn-mc3 wh-btn" @click="declineJob">Decline</button>
            <button v-if="!isMyWorkJob" class="btn-mc2 wh-btn" @click="acceptJob">Accept</button>
            <button v-else class="btn-mc2 wh-btn" @click="acceptJob">Start</button>
        </div>
        <div class="modal fade " id="addErrorModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body d-flex justify-content-center">
                        {{message_error}}    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <button type="button" class="btn-mc3 w110" @click="reloadPage">Okay</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import MainLayout from "../layouts/MainLayout";
import {mapGetters, mapMutations} from 'vuex';
import {errorMessage} from "../services/messages";
import moment from 'moment-timezone'
import {Carousel, Slide} from 'vue-carousel';
import RegistrationEnum from "../enums/RegistrationEnum";
import {Modal} from "bootstrap";

export default {
    name: "JobDetails",
    props: ['jobDetails','index'],

    components: {
        MainLayout,
        Carousel,
        Slide
    },
    data() {
        return {
            modalError: null,
            message_error: null,
        };
    },
    mounted() {
        this.modalError = new Modal(document.getElementById('addErrorModal'))
    },
    destroyed() {
        if (this.modalError) {
            this.modalError = null
        }
    },
    computed: {
        ...mapGetters([
            'getUser',
        ]),
        getBusinessName(){
            return  this.jobDetails?.user?.business_name||'';
        },
        getBusinessBio(){
            return this.jobDetails?.user?.bio||''
        },
        isMyWorkJob() {
            return this.jobDetails?.user_work?.user_id === this.getUser?.id
        },
    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
        ]),
        async declineJob(){
            try {
                this.showLoader();
                await this.$http.postAuth(`${this.$http.apiUrl()}decline-job/${this.jobDetails.id}`);
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();
            this.$emit('declineJob',
                this.index,
            )
        },
        reloadPage(){
            this.$router.go(0);
        },
      async acceptJob(){
            try {
                this.showLoader();
                const response = await this.$http.postAuth(`${this.$http.apiUrl()}accept-job`,{
                    'job_id':this.jobDetails.id
                });
                this.$router.push({path: `/working-job/${this.jobDetails.id}`}).then();

            } catch (e) {
                 this.message_error = e?.response?.data?.error?.message || 'ERROR';
                this.modalError.show()
               // errorMessage(message)
            }
            this.hideLoader();
        },
        dateJob(date) {
            return moment(date).format('MMM DD, YYYY')
        },
        getJobImages(jobs) {
            if (jobs?.images || null) {
                return Object.values(jobs?.images).map(key => key.src_cropped??key.src);
            }
            return [];
        },
    }
}
</script>
<style lang="scss">
.details_job {
    .VueCarousel {
        height: 100%;
        flex-direction:unset;
    }
    .VueCarousel-pagination {
        position: absolute;
        bottom: -50px;
    }
    .VueCarousel-inner{

        height: 100%!important;
        flex-basis:unset!important;
    }
    .VueCarousel-slide{
        width: 100%!important;
        height: 100%!important;
    }
}
</style>
<style lang="scss" scoped>
.mt60 {
    margin-top: 28px;
}

.details_job {
    padding: 68px 30px 73px 30px;
    background: #FFFFFF;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.15);
    border-radius: 5px;
}

.title {
    font-style: normal;
    font-weight: 600;
    font-size: 24px;
    line-height: 44px;
    color: #494949;
}

.details_job .image {
    padding: 11px 13px;
    width: 332px;
    height: 318px;
    background: #FFFFFF;
    border-radius: 5px;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.15);
}

.details_job img {
    width: 100%;
    object-fit: cover;
    height: 100%;
}

.title_2 {
    width: 118px;
    text-align: right;
    font-style: normal;
    font-weight: 600;
    font-size: 17px;
    line-height: 40px;
    color: #000000;
}

.job_info_item {
    display: flex;
    align-items: center;
}

.desc2 {
    font-style: normal;
    font-weight: normal;
    font-size: 17px;
    color: #000000;
}

.date_desc_t {
    font-style: normal;
    font-weight: 600;
    font-size: 15px;
    line-height: 18px;
    color: #494949;
}

.date_desc {
    font-style: normal;
    font-weight: 500;
    font-size: 15px;
    line-height: 18px;
    color: #494949;
}

.order_4 {
    display: none;
}

.pt18 {
    margin-top: 18px;
}

.wh-btn {
    width: 148px;
    height: 47px;
}

.btns_box {
    width: 100%;
    display: flex;
    justify-content: center;
    gap: 22px;
    padding-top: 60px;
}
.w110 {
    width: 110px;
}
.msdt{
    margin-left: 24px;
}
.modal-header, .modal-footer {
    border-bottom: 0px solid #dee2e6;
    border-top: 0px solid #dee2e6;
}
#addErrorModal{
    z-index: 99999;
}
@media only screen and (max-width: 992px) {
    .msdt{
        margin-left: 64px;
    }
    .info_box{
        width: 100%;
    }
    .m_title{
        font-style: normal;
        font-weight: 500;
        font-size: 15px;
        line-height: 18px;
        color: #494949;
    }
    .mpt5{
        padding-top:20px ;
    }
    .order_1 {
        order: 1;
        padding-bottom: 43px;
    }
    .order_2 {
        order: 2;
        padding-bottom: 23px;

    }
    .order_3 {
        order: 3;
    }
    .order_4 {
        display: block;
        order: 4;
        padding-top: 23px;
    }
    .btns_box {
        width: 100%;
        display: flex;
        justify-content: center;
        gap: 12px;
        padding-top: 60px;
        padding-bottom: 120px;
    }
    .wh-btn {
        width: 150px;
        height: 60px;
    }
    .details_job {
        background: #F4F2F2;
        padding: 68px 0 0 0
    }
    .dt_row {
        display: flex;
        justify-content: center;
    }
    .date_desc {
        display: none;
    }
    .date_desc_mob {
        display: block;
        font-style: normal;
        font-weight: 500;
        font-size: 15px;
        line-height: 18px;
        color: #494949;
    }
    .desc2 {
        font-style: normal;
        font-weight: 500;
        font-size: 12.5px;
        line-height: 20px;
        color: #000000;
    }
    .title_2{
        font-style: normal;
        font-weight: 500;
        font-size: 12.5px;
        line-height: 25px;
        color: #494949;
    }
    .title_3{
        font-style: normal;
        font-weight: 400;
        font-size: 12.5px;
        line-height: 25px;
        color: #494949;
    }
}

</style>
