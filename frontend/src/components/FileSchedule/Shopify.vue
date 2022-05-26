<template>
    <div>
        <div class="box">
            <div class="box_img">
                <img class="" :src="getFirstImg">
            </div>
            <div class="box_textarea">
                <textarea v-model="description" placeholder="Write a caption... " class="textarea" id=""></textarea>
            </div>
        </div>
        <div class="box_i">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class=" tabs_items">
                        </div>
                        <div class="box_tab_i">
                            <div class="box_select">
                                <div class="select_calendar" @click="selectProduct">
                                    <div class="title_s">
                                        Product {{ getProd }}
                                    </div>
                                    <div class="col-2 text-end">
                                        <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M18.8528 6.94727C18.6066 6.70121 18.2729 6.56299 17.9248 6.56299C17.5768 6.56299 17.243 6.70121 16.9969 6.94727L10.5 13.4441L4.00315 6.94727C3.75561 6.70819 3.42407 6.5759 3.07994 6.57889C2.73581 6.58188 2.40661 6.71991 2.16327 6.96326C1.91992 7.20661 1.78189 7.5358 1.7789 7.87993C1.77591 8.22406 1.9082 8.5556 2.14728 8.80314L9.57209 16.228C9.81822 16.474 10.152 16.6122 10.5 16.6122C10.8481 16.6122 11.1818 16.474 11.428 16.228L18.8528 8.80314C19.0988 8.55701 19.2371 8.22324 19.2371 7.87521C19.2371 7.52718 19.0988 7.1934 18.8528 6.94727Z"
                                                fill="#494949"/>
                                        </svg>
                                    </div>
                                </div>
                                <div v-if="select_product" class="calendar_box">
                                    <div class="box_search_i">
                                        <input @keyup="keypressSearchProduct" type="text" class="search_prod_inp"
                                               placeholder="name, ID, SKU">
                                        <img class="search_icon" src="@/assets/icons/search.svg" alt="">
                                    </div>
                                    <div v-for="prod in shopify_products" class="box_search_prod_shopify">
                                        <div class="prod_info">
                                            <div class="prod_info_item">{{ prod.title }}</div>
                                            <div class="prod_info_item">SKU {{ prod.variants[0].sku }}</div>
                                            <div class="prod_info_item">ID {{ prod.id }}</div>
                                        </div>
                                        <div class="box_add_p" v-if="(added_prod&&added_prod['id']==prod['id'])">
                                            <button class="btn_added_prod" @click="addProd(prod)">Added </button>
                                        </div>
                                        <div v-else class="box_add_p">
                                            <button class="btn_add_prod" @click="addProd(prod)">Add </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box_select">
                                <div class="select_calendar" @click="selectOrder">
                                    <div class="title_s">
                                        Order
                                    </div>
                                    <div class="col-2 text-end">
                                        <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M18.8528 6.94727C18.6066 6.70121 18.2729 6.56299 17.9248 6.56299C17.5768 6.56299 17.243 6.70121 16.9969 6.94727L10.5 13.4441L4.00315 6.94727C3.75561 6.70819 3.42407 6.5759 3.07994 6.57889C2.73581 6.58188 2.40661 6.71991 2.16327 6.96326C1.91992 7.20661 1.78189 7.5358 1.7789 7.87993C1.77591 8.22406 1.9082 8.5556 2.14728 8.80314L9.57209 16.228C9.81822 16.474 10.152 16.6122 10.5 16.6122C10.8481 16.6122 11.1818 16.474 11.428 16.228L18.8528 8.80314C19.0988 8.55701 19.2371 8.22324 19.2371 7.87521C19.2371 7.52718 19.0988 7.1934 18.8528 6.94727Z"
                                                fill="#494949"/>
                                        </svg>
                                    </div>
                                </div>
                                <div v-if="select_order" class="calendar_box">
                                    <draggable v-model="worked_image">
                                        <transition-group class="box_drop_items">
                                            <div class="box_list_img" v-for="element in worked_image" :key="element.id">
                                                <img :src="element.image_url" alt="">
                                            </div>
                                        </transition-group>
                                    </draggable>
                                </div>
                            </div>
                            <div class="box_select">
                                <div class="select_calendar" @click="selectSaleChannel">
                                    <div class="title_s">
                                        Sale Channel
                                    </div>
                                    <div class="col-2 text-end">
                                        <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M18.8528 6.94727C18.6066 6.70121 18.2729 6.56299 17.9248 6.56299C17.5768 6.56299 17.243 6.70121 16.9969 6.94727L10.5 13.4441L4.00315 6.94727C3.75561 6.70819 3.42407 6.5759 3.07994 6.57889C2.73581 6.58188 2.40661 6.71991 2.16327 6.96326C1.91992 7.20661 1.78189 7.5358 1.7789 7.87993C1.77591 8.22406 1.9082 8.5556 2.14728 8.80314L9.57209 16.228C9.81822 16.474 10.152 16.6122 10.5 16.6122C10.8481 16.6122 11.1818 16.474 11.428 16.228L18.8528 8.80314C19.0988 8.55701 19.2371 8.22324 19.2371 7.87521C19.2371 7.52718 19.0988 7.1934 18.8528 6.94727Z"
                                                fill="#494949"/>
                                        </svg>
                                    </div>
                                </div>
                                <div v-if="select_sale_channel" class="calendar_box">

                                </div>
                            </div>
                            <div class="box_select">
                                <div class="select_calendar" @click="selectCalendar">
                                    <div class="title_s">
                                        Schedule
                                    </div>
                                    <div class="col-2 text-end">
                                        <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M18.8528 6.94727C18.6066 6.70121 18.2729 6.56299 17.9248 6.56299C17.5768 6.56299 17.243 6.70121 16.9969 6.94727L10.5 13.4441L4.00315 6.94727C3.75561 6.70819 3.42407 6.5759 3.07994 6.57889C2.73581 6.58188 2.40661 6.71991 2.16327 6.96326C1.91992 7.20661 1.78189 7.5358 1.7789 7.87993C1.77591 8.22406 1.9082 8.5556 2.14728 8.80314L9.57209 16.228C9.81822 16.474 10.152 16.6122 10.5 16.6122C10.8481 16.6122 11.1818 16.474 11.428 16.228L18.8528 8.80314C19.0988 8.55701 19.2371 8.22324 19.2371 7.87521C19.2371 7.52718 19.0988 7.1934 18.8528 6.94727Z"
                                                fill="#494949"/>
                                        </svg>
                                    </div>
                                </div>
                                <div v-if="select_calendar" class="calendar_box">
                                    <Calendar @selected_date="selectedDate"></Calendar>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="box_form2">
                            <div class="box_form2_item">
                                <div class="text_st1 w_text">Add Alt Text</div>
                                <input v-model="alt_text" type="text" class="input_text_stile"
                                       placeholder="Type something...">
                            </div>
                        </div>
                        <div class="time_date" v-if="select_calendar">
                            <div class="box_time">
                                <select v-model="hour" class="time_b">
                                    <option v-for="item in hours()" :value="item">{{ item }}</option>
                                </select>
                                <div class="colon">:</div>
                                <select v-model="minute" class="time_b">
                                    <option v-for="min in minutes()" :value="min">{{ min }}</option>
                                </select>
                                <div>
                                    <div class="box_12" :class="{active_am_pm:am_pm==='AM'}" @click="amPm('AM')">
                                        AM
                                    </div>
                                    <div class="box_12" :class="{active_am_pm:am_pm==='PM'}" @click="amPm('PM')">
                                        PM
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box_i_mobile">
            <div class="box_select">
                <div class="select_calendar" @click="selectProduct">
                    <div class="title_s">
                        Product {{ getProd }}
                    </div>
                    <div class="col-2 text-end">
                        <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M18.8528 6.94727C18.6066 6.70121 18.2729 6.56299 17.9248 6.56299C17.5768 6.56299 17.243 6.70121 16.9969 6.94727L10.5 13.4441L4.00315 6.94727C3.75561 6.70819 3.42407 6.5759 3.07994 6.57889C2.73581 6.58188 2.40661 6.71991 2.16327 6.96326C1.91992 7.20661 1.78189 7.5358 1.7789 7.87993C1.77591 8.22406 1.9082 8.5556 2.14728 8.80314L9.57209 16.228C9.81822 16.474 10.152 16.6122 10.5 16.6122C10.8481 16.6122 11.1818 16.474 11.428 16.228L18.8528 8.80314C19.0988 8.55701 19.2371 8.22324 19.2371 7.87521C19.2371 7.52718 19.0988 7.1934 18.8528 6.94727Z"
                                fill="#494949"/>
                        </svg>
                    </div>
                </div>
                <div v-if="select_product" class="calendar_box">
                    <div class="box_search_i">
                        <input @keyup="keypressSearchProduct" type="text" class="search_prod_inp"
                               placeholder="name, ID, SKU">
                        <img class="search_icon" src="@/assets/icons/search.svg" alt="">
                    </div>
                    <div v-for="prod in shopify_products" class="box_search_prod_shopify">
                        <div class="prod_info">
                            <div class="prod_info_item">{{ prod.title }}</div>
                            <div class="prod_info_item">SKU {{ prod.variants[0].sku }}</div>
                            <div class="prod_info_item">ID {{ prod.id }}</div>
                        </div>
                        <div class="box_add_p" v-if="added_prod&&added_prod['id']!==prod['id']">
                            <button class="btn_add_prod"  @click="addProd(prod)">Add</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box_select">
                <div class="select_calendar" @click="selectOrder">
                    <div class="title_s">
                        Order
                    </div>
                    <div class="col-2 text-end">
                        <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M18.8528 6.94727C18.6066 6.70121 18.2729 6.56299 17.9248 6.56299C17.5768 6.56299 17.243 6.70121 16.9969 6.94727L10.5 13.4441L4.00315 6.94727C3.75561 6.70819 3.42407 6.5759 3.07994 6.57889C2.73581 6.58188 2.40661 6.71991 2.16327 6.96326C1.91992 7.20661 1.78189 7.5358 1.7789 7.87993C1.77591 8.22406 1.9082 8.5556 2.14728 8.80314L9.57209 16.228C9.81822 16.474 10.152 16.6122 10.5 16.6122C10.8481 16.6122 11.1818 16.474 11.428 16.228L18.8528 8.80314C19.0988 8.55701 19.2371 8.22324 19.2371 7.87521C19.2371 7.52718 19.0988 7.1934 18.8528 6.94727Z"
                                fill="#494949"/>
                        </svg>
                    </div>
                </div>
                <div v-if="select_order" class="calendar_box">
                    <draggable v-model="worked_image">
                        <transition-group class="box_drop_items">
                            <div class="box_list_img" v-for="element in worked_image" :key="element.id">
                                <img :src="element.image_url" alt="">
                            </div>
                        </transition-group>
                    </draggable>
                </div>
            </div>
            <div class="box_select">
                <div class="select_calendar" @click="selectSaleChannel">
                    <div class="title_s">
                        Sale Channel
                    </div>
                    <div class="col-2 text-end">
                        <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M18.8528 6.94727C18.6066 6.70121 18.2729 6.56299 17.9248 6.56299C17.5768 6.56299 17.243 6.70121 16.9969 6.94727L10.5 13.4441L4.00315 6.94727C3.75561 6.70819 3.42407 6.5759 3.07994 6.57889C2.73581 6.58188 2.40661 6.71991 2.16327 6.96326C1.91992 7.20661 1.78189 7.5358 1.7789 7.87993C1.77591 8.22406 1.9082 8.5556 2.14728 8.80314L9.57209 16.228C9.81822 16.474 10.152 16.6122 10.5 16.6122C10.8481 16.6122 11.1818 16.474 11.428 16.228L18.8528 8.80314C19.0988 8.55701 19.2371 8.22324 19.2371 7.87521C19.2371 7.52718 19.0988 7.1934 18.8528 6.94727Z"
                                fill="#494949"/>
                        </svg>
                    </div>
                </div>
                <div v-if="select_sale_channel" class="calendar_box">

                </div>
            </div>
            <div class="box_select">
                <div class="select_calendar" @click="selectCalendar">
                    <div class="title_s">
                        Schedule
                    </div>
                    <div class="col-2 text-end">
                        <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M18.8528 6.94727C18.6066 6.70121 18.2729 6.56299 17.9248 6.56299C17.5768 6.56299 17.243 6.70121 16.9969 6.94727L10.5 13.4441L4.00315 6.94727C3.75561 6.70819 3.42407 6.5759 3.07994 6.57889C2.73581 6.58188 2.40661 6.71991 2.16327 6.96326C1.91992 7.20661 1.78189 7.5358 1.7789 7.87993C1.77591 8.22406 1.9082 8.5556 2.14728 8.80314L9.57209 16.228C9.81822 16.474 10.152 16.6122 10.5 16.6122C10.8481 16.6122 11.1818 16.474 11.428 16.228L18.8528 8.80314C19.0988 8.55701 19.2371 8.22324 19.2371 7.87521C19.2371 7.52718 19.0988 7.1934 18.8528 6.94727Z"
                                fill="#494949"/>
                        </svg>
                    </div>
                </div>
                <div v-if="select_calendar" class="calendar_box">
                    <Calendar @selected_date="selectedDate"></Calendar>
                    <div class="time_date_mobile" v-if="select_calendar">
                        <div class="box_time">
                            <select v-model="hour" class="time_b time_b_mob">
                                <option v-for="item in hours()" :value="item">{{ item }}</option>
                            </select>
                            <div class="colon">:</div>
                            <select v-model="minute" class="time_b time_b_mob">
                                <option v-for="min in minutes()" :value="min">{{ min }}</option>
                            </select>
                            <div>
                                <div class="box_12 time_b_mob" :class="{active_am_pm:am_pm==='AM'}"
                                     @click="amPm('AM')">
                                    AM
                                </div>
                                <div class="box_12 time_b_mob" :class="{active_am_pm:am_pm==='PM'}"
                                     @click="amPm('PM')">
                                    PM
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box_form2">
                <div class="box_form2_item">
                    <div class="text_st1 w_text">Add Alt Text</div>
                    <input v-model="alt_text" type="text" class="input_text_stile" placeholder="Type something...">
                </div>
            </div>
        </div>
        <div class="btn_box">
            <button class="btn_post_n" @click="publishShopify">Publish</button>
        </div>
        <div class="modal" id="modalLoginShopify" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Shopify Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Shop</label>
                            <input @keypress="keypressShop" v-model="shop" type="text" class="form-control"
                                   id="exampleFormControlInput1" :class="{ 'is-invalid': shop_is_valid }"
                                   placeholder="Shop name">
                            <div id="emailSignUpFeedback" class="invalid-feedback">{{ shop_is_valid }}</div>
                        </div>
                        <!--                        <div class="mb-3">-->
                        <!--                            <label for="exampleFormControlInput3" class="form-label">Api Key</label>-->
                        <!--                            <input @keypress="keypressApiKey" v-model="api_key" type="text" class="form-control"-->
                        <!--                                   id="exampleFormControlInput3" :class="{ 'is-invalid': api_key_is_valid }"-->
                        <!--                                   placeholder="Api Key">-->
                        <!--                            <div id="emailSignUpFeedback3" class="invalid-feedback">{{ api_key_is_valid }}</div>-->
                        <!--                        </div>-->
                        <!--                        <div class="mb-3">-->
                        <!--                            <label for="exampleFormControlInput2" class="form-label">Password</label>-->
                        <!--                            <input @keypress="keypressPassword" v-model="password" type="password"-->
                        <!--                                   class="form-control"-->
                        <!--                                   id="exampleFormControlInput2" :class="{ 'is-invalid': password_is_valid }"-->
                        <!--                                   placeholder="Password">-->
                        <!--                            <div class="invalid-feedback">{{ password_is_valid }}</div>-->
                        <!--                        </div>-->
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <button class="btn_post_n" @click="loginShopify">Login</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="modalSuccessPosting" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-center ">
                        <h5 class="modal-title title_modal_h">Great Job!</h5>
                    </div>
                    <div class="modal-body box_md_t">
                        <div class="title_mst3">
                            Would you like to continue
                        </div>
                        <div class="title_mst3 mt-2">
                            working on the same project?
                        </div>
                    </div>
                    <div class="modal-footer box_btn_modal mb-4">
                        <button class="btn_post_n2" @click="goToHome">Go Home</button>
                        <button class="btn_post_n " data-bs-dismiss="modal" aria-label="Close">Yes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import MainLayout from "../../layouts/MainLayout";
