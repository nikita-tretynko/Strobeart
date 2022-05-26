<template>
    <MainLayout>
        <div class="container body_active file_approval">
            <div class="row" :class="window_desktop ? 'm_t_78' : 'm_t_16'">
                <div class="col-12 main_text" :class="window_desktop ? 'title_desktop' : 'title_mobile'">
                    File Approval
                </div>
            </div>

            <div class="row m_t_16">
                <div class="col-12 col-lg-6 main_text text-start"
                     :class="window_desktop ? 'description_desktop' : 'description_mobile'">
                    Please accept all files that are edited correctly.
                </div>
                <div class="col-12 col-lg-6 selected_text text-end"
                     :class="window_desktop ? 'description_desktop' : 'description_mobile'"
                     v-if="window_desktop">
                    {{ checked_images.length }} out of {{ job ? (job.images ? job.images.length : 0) : 0 }} pictures
                    selected
                </div>
            </div>

            <div class="row image_list m_t_55">
                <div
                    @click="openImageApprovalModal(image)"
                    class="col-4 div_item text-center"
                    :class="image.approval ? 'approval_image' : ''"
                    v-if="job.images.length"
                    v-for="(image, index) in job.images"
                    :key="'image-' + index">
                    <div
                        class="image_item text-center"
                        :class="image.approval ? 'approval_image' : ''"
                    >

                        <div class="img_div text-center">
                            <img :src="getWorkedImg(image)" :alt="'image-' + index">
                        </div>

                        <div class="div_svg"

                             v-if="!isCheckedImage(image.id)||image.approval">
                            <svg class="approval_svg" width="49" height="49" viewBox="0 0 49 49" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M49 24.5C49 30.9978 46.4188 37.2295 41.8241 41.8241C37.2295 46.4188 30.9978 49 24.5 49C18.0022 49 11.7705 46.4188 7.17588 41.8241C2.58124 37.2295 0 30.9978 0 24.5C0 18.0022 2.58124 11.7705 7.17588 7.17588C11.7705 2.58124 18.0022 0 24.5 0C30.9978 0 37.2295 2.58124 41.8241 7.17588C46.4188 11.7705 49 18.0022 49 24.5ZM36.8419 15.2206C36.6231 15.0026 36.3626 14.831 36.076 14.716C35.7894 14.601 35.4826 14.5449 35.1738 14.5512C34.865 14.5575 34.5607 14.626 34.279 14.7526C33.9973 14.8792 33.7441 15.0613 33.5344 15.288L22.8983 28.8396L16.4885 22.4267C16.0531 22.021 15.4772 21.8001 14.8821 21.8106C14.2871 21.8211 13.7194 22.0621 13.2985 22.483C12.8777 22.9038 12.6367 23.4715 12.6262 24.0666C12.6157 24.6616 12.8365 25.2375 13.2422 25.6729L21.3456 33.7794C21.5639 33.9973 21.8239 34.169 22.11 34.2842C22.3961 34.3995 22.7025 34.456 23.0108 34.4502C23.3192 34.4445 23.6233 34.3768 23.9049 34.251C24.1866 34.1252 24.44 33.944 24.6501 33.7181L36.8756 18.4363C37.2923 18.0029 37.5226 17.4234 37.5169 16.8222C37.5111 16.221 37.2699 15.646 36.8449 15.2206H36.8419Z"
                                    fill="#D8C3AF"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="row text-center m_t_130" :class="window_desktop ? 'm_b_120' : ''">
                <div class="col-6 text-end">
                    <button
                        :disabled="!checked_images.length"
                        @click="openModalDecline"
                        class="custom_button decline_button main_text title_mobile white_background">
                        Decline
                    </button>
                </div>
                <div class="col-6 text-start">
                    <button
                        :class="window_desktop ? 'approval_button' : 'decline_button'"
                        :disabled="!checked_images.length"
                        class="custom_button main_text title_mobile braun_background white_text"
                        @click="approvalImages()">
                        Accept
                    </button>
                </div>
            </div>

            <!-- MODAL FILE DECLINE -->
            <!-- Button trigger modal -->
            <button
                id="decline-modal"
                type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#declineModal"
                hidden>
                Launch decline modal
            </button>

            <!-- Modal -->
            <div class="modal fade" id="declineModal" tabindex="-1" aria-labelledby="declineModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content white_background">
                        <div class="modal-header feedback_header">
                            <div class="row black_text title_feedback in_center text-center align-content-center">
                                Help the editor make your file/s better by providing feedback                            </div>
                        </div>
                        <div class="modal-body feedback_body">
                            <div class="row text-center align-content-center">
                                <div class="col-12">
                                    <textarea class="feedback_text_message"
                                              @click="dropErrorDeclineMessage"
                                              required
                                              v-model="decline_message"
                                              id="textMessage"
                                              autocapitalize="sentences"
                                              :class="{ 'is-invalid': message_error }"
                                              placeholder="Remove the background; resize the image..."/>

                                    <div id="textMessageFeedback" class="invalid-feedback">
                                        Please leave a comment to help the editor to make your file/s better.
                                    </div>
                                </div>
                            </div>
                            <div class="row align-content-center m_t_55">
                                <div :class="window_desktop ? 'col-6 text-end' : 'col-12 text-center'">
                                    <button @click="cancelDeclineModal"
                                            data-bs-dismiss="modal"
                                            class="cancel_button white_background main_text custom_button keep_title">
                                        Cancel
                                    </button>
                                </div>
                                <div :class="window_desktop ? 'col-6 text-start' : 'col-12 text-center m_t_55 m_b_40'">
                                    <button @click="declineImages"
                                            class="send_button braun_background white_text custom_button keep_title">
                                        Send
                                    </button>
                                    <button id="cancel-decline" data-bs-dismiss="modal" hidden></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalDecline2" tabindex="-1" aria-labelledby="declineModalLabel2"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content white_background">
                        <div class="modal-header feedback_header">
                            <div class="row black_text title_feedback in_center text-center align-content-center">
                                You already declined this file once. If you are still not satisfied with the editing,
                                please leave a reaview about the editors so others will be aware of his/her skills.
                            </div>
                        </div>
                        <div class="modal-body feedback_body">
                            <div class="box_text row text-center align-content-center">
                                <div class="col-12">
                                    <textarea class="feedback_text_message"
                                              @click="dropErrorDeclineMessage"
                                              :class="{ 'is-invalid': message_error2 }"
                                              v-model="decline_review"
                                              id="textMessage"
                                              autocapitalize="sentences"
                                              placeholder="Write a review about [the name of the editor here]..."/>
                                    <div id="textMessageFeedback2" class="invalid-feedback">
                                        Please leave a comment to help the editor to make your file/s better.
                                    </div>
                                </div>
                            </div>
                            <div class="box_btn_md">
                                <div>
                                    <button @click="cancelDeclineModal"
                                            data-bs-dismiss="modal"
                                            class="cancel_button white_background main_text custom_button keep_title">
                                        Cancel
                                    </button>
                                </div>
                                <div>
                                    <button @click="sendReviewAccept(true)"
                                            data-bs-dismiss="modal"
                                            class="cancel_button white_background main_text custom_button keep_title">
                                        Skip
                                    </button>
                                </div>
                                <div>
                                    <button @click="sendReviewAccept()"
                                            class="send_button braun_background white_text custom_button keep_title">
                                        Send
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END MODAL FILE DECLINE -->

            <!-- MODAL FILE APPROVAL -->
            <!-- Button trigger modal -->
            <button
                id="approval-modal"
                type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#approvalModal"
                hidden>
                Launch approval modal
            </button>

            <!-- Modal -->
            <div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button @click="closeImageApprovalModal"
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div
                                class="modal_image"
                                v-if="modal_image"
                            >
                                <carousel :per-page="1" :mouse-drag="true" :touch-drag="true">
                                    <slide v-for="image in getImages(modal_image)">
                                        <img :src="image" alt="">
                                    </slide>
                                </carousel>
                                <!--                                <img :src="getImages(modal_image)[0]">-->
                                <button
                                    @click="toggleApproval(modal_image)"
                                    class="circle_check_point">
                                    <svg v-if="isCheckedImage(modal_image.id)" width="49" height="49"
                                         viewBox="0 0 49 49" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="18.5022" cy="18.8208" r="17.4768" fill="white" stroke="#AD967F"/>
                                    </svg>
                                    <svg v-else width="49" height="49" viewBox="0 0 49 49" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M49 24.5C49 30.9978 46.4188 37.2295 41.8241 41.8241C37.2295 46.4188 30.9978 49 24.5 49C18.0022 49 11.7705 46.4188 7.17588 41.8241C2.58124 37.2295 0 30.9978 0 24.5C0 18.0022 2.58124 11.7705 7.17588 7.17588C11.7705 2.58124 18.0022 0 24.5 0C30.9978 0 37.2295 2.58124 41.8241 7.17588C46.4188 11.7705 49 18.0022 49 24.5ZM36.8419 15.2206C36.6231 15.0026 36.3626 14.831 36.076 14.716C35.7894 14.601 35.4826 14.5449 35.1738 14.5512C34.865 14.5575 34.5607 14.626 34.279 14.7526C33.9973 14.8792 33.7441 15.0613 33.5344 15.288L22.8983 28.8396L16.4885 22.4267C16.0531 22.021 15.4772 21.8001 14.8821 21.8106C14.2871 21.8211 13.7194 22.0621 13.2985 22.483C12.8777 22.9038 12.6367 23.4715 12.6262 24.0666C12.6157 24.6616 12.8365 25.2375 13.2422 25.6729L21.3456 33.7794C21.5639 33.9973 21.8239 34.169 22.11 34.2842C22.3961 34.3995 22.7025 34.456 23.0108 34.4502C23.3192 34.4445 23.6233 34.3768 23.9049 34.251C24.1866 34.1252 24.44 33.944 24.6501 33.7181L36.8756 18.4363C37.2923 18.0029 37.5226 17.4234 37.5169 16.8222C37.5111 16.221 37.2699 15.646 36.8449 15.2206H36.8419Z"
                                            fill="#D8C3AF"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END MODAL FILE APPROVAL -->

            <!-- MODAL IMAGES ACCEPT -->
            <!-- Button trigger modal -->
            <button
                id="accept-modal"
                type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#acceptModal"
                hidden>
                Launch approval modal
            </button>

            <!-- Modal -->
            <div class="modal fade" id="acceptModal" tabindex="-1" aria-labelledby="acceptModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content white_background">
                        <div class="modal-header feedback_header">
                            <div class="row black_text title_feedback in_center text-center align-content-center">
                                Thank you for you feedback!
                            </div>
                        </div>
                        <div class="modal-body feedback_body">
                            <div
                                class="row black_text in_center message_feedback message_feedback_block text-center align-content-center">
                                The editor will resubmit the file/s ASAP.
                            </div>
                            <div class="box-btn row in_center align-content-center">
                                <button @click="approvalImages()"
                                        data-bs-dismiss="modal"
                                        class="btn_custom1 keep_approving_button braun_background white_text custom_button keep_title">
                                    Keep Approving Files
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="acceptModalReview" tabindex="-1" aria-labelledby="acceptModalLabel2"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content white_background">
                        <div class="modal-header feedback_header">
                            <div class="row black_text title_feedback in_center text-center align-content-center">
                                Thank you for you feedback!
                            </div>
                        </div>
                        <div class="modal-body feedback_body">
                            <div class="box-btn row in_center align-content-center">
                                <button @click="closeModelReview"
                                        data-bs-dismiss="modal"
                                        class="btn_custom1 keep_approving_button braun_background white_text custom_button keep_title">
                                    Keep Approving Files
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- END IMAGES ACCEPT MODAL -->
        </div>
    </MainLayout>
