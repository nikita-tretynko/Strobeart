<template>
    <MainLayout :vh_header="true" :is-show-header=!showHeaderMob >
        <div class="body_active">
            <div class="container container_body">
                <div class="pl-15 row">
                    <div class="col-12 main_text m_bt_16" :class="window_desktop ? 'title_desktop' : 'title_mobile'">
                        <span>
                            Add New Job
                        </span>
                    </div>
                </div>
                <div class="pl-15 row m_b_56" v-if="!window_desktop">
                    <div class="col-12 main_text description_mobile">
                        <span>
                            To add new job follow the steps bellow.
                        </span>
                    </div>
                </div>

                <!-- TABS -->
                <div class="row row_tab justify-content-between m_b_30" v-if="window_desktop">
                    <div
                        class="col main_text tab_title text-center"
                        v-for="(tab, index) in tabs"
                        :key="'tab-' + index"
                        :class="index !== active_tab ? 'no_active_title_tab' : ''"
                        @click="selectTab(index)">
                            {{tab.title}}
                    </div>
                </div>

                <div class="row align-items-center" v-if="window_desktop">
                    <div
                        class="col main_text tab_title text-center tab_item"
                        v-for="(tab, index) in tabs"
                        :key="'tab-border-' + index"
                        :class="index === active_tab ? 'active_tab' : ''">
                    </div>
                </div>

                <div
                    class="row m_t_23"
                    :class="active_tab === tabs_indexes.add_file ? 'm_b_89' : (active_tab === tabs_indexes.add_requirements ? 'm_b_100' : 'm_b_56')"
                    v-if="window_desktop">
                    <div class="col-12 text-start main_text description_desktop">
                        {{tabs[active_tab].description}}
                    </div>
                </div>
                <!-- END TABS -->

                <!-- ADD FILES DESKTOP -->
                <div class="row" v-if="window_desktop && active_tab === tabs_indexes.add_file">
                    <AddFile
                        :images="images"
                        :files="files"
                        v-on:next-tab="nextTab"/>
                </div>
                <!-- END ADD FILES DESKTOP -->

                <!-- ADD FILES MOBILE -->
                <div class="m_b_15" v-if="!window_desktop">
                    <button
                        class="col-12 drop_button"
                        :class="bodies.add_file_body ? 'drop_button_radius_open' : 'drop_button_radius_closed'"
                        @click="addBody('add_file_body')">
                        <div class="row">
                            <div class="col-10 text-start main_text title_mobile">
                                Add Files
                            </div>
                            <div class="col-2 text-end">
                                <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18.8528 6.94727C18.6066 6.70121 18.2729 6.56299 17.9248 6.56299C17.5768 6.56299 17.243 6.70121 16.9969 6.94727L10.5 13.4441L4.00315 6.94727C3.75561 6.70819 3.42407 6.5759 3.07994 6.57889C2.73581 6.58188 2.40661 6.71991 2.16327 6.96326C1.91992 7.20661 1.78189 7.5358 1.7789 7.87993C1.77591 8.22406 1.9082 8.5556 2.14728 8.80314L9.57209 16.228C9.81822 16.474 10.152 16.6122 10.5 16.6122C10.8481 16.6122 11.1818 16.474 11.428 16.228L18.8528 8.80314C19.0988 8.55701 19.2371 8.22324 19.2371 7.87521C19.2371 7.52718 19.0988 7.1934 18.8528 6.94727Z" fill="#494949"/>
                                </svg>
                            </div>
                        </div>
                    </button>

                    <div class="col-12 drop_body add_img_body" v-if="bodies.add_file_body">
                        <AddFile ref="addFile"
                                 v-on:upload_modal="uploadFiles"
                                 :images="images" :files="files" />
                    </div>
                </div>
                <!-- END ADD FILES MOBILE -->

                <!-- ADD REQUIREMENTS DESKTOP -->
                <div class="row" v-if="window_desktop && active_tab === tabs_indexes.add_requirements">
                    <AddRequirements
                        :requirements="requirements"
                        :validated_array="validated_array"
                        :errors="errors"
                        v-on:prev-tab="prevTab"
                        v-on:next-tab="nextTab"/>
                </div>
                <!-- END ADD REQUIREMENTS DESKTOP -->

                <!-- ADD REQUIREMENTS MOBILE -->
                <div class="m_b_15" v-if="!window_desktop">
                    <button class="col-12 drop_button"
                            :class="bodies.add_requirements_body ? 'drop_button_radius_open' : 'drop_button_radius_closed'"
                            @click="addBody('add_requirements_body')">
                        <div class="row">
                            <div class="col-10 text-start main_text title_mobile">
                                Add Requirements
                            </div>
                            <div class="col-2 text-end">
                                <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18.8528 6.94727C18.6066 6.70121 18.2729 6.56299 17.9248 6.56299C17.5768 6.56299 17.243 6.70121 16.9969 6.94727L10.5 13.4441L4.00315 6.94727C3.75561 6.70819 3.42407 6.5759 3.07994 6.57889C2.73581 6.58188 2.40661 6.71991 2.16327 6.96326C1.91992 7.20661 1.78189 7.5358 1.7789 7.87993C1.77591 8.22406 1.9082 8.5556 2.14728 8.80314L9.57209 16.228C9.81822 16.474 10.152 16.6122 10.5 16.6122C10.8481 16.6122 11.1818 16.474 11.428 16.228L18.8528 8.80314C19.0988 8.55701 19.2371 8.22324 19.2371 7.87521C19.2371 7.52718 19.0988 7.1934 18.8528 6.94727Z" fill="#494949"/>
                                </svg>
                            </div>
                        </div>
                    </button>

                    <div class="col-12 drop_body" v-if="bodies.add_requirements_body">
                        <AddRequirements
                            :validated_array="validated_array"
                            :errors="errors"
                            :requirements="requirements"/>
                    </div>
                </div>
                <!-- END ADD REQUIREMENTS MOBILE -->

                <!-- ADD DUE DATE DESKTOP -->
                <div class="row" v-if="window_desktop && active_tab === tabs_indexes.add_due_date">
                    <AddDueDate :dates="dates"
                                v-on:prev-tab="prevTab"
                                v-on:submit-form="validateForm"/>
                </div>
                <!-- END ADD DUE DATE DESKTOP -->

                <!-- ADD DUE DATE MOBILE -->
                <div class="m_b_15" v-if="!window_desktop">
                    <button class="col-12 drop_button"
                            :class="bodies.add_due_date_body ? 'drop_button_radius_open' : 'drop_button_radius_closed'"
                            @click="addBody('add_due_date_body')">
                        <div class="row">
                            <div class="col-10 text-start main_text title_mobile">
                                Add Due Date
                            </div>
                            <div class="col-2 text-end">
                                <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18.8528 6.94727C18.6066 6.70121 18.2729 6.56299 17.9248 6.56299C17.5768 6.56299 17.243 6.70121 16.9969 6.94727L10.5 13.4441L4.00315 6.94727C3.75561 6.70819 3.42407 6.5759 3.07994 6.57889C2.73581 6.58188 2.40661 6.71991 2.16327 6.96326C1.91992 7.20661 1.78189 7.5358 1.7789 7.87993C1.77591 8.22406 1.9082 8.5556 2.14728 8.80314L9.57209 16.228C9.81822 16.474 10.152 16.6122 10.5 16.6122C10.8481 16.6122 11.1818 16.474 11.428 16.228L18.8528 8.80314C19.0988 8.55701 19.2371 8.22324 19.2371 7.87521C19.2371 7.52718 19.0988 7.1934 18.8528 6.94727Z" fill="#494949"/>
                                </svg>
                            </div>
                        </div>
                    </button>

                    <div class="col-12 drop_body" v-if="bodies.add_due_date_body">
                        <AddDueDate
                            :dates="dates"/>
                    </div>
                </div>
                <!-- END ADD DUE DATE MOBILE -->
            </div>

            <div class="container last_row" v-if="!window_desktop">
                <div class="row">
                    <div class="col-12 text-center">
                        <button
                            class="post_button text-center"
                            :disabled="!data_validation"
                            :class="!data_validation ? 'disabled_button' : ''"
                            @click="validateForm">
                            Post
                        </button>
                    </div>
                </div>
            </div>

            <!-- OPEN CAMERA -->
            <button
                id="modal-camera-button"
                type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#cameraModal"
                hidden>
                Launch success modal
            </button>

            <div class="modal" id="cameraModal" tabindex="-1" aria-labelledby="cameraModal" aria-hidden="true">
                <div class="modal-dialog modal-fullscreen">
                    <div class="modal-content">
                        <button id="closeCameraModal" data-bs-dismiss="modal" hidden></button>
                        <AddFromTheCamera
                            ref="fromCamera"
                            v-on:close-camera-modal="closeCameraModal"
                            :images="images"/>
                    </div>
                </div>
            </div>
            <!-- END OPEN CAMERA -->

            <!-- SUCCESS CREATE JOB -->
            <!-- Button trigger modal -->
            <button
                id="modal-success-button"
                type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#successModal"
                hidden>
                Launch success modal
            </button>

            <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModal" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="row m_t_23 m_b_16">
                            <div class="col-12 text-center main_text success_message" v-if="window_desktop">
                                Your new job has been scheduled for
                            </div>

                            <div class="col-12 text-center main_text success_message_2" v-else>
                                The job has been posted!
                            </div>

                            <div class="col-12 text-center main_text success_message_strong" v-if="window_desktop">
                                <strong>
                                    {{dateFormated(new Date(this.dates.due_date))}}!
                                </strong>
                            </div>
                        </div>
                        <div class="row pre_line">
                            <div class="col-12 line"></div>
                        </div>

                        <div class="row body_modal_message">
                            <div class="col-12 align-self-center main_text question text-center">
                                Would you like to continue posting jobs?
                            </div>
                        </div>
                        <div class="row" :class="window_desktop ? 'm_b_44' : 'm_b_22'">
                            <div class="col-6 text-center">
                                <button
                                    @click="goHome"
                                    type="button"
                                    class="custom_button_success white_background main_text"
                                    data-bs-dismiss="modal">
                                    No
                                </button>
                            </div>
                            <div class="col-6 text-center">
                                <button
                                    @click="reloadPage"
                                    type="button"
                                    class="custom_button_success braun_background white_text"
                                    data-bs-dismiss="modal">
                                    Yes
                                </button>
                            </div>
                        </div>
                        <div class="row m_b_44" v-if="window_desktop">
                            <div class="col-12 text-center">
                                <button
                                    class="empty_button white_background"
                                    @click="goHome"
                                    data-bs-dismiss="modal">
                                    Go back
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END SUCCESS CREATE JOB -->

            <!-- MODAL SELECT FILE UPLOAD METHODS -->
            <!-- Button trigger modal -->
            <button
                id="modal-upload-button"
                type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#exampleModal"
                hidden>
                Launch upload modal
            </button>

            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-6">
                                    <button
                                        class="text-center item_image in_center"
                                        data-bs-dismiss="modal"
                                        @click="uploadFiles">
                                        <svg width="130" height="130" viewBox="0 0 130 130" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M97.1427 48.7502H97.5489C100.745 48.7507 103.909 49.3818 106.861 50.6073C109.812 51.8329 112.493 53.6287 114.75 55.8921C119.313 60.4662 121.876 66.6638 121.876 73.1252C121.876 79.5866 119.313 85.7842 114.75 90.3583C112.493 92.6216 109.812 94.4175 106.861 95.643C103.909 96.8686 100.745 97.4997 97.5489 97.5002H81.3395V89.3752H97.5489C101.733 89.188 105.684 87.3942 108.578 84.3672C111.473 81.3402 113.088 77.3134 113.088 73.1252C113.088 68.937 111.473 64.9101 108.578 61.8831C105.684 58.8562 101.733 57.0623 97.5489 56.8752H90.1227L89.1233 49.9121C88.5136 45.5495 86.4967 41.5052 83.379 38.3934C80.2613 35.2816 76.2132 33.2723 71.8496 32.6708C67.4833 32.0744 63.042 32.9269 59.207 35.0976C55.3719 37.2683 52.355 40.6373 50.6189 44.6877L47.9133 50.8789L41.3402 49.3433C39.8377 48.9706 38.2974 48.7716 36.7496 48.7502C31.3708 48.7502 26.2114 50.8871 22.4171 54.6977C19.5893 57.541 17.6653 61.1569 16.8868 65.0906C16.1084 69.0244 16.5103 73.1005 18.042 76.8065C19.5737 80.5126 22.1669 83.683 25.4955 85.9193C28.8241 88.1556 32.7395 89.358 36.7496 89.3752H57.0133V97.5002H36.7496C32.7251 97.5364 28.7392 96.7145 25.0574 95.0893C21.3755 93.4642 18.0823 91.073 15.3971 88.0752C11.3371 83.5538 8.83118 77.8514 8.24636 71.8029C7.66154 65.7544 9.02849 59.6775 12.1471 54.4621C14.2126 51.0067 16.9888 48.0297 20.2917 45.7283C23.5946 43.4269 27.3489 41.8536 31.3058 41.1127C35.2546 40.3814 39.3252 40.4871 43.2333 41.4458C45.6959 35.8072 49.9276 31.1238 55.2885 28.104C60.6494 25.0842 66.8479 23.8922 72.9464 24.7083C79.0475 25.5339 84.7122 28.3288 89.0796 32.6683C93.4469 37.0078 96.2781 42.6545 97.1427 48.7502ZM83.6308 80.0558L72.9546 69.3877V113.555H64.8783V69.6802L54.4946 80.0639L48.7502 74.3114L66.1864 56.8752H71.9389L89.3752 74.3114L83.6308 80.0558Z" fill="#494949"/>
                                        </svg>

                                        <div class="text-center description_mobile main_text">
                                            Select from Gallery
                                        </div>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button
                                        class="text-center item_image in_center"
                                        @click="openCameraModal"
                                        data-bs-dismiss="modal">
                                        <svg width="46" height="46" viewBox="0 0 46 46" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5.75 15.3337C5.75 16.3878 6.6125 17.2503 7.66667 17.2503C8.72083 17.2503 9.58333 16.3878 9.58333 15.3337V11.5003H13.4167C14.4708 11.5003 15.3333 10.6378 15.3333 9.58366C15.3333 8.52949 14.4708 7.66699 13.4167 7.66699H9.58333V3.83366C9.58333 2.77949 8.72083 1.91699 7.66667 1.91699C6.6125 1.91699 5.75 2.77949 5.75 3.83366V7.66699H1.91667C0.8625 7.66699 0 8.52949 0 9.58366C0 10.6378 0.8625 11.5003 1.91667 11.5003H5.75V15.3337Z" fill="#494949"/>
                                            <path d="M25.1499 31.5C27.7733 31.5 29.8999 29.3734 29.8999 26.75C29.8999 24.1266 27.7733 22 25.1499 22C22.5265 22 20.3999 24.1266 20.3999 26.75C20.3999 29.3734 22.5265 31.5 25.1499 31.5Z" fill="#494949"/>
                                            <path d="M40.25 11.5003H34.1742L31.7975 8.91283C31.4403 8.52066 31.0053 8.20731 30.5201 7.9928C30.035 7.77828 29.5104 7.66732 28.98 7.66699H16.7133C17.0392 8.24199 17.25 8.87449 17.25 9.58366C17.25 11.692 15.525 13.417 13.4167 13.417H11.5V15.3337C11.5 17.442 9.775 19.167 7.66667 19.167C6.9575 19.167 6.325 18.9562 5.75 18.6303V38.3337C5.75 40.442 7.475 42.167 9.58333 42.167H40.25C42.3583 42.167 44.0833 40.442 44.0833 38.3337V15.3337C44.0833 13.2253 42.3583 11.5003 40.25 11.5003ZM24.9167 36.417C19.6267 36.417 15.3333 32.1237 15.3333 26.8337C15.3333 21.5437 19.6267 17.2503 24.9167 17.2503C30.2067 17.2503 34.5 21.5437 34.5 26.8337C34.5 32.1237 30.2067 36.417 24.9167 36.417Z" fill="#494949"/>
                                        </svg>

                                        <div class="text-center description_mobile main_text">
                                            Take a Photo
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="close_modal main_text" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END MODAL SELECT FILE UPLOAD METHODS -->

            <!-- Button trigger modal -->
            <button
                id="modal-error-button"
                type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#errorModal"
                hidden>
                Launch error modal
            </button>
            <!-- MODAL ERROR -->
            <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModal" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title red_color" id="errorModalLabel">ERROR!</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body red_color">
                            {{error_message}}
                        </div>
                        <div class="modal-footer">
                            <button @click="refreshError" type="button" class="close_modal main_text" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END MODAL ERROR -->
        </div>
    </MainLayout>