import Calendar from "../../components/Calendar";
import {errorMessage} from "../../services/messages";
import {mapMutations} from "vuex";
import {Modal} from 'bootstrap'
import moment from 'moment-timezone'
import draggable from 'vuedraggable'

export default {
    name: 'Shopify',
    components: {
        MainLayout,
        Calendar,
        draggable
    },
    data() {
        return {
            shopify_connect: false,
            api_key: '',
            shop: '',
            password: '',
            added_prod: null,
            worked_image: {},
            am_pm: 'AM',
            hour: '0',
            minute: '00',
            job_id: this.$route.params.id,
            job: {},
            platform: 'shopify',
            description: '',
            active_table: 1,

            select_calendar: false,
            select_sale_channel: false,
            select_product: false,
            select_order: false,

            selected_date: null,
            alt_text: '',
            modalLoginShopify: null,
            modalSuccessPosting: null,

            shop_is_valid: false,
            api_key_is_valid: false,
            password_is_valid: false,

            shopify_products: {},
        }
    },
    mounted() {
        this.modalLoginShopify = new Modal(document.getElementById('modalLoginShopify'));
        this.modalSuccessPosting = new Modal(document.getElementById('modalSuccessPosting'));
        this.getJobs()
        this.shopifyConnect();
    },
    destroyed() {
        if (this.modalLoginShopify) {
            this.modalLoginShopify = null
        }
        if (this.modalSuccessPosting) {
            this.modalSuccessPosting = null
        }
    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
        ]),
        showModalConnect() {
            if (!this.shopify_connect) {
                this.modalLoginShopify.show()
            }
        },
        async shopifyConnect() {
            this.showLoader();
            try {
                const resp = await this.$http.getAuth(`${this.$http.apiUrl()}shopify-connect`);
                this.shopify_connect = resp.data.data || false;
                this.showModalConnect()
            } catch (e) {
            }
            this.hideLoader();
        },
        async loginShopify() {
            let error_form = false
            if (!this.shop) {
                this.shop_is_valid = 'This field is required'
                error_form = true
            }
            if (error_form) {
                return
            }
            this.showLoader();
            try {
                const resp = await this.$http.postAuth(`${this.$http.apiUrl()}login-shopify`, {
                    shop: this.shop,
                });
                window.open(resp?.data?.data);
                this.modalLoginShopify.hide()
            } catch (e) {
                const message = 'Invalid login or password';
                errorMessage(message)
            }
            this.hideLoader();
        },
        addProd(prod) {
            this.added_prod=prod
            console.log(this.added_prod)
        },
        async keypressSearchProduct(event) {
            let search_text = event.target.value;
            if (search_text && search_text.length > 2) {
                this.showLoader();
                try {
                    const result = await this.$http.postAuth(`${this.$http.apiUrl()}search-product-shopify`, {
                        search: search_text
                    });

                    this.shopify_products = result?.data?.data
                } catch (e) {
                    const message = e?.response?.data?.error?.message || 'ERROR';
                    errorMessage(message)
                }
                this.hideLoader();
            } else {
                this.shopify_products = {}
            }
        },
        async getJobs() {
            this.showLoader();
            if (!this.$route.params.id) {
                return errorMessage('OOPS... No ID job!');
            }
            try {
                const result = await this.$http.getAuth(`${this.$http.apiUrl()}get-job/${this.$route.params.id}`);
                this.job = result?.data?.data?.job || {}
                this.worked_image = result?.data?.data?.job?.worked_image || {}
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();
        },
        getDatePosted() {
            return moment(this.date_m).format('dddd, MMM DD') + ' at ' + this.modal_date + '!'
        },
        goToHome() {
            this.modalSuccessPosting.hide()
            this.$router.push({to: '/', name: "Home"}).then();
        },
        keypressShop() {
            this.shop_is_valid = false
        },
        keypressApiKey() {
            this.api_key_is_valid = false
        },
        keypressPassword() {
            this.password_is_valid = false
        },
        hours() {
            let h = []
            for (let i = 0; i <= 12; i++) {
                h.push('' + i);
            }
            return h;
        },
        minutes() {
            let min = []
            for (let i = 0; i < 60; i++) {
                if (i < 10) {
                    min.push('0' + i)
                } else {
                    min.push('' + i);
                }
            }
            return min;
        },
        amPm(am_pm) {
            this.am_pm = am_pm
        },

        async publishShopify() {
            if (!this.shopify_connect) {
                this.modalLoginShopify.show()
                return
            }
            if (!this.added_prod) {
                alert('Add Product')
                return
            }
            let hour = this.hour
            if (this.hour < 10) {
                hour = '0' + this.hour
            }
            let date = this.selected_date + ' ' + hour + ':' + this.minute + ' ' + this.am_pm;
            let id_images = [];
            this.worked_image.forEach((value) => {
                id_images.push(value.id)
            });
            try {
                this.showLoader();
                const response = await this.$http.postAuth(`${this.$http.apiUrl()}post-images`, {
                    image_jobs_id: this.job_id,
                    product_id: this.added_prod['id'],
                    date_publication: date,
                    sequence_pictures:id_images??[],
                    description: this.description,
                    platform: this.platform,
                    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                    api_key: this.api_key,
                    shop: this.shop,
                    password: this.password
                });
                this.modalSuccessPosting.show();
                this.clearForm();
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.modalLoginShopify.hide();

            this.hideLoader();
        },
        clearForm() {
            this.am_pm = 'AM';
            this.hour = '0';
            this.minute = '00';
            this.description = '';
            this.select_calendar = false;
            this.selected_date = null
            this.alt_text = ''

            this.shop_is_valid = false
            this.api_key_is_valid = false
            this.password_is_valid = false
        },
        selectedDate(data) {
            this.selected_date = data;
        },
        selectCalendar() {
            this.select_calendar = !this.select_calendar
        },
        selectSaleChannel() {
            this.select_sale_channel = !this.select_sale_channel
        },
        selectOrder() {
            this.select_order = !this.select_order
        },
        selectProduct() {
            this.select_product = !this.select_product
        },
        activeTab(tab) {
            this.active_table = tab;
        },
        isMobile() {
            return window.innerWidth <= 992;
        },
    },
    computed: {
        getProd() {
            if (this.added_prod?.title || '') {
                return '- ' + this.added_prod?.title || ''
            }
            return ''
        },
        showJobDetailsMob() {
            return this.isMobile()
        },
        getFirstImg() {
            if (this.job?.images) {
                return this.job?.images[0]?.src_cropped || null
            }
        }
    }
}
</script>
<style lang="scss" scoped>