</template>
<script>
import {mapMutations, mapGetters} from 'vuex';
import MainLayout from "@/layouts/MainLayout";
import TypeUserEnum from "@/enums/TypeUserEnum";
import {errorMessage} from "../services/messages";
import {Modal} from "bootstrap";
import {Carousel, Slide} from 'vue-carousel';

export default {
    name: 'FileApproval',
    components: {
        MainLayout,
        Carousel,
        Slide
    },
    props: [],
    data() {
        return {
            sizeW: null,
            window_desktop: true,
            image_item_width: null,
            image_item_height: null,
            approval_svg_height: null,
            svg_padding_top: null,
            confirm_user_type: TypeUserEnum.BUSINESS,
            decline_message: null,
            job: {},
            selected: 0,
            message_error: false,
            message_error2: false,
            modal_image: null,
            modalDecline2: null,
            checked_images: [],
            decline_review: null,
            acceptModalReview: null,
            images: [],
            // modal_image: {
            //     id:null,
            //     index: null,
            //     src: null,
            //     checked: false,
            // },
        }
    },
    created() {
        window.addEventListener('resize', this.updateWidth);
        this.updateWidth();
        window.addEventListener('resize', this.updateImageItemWidth);
        window.addEventListener('resize', this.updateApprovalSvg);
    },
    async mounted() {
        await this.testUser();
        await this.getJobs();
        this.updateImageItemWidth();
        this.modalDecline2 = new Modal(document.getElementById('modalDecline2'))
        this.acceptModalReview = new Modal(document.getElementById('acceptModalReview'))
        //  this.updateApprovalSvg();
        let myModal = document.getElementById('approvalModal')
        myModal.addEventListener('shown.bs.modal', function () {
            setTimeout(() => {
                window.dispatchEvent(new Event('resize'));
            }, 0);
        })
    },
    beforeDestroy() {
        window.removeEventListener("resize", this.updateWidth);
        window.removeEventListener('resize', this.updateImageItemWidth);
        window.removeEventListener('resize', this.updateApprovalSvg);
        if (this.modalDecline2) {
            this.modalDecline2 = null
        }
        if (this.acceptModalReview) {
            this.acceptModalReview = null
        }
    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
        ]),
        getWorkedImg(image){
            return image?.worked_img?.image_url || image.src_cropped;
        },
        getImages(image) {
            let img = []
            if (image?.worked_img?.image_url || null) {
                img.push(image?.worked_img?.image_url || null)
            }
            img.push(image.src_cropped)
            return img
        },
        closeModelReview() {
            this.acceptModalReview.hide();
        },
        sendReviewAccept(skip = null) {
            if (skip) {
                this.approvalImages();
                return
            } else {
                if (!this.decline_review) {
                    this.message_error2 = true;
                    return
                }
                this.approvalImages(this.decline_review);
            }
            this.modalDecline2.hide()
        },

        isCheckedImage(img_id) {
            return !this.checked_images.includes(img_id);
        },
        updateWidth() {
            this.sizeW = document.documentElement.clientWidth;
        },
        updateImageItemWidth() {
            this.image_item_width = document.querySelector('.image_item').offsetWidth;
        },
        updateApprovalSvg() {
            // if (this.selected > 0) {
            //     this.approval_svg_height = document.querySelector('.div_svg').offsetHeight;
            // } else {
            //     let test_bloc_height = document.querySelector('.image_item').offsetHeight;
            //     this.approval_svg_height = test_bloc_height * 0.15;
            //     this.svg_padding_top = Math.ceil(test_bloc_height - (this.approval_svg_height + 16));
            // }
        },
        goHome() {
            this.$router.push({to: '/login', name: "Login"});
        },
        testUser() {
            this.user = JSON.parse(localStorage.getItem('strobeart_user'));

            if (!this.user || this.user.type_user !== this.confirm_user_type) {
                this.goHome();
            }
        },
        openImageApprovalModal(image) {
            this.modal_image = image
            // this.modal_image = {
            //     index: index,
            //     src: this.job.images[index].src,
            //     checked: this.job.images[index].approval,
            // };
            document.getElementById("approval-modal").click();
        },
        cleanModalImage() {
            this.modal_image = null;
            // this.modal_image = {
            //     index: null,
            //     src: null,
            //     checked: false,
            // };
        },
        toggleApproval(modal_image) {
            this.images = modal_image;
            if (!this.checked_images.includes(modal_image.id)) {
                this.checked_images.push(modal_image.id)
            } else {
                this.checked_images = this.checked_images.filter(function (item) {
                    return item !== modal_image.id
                })
                //this.checked_images.push(modal_image.id)
            }
            // this.modal_image.checked = !this.modal_image.checked;
            // if (this.modal_image.checked) {
            //     this.selected += 1;
            // } else {
            //     this.selected -= 1;
            // }
        },
        closeImageApprovalModal() {
            // this.job.images[this.modal_image.index].approval = this.modal_image.checked;
            this.cleanModalImage();
        },
        openAcceptModal() {
            document.getElementById("accept-modal").click();
        },

        openModalDecline() {
            // if (this.selected === this.job.images.length) {
            //     return;
            // }
            let decline_img = this.isDeclineImage()
            if (decline_img.length) {
                let filter1 = this.checked_images.filter(i => !decline_img.includes(i))
                if (!filter1.length) {
                    this.modalDecline2.show()
                    return
                }
                this.checked_images = this.checked_images.filter(function (item) {
                    return !decline_img.includes(item)
                })
            }
            document.getElementById("decline-modal").click();

        },
        cancelDeclineModal() {
            this.decline_message = null;
            this.modalDecline2.hide();
            this.decline_review = null;
        },
        updateSelected() {
            // this.selected = 0;
            //
            // if (this.job && this.job.images.length) {
            //     for (let i = 0; i < this.job.images.length; i++) {
            //         if (this.job.images[i].approval) {
            //             this.selected += 1;
            //         }
            //     }
            // }
        },
        dropErrorDeclineMessage() {
            this.message_error = false;
            this.message_error2 = false;
        },
        isDeclineImage() {
            let decline_img_id = []
            let images = this.job?.images || {}
            Object.keys(images).map(function (key, index) {
                if (images[key]['decline'] == 2) {
                    decline_img_id.push(images[key]['id'])
                }
            });
            return decline_img_id;

        },
        async declineImages() {

            if (!this.decline_message) {
                this.message_error = true;
                return;
            }
            document.getElementById("cancel-decline").click();

            try {
                this.showLoader();
                await this.$http.postAuth(`${this.$http.apiUrl()}decline-image`, {
                    job_id: this.job.id,
                    message: this.decline_message,
                    checked_images: this.checked_images
                });
                this.decline_message = null;
                this.checked_images = []
                this.goHome();
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();

        },
        async approvalImages(review = null) {
            this.showLoader();
            try {
               const resp = await this.$http.postAuth(`${this.$http.apiUrl()}approval-image`, {
                    job_id: this.job.id,
                    checked_images: this.checked_images,
                    review: review
                });
               if (resp?.data?.data?.status =='FINISHED'){
                   this.goHome();
               }
                this.checked_images = []
                await this.getJobs();
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();
            if (review) {
                this.acceptModalReview.show();
                this.decline_message = null
            }
        },
        async checkResult(result) {
            if (!result?.data?.success) {
                this.hideLoader();
                return errorMessage('OOPS... Something went wrong...');
            }

            this.job = result?.data?.data?.job || {};

            this.updateSelected();

            this.hideLoader();
        },
        async saveResult() {
            const res = await this.$http.postAuth(
                `${this.$http.apiUrl()}approval-or-decline`,
                {
                    job: this.job,
                    message: this.decline_message,
                    filtered: true
                }
            );

            await this.checkResult(res);
        },
        async getJobs() {
            this.showLoader();
            if (!this.$route.params.id) {
                return;
            }

            const result = await this.$http.getAuth(`${this.$http.apiUrl()}get-job/${this.$route.params.id}?filtered=true`);

            await this.checkResult(result);
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
        image_item_width(val) {
            if (val && val !== 0) {
                this.image_item_height = Math.ceil(val / 1.04);

                this.svg_padding_top = Math.ceil(this.image_item_height - (this.approval_svg_height + 16));
            }
        },
    }
}
</script>
<style lang="scss">
.file_approval {
    .VueCarousel {
        height: 100%;
        flex-direction: unset;
    }

    .VueCarousel-pagination {
        position: absolute;
        bottom: -50px;
    }

    .VueCarousel-inner {
        height: 100% !important;
        flex-basis: unset !important;
    }

    .VueCarousel-slide {
        width: 100% !important;
        height: 100% !important;
    }
}

/*.VueCarousel-slide {*/
/*    visibility: visible;*/
/*    flex-basis: 100%;*/
/*    width:100%;*/
/*}*/
</style>
<style lang="scss" scoped>
$primary_font_family: 'Montserrat', sans-serif;
.modal-dialog {
    max-width: 692px;
}

#modalDecline2 .modal-dialog {
    max-width: 846px;
}

