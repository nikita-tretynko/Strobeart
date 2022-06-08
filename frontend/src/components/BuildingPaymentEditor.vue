<template>
    <div>
        <div class="box_balances">
            <div>
                <div class="title_b">Pending Balance <img data-bs-toggle="tooltip" title="Your funds are currently being held in escrow by Stripe and will be available
after 7 working days from the end of your work" class="icon_info cp" src='@/assets/icons/info.svg' alt="">
                </div>
                <div class="title_p">{{ getPendingBalance() }}</div>
            </div>
            <div>
                <div class="title_b">Available balance</div>
                <div class="title_p">{{ getAvailableBalance() }}</div>
            </div>
            <div class="box_btn_withdrawal ">
                <div @click="openModalWithdrawal()" v-if="isEmptyObject(user.connect_id)" class="btn_withdrawal ">
                    Withdrawal
                </div>
            </div>
        </div>
        <div class="box_btn_stripe" v-if="!isEmptyObject(user.connect_id)" >
        <div class="stripe_connect_b stripe_connect" ><a @click="connectStripe()" href="#" class="stripe-connect white"><span>Connect with</span></a></div>
        </div>
        <template v-if="isEmptyObject(user.connect_id)">
            <div class="user_name d-flex align-items-center ph-margin">Connected Bank Accounts <img
                @click="linkStripe()" class="cp icon-edit" src='@/assets/icons/edit.svg' alt=""></div>
            <div class="list_cards_st">
                <div class="item_cards_st" v-for="card in user.short_info">
                    <div class="d-flex align-items-center"><img src='@/assets/icons/bank.svg' alt=""></div>
                    <div class="number_card_st">XXXX XXXX XXXX {{ card.last4 }}</div>
                </div>
            </div>
        </template>
        <template v-if="is_money_transfers">
            <div class="user_name ph-margin">Payments History</div>
            <div class="list_payments_history">
                <div class="item_history" v-for="item in money_transfers">
                    <template v-if="item.type_balance==='AVAILABLE'||item.type_balance==='PENDING'">
                        <div>{{ createdPayment(item.created_at) }}:
                            {{ getBusinessName(item.payment) }}
                            ({{ getCountImages(item) }} images)
                        </div>
                        <div>+${{ item.net / 100 }}</div>
                    </template>
                    <template v-if="item.type_balance=='WITHDRAWN'">
                        <div class="color_text">{{ createdPayment(item.created_at) }}:
                            Withdrawn
                        </div>
                        <div class="color_text">-${{ item.net / 100 }}</div>
                    </template>
                </div>
            </div>
        </template>
        <div class="modal" id="userCardModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Withdraw</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="input_modal">
                            <div class="amount_input_box">
                                <div class="style_btn3">
                                            <span>
                                                <span class="style_text04">$</span>
                                                <input @keypress="soloNumber" placeholder="00" autocomplete="off" maxlength="5"
                                                       class="input_classT" v-model="amount">
                                                <span class="style_text04">USD </span>
                                            </span>
                                </div>
                                <div class="min_amount">Min amount: <span class="min_amount_sum"> $10 USD</span>
                                </div>
                            </div>
                            <div class="list_cards_st">
                                <div class="item_cards_st" v-for="card in user.short_info">
                                    <div class="form-check">
                                        <input  @click="radioBoxCard" :data-id="card.id" :data-last4="card.last4" class="form-check-input" type="radio" name="flexRadioDefault">
                                        <label class="form-check-label"></label>
                                    </div>
                                    <div class="d-flex align-items-center"><img src='@/assets/icons/bank.svg' alt=""></div>
                                    <div class="number_card_st">XXXX XXXX XXXX {{ card.last4 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn_modal_t1" data-bs-dismiss="modal">Close</button>
                        <button class="btn_modal_t2" @click="withdraw()">Withdraw</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {mapGetters, mapMutations} from "vuex";
import {Modal} from "bootstrap";
import {errorMessage} from "../services/messages";
import moment from 'moment-timezone'

export default {
    name: "BuildingPaymentEditor",
    props: ['user_card', 'user'],
    components: {},
    data() {
        return {
            cardElement: null,
            userCardModal: null,
            user_card_n: {},
            amount: '',
            card_id:null,
            last4:null,
            data_user:this.user,
        };
    },
    destroyed() {
        if (this.userCardModal) {
            this.userCardModal.dispose()
        }
    },
    async mounted() {
        this.userCardModal = new Modal(document.getElementById('userCardModal'));
        this.user_card_n = this.user_card
    },
    computed: {
        ...mapGetters([
            'getUser',
            'getStripeKey'
        ]),
        getLast4() {
            return this.user_card_n?.last4 || null
        },
        is_money_transfers() {
            return this.data_user?.money_transfers_done.length || null
        },
        money_transfers() {
            return this.data_user?.money_transfers_done || null
        }
    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
        ]),
        soloNumber($event) {
            let keyCode = ($event.keyCode ? $event.keyCode : $event.which)
            if ((keyCode < 48 || keyCode > 57) && (keyCode !== 46)) {
                $event.preventDefault()
            }
        },
        radioBoxCard(event) {
            this.last4 = event.target.getAttribute('data-last4');
            this.card_id = event.target.getAttribute('data-id');
        },
        openModalWithdrawal() {
            this.userCardModal.show();
        },
        async withdraw() {
            if(this.last4&&this.card_id&&this.amount>=10) {
                this.showLoader()
                try {
                    const resp = await this.$http.postAuth(`${this.$http.apiUrl()}profile/withdraw-money`, {
                        amount: this.amount * 100,
                        card_id: this.card_id
                    });
                    this.userCardModal.hide();
                    this.data_user = resp?.data?.data || {}
                    // window.location = resp?.data?.data?.url;
                } catch (e) {
                    if (e?.response?.data?.error?.message) {
                        errorMessage(e.response.data.error.message)
                    } else {
                        errorMessage('ERROR')
                    }
                }
                this.hideLoader()
            }
        },

        async linkStripe() {
            this.showLoader()
            try {
                const resp = await this.$http.getAuth(`${this.$http.apiUrl()}profile/link-stripe`);
                window.open(resp?.data?.data?.url, '_blank');
            } catch (e) {
                if (e?.response?.data?.error?.message) {
                    errorMessage(e.response.data.error.message)
                } else {
                    errorMessage('ERROR')
                }
            }
            this.hideLoader()
        },
        async connectStripe() {
            this.showLoader()
            try {
                const resp = await this.$http.getAuth(`${this.$http.apiUrl()}profile/connect-stripe`);
                window.open(resp?.data?.data?.url, '_blank');
            } catch (e) {
                if (e?.response?.data?.error?.message) {
                    errorMessage(e.response.data.error.message)
                } else {
                    errorMessage('ERROR')
                }
            }
            this.hideLoader()
        },
        getCountImages(item) {
            return item?.payment_images?.length || 0
        },
        getPendingBalance() {
            if (this.data_user?.balance['pending']) {
                return '$' + (this.data_user.balance['pending'] / 100).toFixed(2);
            }
            return '$0'
        },
        getAvailableBalance() {
            if (this.data_user?.balance['available']) {
                return '$' + (this.data_user.balance['available'] / 100).toFixed(2);
            }
            return '$0'
        },
        createdPayment(date) {
            return (moment(date).tz(Intl.DateTimeFormat().resolvedOptions().timeZone).format('DD-MMM-YYYY')).toLowerCase();
        },
        getBusinessName(value) {
            if (value?.user_business?.first_name || value?.user_business?.last_name) {
                return value?.user_business?.first_name + '' + ' ' + value?.user_business?.last_name
            }
            return '';
        },
        openModalCard() {
            this.userCardModal.show()
        },
        closeAddCardModal() {
            this.cardElement = this.$refs.card.stripeElement;
            this.cardElement.clear();
        },
        isEmptyObject(obj) {
            if (obj) {
                return Object.keys(obj).length !== 0;
            }
            return 0;
        },
    }

}
</script>