.title_mst1 {
    font-style: normal;
    font-weight: 600;
    font-size: 24px;
    display: flex;
    align-items: center;
    text-align: center;
    justify-content: center;
    color: #494949;
}

.title_mst2 {
    font-style: normal;
    font-weight: 500;
    font-size: 24px;
    line-height: 50px;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    color: #494949;
}

.title_mst3 {
    font-style: normal;
    font-weight: 400;
    font-size: 20px;
    line-height: 24px;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    color: #494949;
}

.box_md_t {
    margin: 30px 0;
}

.box_btn_modal {
    display: flex;
    justify-content: center;
    gap: 40px;
}

.modal-footer {
    border-top: none;
}

.btn_post_n2 {
    background: white;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.25);
    border-radius: 100px;
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    line-height: 22px;
    color: #494949;
    width: 200px;
    height: 60px;
}

.btn_post_n {
    background: #D8C3AF;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.25);
    border-radius: 100px;
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    line-height: 22px;
    color: #FFFFFF;
    width: 200px;
    height: 60px;
}

.box_time select {
    -webkit-appearance: none;
    -moz-appearance: none;
    text-indent: 1px;
    text-overflow: '';
    text-align: center;
}

.btn-save {
    background: #FFFFFF;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.25);
    border-radius: 100px;
    font-style: normal;
    font-weight: 500;
    font-size: 18px;
    line-height: 22px;

    color: #494949;
    width: 200px;
    height: 60px;
}

