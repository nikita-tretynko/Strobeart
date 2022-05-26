<template>
    <div>
        <MainLayout :is-show-header=!showJobDetailsMob>
            <div class="page_profile_setting">
                <div class="col-3 menu_setting">
                    <div class="menu_title cp" @click="selectMenu(1)" :class="{active_menu:selected_menu===1}">
                        <template v-if="!showJobDetailsMob">User info</template>
                        <template v-else>Info</template>
                    </div>
                    <div v-if="is_type_user===business" class="menu_title cp" @click="selectMenu(2)"
                         :class="{active_menu:selected_menu===2}">
                        Membership
                    </div>
                    <div class="menu_title cp" @click="selectMenu(3)" :class="{active_menu:selected_menu===3}">
                        <template v-if="!showJobDetailsMob">Billing & Payments</template>
                        <template v-else>Billing</template>
                    </div>
                </div>
                <div class="col-9 setting_box">
                    <div class="d-flex box_selected_m" v-if="selected_menu===1">
                        <div class="d-flex">
                            <div class="col-2 box_avatar">
                                <img v-if="avatar" class="avatar" :src=avatar alt="">
                                <img v-else class="avatar" src='@/assets/images/sbcf-default-avatar.png' alt="">
                                <img @click="editAvatar" class="icon_edit" src='@/assets/icons/editor.svg' alt="">
                                <input type="file" ref="avatar_file" @change="updateAvatar" hidden>
                            </div>
                            <div class="col-10 col_info">
                                <div class="info_user">
                                    <div class="user_name">{{ user.first_name }} {{ user.last_name }}</div>
                                    <img class="ps-2 cp ic_editor" @click="editNameUser" src='@/assets/icons/editor.svg'
                                         alt="">
                                </div>
                                <div class="info2 info2_desc">
                                    <div class="info_user">
                                        <img v-if="is_type_user===business" class="me-1" src='@/assets/icons/business-center.svg' alt="">
                                        <img v-else class="me-1" src='@/assets/icons/bi_person-fill.svg' alt="">
                                        <div class="text2">{{ user.business_name }}</div>
                                        <img class="ps-2 cp ic_editor" @click="editBusinessUser"
                                             src='@/assets/icons/editor.svg' alt="">
                                    </div>
                                    <div class="info_user">
                                        <img class="me-1" src='@/assets/icons/ep_location.svg' alt="">
                                        <div class="text2">{{ user.location }}</div>
                                        <img class="ps-2 cp ic_editor" @click="editLocation"
                                             src='@/assets/icons/editor.svg' alt="">
                                    </div>
                                </div>
                                <div class="bio_box bio_box_desc">
                                    <div class="bio_t">
                                        <div class="user_name">Bio</div>
                                        <img class="ps-2 cp ic_editor" @click="editBio" src='@/assets/icons/editor.svg'
                                             alt="">
                                    </div>
                                    <div v-if="user.bio" class="bio_text">
                                        {{ user.bio }}
                                    </div>
                                </div>
                                <div v-if="is_type_user===business&&is_instagram" @click="unlinkInstagramModalO()" class="unlink_instagram desk_v">
                                    Unlink Instagram
                                </div>
                                <div v-if="is_type_user===business&&is_shopify" @click="unlinkShopifyModalOpen()" class="unlink_instagram desk_v">
                                    Unlink Shopify
                                </div>
                                <div class="btn_logout desc_st" @click="logOut()">Log out</div>
                            </div>
                        </div>
                        <div class="mobile_menu_1">
                            <div class="info2">
                                <div class="info_user">
                                    <img class="me-1" v-if="is_type_user===business" src='@/assets/icons/business-center.svg' alt="">
                                    <img class="me-1" v-else src='@/assets/icons/bi_person-fill.svg' alt="">
                                    <div class="text2">{{ user.business_name }}</div>
                                    <img class="ps-2 cp ic_editor" @click="editBusinessUser"
                                         src='@/assets/icons/editor.svg' alt="">
                                </div>
                                <div class="info_user">
                                    <img class="me-1" src='@/assets/icons/ep_location.svg' alt="">
                                    <div class="text2">{{ user.location }}</div>
                                    <img class="ps-2 cp ic_editor" @click="editLocation" src='@/assets/icons/editor.svg'
                                         alt="">
                                </div>
                            </div>
                            <div class="bio_box">
                                <div class="bio_t">
                                    <div class="user_name">Bio</div>
                                    <img class="ps-2 cp ic_editor" @click="editBio" src='@/assets/icons/editor.svg'
                                         alt="">
                                </div>
                                <div v-if="user.bio" class="bio_text">
                                    {{ user.bio }}
                                </div>
                            </div>
                            <div v-if="is_type_user===business&&is_instagram" @click="unlinkInstagramModalO()" class="unlink_instagram mob_v">
                                Unlink Instagram
                            </div>
                            <div class="mob_st">
                                 <div class="btn_logout mob_st mob_st_v1" @click="logOut()">Log out</div>
                            </div>
                        </div>
                    </div>
                    <div class="box_selected_m" v-if="selected_menu===2&&is_type_user===business">
                        <PaymentPlan :user="user"></PaymentPlan>
                    </div>
                    <div class="box_selected_m" v-if="selected_menu===3">
                        <BuildingPayment v-if="is_type_user===business" :payment_history="payment_history"
                                         :user_card="user_card"></BuildingPayment>
                        <BuildingPaymentEditor v-else :user="user" :payment_history="payment_history"
                                               :user_card="user_card"></BuildingPaymentEditor>
                    </div>
                </div>
            </div>
        </MainLayout>
        <div class="modal" id="unlinkInstagramModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="input_modal">
                            Are you sure you want to unlink Instagram? StrobeArt will not be able to post to Instagram on your behalf.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn_modal_t1" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn_modal_t2" @click="unlinkInstagram()">Yes</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="unlinkShopifyModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="input_modal">
                            Are you sure you want to unlink Shopify? StrobeArt will not be able to post to Shopify on your behalf.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn_modal_t1" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn_modal_t2" @click="unlinkShopify()">Yes</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="editModalUserName" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Name</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="input_modal">
                            <input :class="{ 'is-invalid': errors.user_first_name}"
                                   type="text" class="form-control" v-model="user_first_name" placeholder="First Name">
                            <div class="invalid-feedback">
                                This field is required
                            </div>
                            <input :class="{ 'is-invalid': errors.user_last_name}"
                                   type="text" class="form-control mt-3" v-model="user_last_name"
                                   placeholder="Last Name">
                            <div class="invalid-feedback">
                                This field is required
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn_modal_t1" data-bs-dismiss="modal">Close</button>
                        <button class="btn_modal_t2" @click="updateUserName">Save</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="editModalUserBusiness" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" v-if="is_type_user===business">Business</h5>
                        <h5 class="modal-title" v-else>Level of retoaching</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="input_modal">
                            <input v-if="is_type_user===business" :class="{ 'is-invalid': errors.user_business}"
                                   type="text" class="form-control" v-model="user_business" placeholder="Business">
                            <select class="form-select" v-else v-model="user_business" >
                                <option value="" disabled>Select your level</option>
                                <option v-for="(item,index) in level_user" :value="item" v-bind:selected="index === 0">{{ item }}</option>
                            </select>
                            <div class="invalid-feedback">
                                This field is required
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn_modal_t1" data-bs-dismiss="modal">Close</button>
                        <button class="btn_modal_t2" @click="updateUserBusiness">Save</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="editModalUserLocation" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Location</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="input_modal">
                            <input :class="{ 'is-invalid': errors.user_location}"
                                   type="text" class="form-control" v-model="user_location" placeholder="Location">
                            <div class="invalid-feedback">
                                This field is required
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn_modal_t1" data-bs-dismiss="modal">Close</button>
                        <button class="btn_modal_t2" @click="updateUserLocation">Save</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="editModalUserBio" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Bio</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="input_modal">
                            <textarea :class="{ 'is-invalid': errors.user_bio}" v-model="user_bio" placeholder="Bio"
                                      class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                            <div class="invalid-feedback">
                                This field is required
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn_modal_t1" data-bs-dismiss="modal">Close</button>
                        <button class="btn_modal_t2" @click="updateUserBio">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import MainLayout from "../layouts/MainLayout";