#textMessage::first-letter {
    text-transform: capitalize;
}

.is-invalid {
    border: 2px solid #dc3545 !important;
}

.body_active {
    height: 100%;
}

.main_text {
    color: #494949;
    font-style: normal;
    font-family: $primary_font_family;
}

.selected_text {
    color: #AD967F;
    font-style: normal;
    font-family: $primary_font_family;
}

.white_text {
    color: #FFFFFF;
    font-style: normal;
    font-family: $primary_font_family;
}

.black_text {
    color: black;
    font-style: normal;
    font-family: $primary_font_family;
}

.title_feedback {
    font-weight: 600;
    font-size: 24px;
    line-height: 44px;
    padding: 30px;
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

.keep_title {
    font-family: 'Montserrat';
    font-style: normal;
    font-weight: 700;
    font-size: 30px;
    line-height: 37px;
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

.message_feedback {
    font-weight: 400;
    font-size: 24px;
    line-height: 24px;
}

.message_feedback_block {
    margin-top: 28px;
    min-height: 155px;
}

.braun_background {
    background: #D8C3AF;
}

.white_background {
    background: #FFFFFF !important;
    opacity: 0.9;
}

.in_center {
    margin: 0 auto;
    display: flex;
    justify-content: center;
    align-items: center;
}

.feedback_text_message {
    width: 100%;
    min-height: 174px;
    background: #E9EAEB;
    border: none;
    border-radius: 3px;
    padding: 30px;
}

.image_list {
    width: 100%;

    .div_item {
        padding: 10px;
    }

    .image_item {
        border: none !important;
        border-radius: 5px;
        box-sizing: border-box;
        box-shadow: 0 2px 2px rgba(0, 0, 0, 0.25);
        padding: 12px;
        cursor: pointer;
        background: #FFFFFF;
        position: relative;
    }

    .img_div {
        width: 100%;
        height: 100%;
        border-radius: 5px;
        overflow: hidden;
        position: relative;
    }

    .div_svg {
        width: 12%;
        height: 12%;
        position: absolute;
        left: calc(83% - 8px);
        bottom: 25px;

        svg {
            width: 100% !important;
            height: 100% !important;
        }
    }

    img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .approval_image {
        opacity: 0.6;
        filter: drop-shadow(0px 3px 3px rgba(0, 0, 0, 0.15));
        pointer-events: none;
    }
}

.modal-content {
    background: none;
    border: none;
}

.modal-header {
    border: none;
    padding-bottom: 0;
}

.modal-header {
    padding: 0;
}

.modal_image {
    width: 100%;
    padding: 12px;
    border-radius: 5px;
    box-sizing: border-box;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.25);
    background: #FFFFFF;
    position: relative;

    img {
        width: 100%;
        border-radius: 5px;
    }

    .circle_check_point {
        border: none !important;
        background: none;
        position: absolute;
        right: 17px;
        bottom: 22px;
    }
}

.custom_button {
    border: 0.5px solid #494949;
    box-sizing: border-box;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.25);
    border-radius: 100px;
}