.btn-c2 {
    color: white;
}

.form-check-input:checked {
    background-color: black;
    border-color: white;
}

.form-switch .form-check-input {
    height: 25px;
    width: 50px;
}

.form-switch .form-check-input:checked {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='white'/%3e%3c/svg%3e");
}

.form-switch .form-check-input {
    border-color: black;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='black'/%3e%3c/svg%3e");
}

.pr-50 {
    padding-right: 50px;
}

.box_i {
    padding-top: 70px;
    width: 100%;
    display: flex;
}

.container_page {
    padding: 70px 12px;
}

.title_page {
    font-style: normal;
    font-weight: 600;
    font-size: 35px;
    line-height: 44px;
    color: #494949;
}

.title_modal_h {
    font-style: normal;
    font-weight: 600;
    font-size: 35px;
    line-height: 44px;
    display: flex;
    text-align: center;
    align-items: center;
    justify-content: center;
    color: #000000;
    padding: 20px 0;
}

.form-select {
    border-radius: 19px;
    color: rgba(74, 74, 74, 0.6);
    border-color: rgba(74, 74, 74, 0.6);
    overflow: hidden;
    text-overflow: ellipsis;
    width: 348px;
}

.form-select:focus {
    border-color: rgba(74, 74, 74, 0.6);
}