</template>

<script>

import {mapMutations} from 'vuex';
import MainLayout from "../layouts/MainLayout";
import AddNewJobEnum from "../enums/AddNewJobEnum";
import TabsEnum from "@/enums/TabsEnum";
import TimeEnEnum from "@/enums/TimeEnEnum";
import AddFile from "../components/addnewjob/AddFile";
import AddRequirements from "../components/addnewjob/AddRequirements";
import AddDueDate from "../components/addnewjob/AddDueDate";
import AddFromTheCamera from "@/components/addnewjob/AddFromTheCamera";
import {errorMessage} from "../services/messages";

export default {
    name: 'AddNewJob',
    components: {
        MainLayout,
        AddFile,
        AddRequirements,
        AddDueDate,
        AddFromTheCamera
    },
    data() {
        return {
            sizeW: null,
            window_desktop: true,
            title: AddNewJobEnum.TITLE_1,
            description: AddNewJobEnum.DESCRIPTION_1,
            tabs: TabsEnum.ADDNEWJOB,
            tabs_indexes: TabsEnum.INDEXES,
            validates_images: true,
            validates_requirements: true,
            validates_due_date: true,
            active_tab: 0,
            data_validation: true,
            bodies: {
                add_file_body: false,
                add_requirements_body: false,
                add_due_date_body: false,
            },
            bodies_array: [
                'add_file_body',
                'add_requirements_body',
                'add_due_date_body',
            ],
            images: [],
            files:[],
            requirements: {
                editing_level: "",
                style_guide: '',
                color_profile: '',
                file_format: '',
                other: '',
                checked_option: {
                    add_logo: false,
                    add_watermark: false,
                    white_background: false,
                    red_eye: false,
                },
            },
            dates: {
                due_date: null,
            },
            validated_array: [
                'editing_level',
                'color_profile',
                'file_format',
            ],
            errors: {
                editing_level: false,
                style_guide: false,
                color_profile: false,
                file_format: false,
            },
            error_message: '',
            valid_obj: {
                first: {
                    index: 'add_file',
                    bodies: 'add_file_body',
                    message: 'There must be at least one picture in the task!',
                },
                second: {
                    index: 'add_requirements',
                    bodies: 'add_requirements_body',
                    message: 'Fill in the required fields of the form!',
                },
                three: {
                    index: 'add_due_date',
                    bodies: 'add_due_date_body',
                    message: 'Choose a completion date!',
                },
            },
            days_string: TimeEnEnum.DAYS,
            months_string: TimeEnEnum.MONTHS,
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
        isMobile() {
            return window.innerWidth <= 992;
        },
        updateWidth() {
            this.sizeW = document.documentElement.clientWidth;
        },
        goHome() {
            this.$router.push({to: '/business-homepage', name: "BusinessHomePage"});
        },
        reloadPage() {
            this.$router.go();
        },
        openUploadModal() {
            document.getElementById("modal-upload-button").click();
        },
        uploadFiles() {
            this.$refs.addFile.chooseFiles();
        },
        openCameraModal() {
            document.getElementById("modal-camera-button").click();
            this.$refs.fromCamera.startCamera();
        },
        closeCameraModal() {
            document.getElementById("closeCameraModal").click();
            this.$refs.fromCamera.stopCamera();
        },
        addBody(val) {
            for (let i = 0; i < this.bodies_array.length; i++) {
                if (this.bodies_array[i] === val) {
                    this.bodies[this.bodies_array[i]] = !this.bodies[this.bodies_array[i]];
                } else {
                    this.bodies[this.bodies_array[i]] = false;
                }
            }
        },
        selectTab(index) {
            this.active_tab = index;
        },
        nextTab() {
            this.active_tab = this.active_tab + 1;
        },
        prevTab() {
            this.active_tab = this.active_tab - 1;
        },
        validateForm() {
            if (!this.images.length) {
                this.validates_images = false;
                errorMessage('Error Validate')
                return;
            }

            for (let i = 0; i < this.validated_array.length; i++) {
                let test = this.requirements[this.validated_array[i]] ?? null;
                if (test === '' || test === null) {
                    this.validated = false;
                    this.errors[this.validated_array[i]] = true;
                    this.validates_requirements = false;
                    errorMessage('Error Validate')
                    return;
                }
            }

            if (!this.dates.due_date) {
                this.validates_due_date = false;
                errorMessage('Error Validate')
                return;
            }

            this.submitForm();
        },
        async submitForm() {
            this.showLoader()
            try {
                let date = new Date(this.dates.due_date);
                let data = new FormData();
                this.files.forEach((element, index) => {
                    data.append(`files[${index}]`, element);
                });
                let body = JSON.stringify({
                    images: this.images,
                    due_date: date,
                    editing_level: this.requirements.editing_level,
                    style_guide: this.requirements?.style_guide?.name||'',
                    color_profile: this.requirements.color_profile,
                    file_format: this.requirements.file_format,
                    other: this.requirements.other,
                    add_logo: this.requirements.checked_option.add_logo,
                    add_watermark: this.requirements.checked_option.add_watermark,
                    white_background: this.requirements.checked_option.white_background,
                    red_eye: this.requirements.checked_option.red_eye,
                });
                data.append('body', body);
                const result = await this.$http.postAuth(`${this.$http.apiUrl()}create_new_job`, data);
                document.getElementById("modal-success-button").click()
            }catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();

        },
        checkedValidationErrors(index) {
            this.data_validation = false;

            if (this.window_desktop) {
                this.selectTab(this.tabs_indexes[this.valid_obj[index].index]);
            } else {
                if (!this.bodies[this.valid_obj[index].bodies]) {
                    this.addBody(this.valid_obj[index].bodies);
                }
            }
            this.viewError(this.valid_obj[index].message);
        },
        viewModal: function() {
            document.getElementById("modal-error-button").click()
        },
        viewError(message) {
            this.error_message = message;
            this.viewModal();
        },
        refreshError() {
            this.data_validation = true;

            this.validates_images = true;
            this.validates_requirements = true;
            this.validates_due_date = true;
        },
        dateFormated(date) {
            return this.days_string[date.getDay()] + ', ' + this.months_string[date.getMonth()] + ' ' + date.getDate();
        },
        checkedOpt() {
            console.log('CHECKED OPTION');
            console.log(this.requirements)
        }
    },
    computed: {
        showHeaderMob() {
            return this.isMobile()
        },
    },
    watch: {
        sizeW(val) {
            this.window_desktop = val > 992;
        },
        validates_images(val) {
            if (!val) {
                this.checkedValidationErrors('first');
            }
        },
        validates_requirements(val) {
            if (!val) {
                this.checkedValidationErrors('second');
            }
        },
        validates_due_date(val) {
            if (!val) {
                this.checkedValidationErrors('three');
            }
        },
    }
}
</script>
<style lang="scss" scoped>
$primary_font_family: 'Montserrat', sans-serif;

.body_active {
    height: 100%;
}
.pl-15{
    padding-left: 15px;
}
.container_body {
    height: 100%;
    padding-top: 50px;
}
.last_row {
    padding-bottom: 124px;
    padding-top: 15px;
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
.question {
    font-weight: normal;
    font-size: 20px;
    line-height: 24px;
}
.tab_title {
    font-weight: 600;
    font-size: 24px;
    line-height: 44px;
    cursor: pointer;
}
.row_tab {
    margin-top: 93px;
}
.tab_item {
    height: 0 !important;
    padding: 0 !important;
    background: rgba(73, 73, 73, 0.25);
    border: 0.5px solid rgba(73, 73, 73, 0.25);
}
.active_tab {
    height: 0 !important;
    padding: 0 !important;
    background: #494949;
    border: 2.5px solid #494949;
    border-radius: 100px;
}
.disabled_button {
    opacity: 0.35;
}
.post_button {
    width: 150px;
    height: 60px;
    color: #FFFFFF;
    background: #D8C3AF;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.25);
    border-radius: 100px;
    font-family: $primary_font_family;
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    line-height: 22px;
}
.drop_button {
    width: 100%;
    height: 65px;
    padding: 21px 26px 21px 31px;
    background: #FFFFFF;
    border: none;
    filter: drop-shadow(0px 3px 3px rgba(0, 0, 0, 0.15));
}
.drop_button_radius_closed {
    border-radius: 5px;
}
.drop_button_radius_open {
    border-radius: 5px 5px 0 0;
    box-shadow: none;
}
.drop_body {
    width: 100%;
    background: #FFFFFF;
    border: none;
    border-radius: 0 0 5px 5px;
    filter: drop-shadow(0 3px 3px rgba(0, 0, 0, 0.15));
}
.add_img_body {
    overflow: scroll;
    height: 340px;
}
.modal-footer {
    justify-content: center;
}
.modal-body {
    width: 100%;
}
.close_modal {
    width: 120px;
    height: 50px;
    background: #FFFFFF;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.25);
    border-radius: 100px;
}
.item_image {
    width: 102px;
    height: 102px;
    overflow: hidden;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3px;
    margin: 3px 0;
    box-shadow: 0 3px 3px rgba(0, 0, 0, 0.15) !important;
    box-sizing: border-box;
    border-radius: 5px;
    border: none;
}
.custom_button {
    width: 200px;
    height: 60px;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.25);
    border-radius: 100px;
}
.custom_button_success {
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
.success_message {
    font-style: normal;
    font-weight: 600;
    font-size: 24px;
    line-height: 50px;
}
.success_message_strong {
    font-style: normal;
    font-weight: 700;
    font-size: 24px;
    line-height: 50px;
}
.success_message_2 {
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    line-height: 22px;
    text-transform: capitalize;
}
.in_center {
    margin: 0 auto;
}
.red_color {
    color: #bb2d3b;
}
.body_modal_message {
    height: 168px;
}
.pre_line {
    padding: 0 12px;
}
.line {
    border: 1px solid #494949;
}
.no_active_title_tab {
    color: rgba(73, 73, 73, 0.6);
}
.m_b_15 {
    margin-bottom: 15px;
}
.m_b_16 {
    margin-bottom: 16px;
}
.m_b_22 {
    margin-bottom: 22px;
}
.m_b_30 {
    margin-bottom: 30px;
}
.m_b_56 {
    margin-bottom: 56px;
}
.m_bt_16 {
    margin: 16px 0;
}
.m_b_44 {
    margin-bottom: 44px;
}
.m_b_89 {
    margin-bottom: 89px;
}
.m_b_100 {
    margin-bottom: 100px;
}
.m_t_23 {
    margin-top: 23px;
}
.in_center {
    margin: 0 auto;
}
.empty_button {
    border-bottom: 1px solid #494949;
    border-left: none !important;
    border-right: none !important;
    border-top: none !important;
    box-shadow: none !important;
}
@media screen and (max-width: 992px) {
    .body_active {
        height: 100%;
        min-height: 100vh;
    }
    .container_body {
        padding-top: 70px;
        height: 100%;
        min-height: calc(100vh - 180px);
    }
    .custom_button_success {
        width: 120px;
        height: 50px;
    }
    .question {
        font-size: 16px;
        line-height: 20px;
    }
}
</style>