.keep_approving_button {
    width: 451px;
    height: 90px;
}

.decline_button {
    width: 150px;
    height: 60px;
}

.approval_button {
    width: 215px;
    height: 60px;
}

.cancel_button {
    width: 172px;
    height: 72px;
}

.send_button {
    width: 210px;
    height: 72px;
}

button:disabled {
    opacity: 0.6;
    filter: drop-shadow(0px 3px 3px rgba(0, 0, 0, 0.15));
}

.feedback_header {

}

.feedback_body {
    min-height: 350px;
    border-top: 1px solid #49494945;
    transform: matrix(1, 0, 0, 1, 0, 0);
}

.m_t_130 {
    margin-top: 130px;
}

.m_t_78 {
    margin-top: 78px;
}

.m_t_55 {
    margin-top: 55px;
}

.m_t_16 {
    margin-top: 16px;
}

.m_t_10 {
    margin-top: 10px;
}

.m_b_40 {
    margin-bottom: 40px;
}

.m_b_55 {
    margin-bottom: 55px;
}

.m_b_120 {
    margin-bottom: 120px;
}

.box_btn_md {
    display: flex;
    justify-content: space-around;
    padding-top: 20px;
}

#modalDecline2 .title_feedback {
    padding: 30px 77px 5px 77px;
}