.select_socialite {
    padding-top: 70px;
    width: 100%;
    display: flex;
    justify-content: end;
    align-items: center;
}

.title_2 {
    font-style: normal;
    font-weight: 600;
    font-size: 20px;
    line-height: 24px;
    color: #494949;
}

.box {
    display: flex;
    margin-top: 64px;
    padding: 21px;
    background: #FFFFFF;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.15);
    border-radius: 5px;
    gap: 47px;
}

.box_img {
    //width: 313px;
    //height: 300px;
    width: 28%;
    height: auto;
}

.box_img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.textarea {
    width: 100%;
    height: 300px;
    resize: none;
    border: none;
    font-style: normal;
    font-weight: 400;
    font-size: 17px;
    line-height: 24px;
    padding-top: 20px;
}

textarea::-webkit-input-placeholder {
    color: rgba(73, 73, 73, 0.45);
    font-style: normal;
    font-weight: 400;
    font-size: 17px;
    line-height: 24px;
}

textarea:-moz-placeholder {
    color: rgba(73, 73, 73, 0.45);
    font-style: normal;
    font-weight: 400;
    font-size: 17px;
    line-height: 24px;
}

.box_textarea {
    width: 72%;
    height: 100%;
}

.tabbable-panel {
    border: 1px solid #eee;
    padding: 10px;
}