import {errorMessage} from "../services/messages";
import {mapGetters, mapMutations} from "vuex";
import {Modal} from "bootstrap";
import TypeUserEnum from "../enums/TypeUserEnum";
import BuildingPayment from "../components/BuildingPayment";
import PaymentPlan from "../components/PaymentPlan";
import BuildingPaymentEditor from "../components/BuildingPaymentEditor";

export default {
    name: 'Settings',
    components: {
        MainLayout,
        BuildingPayment,
        BuildingPaymentEditor,
        PaymentPlan
    },
    data() {
        return {
            level_user:['Beginner','Proficient','Expert'],
            is_instagram:false,
            is_shopify:false,
            business: TypeUserEnum.BUSINESS,
            editor: TypeUserEnum.EDITOR,
            user: {},
            payment_history: {},
            is_type_user: true,
            selected_menu: 1,
            editModalUserName: null,
            editModalUserLocation: null,
            unlinkInstagramModal:null,
            unlinkShopifyModal:null,
            editModalUserBio: null,
            editModalUserBusiness: null,
            user_first_name: '',
            user_last_name: '',
            user_business: "",
            user_location: '',
            user_bio: '',
            errors: {
                user_first_name: false,
                user_last_name: false,
                user_business: false,
                user_location: false,
                user_bio: false,
            },
            user_card: null,
        }
    },
    destroyed() {
        if (this.editModalUserName) {
            this.editModalUserName.dispose()
        }
        if (this.unlinkShopifyModal) {
            this.unlinkShopifyModal.dispose()
        }
        if (this.unlinkInstagramModal) {
            this.unlinkInstagramModal.dispose()
        }
        if (this.editModalUserBusiness) {
            this.editModalUserBusiness.dispose()
        }
        if (this.editModalUserLocation) {
            this.editModalUserLocation.dispose()
        }
        if (this.editModalUserBio) {
            this.editModalUserBio.dispose()
        }
    },
    mounted() {
        this.getProfileSetting()
        this.editModalUserName = new Modal(document.getElementById('editModalUserName'));
        this.unlinkInstagramModal = new Modal(document.getElementById('unlinkInstagramModal'));
        this.unlinkShopifyModal = new Modal(document.getElementById('unlinkShopifyModal'));
        this.editModalUserBusiness = new Modal(document.getElementById('editModalUserBusiness'));
        this.editModalUserLocation = new Modal(document.getElementById('editModalUserLocation'));
        this.editModalUserBio = new Modal(document.getElementById('editModalUserBio'));
    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
            'setUser',
            'setUpdateUserProfile'
        ]),
        logOut(){
            delete localStorage.strobeart_user;
            delete localStorage.strobeart_jwt;
            this.$router.push({name: 'Login'}).then();
        },
        isEmptyObject(obj) {
            if (obj) {
                return Object.keys(obj).length !== 0;
            }
            return 0;
        },
        editNameUser() {
            this.errors.user_name = false
            this.editModalUserName.show();
        },
        editLocation() {
            this.errors.user_Location = false
            this.editModalUserLocation.show();
        },
        editBio() {
            this.errors.user_bio = false
            this.editModalUserBio.show();
        },
        editBusinessUser() {
            this.errors.user_business = false
            this.editModalUserBusiness.show();
        },
        async updateUserName() {
            let error = false
            if (!this.user_first_name) {
                this.errors.user_first_name = true
                error = true
            }
            if (!this.user_last_name) {
                this.errors.user_last_name = true
                error = true
            }
            if (error) {
                return
            }
            try {
                this.showLoader();
                const resp = await this.$http.postAuth(`${this.$http.apiUrl()}profile/update-user_name`, {
                    'first_name': this.user_first_name,
                    'last_name': this.user_last_name
                });
                this.updateUserStorage(resp?.data?.data || null)
                await this.getProfileSetting()
                this.editModalUserName.hide()
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();
        },
        async updateUserLocation() {
            if (!this.user_location) {
                this.errors.user_location = true
                return
            }
            try {
                this.showLoader();
                const resp = await this.$http.postAuth(`${this.$http.apiUrl()}profile/update-user_location`, {'location': this.user_location});
                this.updateUserStorage(resp?.data?.data || null)
                await this.getProfileSetting()
                this.editModalUserLocation.hide()
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();
        },
        async updateUserBio() {
            if (!this.user_bio) {
                this.errors.user_bio = true
                return
            }
            try {
                this.showLoader();
                const resp = await this.$http.postAuth(`${this.$http.apiUrl()}profile/update-user_bio`, {'bio': this.user_bio});
                this.updateUserStorage(resp?.data?.data || null)
                await this.getProfileSetting()
                this.editModalUserBio.hide()
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();
        },
        async updateUserBusiness() {
            if (!this.user_business) {
                this.errors.user_business = true
                return
            }
            try {
                this.showLoader();
                const resp = await this.$http.postAuth(`${this.$http.apiUrl()}profile/update-user_business`, {'business': this.user_business});
                this.updateUserStorage(resp?.data?.data || null)
                await this.getProfileSetting()
                this.editModalUserBusiness.hide()
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();
        },
        selectMenu(val) {
            this.selected_menu = val;
        },
        editAvatar() {
            this.$refs.avatar_file.click();
        },
        updateUserStorage(data) {
            if (data) {
                localStorage.setItem('strobeart_user', JSON.stringify(data))
                this.setUser(data || {})
                this.setUpdateUserProfile(true)
            }
        },
        async updateAvatar(e) {
            const tmpFiles = e.target.files
            if (tmpFiles.length === 0) {
                return false;
            }
            try {
                this.showLoader();
                let data = new FormData();
                data.append('avatar', tmpFiles[0]);
                const resp = await this.$http.postAuth(`${this.$http.apiUrl()}profile/update-avatar`, data);
                this.updateUserStorage(resp?.data?.data || null)
                await this.getProfileSetting()
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();
        },
       async unlinkInstagram(){
           try {
               this.showLoader();
               await this.$http.postAuth(`${this.$http.apiUrl()}profile/unlink-instagram`);
               this.is_instagram=false
               this.unlinkInstagramModal.hide();
           } catch (e) {
               const message = e?.response?.data?.error?.message || 'ERROR';
               errorMessage(message)
           }
           this.hideLoader();
       },
        async unlinkShopify(){
            try {
                this.showLoader();
                await this.$http.postAuth(`${this.$http.apiUrl()}profile/unlink-shopify`);
                this.is_shopify = false
                this.unlinkShopifyModal.hide();
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();
        },
        unlinkShopifyModalOpen(){
            this.unlinkShopifyModal.show();
        },
        unlinkInstagramModalO(){
            this.unlinkInstagramModal.show();
        },
        async getProfileSetting() {
            try {
                this.showLoader();
                const response = await this.$http.getAuth(`${this.$http.apiUrl()}profile/setting`);
                this.user = response?.data?.data?.user || {}
                this.user_card = response?.data?.data?.user?.user_card || {}
                this.is_type_user = response?.data?.data?.user.type_user || false
                this.user_first_name = response?.data?.data?.user?.first_name || ''
                this.user_last_name = response?.data?.data?.user?.last_name || ''
                this.user_bio = response?.data?.data?.user?.bio || ''
                this.user_location = response?.data?.data?.user?.location || ''
                this.user_business = response?.data?.data?.user?.business_name || ''
                this.payment_history = response?.data?.data?.user?.payment_history || {}
                this.is_instagram = this.isEmptyObject(response?.data?.data?.user?.login_instagram || {})
                this.is_shopify = this.isEmptyObject(response?.data?.data?.user?.shopify_connect || {})
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();
        },
        isMobile() {
            return window.innerWidth <= 992;
        },

    },
    computed: {
        avatar() {
            return this.user?.avatar?.url || null;
        }
        ,
        showJobDetailsMob() {
            return this.isMobile()
        },
    }
}
</script>
<style lang="scss" scoped>
.form-select:focus {
    border-color: #cccccc;
    box-shadow: none;
}
.text2 {
    font-style: normal;
    font-weight: 400;
    font-size: 17px;
    line-height: 30px;
    color: #494949;
}

.page_profile_setting {
    margin-top: 177px;
    margin-bottom: 230px;
    display: flex;
}

.menu_setting {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.setting_box {
    border-left: 1px solid rgba(73, 73, 73, 0.5);
    padding: 20px 0 20px 85px
}

.menu_title {
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    line-height: 22px;
    color: rgba(73, 73, 73, 0.35);
}

.active_menu {
    color: #494949;
}

.box_avatar {
    position: relative;
    width: 136px;
    height: 136px;
}

.icon_edit {
    position: absolute;
    bottom: 10px;
    right: 10px;
    cursor: pointer;
}

.avatar {
    border-radius: 50%;
    width: 100%;
    height: 100%;
    object-fit: cover;
    border: 0.2px solid #494949;
}

.user_name {
    font-style: normal;
    font-weight: 600;
    font-size: 20px;
    line-height: 44px;
    color: #494949;
}

.info_user {
    display: flex;
}

.col_info {
    padding-left: 83px;
    padding-top: 30px;
}

.info2 {
    padding-top: 30px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.bio_t {
    display: flex;
    padding-top: 33px;
}

.bio_text {
    border: 0.7px solid rgba(73, 73, 73, 0.45);
    box-sizing: border-box;
    border-radius: 4px;
    padding: 6px 15px;
    font-style: normal;
    font-weight: 400;
    font-size: 17px;
    line-height: 24px;
    color: rgba(73, 73, 73, 0.35);
    position: relative;
    left: -10px;
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

.modal-title {
    width: 100%;
    text-align: center;
}

.input_modal input {
    width: 100%;
    border: 0.3px solid #494949;
    box-sizing: border-box;
    border-radius: 100px;
    padding: 7px 16px;
}

.form-control.is-invalid, .form-control:invalid {
    border-color: #dc3545 !important;
}

textarea:focus {
    border: 1px solid #ced4da;
}

.mobile_menu_1 {
    display: none;
}

.number_card {
    font-style: normal;
    font-weight: 400;
    font-size: 17px;
    line-height: 24px;
    display: flex;
    align-items: center;
    color: #494949;
}

.btn_box_card {
    margin-left: 20%;
}

.item_history {
    border-bottom: 0.5px solid #494949;
    font-style: normal;
    font-weight: 400;
    font-size: 17px;
    line-height: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #ff0000c2;
    padding: 25px 0 25px 35px;
}

.ph-margin {
    margin-top: 66px;
}

.list_payments_history {
    margin-top: 38px;
}

.box_card_user {
    max-width: 550px;
    justify-content: space-between;
}

.unlink_instagram {
    background: #FFFFFF;
    color: #AD967F;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    border-radius: 100px;
    width: 225px;
    cursor: pointer;
    height: 40px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    line-height: 22px;
    margin-top: 30px;
    box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.25);
}
.desk_v{
  display: flex;
}
.mob_v{
  display: none;
}
.box_selected_m {
    position: relative;
}
.btn_logout{
    background: #FFFFFF;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.25);
    border-radius: 100px;
    height: 60px;
    width: 200px;
    font-style: normal;
    font-weight: 500;
    font-size: 18px;
    line-height: 22px;
    display: flex;
    justify-content: center;
    cursor: pointer;
    align-items: center;
    text-align: center;
    color: #494949;
}
.desc_st{
    display: flex;
    position: absolute;
    top: 0;
    right: 25px;
}
.mob_st{
    display: none;
}
@media only screen and (max-width: 992px) {
    .page_profile_setting{
        margin-bottom: 0;
    }
    .desc_st{
        display: none;
    }
    .mob_st{
        display: flex;
        justify-content: center;
    }
    .mob_st_v1{
        position: absolute;
        bottom: 0;
    }
    .desk_v{
        display: none;
    }
    .mob_v{
        display: flex;
    }
    .page_profile_setting {
        margin-top: 78px;
        flex-direction: column;
    }
    .menu_setting {
        display: flex;
        flex-direction: initial;
        gap: 0;
        width: 100%;
        border-bottom: 1.5px solid rgba(73, 73, 73, 0.25);
    }
    .menu_title {
        font-weight: 600;
        font-size: 15px;
        line-height: 18px;
        width: 33%;
        text-align: center;
    }
    .active_menu {
        border-bottom: 2.3px solid #494949;
        padding-bottom: 15px;
    }
    .box_avatar {
        width: 45px;
        height: 45px;
    }
    .col_info {
        padding: 0;
        padding-left: 15px;
        padding-bottom: 5px;
        display: flex;
        align-items: end;
    }
    .setting_box {
        border-left: 0px solid rgba(73, 73, 73, 0.5);
        padding: 19px 27px;
        width: 100%;
    }
    .info2_desc {
        display: none;
    }
    .bio_box_desc {
        display: none;
    }
    .user_name {
        font-size: 18px;
        line-height: 22px;
    }
    .icon_edit {
        bottom: 2px;
        right: -6px;
        width: 20px;
        height: 20px;
    }
    .ic_editor {
        height: 20px;
    }
    .box_selected_m {
        flex-direction: column;
        height: 65vh;
    }
    .mobile_menu_1 {
        display: block;
    }
    .info_user {
        align-items: center;
    }
    .bio_text {
        border: 0px solid rgba(73, 73, 73, 0.45);
        box-sizing: border-box;
        border-radius: 4px;
        padding: 12px 0px;
        font-style: normal;

        position: relative;
        left: 0;


        font-weight: 500;
        font-size: 12.5px;
        line-height: 20px;
        color: #494949;
    }
    .text2 {
        font-weight: 500;
        font-size: 12.5px;
        line-height: 20px;
    }
    .modal-footer {
        gap: 10px;
    }
}

</style>