<style lang="scss" scoped>
.card_style {
    padding: 10px;
    background: white;
    border: 1px solid #B7BCC3;
    box-sizing: border-box;
    border-radius: 5px 5px 0 0;
}

.form-check-input:checked {
    background-color: #d7c3ae;
    border-color: #ffffff;
}

.modal-title {
    width: 100%;
    text-align: center;
}

.title_name {
    font-style: normal;
    font-weight: 600;
    font-size: 20px;
    line-height: 44px;
    color: #494949;
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
    color: rgba(95, 203, 22, 0.76);
    padding: 25px 0 25px 35px;
}

.item_cards_st {
    font-style: normal;
    font-weight: 400;
    font-size: 17px;
    display: flex;
    align-items: center;
    padding: 19px 23px;
    background: #D8C3AF;
    color: white;
}

.form-check {
    display: flex;
    align-items: center;
    margin-right: 20px;
}

.ph-margin {
    margin-top: 66px;
}

.list_payments_history {
    margin-top: 38px;
}

.list_cards_st {
    margin-top: 38px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.box_card_user {
    max-width: 550px;
    justify-content: space-between;
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

.icon_info {
    padding-left: 8px;
}

.box_balances {
    display: flex;
    gap: 30px;
}

.title_b {
    font-style: normal;
    font-weight: 500;
    font-size: 12px;
    line-height: 18px;
    color: #494949;
    display: flex;
    align-items: center;
}

.title_p {
    display: flex;
    align-items: center;
    justify-content: center;
    font-style: normal;
    font-weight: 700;
    font-size: 26px;
    line-height: 34px;
    color: #494949;
}

.btn_withdrawal {
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    width: 148px;
    height: 47px;
    color: white;
    background: #D8C3AF;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.25);
    border-radius: 100px;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
}

.box_btn_withdrawal {
    padding-left: 40px;
}

.stripe_connect {
    margin-top: 55px;
    margin-left: 30px;
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

.icon-edit {
    width: 35px;
    height: 35px;
    margin-left: 20px;
}

.number_card_st {
    font-style: normal;
    font-weight: 500;
    font-size: 16px;
    line-height: 22px;
    margin-left: 15px;
}

.amount_input_box {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 55px;
}
.style_btn3 {
    padding: 8px 8px;
    width: 160px;
    font-style: normal;
    font-weight: normal;
    font-size: 14px;
    line-height: 20px;
    text-align: center;
    border: 1px solid #5E6162FF;
    background: white;
    color: #5E6162FF;
}
.style_text04 {
    color: #5E6162FF;
}
.input_classT:focus {
    outline: none;
    box-shadow: 0 0 0 0 #26a69a;
}

.input_classT {
    border: 0px solid #9e9e9e;
    color: #5E6162FF;
    width: 51px;
    margin: 0;
    font-size: 14px;
    text-align: center;
}

.input_classT::-webkit-outer-spin-button,
.input_classT::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.min_amount{
    font-style: normal;
    font-weight: 400;
    font-size: 14px;
    line-height: 20px;
    color: #494949;
}
.min_amount_sum{
    font-style: normal;
    font-weight: 600;
    font-size: 14px;
    line-height: 20px;
    color: #494949;
}
.input_modal{
    min-width: 70%;
}
.modal-body{
   display: flex;
    justify-content: center;
}
.color_text{
    color: black;
}
.stripe-connect {
    background: #635bff;
    display: inline-block;
    height: 38px;
    text-decoration: none;
    width: 180px;

    border-radius: 4px;
    -moz-border-radius: 4px;
    -webkit-border-radius: 4px;

    user-select: none;
    -moz-user-select: none;
    -webkit-user-select: none;
    -ms-user-select: none;

    -webkit-font-smoothing: antialiased;
}

.stripe-connect span {
    color: #ffffff;
    display: block;
    font-family: sohne-var, "Helvetica Neue", Arial, sans-serif;
    font-size: 15px;
    font-weight: 400;
    line-height: 14px;
    padding: 11px 0px 0px 24px;
    position: relative;
    text-align: left;
}

.stripe-connect:hover {
    background: #7a73ff;
}

.stripe-connect.slate {
    background: #0a2540;
}

.stripe-connect.slate:hover {
    background: #425466;
}

.stripe-connect.white {
    background: #ffffff;
}

.stripe-connect.white span {
    color: #0a2540;
}

.stripe-connect.white:hover {
    background: #f6f9fc;
}

.stripe-connect span::after {
    background-repeat: no-repeat;
    background-size: 49.58px;
    content: "";
    height: 20px;
    left: 62%;
    position: absolute;
    top: 28.95%;
    width: 49.58px;
}

.stripe-connect span::after {
    background-image: url("data:image/svg+xml,%3C%3Fxml version='1.0' encoding='utf-8'%3F%3E%3C!-- Generator: Adobe Illustrator 23.0.4, SVG Export Plug-In . SVG Version: 6.00 Build 0) --%3E%3Csvg version='1.1' id='Layer_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' viewBox='0 0 468 222.5' style='enable-background:new 0 0 468 222.5;' xml:space='preserve'%3E%3Cstyle type='text/css'%3E .st0%7Bfill-rule:evenodd;clip-rule:evenodd;fill:%23FFFFFF;%7D%0A%3C/style%3E%3Cg%3E%3Cpath class='st0' d='M414,113.4c0-25.6-12.4-45.8-36.1-45.8c-23.8,0-38.2,20.2-38.2,45.6c0,30.1,17,45.3,41.4,45.3 c11.9,0,20.9-2.7,27.7-6.5v-20c-6.8,3.4-14.6,5.5-24.5,5.5c-9.7,0-18.3-3.4-19.4-15.2h48.9C413.8,121,414,115.8,414,113.4z M364.6,103.9c0-11.3,6.9-16,13.2-16c6.1,0,12.6,4.7,12.6,16H364.6z'/%3E%3Cpath class='st0' d='M301.1,67.6c-9.8,0-16.1,4.6-19.6,7.8l-1.3-6.2h-22v116.6l25-5.3l0.1-28.3c3.6,2.6,8.9,6.3,17.7,6.3 c17.9,0,34.2-14.4,34.2-46.1C335.1,83.4,318.6,67.6,301.1,67.6z M295.1,136.5c-5.9,0-9.4-2.1-11.8-4.7l-0.1-37.1 c2.6-2.9,6.2-4.9,11.9-4.9c9.1,0,15.4,10.2,15.4,23.3C310.5,126.5,304.3,136.5,295.1,136.5z'/%3E%3Cpolygon class='st0' points='223.8,61.7 248.9,56.3 248.9,36 223.8,41.3 '/%3E%3Crect x='223.8' y='69.3' class='st0' width='25.1' height='87.5'/%3E%3Cpath class='st0' d='M196.9,76.7l-1.6-7.4h-21.6v87.5h25V97.5c5.9-7.7,15.9-6.3,19-5.2v-23C214.5,68.1,202.8,65.9,196.9,76.7z'/%3E%3Cpath class='st0' d='M146.9,47.6l-24.4,5.2l-0.1,80.1c0,14.8,11.1,25.7,25.9,25.7c8.2,0,14.2-1.5,17.5-3.3V135 c-3.2,1.3-19,5.9-19-8.9V90.6h19V69.3h-19L146.9,47.6z'/%3E%3Cpath class='st0' d='M79.3,94.7c0-3.9,3.2-5.4,8.5-5.4c7.6,0,17.2,2.3,24.8,6.4V72.2c-8.3-3.3-16.5-4.6-24.8-4.6 C67.5,67.6,54,78.2,54,95.9c0,27.6,38,23.2,38,35.1c0,4.6-4,6.1-9.6,6.1c-8.3,0-18.9-3.4-27.3-8v23.8c9.3,4,18.7,5.7,27.3,5.7 c20.8,0,35.1-10.3,35.1-28.2C117.4,100.6,79.3,105.9,79.3,94.7z'/%3E%3C/g%3E%3C/svg%3E");
}

.stripe-connect.white span::after {
    background-image: url("data:image/svg+xml,%3C%3Fxml version='1.0' encoding='utf-8'%3F%3E%3C!-- Generator: Adobe Illustrator 24.0.3, SVG Export Plug-In . SVG Version: 6.00 Build 0) --%3E%3Csvg version='1.1' id='Layer_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' viewBox='0 0 468 222.5' style='enable-background:new 0 0 468 222.5;' xml:space='preserve'%3E%3Cstyle type='text/css'%3E .st0%7Bfill-rule:evenodd;clip-rule:evenodd;fill:%230A2540;%7D%0A%3C/style%3E%3Cg%3E%3Cpath class='st0' d='M414,113.4c0-25.6-12.4-45.8-36.1-45.8c-23.8,0-38.2,20.2-38.2,45.6c0,30.1,17,45.3,41.4,45.3 c11.9,0,20.9-2.7,27.7-6.5v-20c-6.8,3.4-14.6,5.5-24.5,5.5c-9.7,0-18.3-3.4-19.4-15.2h48.9C413.8,121,414,115.8,414,113.4z M364.6,103.9c0-11.3,6.9-16,13.2-16c6.1,0,12.6,4.7,12.6,16H364.6z'/%3E%3Cpath class='st0' d='M301.1,67.6c-9.8,0-16.1,4.6-19.6,7.8l-1.3-6.2h-22v116.6l25-5.3l0.1-28.3c3.6,2.6,8.9,6.3,17.7,6.3 c17.9,0,34.2-14.4,34.2-46.1C335.1,83.4,318.6,67.6,301.1,67.6z M295.1,136.5c-5.9,0-9.4-2.1-11.8-4.7l-0.1-37.1 c2.6-2.9,6.2-4.9,11.9-4.9c9.1,0,15.4,10.2,15.4,23.3C310.5,126.5,304.3,136.5,295.1,136.5z'/%3E%3Cpolygon class='st0' points='223.8,61.7 248.9,56.3 248.9,36 223.8,41.3 '/%3E%3Crect x='223.8' y='69.3' class='st0' width='25.1' height='87.5'/%3E%3Cpath class='st0' d='M196.9,76.7l-1.6-7.4h-21.6v87.5h25V97.5c5.9-7.7,15.9-6.3,19-5.2v-23C214.5,68.1,202.8,65.9,196.9,76.7z'/%3E%3Cpath class='st0' d='M146.9,47.6l-24.4,5.2l-0.1,80.1c0,14.8,11.1,25.7,25.9,25.7c8.2,0,14.2-1.5,17.5-3.3V135 c-3.2,1.3-19,5.9-19-8.9V90.6h19V69.3h-19L146.9,47.6z'/%3E%3Cpath class='st0' d='M79.3,94.7c0-3.9,3.2-5.4,8.5-5.4c7.6,0,17.2,2.3,24.8,6.4V72.2c-8.3-3.3-16.5-4.6-24.8-4.6 C67.5,67.6,54,78.2,54,95.9c0,27.6,38,23.2,38,35.1c0,4.6-4,6.1-9.6,6.1c-8.3,0-18.9-3.4-27.3-8v23.8c9.3,4,18.7,5.7,27.3,5.7 c20.8,0,35.1-10.3,35.1-28.2C117.4,100.6,79.3,105.9,79.3,94.7z'/%3E%3C/g%3E%3C/svg%3E");
}

.stripe-connect.white {
    left: 1px;
    position: relative;
    top: 1px;
}

.stripe_connect_b{
    background-color: #e5e5e5;
    height: 40px;
    width: 182px;

    border-radius: 4px;
    -moz-border-radius: 4px;
    -webkit-border-radius: 4px;
}

@media only screen and (max-width: 992px) {
    .style_btn3{
        width: 130px;
    }
    .box_btn_stripe{
        display: flex;
        justify-content: center;
    }
    .stripe_connect {
        margin-left: 0px;
        display: flex;
        justify-content: center;
    }
    .box_btn_withdrawal {
        padding-left: 0px;
        display: flex;
        justify-content: center;
    }
    .user_name {
        font-size: 18px;
        line-height: 22px;
        margin-left: 0;
    }
    .item_history {
        padding: 15px 0 15px 0px;
        font-size: 13px;
    }

    .list_payments_history {
        margin-top: 20px;
    }
    .list_cards_st {
        margin-top: 20px;
    }
    .box_card_user {
        flex-direction: column;
        gap: 20px;
    }
    .modal-footer {
        gap: 10px;
    }
    .btn_box_card {
        display: flex;
        justify-content: center;
        margin-left: 0;
    }
    .box_balances {
        flex-direction: column;
    }
}
</style>