.tab_item {
    width: 50%;
    display: flex;
    justify-content: center;
    font-style: normal;
    font-weight: 600;
    font-size: 24px;
    line-height: 44px;
    color: rgba(73, 73, 73, 0.6);
    cursor: pointer;
    border-bottom: 1.5px solid rgba(73, 73, 73, 0.25);
}

.active_tab {
    color: #494949;
    border-bottom: 2.3px solid #494949;

}

.tabs_items {
    display: flex;
}

.box_tab_i {
    //padding-top: 40px;
}

.item_inf {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.text_st1 {
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    line-height: 22px;
    color: #494949;
}

.items_inf {
    display: flex;
    flex-direction: column;
    gap: 25px;
    padding: 0 55px 2px 35px;
}

.input_text_stile {
    height: 30px;
    background: #FFFFFF;
    border: 0.3px solid #494949;
    box-sizing: border-box;
    border-radius: 100px;
    padding: 6px 19px;
    width: 100%;
    font-style: normal;
    font-weight: 400;
    font-size: 13px;
    line-height: 18px;
}

.box_form2_item {
    display: flex;
    align-items: center;
}

.w_text {
    width: 60%;
}

.box_form2 {
    display: flex;
    flex-direction: column;
    gap: 16px;
    padding-top: 45px;
    padding-left: 35px;
}

.btn_box {
    display: flex;
    justify-content: center;
    gap: 40px;
    padding-top: 170px;
    margin-bottom: 80px;
}

.select_calendar {
    display: flex;
    justify-content: space-between;
    padding: 21px 24px 21px 34px;
    height: 65px;
    align-items: center;
}

.box_select {
    cursor: pointer;
    margin-top: 27px;
    background: #FFFFFF;
    border-radius: 5px;
    filter: drop-shadow(0px 3px 3px rgba(0, 0, 0, 0.15));
}

.title_s {
    font-style: normal;
    font-weight: 600;
    font-size: 24px;
    line-height: 44px;
    color: #494949;
}

.box_i_mobile {
    width: 100%;
    display: none;
}

.calendar_box {
    padding: 33px;
}

.time_date_mobile {
    display: none;
}

.time_date {
    padding-top: 100px;
    display: flex;
    justify-content: center;
}

.box_time {
    display: flex;
}

.time_b {
    padding: 9px 22px;
    font-style: normal;
    font-weight: 600;
    font-size: 35px;
    line-height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #494949;
    background: #FFFFFF;
    border: 0.3px solid #494949;
    box-sizing: border-box;
    border-radius: 5px;
    height: 60px;
}

.colon {
    color: #494949;
    font-style: normal;
    font-weight: 600;
    font-size: 30px;
    line-height: 44px;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 0 5px;
}

.box_12 {
    background: #FFFFFF;
    border: 0.3px solid #494949;
    box-sizing: border-box;
    border-radius: 5px;
    font-style: normal;
    font-weight: 600;
    font-size: 15px;
    line-height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 7px;
    color: #000000;
    cursor: pointer;
    height: 30px;
    margin-left: 7px;
}

.search_icon {
    position: absolute;
    left: 16px;
    top: 14px;
    cursor: pointer;
}

.active_am_pm {
    background: rgba(216, 195, 175, 0.7);
}

.search_prod_inp {
    background: #FFFFFF;
    border: 0.3px solid #494949;
    box-sizing: border-box;
    border-radius: 100px;
    height: 41px;
    width: 100%;
    padding-left: 45px;
}

.box_search_i {
    position: relative;
}

.box_search_prod_shopify {
    margin-top: 33px;
    background: #FFFFFF;
    border: 0.2px solid #494949;
    box-sizing: border-box;
    border-radius: 5px;
    display: flex;
}

.btn_add_prod {
    background: #D8C3AF;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.25);
    border-radius: 100px;
    font-style: normal;
    font-weight: 600;
    font-size: 14px;
    line-height: 17px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #FFFFFF;
    height: 34px;
    width: 86px;
}
.btn_added_prod{
    background: #ffffff;
    border: 0.5px solid #D8C3AF;
    box-sizing: border-box;
    border-radius: 100px;
    font-style: normal;
    font-weight: 600;
    font-size: 14px;
    line-height: 17px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #D8C3AF;
    height: 34px;
    width: 86px;
    cursor: auto;
}

.prod_info {
    padding: 22px 24px;
    width: 75%;
}

.box_add_p {
    padding: 13px 15px;
    width: 25%;
    display: flex;
    justify-content: end;
    align-items: end;
}

.prod_info_item {
    font-style: normal;
    font-weight: 400;
    font-size: 16px;
    line-height: 150%;
    color: #000000;
    padding: 5px 0;
}

.box_list_img img {
    width: 100%;
    object-fit: cover;
    height: 100%;
}

.box_list_img {
    background: #FFFFFF;
    border-radius: 5px;
    padding: 5px;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.15);
    //width: 100%;
    //height: 100%;
    object-fit: cover;
}

