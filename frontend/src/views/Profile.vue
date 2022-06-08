<template>
    <div>
        <MainLayout :is-show-header=!showJobDetailsMob>
            <div v-if="load_page" class="page_profile">
                <div class="page_header">
                    <div class="left_column">
                        <div class="avatar_column">
                            <div class="avatar_name">
                                <img v-if="avatar" class="avatar" :src=avatar alt="">
                                <img v-else class="avatar" src='@/assets/images/sbcf-default-avatar.png' alt="">
                                <div class="user_name">{{ user.first_name }} {{ user.last_name }}</div>
                            </div>
                            <router-link :to="{ name: 'Settings' }">
                                <img class="icon_setting cp" src='@/assets/icons/setting.svg' alt="">
                            </router-link>
                        </div>
                        <div class="info_user">
                            <div v-if="is_type_user===editor" class="item_info item_info_mb">
                                <img class="style_icon" src='@/assets/icons/star.svg' alt="">
                                <div class="title_info">{{ avgRating() }}</div>
                            </div>
                            <div v-if="is_type_user===business" class="item_info">
                                <img class="style_icon" src='@/assets/icons/business-center.svg' alt="">
                                <div class="title_info">{{ user.business_name }}</div>
                            </div>
                            <div v-else class="item_info ">
                                <img class="style_icon" src='@/assets/icons/bi_person-fill.svg' alt="">
                                <!--                                <div class="title_info">{{ user.business_name }}</div>-->
                                <div class="title_info">{{ user.business_name }}<span
                                    class="editor_b"></span></div>
                            </div>
                            <div class="item_info item_info_mt">
                                <img class="style_icon" src='@/assets/icons/ep_location.svg' alt="">
                                <div class="title_info">{{ user.location }}</div>
                            </div>
                            <div class="bio_title">
                                Bio
                            </div>
                            <div class="bio_text">
                                {{ user.bio }}
                            </div>
                        </div>
                    </div>
                    <div class="right_column">
                        <div class="title-1 ps-3">Inbox</div>
                        <div class="pt-4 cp" @click="goToChats">
                            <div class="inbox">
                                <div class="me-4">
                                    <img class="" src="@/assets/icons/mail.svg" alt="icon-approved.svg"/>
                                </div>
                                <div class="inbox_text">You have <span class="count_message"><ChatsMessageCount></ChatsMessageCount></span>
                                    new messages.
                                </div>
                            </div>
                        </div>
                        <div class="title-1 mt_29">
                            Past Jobs
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="active_jobs_row" v-if="past_jobs.length">
                                    <div
                                        class="card_job card_job_s cp"
                                        v-if="past_jobs.length"
                                        v-for="(job, index) in past_jobs"
                                        :key="'job-card-' + index">
                                        <div @click="sheduleFile(job)" class="img_div"
                                             :class="{'img_div_editor':is_type_user===editor}">
                                            <img
                                                :src="mainImage(job)"
                                                class="card-img-top"
                                                :alt="'job-image-' + index">
                                            <div v-if="is_type_user===editor" class="details_work_images">
                                                <div>Files: {{ job['images'].length }}</div>
                                                <div>Editing Level: {{ job['editing_level'] }}</div>
                                                <div>Acceptance: {{ acceptance(job) }}%</div>
                                                <div>Time Frame: {{ timerFrame(job) }} min/avg</div>
                                            </div>
                                            <div class="name_div main_text text-center">
                                                {{ job.style_guide }}
                                            </div>
                                        </div>
                                        <template v-if="is_type_user===editor">
                                            <div class="loader_line">
                                                <div class="done" style="width:100%"></div>
                                            </div>
                                            <div class="card_body text-center">
                                                All files approved
                                            </div>
                                        </template>
                                        <template v-else>
                                            <div v-if="!isEmptyObject(job.review)" @click="openReviewModal(job)"
                                                 class="leave-review">
                                                Leave Review
                                            </div>
                                            <div class="rating_job" v-else>
                                                <star-rating
                                                    v-model="job.review.rating"
                                                    :show-rating="false"
                                                    v-bind:star-size="22"
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
                </div>
                <div v-if="is_type_user===editor" class="review_colum">
                    <div class="title_r">Reviews</div>
                    <div class="review_items">
                        <div class="review_item" v-for="review in editor_reviews">
                            <div class="header_box_review">
                                <div>
                                    <img v-if="getAvatar(review.user)" class="avatar" :src="getAvatar(review.user)"
                                         alt="avatar">
                                    <img v-else class="avatar" src='@/assets/images/sbcf-default-avatar.png' alt="">
                                </div>
                                <div>
                                    <star-rating
                                        v-model="review.rating"
                                        :show-rating="false"
                                        v-bind:star-size="18"
                                        inactive-color="#D1D2D3"
                                        active-color="#494949"
                                        :read-only="true"
                                    >
                                    </star-rating>
                                </div>
                            </div>
                            <div class="d-flex gap-3 mt-4">
                                <div>
                                    <svg width="15" height="13" viewBox="0 0 15 13" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_3328_3146)">
                                            <path
                                                d="M6.40167 2.11288L5.85774 1.31543C2.09205 3.72771 0 6.65833 0 9.07061C0 11.4031 1.79916 12.4996 3.32636 12.4996C5.25105 12.4996 6.61088 10.9446 6.61088 9.30985C6.61088 7.93425 5.69038 6.75801 4.45607 6.31942C4.10042 6.1998 3.76569 6.10012 3.76569 5.52197C3.76569 4.78433 4.33054 3.68784 6.40167 2.11288ZM14.7071 2.11288L14.1632 1.31543C10.4393 3.72771 8.30544 6.65833 8.30544 9.07061C8.30544 11.4031 10.1464 12.4996 11.6736 12.4996C13.6192 12.4996 15 10.9446 15 9.30985C15 7.93425 14.0586 6.75801 12.7824 6.31942C12.4268 6.1998 12.113 6.10012 12.113 5.52197C12.113 4.78433 12.6987 3.68784 14.7071 2.11288Z"
                                                fill="#494949"/>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_3328_3146">
                                                <rect width="15" height="12.5" fill="white"/>
                                            </clipPath>
                                        </defs>
                                    </svg>

                                </div>
                                <div class="review_message">{{ review.message }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <template v-if="is_type_user===business">
                    <div class="tab_pages">
                        <div class="col item_tab" @click="selectTab(1)" :class="{item_tab_active:tab===1}">Style Guides
                        </div>
                        <div class="col item_tab" @click="selectTab(2)" :class="{item_tab_active:tab===2}">Sample Work
                        </div>
                        <div class="col item_tab" @click="selectTab(3)" :class="{item_tab_active:tab===3}">Files</div>
                    </div>
                    <div v-if="tab===1">
                        <div class="tab_item_d">
                            <div class="selects_tab_item">
                                <div v-for="style in style_guides" class="box_select">
                                    <div @click="selectStyleG(style.id)" class="select_tab1">
                                        <div class="title-2">{{ style.name }}</div>
                                        <div class="col-2 text-end">
                                            <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M18.8528 6.94727C18.6066 6.70121 18.2729 6.56299 17.9248 6.56299C17.5768 6.56299 17.243 6.70121 16.9969 6.94727L10.5 13.4441L4.00315 6.94727C3.75561 6.70819 3.42407 6.5759 3.07994 6.57889C2.73581 6.58188 2.40661 6.71991 2.16327 6.96326C1.91992 7.20661 1.78189 7.5358 1.7789 7.87993C1.77591 8.22406 1.9082 8.5556 2.14728 8.80314L9.57209 16.228C9.81822 16.474 10.152 16.6122 10.5 16.6122C10.8481 16.6122 11.1818 16.474 11.428 16.228L18.8528 8.80314C19.0988 8.55701 19.2371 8.22324 19.2371 7.87521C19.2371 7.52718 19.0988 7.1934 18.8528 6.94727Z"
                                                    fill="#494949"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="select_item" v-if="selected_style_guide==style.id">
                                        <ul class="list_onesies">
                                            <li>Background
                                                <div>{{ style.remove_background }}</div>
                                            </li>
                                            <li> Size
                                                <div>{{ style.size_with }} x {{ style.size_height }}
                                                    {{ style.unit_measurement }}
                                                </div>
                                            </li>
                                            <li> File Format
                                                <div>{{ style.file_format }}</div>
                                            </li>
                                            <li> Color profile
                                                <div> {{ style.color_profile }}</div>
                                            </li>
                                            <li> Resolution
                                                <div> {{ style.resolution }} {{ style.resolution_units }}</div>
                                            </li>
                                            <li> Logo
                                                <div>{{ styleFileLogo(style) }}</div>
                                            </li>
                                            <li> Watermark
                                                <div>{{ styleFileWatermark(style) }}</div>
                                            </li>
                                            <li> Video
                                                <div><a class="video_href"
                                                        :href="style.file_video_instructions.file_url"
                                                        target="_blank">{{
                                                        style.file_video_instructions.file_name
                                                    }}.{{ style.file_video_instructions.type }}</a>
                                                </div>
                                            </li>
                                            <li> Color palette
                                                <div>{{ styleFileColorPalette(style) }}</div>
                                            </li>
                                            <li> Typography
                                                <div>{{ styleFileTypography(style) }}</div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="box_select box_add_style_guide">
                                    <div @click="addStyleGuideMob" class="select_tab1">
                                        <div class="title-2">Add Style Guide</div>
                                        <div class="col-2 text-end">
                                            <svg width="20" height="19" viewBox="0 0 20 19" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <line x1="9.34399" y1="1.25" x2="9.34399" y2="17.75" stroke="#494949"
                                                      stroke-width="2.5" stroke-linecap="round"/>
                                                <line x1="17.9736" y1="9.25" x2="1.2501" y2="9.25" stroke="#494949"
                                                      stroke-width="2.5" stroke-linecap="round"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="mobile_guide_box" v-if="add_style_guide_mob">
                                        <div class="input_item">
                                            <div class="title_form">Name</div>
                                            <div class="input_b1"><input type="text"
                                                                         :class="{ 'is-invalid': errors.name }"
                                                                         v-model="style_guide.name"
                                                                         class="form-control style_input1"
                                                                         placeholder="Type something...">
                                                <div class="invalid-feedback">
                                                    This field is required
                                                </div>
                                            </div>
                                        </div>
                                        <div class="input_item">
                                            <div class="title_form">Remove Background</div>
                                            <div class="input_b1"><select v-model="style_guide.remove_background"
                                                                          :class="{ 'is-invalid': errors.remove_background }"
                                                                          class="style_input1 form-select">
                                                <option selected disabled hidden value="">Yes / No</option>
                                                <option key="yes">Yes</option>
                                                <option key="no">No</option>
                                            </select>
                                                <div class="invalid-feedback">
                                                    This field is required
                                                </div>
                                            </div>
                                        </div>
                                        <div class="input_item">
                                            <div class="title_form">Size</div>
                                            <div class="d-flex justify-content-between input_b1">
                                                <div class="d-flex align-items-center">
                                                    <input type="text" v-model="style_guide.size_with"
                                                           :class="{ 'is-invalid_c': errors.size_with}"
                                                           class="form-control style_input1 with_input1"
                                                           placeholder="300">
                                                    <div class="x">x</div>
                                                    <input type="text" v-model="style_guide.size_height"
                                                           :class="{ 'is-invalid_c': errors.size_height}"
                                                           class="form-control style_input1 with_input1"
                                                           placeholder="300">
                                                </div>
                                                <input type="text" v-model="style_guide.unit_measurement"
                                                       :class="{ 'is-invalid_c': errors.unit_measurement}"
                                                       class="stmob form-control style_input1 with_input1"
                                                       placeholder="px">
                                            </div>
                                        </div>
                                        <div class="input_item">
                                            <div class="title_form">File Format</div>
                                            <div class="input_b1"><select v-model="style_guide.file_format"
                                                                          :class="{ 'is-invalid': errors.file_format}"
                                                                          class="style_input1 form-select">
                                                <option selected disabled hidden value="">PNG, JPEG...</option>
                                                <option key="PNG">PNG</option>
                                                <option key="JPG">JPG</option>
                                                <option key="Jpeg">JPEG</option>
                                                <option key="TIF">TIF</option>
                                                <option key="GIF">GIF</option>
                                            </select>
                                                <div class="invalid-feedback">
                                                    This field is required
                                                </div>
                                            </div>
                                        </div>
                                        <div class="input_item">
                                            <div class="title_form">Color Profile</div>
                                            <div class="input_b1">
                                                <select v-model="style_guide.color_profile"
                                                        :class="{ 'is-invalid': errors.color_profile}"
                                                        class="style_input1 form-select">
                                                    <option selected disabled hidden value=""> RBG, CMYK, ICC...
                                                    </option>
                                                    <option key="RBG">RBG</option>
                                                    <option key="CMYK">CMYK</option>
                                                    <option key="ICC">ICC</option>
                                                </select>
                                                <div class="invalid-feedback">
                                                    This field is required
                                                </div>
                                            </div>
                                        </div>
                                        <div class="input_item">
                                            <div class="title_form">Resolution</div>
                                            <div class="d-flex align-items-center">
                                                <input type="text" v-model="style_guide.resolution"
                                                       class="style_input1 with_input1"
                                                       :class="{ 'is-invalid_c': errors.resolution}"
                                                       placeholder="300">
                                                <div class="x"></div>
                                                <input type="text" v-model="style_guide.resolution_units"
                                                       :class="{ 'is-invalid_c': errors.resolution_units}"
                                                       class="style_input1 with_input1" placeholder="dpi">
                                            </div>
                                        </div>
                                        <div class="input_item">
                                            <div class="title_form">Logo</div>
                                            <div class="input_b1">
                                                <select v-model="style_guide.file_id_logo"
                                                        class="style_input1 form-select">
                                                    <option selected disabled hidden value="">Logo</option>
                                                    <option v-for="file in user_files" :value="file.id" :key="file.id">
                                                        {{ file.file_name }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="input_item">
                                            <div class="title_form">Watermark</div>
                                            <div class="input_b1">
                                                <select v-model="style_guide.file_id_watermark"
                                                        class="style_input1 form-select">
                                                    <option selected disabled hidden value="">Watermark</option>
                                                    <option v-for="file in user_files" :value="file.id" :key="file.id">
                                                        {{ file.file_name }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="input_item">
                                            <div class="title_form">Video Instructions</div>
                                            <div class="input_b1">
                                                <select v-model="style_guide.file_id_video_instructions"
                                                        class="style_input1 form-select">
                                                    <option selected disabled hidden value="">Video Instructions
                                                    </option>
                                                    <option v-for="file in user_files" :value="file.id" :key="file.id">
                                                        {{ file.file_name }}
                                                    </option>
                                                </select>
                                                <div v-if="errors.video_instructions" class="invalid-feedback_c">
                                                    This field is required
                                                </div>
                                            </div>
                                        </div>
                                        <div class="input_item">
                                            <div class="title_form">Color palette</div>
                                            <div class="input_b1">
                                                <select v-model="style_guide.file_id_color_palette"
                                                        class="style_input1 form-select">
                                                    <option selected disabled hidden value="">Color palette</option>
                                                    <option v-for="file in user_files" :value="file.id" :key="file.id">
                                                        {{ file.file_name }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="input_item">
                                            <div class="title_form">Typography</div>
                                            <div class="input_b1">
                                                <select v-model="style_guide.file_id_video_typography"
                                                        class="style_input1 form-select">
                                                    <option selected disabled hidden value="">Typography</option>
                                                    <option v-for="file in user_files" :value="file.id" :key="file.id">
                                                        {{ file.file_name }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="input_item">
                                            <div class="title_form">Other</div>
                                            <div class="input_b1"><input type="text" v-model="style_guide.other"
                                                                         class="style_input1"
                                                                         placeholder="Type something..."></div>
                                        </div>
                                        <div class="btn_form">
                                            <div class="btn-add_s cp" @click="saveStyleGuide">Add</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div @click="addStyleGuide" class="add_button">
                                <div class="icon">
                                    <svg width="80" height="80" viewBox="0 0 80 80" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <line x1="39.5" y1="4.5" x2="39.5" y2="75.5" stroke="#494949" stroke-width="9"
                                              stroke-linecap="round"/>
                                        <line x1="75.5" y1="39.5" x2="4.5" y2="39.5" stroke="#494949" stroke-width="9"
                                              stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="button_label main_text m_t_38">
                                    Add Style Guide
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="tab===2">
                        <div class="tab_item_d2">
                            <div class="box_search_sample_work">
                                <div class="search_sample_work">
                                    <input @keyup="keypressSearchSampleWork" type="text" class="search_prod_inp"
                                           placeholder="">
                                    <img class="search_icon" src="@/assets/icons/search.svg" alt="">
                                </div>
                            </div>
                            <div class="sample_work">
                                <div class="spw_item">
                                    <div @click="selectWork('Onesies')" class="item_sample_work">
                                        <div class="d-flex">
                                            <div class="box_sample_work_img_item">
                                                <img class="img_item_w" src='@/assets/images/sbcf-default-avatar.png'
                                                     alt="">
                                            </div>
                                            <div class="title_name_work">Onesies</div>
                                        </div>
                                        <div class="arrow_select">
                                            <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M18.8528 6.94727C18.6066 6.70121 18.2729 6.56299 17.9248 6.56299C17.5768 6.56299 17.243 6.70121 16.9969 6.94727L10.5 13.4441L4.00315 6.94727C3.75561 6.70819 3.42407 6.5759 3.07994 6.57889C2.73581 6.58188 2.40661 6.71991 2.16327 6.96326C1.91992 7.20661 1.78189 7.5358 1.7789 7.87993C1.77591 8.22406 1.9082 8.5556 2.14728 8.80314L9.57209 16.228C9.81822 16.474 10.152 16.6122 10.5 16.6122C10.8481 16.6122 11.1818 16.474 11.428 16.228L18.8528 8.80314C19.0988 8.55701 19.2371 8.22324 19.2371 7.87521C19.2371 7.52718 19.0988 7.1934 18.8528 6.94727Z"
                                                    fill="#494949"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="box_images_work" v-if="select_work=='Onesies'">
                                        <div class="featured-products">
                                            <div @click="openFullImg()" class="featured-product-item">
                                                <img class="featured-product-item-image"
                                                     src='@/assets/images/sbcf-default-avatar.png' alt="">
                                            </div>
                                            <div class="featured-product-item">
                                                <img class="featured-product-item-image"
                                                     src='@/assets/images/sbcf-default-avatar.png' alt="">
                                            </div>
                                            <div class="featured-product-item">
                                                <img class="featured-product-item-image"
                                                     src='@/assets/images/sbcf-default-avatar.png' alt="">
                                            </div>
                                            <div class="featured-product-item">
                                                <img class="featured-product-item-image"
                                                     src='@/assets/images/sbcf-default-avatar.png' alt="">
                                            </div>
                                            <div class="featured-product-item">
                                                <img class="featured-product-item-image"
                                                     src='@/assets/images/sbcf-default-avatar.png' alt="">
                                            </div>
                                            <div class="featured-product-item">
                                                <img class="featured-product-item-image"
                                                     src='@/assets/images/sbcf-default-avatar.png' alt="">
                                            </div>
                                            <div class="featured-product-item">
                                                <img class="featured-product-item-image"
                                                     src='@/assets/images/sbcf-default-avatar.png' alt="">
                                            </div>
                                            <div class="featured-product-item">
                                                <img class="featured-product-item-image"
                                                     src='@/assets/images/sbcf-default-avatar.png' alt="">
                                            </div>
                                            <div class="featured-product-item">
                                                <img class="featured-product-item-image"
                                                     src='@/assets/images/sbcf-default-avatar.png' alt="">
                                            </div>
                                            <div class="featured-product-item">
                                                <img class="featured-product-item-image"
                                                     src='@/assets/images/sbcf-default-avatar.png' alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="tab===3">
                        <div class="tab_item_d2">
                            <div class="box_search_sample_work">
                                <div class="search_sample_work">
                                    <input @keyup="keypressSearchFile" type="text" class="search_prod_inp"
                                           placeholder="">
                                    <img class="search_icon" src="@/assets/icons/search.svg" alt="">
                                </div>
                            </div>
                            <div class="sample_work margin_box_f">
                                <div class="spw_item2" v-for="item in search_file">
                                    <div @click="selectFile(item.id)" class="item_sample_work">
                                        <div class="d-flex">
                                            <div class="box_sample_work_img_item img_file">
                                                <template
                                                    v-if="item.type==='png'||item.type==='jpeg'||item.type==='jpg'">
                                                    <img class="img_item_w p-2" :src=item.file_url
                                                         alt="">
                                                </template>
                                                <template v-else>
                                                    <div class="img_item_w p-2 type_box_f">.{{ item.type }}</div>
                                                </template>
                                            </div>
                                            <div class="box_file_n">
                                                <div class="title_file_name">{{ item.file_name }}</div>
                                                <div class="file_type" @click="goPageFile(item.file_url)">
                                                    {{ item.type }}
                                                </div>
                                            </div>

                                        </div>
                                        <!--                                        <div class="arrow_select">-->
                                        <!--                                            <svg width="21" height="21" viewBox="0 0 21 21" fill="none"-->
                                        <!--                                                 xmlns="http://www.w3.org/2000/svg">-->
                                        <!--                                                <path-->
                                        <!--                                                    d="M18.8528 6.94727C18.6066 6.70121 18.2729 6.56299 17.9248 6.56299C17.5768 6.56299 17.243 6.70121 16.9969 6.94727L10.5 13.4441L4.00315 6.94727C3.75561 6.70819 3.42407 6.5759 3.07994 6.57889C2.73581 6.58188 2.40661 6.71991 2.16327 6.96326C1.91992 7.20661 1.78189 7.5358 1.7789 7.87993C1.77591 8.22406 1.9082 8.5556 2.14728 8.80314L9.57209 16.228C9.81822 16.474 10.152 16.6122 10.5 16.6122C10.8481 16.6122 11.1818 16.474 11.428 16.228L18.8528 8.80314C19.0988 8.55701 19.2371 8.22324 19.2371 7.87521C19.2371 7.52718 19.0988 7.1934 18.8528 6.94727Z"-->
                                        <!--                                                    fill="#494949"/>-->
                                        <!--                                            </svg>-->
                                        <!--                                        </div>-->
                                    </div>
                                    <!--                                    <div class="box_images_work" v-if="select_file==='file1'">-->
                                    <!--                                        <div class="featured-products">-->
                                    <!--                                          `12345678-->
                                    <!--                                        </div>-->
                                    <!--                                    </div>-->
                                </div>
                                <div class="spw_item2">
                                    <div @click="openModalAddFile" class="item_sample_work">
                                        <div class="d-flex">
                                            <div class="box_sample_work_img_item">
                                                <img class="plus_icon" src='@/assets/images/Plus.png'
                                                     alt="">
                                            </div>
                                            <div class="title_name_work">Add New File</div>
                                        </div>
                                        <!--                                        <div class="arrow_select">-->
                                        <!--                                            <svg width="21" height="21" viewBox="0 0 21 21" fill="none"-->
                                        <!--                                                 xmlns="http://www.w3.org/2000/svg">-->
                                        <!--                                                <path-->
                                        <!--                                                    d="M18.8528 6.94727C18.6066 6.70121 18.2729 6.56299 17.9248 6.56299C17.5768 6.56299 17.243 6.70121 16.9969 6.94727L10.5 13.4441L4.00315 6.94727C3.75561 6.70819 3.42407 6.5759 3.07994 6.57889C2.73581 6.58188 2.40661 6.71991 2.16327 6.96326C1.91992 7.20661 1.78189 7.5358 1.7789 7.87993C1.77591 8.22406 1.9082 8.5556 2.14728 8.80314L9.57209 16.228C9.81822 16.474 10.152 16.6122 10.5 16.6122C10.8481 16.6122 11.1818 16.474 11.428 16.228L18.8528 8.80314C19.0988 8.55701 19.2371 8.22324 19.2371 7.87521C19.2371 7.52718 19.0988 7.1934 18.8528 6.94727Z"-->
                                        <!--                                                    fill="#494949"/>-->
                                        <!--                                            </svg>-->
                                        <!--                                        </div>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="add_style_guide" class="style_guide_box">
                        <div class="title-1">Add Style Guide</div>
                        <div class="form_style_guide">
                            <div class="col">
                                <div class="input_item">
                                    <div class="title_form">Name</div>
                                    <div class="input_b1"><input type="text" :class="{ 'is-invalid': errors.name }"
                                                                 v-model="style_guide.name"
                                                                 class="form-control style_input1"
                                                                 placeholder="Type something...">
                                        <div class="invalid-feedback">
                                            This field is required
                                        </div>
                                    </div>
                                </div>
                                <div class="input_item">
                                    <div class="title_form">Remove Background</div>
                                    <div class="input_b1"><select v-model="style_guide.remove_background"
                                                                  :class="{ 'is-invalid': errors.remove_background }"
                                                                  class="style_input1 form-select">
                                        <option selected disabled hidden value="">Yes / No</option>
                                        <option key="yes">Yes</option>
                                        <option key="no">No</option>
                                    </select>
                                        <div class="invalid-feedback">
                                            This field is required
                                        </div>
                                    </div>
                                </div>
                                <div class="input_item">
                                    <div class="title_form">Size</div>
                                    <div class="d-flex justify-content-between input_b1">
                                        <div class="d-flex align-items-center">
                                            <input type="text" v-model="style_guide.size_with"
                                                   :class="{ 'is-invalid_c': errors.size_with}"
                                                   class="form-control style_input1 with_input1" placeholder="300">
                                            <div class="x">x</div>
                                            <input type="text" v-model="style_guide.size_height"
                                                   :class="{ 'is-invalid_c': errors.size_height}"
                                                   class="form-control style_input1 with_input1" placeholder="300">
                                        </div>
                                        <input type="text" v-model="style_guide.unit_measurement"
                                               :class="{ 'is-invalid_c': errors.unit_measurement}"
                                               class="form-control style_input1 with_input1" placeholder="px">
                                    </div>
                                </div>
                                <div class="input_item">
                                    <div class="title_form">File Format</div>
                                    <div class="input_b1"><select v-model="style_guide.file_format"
                                                                  :class="{ 'is-invalid': errors.file_format}"
                                                                  class="style_input1 form-select">
                                        <option selected disabled hidden value="">PNG, jpeg...</option>
                                        <option key="PNG">PNG</option>
                                        <option key="JPG">JPG</option>
                                        <option key="Jpeg">Jpeg</option>
                                        <option key="TIF">TIF</option>
                                        <option key="GIF">GIF</option>
                                    </select>
                                        <div class="invalid-feedback">
                                            This field is required
                                        </div>
                                    </div>
                                </div>
                                <div class="input_item">
                                    <div class="title_form">Color Profile</div>
                                    <div class="input_b1"><select v-model="style_guide.color_profile"
                                                                  :class="{ 'is-invalid': errors.color_profile}"
                                                                  class="style_input1 form-select">
                                        <option selected disabled hidden value=""> RBG, CMYK, ICC...</option>
                                        <option key="RBG">RBG</option>
                                        <option key="CMYK">CMYK</option>
                                        <option key="ICC">ICC</option>
                                    </select>
                                        <div class="invalid-feedback">
                                            This field is required
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="input_item">
                                    <div class="title_form">Resolution</div>
                                    <div class="d-flex align-items-center">
                                        <input type="text" v-model="style_guide.resolution"
                                               class="style_input1 with_input1"
                                               :class="{ 'is-invalid_c': errors.resolution}"
                                               placeholder="300">
                                        <div class="x"></div>
                                        <input type="text" v-model="style_guide.resolution_units"
                                               :class="{ 'is-invalid_c': errors.resolution_units}"
                                               class="style_input1 with_input1" placeholder="dpi">
                                    </div>
                                </div>
                                <div class="input_item">
                                    <div class="title_form">Logo</div>
                                    <div class="input_b1">
                                        <select v-model="style_guide.file_id_logo"
                                                class="style_input1 form-select">
                                            <option selected disabled hidden value="">Logo</option>
                                            <option v-for="file in user_files" :value="file.id" :key="file.id">
                                                {{ file.file_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="input_item">
                                    <div class="title_form">Watermark</div>
                                    <div class="input_b1">
                                        <select v-model="style_guide.file_id_watermark"
                                                class="style_input1 form-select">
                                            <option selected disabled hidden value="">Watermark</option>
                                            <option v-for="file in user_files" :value="file.id" :key="file.id">
                                                {{ file.file_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="input_item">
                                    <div class="title_form">Video Instructions</div>
                                    <div class="input_b1">
                                        <select v-model="style_guide.file_id_video_instructions"
                                                class="style_input1 form-select">
                                            <option selected disabled hidden value="">Video Instructions</option>
                                            <option v-for="file in user_files" :key="file.id" :value="file.id">
                                                {{ file.file_name }}
                                            </option>
                                        </select>
                                        <div v-if="errors.video_instructions" class="invalid-feedback_c">
                                            This field is required
                                        </div>
                                    </div>
                                </div>
                                <div class="input_item">
                                    <div class="title_form">Color palette</div>
                                    <div class="input_b1">
                                        <select v-model="style_guide.file_id_color_palette"
                                                class="style_input1 form-select">
                                            <option selected disabled hidden value="">Color palette</option>
                                            <option v-for="file in user_files" :value="file.id" :key="file.id">
                                                {{ file.file_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="input_item">
                                    <div class="title_form">Typography</div>
                                    <div class="input_b1">
                                        <select v-model="style_guide.file_id_video_typography"
                                                class="style_input1 form-select">
                                            <option selected disabled hidden value="">Typography</option>
                                            <option v-for="file in user_files" :value="file.id" :key="file.id">
                                                {{ file.file_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="input_item">
                                    <div class="title_form">Other</div>
                                    <div class="input_b1"><input type="text" v-model="style_guide.other"
                                                                 class="style_input1" placeholder="Type something...">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="btn_form">
                            <div class="btn-add_s cp" @click="saveStyleGuide">Add</div>
                        </div>
                    </div>
                </template>
            </div>
        </MainLayout>
        <div class="modal" id="photoModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <img class="photo_full" src='@/assets/images/sbcf-default-avatar.png' alt="">

                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="addFileModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New File</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="fileNameInput1" class="form-label">File name</label>
                            <input type="text" :class="{ 'is-invalid': upload_file.file_name_valid }"
                                   class="form-control" v-model="upload_file.file_name" id="fileNameInput1"
                                   placeholder="File name">
                            <div class="invalid-feedback">{{ upload_file.file_name_valid }}</div>
                        </div>
                        <div class="mb-3">
                            <label for="fileInput2" class="form-label">File</label>
                            <input ref="fileupload" @change="uploadFileChange" type="file"
                                   :class="{ 'is-invalid': upload_file.file_valid }" class="form-control"
                                   id="fileInput2" placeholder="File">
                            <div class="invalid-feedback">{{ upload_file.file_valid }}</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn_modal_t1" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn_modal_t2" @click="uploadFile">Upload</button>
                    </div>
                </div>
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
    </div>
</template>

<script>

import MainLayout from "../layouts/MainLayout";
import {errorMessage} from "../services/messages";
import {mapMutations} from "vuex";
import TypeUserEnum from "@/enums/TypeUserEnum";
import ChatsMessageCount from "../components/ChatsMessageCount";
import {Modal} from "bootstrap";
import StarRating from 'vue-star-rating'

export default {
    name: 'Profile',
    components: {
        MainLayout,
        ChatsMessageCount,
        StarRating
    },
    data() {
        return {
            load_page: false,
            user: {},
            is_type_user: true,
            business: TypeUserEnum.BUSINESS,
            editor: TypeUserEnum.EDITOR,
            past_jobs: {},
            style_guides: {},
            tab: 1,
            selected_style_guide: '',
            select_work: '',
            photoModal: null,
            image_modal: null,
            add_style_guide: false,
            add_style_guide_mob: false,
            style_guide: {
                name: '',
                remove_background: '',
                size_with: '',
                size_height: '',
                unit_measurement: '',
                file_format: '',
                color_profile: '',
                resolution: '300',
                resolution_units: 'dpi',
                other: '',
                file_id_logo: '',
                file_id_watermark: '',
                file_id_video_instructions: '',
                file_id_video_typography: '',
                file_id_color_palette: '',
            },

            errors: {
                name: false,
                remove_background: false,
                size_with: false,
                size_height: false,
                unit_measurement: false,
                file_format: false,
                resolution_units: false,
                resolution: false,
                video_instructions: false,
                color_profile: false
            },
            select_file: '',
            addFileModal: null,
            upload_file: {
                file: null,
                file_name: '',
                file_valid: false,
                file_name_valid: false
            },
            user_files: {},
            search_file: {},
            reviewModal: null,
            add_review: {
                editor_name: '',
                rating: 0,
                editor_id: null,
                image_job_id: null,
                message: '',
            },
            editor_reviews: [],
        }
    },
    destroyed() {
        if (this.photoModal) {
            this.photoModal.dispose()
        }
        if (this.addFileModal) {
            this.addFileModal.dispose()
        }
        if (this.reviewModal) {
            this.reviewModal.dispose()
        }
    },
    async mounted() {
        await this.getProfileUser()
        this.photoModal = new Modal(document.getElementById('photoModal'))
        this.addFileModal = new Modal(document.getElementById('addFileModal'))
        this.reviewModal = new Modal(document.getElementById('reviewModal'))
        await this.isQueryRouteParam();
    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
            'setUser',
        ]),
        styleFileLogo(style) {
            return style?.file_logo?.file_name || 'No'
        },
        styleFileWatermark(style) {
            return style?.file_watermark?.file_name || 'No'
        },
        styleFileColorPalette(style) {
            return style?.file_id_color_palette?.file_name || 'No'
        },
        styleFileTypography(style) {
            return style?.file_id_video_typography?.file_name || 'No'
        },
        avgRating() {
            if (this.user?.avg_rating) {
                return Number(this.user?.avg_rating).toFixed(1)
            }
            return 0;
        },
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
            this.hideLoader();
            this.clearAddReview();
            this.reviewModal.hide();
            await this.getProfileUser()
        },
        clearAddReview() {
            this.add_review.rating = 0;
            this.add_review.editor_id = null;
            this.add_review.image_job_id = null;
            this.add_review.message = '';
        },
        openReviewModal(job) {
            this.add_review.editor_id = job?.user_work?.user_id || null
            this.add_review.image_job_id = job.id
            this.add_review.editor_name = job?.user_work?.user?.first_name || ''
            this.reviewModal.show();
        },
        uploadFileChange(e) {
            const tmpFiles = e.target.files
            if (tmpFiles.length === 0) {
                return false;
            }
            const file = tmpFiles[0]
            this.upload_file.file = file
        },
        clearUploadFile() {
            this.upload_file.file_name = '';
            this.upload_file.file_name_valid = false
            this.upload_file.file = null
            this.upload_file.file_valid = false
            this.$refs.fileupload.value = null;
        },
        async uploadFile() {
            let error = false
            if (!this.upload_file.file_name) {
                this.upload_file.file_name_valid = 'This field is required'
                error = true
            }
            if (!this.upload_file.file) {
                this.upload_file.file_valid = 'This field is required'
                error = true
            }
            if (error) {
                return
            }
            try {
                this.showLoader();
                let data = new FormData();
                data.append('file', this.upload_file.file);
                data.append('file_name', this.upload_file.file_name);
                await this.$http.postAuth(`${this.$http.apiUrl()}profile/upload-file`, data);
                this.clearUploadFile();
                await this.getProfileUser()
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.addFileModal.hide()
            this.hideLoader();
        },
        openModalAddFile() {
            this.addFileModal.show();
        },
        async isQueryRouteParam() {
            let code = this.$route.query.code ?? null
            let shop = this.$route.query.shop ?? null
            if (code && shop) {
                await this.generateShopifyToken(code, shop)
            }
            if (code && !shop) {
                await this.generateFacebookToken(code)
            }
        },
        mainImage(job) {
            if (job?.images[0]?.src_cropped) {
                return job?.images[0].src_cropped
            }
            return job?.images[0]?.src
        },
        acceptance(job) {
            return 100 - (job.images_decline.length * 100) / job.images.length
        },
        timerFrame(job) {
            return parseInt((job.finished_worked_images_sum_sum_timers / job.images.length) / 60)
        },
        async generateFacebookToken(code) {
            try {
                this.showLoader();
                await this.$http.postAuth(`${this.$http.apiUrl()}connect-instagram`, {
                    'code': code,
                });
                await this.$router.replace({'query': null});
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();
        },
        async generateShopifyToken(code, shop) {
            try {
                this.showLoader();
                await this.$http.postAuth(`${this.$http.apiUrl()}get-token-shopify`, {
                    'code': code,
                    'shop': shop
                });
                await this.$router.replace({'query': null});
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
        validationForm() {
            let error = false
            if (!this.style_guide.name) {
                this.errors.name = true
                error = true
            }
            if (!this.style_guide.remove_background) {
                this.errors.remove_background = true
                error = true
            }
            if (!this.style_guide.size_with) {
                this.errors.size_with = true
                error = true
            }
            if (!this.style_guide.size_height) {
                this.errors.size_height = true
                error = true
            }
            if (!this.style_guide.unit_measurement) {
                this.errors.unit_measurement = true
                error = true
            }
            if (!this.style_guide.file_format) {
                this.errors.file_format = true
                error = true
            }
            if (!this.style_guide.resolution) {
                this.errors.resolution = true
                error = true
            }
            if (!this.style_guide.resolution_units) {
                this.errors.resolution_units = true
                error = true
            }
            if (!this.style_guide.file_id_video_instructions) {
                this.errors.video_instructions = true
                error = true
            }
            if (!this.style_guide.color_profile) {
                this.errors.color_profile = true
                error = true
            }
            return error
        },
        async saveStyleGuide() {
            if (this.validationForm()) {
                return
            }
            try {
                this.showLoader();

                const response = await this.$http.postAuth(`${this.$http.apiUrl()}profile/add-style-guide`, this.style_guide);
                this.clearForm();
                this.add_style_guide = false;
                this.add_style_guide_mob = false;
                await this.getProfileUser();
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();
        },
        uploadFileLoad(e) {
            const tmpFiles = e.target.files
            if (tmpFiles.length === 0) {
                return false;
            }
            this.uploaded_logo = tmpFiles[0]

        },
        uploadFileWaterMark(e) {
            const tmpFiles = e.target.files
            if (tmpFiles.length === 0) {
                return false;
            }
            this.upload_watermark = tmpFiles[0]
        },
        uploadVideoInstruction(e) {
            const tmpFiles = e.target.files
            if (tmpFiles.length === 0) {
                return false;
            }
            this.video_instructions = tmpFiles[0]
        },
        openFileLogo() {
            this.$refs.logo_file.click();
        },
        openFileWatermark() {
            this.$refs.watermark.click();
        },
        openFileVideoInstructions() {
            this.$refs.video_instructions_file.click();
        },
        openFullImg(img) {
            this.image_modal = null;
            this.photoModal.show();
        },
        addStyleGuideMob() {
            this.add_style_guide_mob = !this.add_style_guide_mob
            this.clearForm()
        },

        selectStyleG(val) {
            if (val == this.selected_style_guide) {
                this.selected_style_guide = ''
            } else {
                this.selected_style_guide = val
            }
        },
        selectWork(val) {
            if (val == this.select_work) {
                this.select_work = ''
            } else {
                this.select_work = val
            }
        },
        selectFile(val) {
            if (val == this.select_file) {
                this.select_file = ''
            } else {
                this.select_file = val
            }
        },
        selectTab(val) {
            this.tab = val
        },
        typeUser(data) {
            this.is_type_user = data;
        },
        goToChats() {
            this.$router.push({name: 'ChatsList'}).then();
        },
        keypressSearchSampleWork() {

        },
        keypressSearchFile(event) {
            let search_text = (event.target.value).toUpperCase()
            if (search_text && search_text.length > 1) {
                this.search_file = this.user_files.filter(file => (file.file_name).toUpperCase().includes(search_text));
            } else {
                this.search_file = this.user_files
            }
        },
        goPageFile(url) {
            window.open(url, '_blank');
        },
        addStyleGuide() {
            this.add_style_guide = !this.add_style_guide
            this.clearForm()
        },
        getAvatar(user) {
            return user?.avatar?.url || null
        },
        async getProfileUser() {
            try {
                this.showLoader();
                const response = await this.$http.getAuth(`${this.$http.apiUrl()}profile/user`);
                this.user = response?.data?.data?.user || {}
                this.past_jobs = response?.data?.data?.past_job || {}
                this.is_type_user = response?.data?.data?.user?.type_user || false
                this.style_guides = response?.data?.data?.user?.style_guide || {}
                this.user_files = response?.data?.data?.user?.user_files || {}
                this.search_file = response?.data?.data?.user?.user_files || {}
                this.editor_reviews = response?.data?.data?.user?.editor_reviews || {}
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.load_page = true
            this.hideLoader();
        },
        isMobile() {
            return window.innerWidth <= 992;
        },
        fileName(file) {
            return file?.name || 'Choose from files...'
        },
        clearForm() {
            this.style_guide.name = '';
            this.style_guide.remove_background = '';
            this.style_guide.size_with = '';
            this.style_guide.size_height = '';
            this.style_guide.unit_measurement = '';
            this.style_guide.file_format = '';
            this.style_guide.color_profile = '';
            this.style_guide.resolution = '300';
            this.style_guide.resolution_units = 'dpi';
            this.style_guide.other = '';

            this.style_guide.file_id_logo = ''
            this.style_guide.file_id_watermark = '';
            this.style_guide.file_id_video_instructions = '';

            this.errors.name = false;
            this.errors.remove_background = false;
            this.errors.size_with = false;
            this.errors.size_height = false;
            this.errors.unit_measurement = false;
            this.errors.file_format = false;
            this.errors.resolution_units = false;
            this.errors.resolution = false;
            this.errors.video_instructions = false;
            this.errors.color_profile = false;
        }
    },
    computed: {
        avatar() {
            return this.user?.avatar?.url || null;
        }
        ,
        showJobDetailsMob() {
            return this.isMobile()
        }
        ,

    }
}
</script>
<style lang="scss" scoped>
.details_work_images {
    display: none;
    position: absolute;
    top: 0;
    width: 100%;
    height: 100%;
    padding: 10px 13px;
    flex-direction: column;
    justify-content: center;
}

.details_work_images > div {
    font-style: normal;
    font-weight: 700;
    font-size: 16px;
    line-height: 20px;
    letter-spacing: -0.02em;
    color: #494949;
    overflow-x: hidden;
    text-overflow: ellipsis;
}

.img_div_editor:hover {
    .card-img-top {
        opacity: 0.3;
    }

    .details_work_images {
        display: flex;
    }

    .name_div.main_text {
        display: none;
    }
}

.modal-title {
    width: 100%;
    text-align: center;
}

.page_profile {
    margin-top: 177px;
    margin-bottom: 230px;
}

.with_video_i {
    padding-left: 15px;
}

.mt_29 {
    margin-top: 29px;
}

.page_header {
    display: flex;
    gap: 3%;
}

.left_column {
    width: 55%;
}

.right_column {
    width: 45%;
}

.left_column {
    background: #FFFFFF;
    box-shadow: 0 4px 4px rgba(0, 0, 0, 0.25);
    border-radius: 5px;
    padding: 49px 40px;
}

.title-1 {
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    line-height: 22px;
    color: #494949;
}

.avatar {
    border-radius: 50%;
    width: 75px;
    height: 75px;
    object-fit: cover;
}

.user_name {
    font-style: normal;
    font-weight: 600;
    font-size: 20px;
    line-height: 44px;
    color: #494949;
    margin-left: 28px;
    display: flex;
    align-items: center;
}

.avatar_name {
    display: flex;
}

.avatar_column {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.info_user {
    margin-top: 50px;
    margin-left: 15px;
}

.item_info {
    display: flex;
    align-items: center;
}

.title_info {
    font-style: normal;
    font-weight: 400;
    font-size: 17px;
    line-height: 30px;
    color: #494949;
    margin-left: 11px;
}

.item_info_mt {
    margin-top: 15px;
}

.item_info_mb {
    margin-bottom: 15px;
}

.bio_title {
    padding-top: 30px;
    font-style: normal;
    font-weight: 600;
    font-size: 20px;
    line-height: 44px;
    color: #494949;
}

.bio_text {
    font-style: normal;
    font-weight: 400;
    font-size: 17px;
    line-height: 24px;
    color: #494949;
    padding-top: 12px;
}

.inbox {
    background: #FFFFFF;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.15);
    border-radius: 5px;
    display: flex;
    padding: 22px 36px;
    align-items: center;
    width: 80%;
}

.inbox_text {
    font-style: normal;
    font-weight: 400;
    font-size: 16px;
    line-height: 20px;
    color: #494949;
    display: flex;
    gap: 5px;

    .count_message {
        color: red;
    }
}

.plus_icon {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    object-fit: cover;
}

///////////Past job
.active_jobs_row {
    width: 100%;
    height: 300px;
    overflow: auto;
    white-space: nowrap;
    margin-top: 14px;
}

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

    span {
        color: #dc3545;
        margin: 0 5px;
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
    border: 0.2px rgba(0, 0, 0, 0.2) solid;
}

.img_div {
    width: 225px;
    height: 217px;
    overflow: hidden;
    border-radius: 5px;
    margin-bottom: 6px;
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

.main_text {
    color: #494949;
    font-style: normal;
    font-family: $primary_font_family;
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

.card_body {
    width: 200px;
    height: 20px;
    margin: 5px auto;
    font-weight: normal;
    font-size: 10px;
    line-height: 12px;
}

//////
.tab_pages {
    border-bottom: 1.5px solid rgba(73, 73, 73, 0.25);
    display: flex;
    cursor: pointer;
    margin-top: 60px;
}

.item_tab {
    font-style: normal;
    font-weight: 600;
    font-size: 24px;
    color: rgba(73, 73, 73, 0.6);
    padding: 25px 0;
    text-align: center;
}

.item_tab_active {
    color: #494949;
    border-bottom: 5.5px solid #494949;
}

.add_button {
    width: 241px;
    height: 237px;
    border: none;
    background: #FFFFFF;
    box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.25);
    border-radius: 5px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.main_text {
    color: #494949;
    font-style: normal;
    font-family: $primary_font_family;
    font-weight: 600;
    font-size: 18px;
    line-height: 22px;
}

.m_t_38 {
    margin-top: 38px;
}

.tab_item_d {
    margin-top: 64px;
    display: flex;
    gap: 55px;
}

.tab_item_d2 {
    margin-top: 64px;
    gap: 55px;
}

.selects_tab_item {
    display: flex;
    gap: 21px;
    flex-direction: column;
    width: calc(100% - 241px);
}

.box_select {
    box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.25);
    border-radius: 5px;
    padding: 0 36px;
}

.select_tab1 {
    background: #FFFFFF;
    display: flex;
    justify-content: space-between;

    cursor: pointer;
    height: 65px;
    align-items: center;
}

.title-2 {
    font-style: normal;
    font-weight: 600;
    font-size: 24px;
    line-height: 44px;
    color: #494949;
}

.select_item {
    margin-top: 36px;
    margin-bottom: 40px;
}

.list_onesies > li {
    font-style: normal;
    font-weight: 600;
    white-space: nowrap;
    font-size: 17px;
    line-height: 40px;
    color: #000000;
    display: flex;
    width: 50%;
}

.list_onesies div {
    font-style: normal;
    font-weight: 400;
    font-size: 17px;
    line-height: 40px;
    color: #000000;
    display: flex;
    justify-content: end;
    width: 100%;
}

.item_sample_work {
    display: flex;
    justify-content: space-between;
    cursor: pointer;

}

.spw_item, .spw_item2 {
    background: #FFFFFF;
    box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.25);
    border-radius: 5px;
    padding: 12px;
}

.arrow_select {
    display: flex;
    align-items: center;
    padding-right: 20px;
}

.sample_work {
    display: flex;
    flex-direction: column;
    width: 100%;
    gap: 21px;
}

.box_sample_work_img_item {
    width: 158px;
    height: 151px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.img_item_w {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.title_name_work {
    font-style: normal;
    font-weight: 600;
    font-size: 24px;
    line-height: 44px;
    color: #494949;
    margin-left: 55px;
    display: flex;
    align-items: center;
}

.box_images_work {
    margin-top: 38px;
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.search_sample_work {
    width: 30%;
    position: relative;
    margin-bottom: 44px;
}

.box_search_sample_work {
    width: 100%;
    display: flex;
    justify-content: end;
}

.search_icon {
    position: absolute;
    left: 16px;
    top: 11px;
    cursor: pointer;
}

.search_prod_inp {
    background: #FFFFFF;
    border: 0.3px solid #494949;
    box-sizing: border-box;
    border-radius: 100px;
    height: 35px;
    width: 100%;
    padding-left: 45px;
}

.box_list_img {
    background: #FFFFFF;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.15);
    flex: 1 1 9em;
    max-width: 170px;
    height: 160px;
    border-radius: 5px;
    padding: 5px;
    -o-object-fit: cover;
    object-fit: cover;

}

.box_list_img img {
    width: 100%;
    -o-object-fit: cover;
    object-fit: cover;
    height: 100%;
}

.box_add_style_guide {
    display: none;
}

.photo_full {
    width: 100%;
    height: 100%;
    object-fit: cover;
    padding: 23px;
    background: white;
    border-radius: 5px;
}

#photoModal .modal-content {
    background: none;
    border: none;
}

#photoModal .modal-header {
    border: none;
    padding: 0;
}

.style_guide_box {
    width: 100%;
    margin-top: 50px;
    background: #FFFFFF;
    border-radius: 5px;
    border: 1px solid #ddd;
    box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
    padding: 27px 58px;
}

.form_style_guide {
    margin-top: 57px;
    display: flex;
    gap: 30px;
}

.title_form {
    font-style: normal;
    font-weight: 500;
    font-size: 18px;
    line-height: 44px;
    color: #000000;
}

.input_item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-left: 26px;
}

.style_input1 {
    font-style: normal;
    font-weight: 400;
    font-size: 13px;
    line-height: 18px;
    border: 0.3px solid #494949;
    box-sizing: border-box;
    border-radius: 100px;
    height: 30px;
    padding: 6px 19px;
    width: 100%;
}

.with_input1 {
    width: 50px !important;
    padding: 0px 12px !important;
}

.x {
    font-style: normal;
    font-weight: 400;
    font-size: 13px;
    line-height: 18px;
    padding: 7px;
}

.btn-add_s {
    background: #D8C3AF;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.25);
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

input::placeholder {
    font-style: normal;
    font-weight: 400;
    font-size: 13px;
    line-height: 18px;
    color: rgba(0, 0, 0, 0.45);
}

.btn_form {
    width: 100%;
    display: flex;
    justify-content: center;
    padding-top: 70px;
    padding-bottom: 20px;
}

.input_b1 {
    width: 40%;
}

.form-select {
    border-radius: 19px;
    color: rgba(74, 74, 74, 0.6);
    border-color: rgba(74, 74, 74, 0.6);
    overflow: hidden;
    text-overflow: ellipsis;
}

.form-select:focus {
    border-color: rgba(74, 74, 74, 0.6);
}

.st_text {
    font-style: normal;
    font-weight: 400;
    font-size: 13px;
    line-height: 18px;
    color: rgba(0, 0, 0, 0.45);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: pre;
}

.card-img-top {
    opacity: 0.7;
}

.editor_b {
    color: rgba(73, 73, 73, 0.65);
}

.form-control.is-invalid, .form-control:invalid, .form-select:invalid, .form-select.is-invalid, .is-invalid_c {
    border-color: #dc3545 !important;
}

.invalid-feedback, .invalid-feedback_c {
    font-style: normal;
    font-weight: 400;
    font-size: 11px;
    color: #dc3545;
}

.video_href {
    text-decoration: none;
}

@media only screen and (max-width: 992px) {
    .style_guide_box {
        display: none;
    }
    .inbox {
        padding: 22px 20px;
    }
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
    .inbox {
        width: 100%;
    }
    .title_info {
        margin-left: 5px;
    }
    .item_info_mt {
        margin-top: 8px;
    }
    .item_info_mb {
        margin-bottom: 8px;
    }
    .app-page.stp_1 {
        min-height: calc(100vh - 205px);
    }
    /////////
    .right_column {
        display: none;
    }
    .left_column {
        width: 100%;
        box-shadow: none;
        background: none;
        padding: 36px 31px;
    }
    .page_profile {
        margin-top: 25px;
    }
    .user_name {
        align-items: end;
        font-size: 18px;
        line-height: 22px;
        margin-bottom: 10px;
    }
    .info_user {
        margin-left: 0;
        margin-top: 24px;
    }
    .title_info {
        font-weight: 500;
        font-size: 12.5px;
        line-height: 20px;
    }
    .bio_text {
        font-weight: 500;
        font-size: 12.5px;
        line-height: 20px;
    }
    .bio_title {
        font-weight: 600;
        font-size: 18px;
        line-height: 22px;
    }
    .tab_pages {
        margin-top: 0;
    }
    .item_tab {
        font-weight: 600;
        font-size: 15px;
        line-height: 18px;
        padding: 10px 0;
    }
    .add_button {
        display: none;
    }
    .selects_tab_item {
        width: 100%;
        padding: 0 14px;
        gap: 15px;
    }
    .box_select {
        background: white;
        padding: 0 26px 0 30px;
    }
    .tab_item_d {
        margin-top: 12px;
        margin-bottom: 130px;
    }
    .title-2 {
        font-weight: 600;
        font-size: 18px;
        line-height: 22px;
    }
    .select_item {
        margin-top: 0;
    }
    .list_onesies {
        padding-left: 0;
    }
    .list_onesies > li {
        font-weight: 700;
        font-size: 15px;
        line-height: 30px;
        width: 100%;
    }
    .list_onesies div {
        font-size: 15px;
        line-height: 30px;
    }
    .item_tab_active {
        border-bottom: 2.3px solid #494949;
    }
    .box_add_style_guide {
        display: block;
    }
    .page_profile {

        margin-bottom: 10px;
    }
    .tab_item_d2 {
        margin-top: 22px;
        padding: 0 12px;
    }
    .search_sample_work {
        margin-bottom: 16px;
        width: 100%;
    }
    .box_sample_work_img_item {
        width: 104px;
        height: 100px;
    }
    .title_name_work {
        margin-left: 8px;
        font-weight: 700;
        font-size: 22px;
        line-height: 27px;
    }
    .spw_item {
        margin-bottom: 105px;
    }
    .margin_box_f {
        margin-bottom: 105px;
    }
    .box_images_work {
        margin-top: 21px;
    }
    .input_item {
        padding-left: 0;
    }
    .input_b1 {
        width: auto;
    }
    .stmob {
        margin-left: 10px;
    }
    .style_input1 {
        padding: 6px 30px 6px 12px;
        width: 200px;
    }
    .title_form {
        font-weight: 400;
        font-size: 13px;
        line-height: 18px;
    }
    .mobile_guide_box {
        gap: 13px;
        display: flex;
        flex-direction: column;
        margin-bottom: 25px;
        margin-top: 10px;
    }
    .btn_form {
        padding-top: 23px;
        padding-bottom: 0;
    }
}


.featured-products {
    margin-top: 20px;
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    grid-gap: 5px;
}

.style_icon {
    width: 27px;
    height: 27px;
}

.modal-footer {
    border-top: none;
    display: flex;
    justify-content: center;
    padding-bottom: 30px;
    gap: 35px;
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

.featured-product-item {
    border-radius: 5px;
    overflow: hidden;
    text-align: center;
    box-sizing: border-box;
    border: 1px solid #ddd;
    cursor: pointer;
}

.featured-product-item-image {
    height: 100%;
    width: 100%;
    object-fit: cover;
    background-size: cover;
    background-position: center;
}

#fileNameInput1:focus, #fileInput2:focus {
    border-color: #cccccc;
    box-shadow: none;
}

.file_type {
    font-style: normal;
    font-weight: 400;
    font-size: 17px;
    line-height: 21px;
    letter-spacing: -0.02em;
    color: #2555FF;
}

.box_file_n {
    margin-left: 55px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.title_file_name {
    font-style: normal;
    font-weight: 600;
    font-size: 24px;
    line-height: 44px;
    color: #494949;
}

.img_file {
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
}

.type_box_f {
    display: flex;
    justify-content: center;
    align-items: center;
    font-style: normal;
    font-weight: 600;
    font-size: 42px;
    line-height: 44px;
    color: #000000;
    text-transform: uppercase;
}

.leave-review {
    font-style: normal;
    font-weight: 400;
    font-size: 17px;
    line-height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration-line: underline;
    color: #494949;
}

.review_title {
    padding: 25px 0;
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
    justify-content: center;
}

.review_colum {
    background: #FFFFFF;
    box-shadow: 0 4px 4px rgba(0, 0, 0, 0.25);
    border-radius: 5px;
    width: 52%;
    margin-top: 43px;
    padding: 27px 0 27px 32px;
}

.title_r {
    font-style: normal;
    font-weight: 600;
    font-size: 24px;
    line-height: 44px;
    color: #494949;
}

.avatar {
    border-radius: 50%;
    width: 50px;
    height: 50px;
    object-fit: cover;
    border: 0.3px solid #494949;
}

.header_box_review {
    display: flex;
    align-items: center;
    gap: 28px;
}

.review_message {
    font-style: italic;
    font-weight: 400;
    font-size: 17px;
    line-height: 30px;
    color: #494949;

}

.review_item {
    padding-bottom: 20px;
    border-bottom: 0.6px solid rgba(73, 73, 73, 0.65);
}

.review_items {
    display: flex;
    flex-direction: column;
    gap: 30px;
    margin-top: 80px;
    height: 365px;
    overflow: auto;
    padding-right: 32px;
}

@media (max-width: 992px) {
    .style_icon {
        width: 17px;
        height: 17px;
    }
    .featured-products {
        grid-template-columns: repeat(3, 1fr);
    }
    .with_watermark {
        max-width: 205px;
    }
    .with_video_i {
        max-width: 170px;
    }
    .with_logo {
        max-width: 220px;
    }
    .modal-footer {
        border-top: none;
        display: flex;
        justify-content: center;
        padding-bottom: 30px;
        gap: 15px;
    }
    .box_file_n {
        margin-left: 20px;
    }
    .type_box_f {
        font-size: 25px;
    }
    .review_colum {
        background: none;
        width: 100%;
        height: 530px;
        padding-bottom: 90px;
    }
    .review_items {
        margin-top: 20px;
    }
}
</style>
