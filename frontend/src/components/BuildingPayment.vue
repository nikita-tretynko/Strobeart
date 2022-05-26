<template>
    <div>
        <div class="title_name">
            Billing Method
        </div>
        <div class="d-flex box_card_user">
            <div class="number_card">
                <template v-if="isEmptyCard(user_card_n)">Account Number *********{{ getLast4 }}</template>
                <template v-else>none</template>
            </div>
            <div class="btn_box_card">
                <button v-if="isEmptyCard(user_card_n)" class="btn_modal_t2" @click="openModalCard">Change</button>
                <button v-else class="btn_modal_t2" @click="openModalCard">Add</button>
            </div>
        </div>
        <div v-if="isEmptyCard(payment_history)" class="user_name ph-margin">Payments History</div>
        <div v-if="isEmptyCard(payment_history)" class="list_payments_history">
            <div class="item_history" v-for="item in payment_history">
                <div v-if="item.type_payment=='JOB_IMAGE'">{{ createdPayment(item.created_at) }}:
                    {{ getEditorName(item.user_editor) }}
                </div>
                <div v-if="item.type_payment=='MEMBERSHIP'">{{ createdPayment(item.created_at) }}:
                    Membership
                </div>

                <div>{{ item.amount / 100 }}$</div>
            </div>
        </div>
        <div class="modal" id="userCardModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add a card</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="input_modal">
                            <div class="payment-simple">
                                <div class="card_style">
                                    <StripeElements
                                        :stripe-key="getStripeKey"
                                        :instance-options="instanceOptions"
                                        :elements-options="elementsOptions"
                                        #default="{ elements }"
                                        ref="elms"
                                    >
                                        <StripeElement
                                            type="card"
                                            :elements="elements"
                                            :options="cardOptions"
                                            ref="card"
                                        />
                                    </StripeElements>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn_modal_t1" data-bs-dismiss="modal">Close</button>
                        <button class="btn_modal_t2" @click="newMethodPayment" :disabled="hasVoted">Add</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {mapGetters, mapMutations} from "vuex";
import {Modal} from "bootstrap";
import {StripeElements, StripeElement} from 'vue-stripe-elements-plus'
import {errorMessage} from "../services/messages";
import moment from 'moment-timezone'

export default {
    name: "BuildingPayment",
    props: ['user_card', 'payment_history'],
    components: {
        StripeElements,
        StripeElement,
    },
    data() {
        return {
            cardElement: null,
            userCardModal: null,
            hasVoted: false,
            instanceOptions: {},
            elementsOptions: {},
            cardOptions: {
                value: {
                    postalCode: ''
                }
            },
            user_card_n: {},
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
        }
    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
        ]),
        createdPayment(date) {
            return (moment(date).tz(Intl.DateTimeFormat().resolvedOptions().timeZone).format('DD-MMM-YYYY')).toLowerCase();
        },
        getEditorName(user) {
            return user?.first_name + ' ' + user?.last_name
        },
        isEmptyCard(obj) {
            return Object.keys(obj).length !== 0;
        },
        openModalCard() {
            this.userCardModal.show()
        },
        async newMethodPayment() {
            this.hasVoted = true;
            const groupComponent = this.$refs.elms
            const cardComponent = this.$refs.card
            this.cardElement = cardComponent.stripeElement
            try {
                const result = await groupComponent.instance.createToken(this.cardElement);
                if (result.error) {
                    const errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                } else {
                    const token_id = result.token['id'];
                    await this.addPaymentMethod(token_id);
                    this.cardElement.clear();
                }
            } catch (e) {
                console.log(e);
            }

            this.hasVoted = false;
        },
        closeAddCardModal() {
            this.cardElement = this.$refs.card.stripeElement;
            this.cardElement.clear();
        },
        async addPaymentMethod(token_id) {
            this.showLoader()
            try {
                const response = await this.$http.postAuth(`${this.$http.apiUrl()}profile/add_payment_method`, {
                    token_id: token_id,
                });
                this.user_card_n = response?.data?.data || {}
                this.userCardModal.hide();
            } catch (e) {
                if (e?.response?.data?.error?.message) {
                    errorMessage(e.response.data.error.message)
                } else {
                    errorMessage('ERROR')
                }
            }
            this.hideLoader()
            this.closeAddCardModal();
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
@media only screen and (max-width: 992px) {

    .user_name {
        font-size: 18px;
        line-height: 22px;
    }
    .item_history {
        padding: 25px 0 25px 0px;
    }
    .list_payments_history {
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
}
</style>