.box_list_img {
    flex: 1 1 9em;
    max-width: 150px;
    height: 160px;
}

.box_drop_items {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    //width: 100%;
    //display: grid;
    //grid-gap: 20px;
    //grid-template-columns: repeat(auto-fit, minmax(133px, 1fr));
    //grid-template-rows: 150px 1fr;
}

.calendar_box {
    overflow-x: hidden;
    height: 405px;
}

@media only screen and (max-width: 992px) {
    .prod_info {
        padding: 18px 5px 18px 18px;
        width: 65%;
    }
    .box_add_p {
        padding: 13px 15px 13px 5px;
        width: 35%;
    }
    .btn_box {
        margin-bottom: 0px;
    }
    .btn_post_n, .btn_post_n2 {
        width: 155px;
    }
    .box_list_img {
        max-width: 150px;
    }
    .box_drop_items {
        gap: 7px;
    }
    .box_btn_modal {
        gap: 5px;
    }
    .title_mst2 {
        font-size: 19px;
    }
    .title_mst1 {
        font-size: 21px;
    }
    .time_b_mob {
        font-weight: 500;
        font-size: 20px;
    }
    .time_date_mobile {
        padding-top: 30px;
        display: flex;
        justify-content: center;
    }
    .calendar_box {
        padding: 14px 28px 30px 28px;
    }
    .w_text {
        width: 100%;
    }
    .btn_box {
        padding-top: 60px;
        padding-bottom: 50px;
    }
    .box_form2 {
        padding-right: 17px;
    }
    .items_inf {
        padding: 43px 26px 0 30px;
    }
    .box_i {
        display: none;
    }
    .box_select {
        width: 100%;
    }
    .box_i_mobile {
        display: flex;
        flex-direction: column;
    }
    .app-page.stp_1 {
        min-height: calc(100vh - 205px);
    }
    .textarea {
        height: 90px;
        padding-top: 5px;
    }
    .box {
        margin-top: 26px;
    }
    .title_page {
        font-size: 18px;
        line-height: 22px;
    }
    .form-select {
        width: 200px;
    }
    .title_2 {
        font-weight: 400;
        font-size: 13px;
        line-height: 18px;
    }
    .select_socialite {
        justify-content: center;
    }
    .box_img {
        width: 104px;
        height: 100px;
    }
    .box {
        padding: 7px;
        gap: 17px;
    }
}
</style>