#modalDecline2 .modal-body {
    margin: 30px 77px;
}

#modalDecline2 .box_text {
    padding-top: 10px;
}

#acceptModalReview .box-btn {
    padding-top: 28px;
}

#acceptModalReview .modal-body.feedback_body {
    min-height: 190px;
}

@media only screen and (max-width: 580px) {

}

@media only screen and (max-width: 992px) {
    #acceptModalReview .box-btn {
        padding-top: 0px;
    }
    #acceptModalReview .modal-body.feedback_body {
        min-height: 145px;
    }
    #modalDecline2 {
        .send_button, .cancel_button {
            width: 107px;
        }

        .box_btn_md {
            display: flex;
            justify-content: space-between;
            padding-top: 10px;
        }
    }
    #modalDecline2 .title_feedback {
        padding: 0;
        padding-top: 10px;
    }
    #modalDecline2 .modal-body {
        margin: 0;
    }
    .body_active {
        min-height: calc(100vh - 115px);
        padding-bottom: 90px;
    }
    .feedback_header {
        padding: 12px;
    }
    .image_list .div_svg {
        left: calc(83% - 6px);
    }
    .div_svg {
        top: 71px !important;
    }
    .image_list .image_item {
        padding: 4px;
    }
    .modal-dialog {
        max-width: 95%;
    }
    .keep_title {
        font-size: 18px;
        line-height: 22px;
    }
    .title_feedback {
        font-style: normal;
        font-weight: 600;
        font-size: 18px;
        line-height: 22px;
        padding: 12px;
    }
    .btn_custom1 {
        height: 50px;
    }
    .box-btn {
        padding: 0 10px;
        width: 100%;
    }
    .feedback_body {
        display: flex;
        flex-direction: column;
        justify-content: space-around;
    }
    .keep_approving_button {
        width: 100%;
    }

}
</style>
