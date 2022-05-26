<template>
    <div class="plans_box">
        <div class="item_plan">
            <div class="plan_title">Basic Plan</div>
            <div class="d-flex justify-content-center">
                <ul class="list_p">
                    <li class="font_weight">0-100 edited pictures a month</li>
                    <li> Basic Retouching $5/ image</li>
                    <li>Intermediate Retouching $7/image</li>
                    <li>Detailed Retouching $9/image</li>
                </ul>
            </div>
            <div class="price_plan">$9/month</div>
            <template v-if="getPlanName===MembershipPlanEnum.BASIC">
                <div class="btn_box">
                    <div class="btn_p1 btn_p1_0 cp">Your Plan</div>
                </div>
                <div v-if="getContinuationPlan" class="cancel_memb"
                     @click="showModalCancelPlan(MembershipPlanEnum.BASIC)">Cancel Memebership
                </div>
            </template>
            <template v-else>
                <div class="btn_box pb_box">
                    <div class="btn_p1 cp" @click="showAddPlanModal(MembershipPlanEnum.BASIC)">Upgrade</div>
                </div>
            </template>
        </div>
        <div class="item_plan">
            <div class="plan_title">Intermediate Plan</div>
            <div class="d-flex justify-content-center">
                <ul class="list_p">
                    <li class="font_weight">101-1000 edited pictures a month</li>
                    <li> Basic Retouching $4/ image</li>
                    <li>Intermediate Retouching $6/image</li>
                    <li>Detailed Retouching $8/image</li>
                </ul>
            </div>
            <div class="price_plan">$19/month</div>
            <template v-if="getPlanName===MembershipPlanEnum.INTERMEDIATE">
                <div class="btn_box">
                    <div class="btn_p1 btn_p1_0 cp">Your Plan</div>
                </div>
                <div v-if="getContinuationPlan" class="cancel_memb"
                     @click="showModalCancelPlan(MembershipPlanEnum.INTERMEDIATE)">Cancel
                    Memebership
                </div>
            </template>
            <template v-else>
                <div class="btn_box pb_box">
                    <div class="btn_p1 cp" @click="showAddPlanModal(MembershipPlanEnum.INTERMEDIATE)">Upgrade</div>
                </div>
            </template>
        </div>
        <div class="item_plan">
            <div class="plan_title">Expert Plan</div>
            <div class="d-flex justify-content-center">
                <ul class="list_p">
                    <li class="font_weight">1001-and up edited pictures a month</li>
                    <li> Basic Retouching $4/ image</li>
                    <li>Intermediate Retouching $5/image</li>
                    <li>Detailed Retouching $7/image</li>
                </ul>
            </div>
            <div class="price_plan">$29/month</div>
            <template v-if="getPlanName===MembershipPlanEnum.EXPERT">
                <div class="btn_box">
                    <div class="btn_p1 btn_p1_0 cp">Your Plan</div>
                </div>
                <div v-if="getContinuationPlan" class="cancel_memb"
                     @click="showModalCancelPlan(MembershipPlanEnum.EXPERT)">Cancel Memebership
                </div>
            </template>
            <template v-else>
                <div class="btn_box pb_box">
                    <div class="btn_p1 cp" @click="showAddPlanModal(MembershipPlanEnum.EXPERT)">Upgrade</div>
                </div>
            </template>
        </div>
        <div class="modal" id="cancelPlanModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Are you sure you want to cancel your subscription?</h5>
                    </div>
                    <div class="modal-footer">
                        <button class="btn_modal_t1" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn_modal_t2" @click="cancelPlan">Yes</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="addPlanModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Do you want to choose an {{ selected_new_plan }} plan?</h5>
                    </div>
                    <div class="modal-footer">
                        <button class="btn_modal_t1" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn_modal_t2" @click="addPlan">Yes</button>
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
import MembershipPlanEnum from "../enums/MembershipPlanEnum";

export default {
    name: "PaymentPlan",
    props: ['user'],
    components: {
        MembershipPlanEnum
    },
    data() {
        return {
            cancelPlanModal: null,
            addPlanModal: null,
            MembershipPlanEnum: MembershipPlanEnum,
            selected_cancel_plan: null,
            selected_new_plan: null,
            user_plan: {}
        };
    },
    destroyed() {
        if (this.cancelPlanModal) {
            this.cancelPlanModal.dispose()
        }
        if (this.addPlanModal) {
            this.addPlanModal.dispose()
        }
    },
    async mounted() {
        this.addPlanModal = new Modal(document.getElementById('addPlanModal'));
        this.cancelPlanModal = new Modal(document.getElementById('cancelPlanModal'));
        this.getPlan();
    },
    computed: {
        ...mapGetters([
            'getUser',
            'getStripeKey'
        ]),

        getPlanName() {
            return this.user_plan?.plan || null
        },
        getContinuationPlan() {
            console.log(this.user_plan)

            console.log(this.user_plan?.continuation_plan)
            return this.user_plan?.continuation_plan || false
        }
    },
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
        ]),
        getPlan() {
            this.user_plan = this.user?.plan || {}
        },
        showAddPlanModal(value) {
            this.selected_new_plan = value
            this.addPlanModal.show();
        },
        showModalCancelPlan(value) {
            this.cancelPlanModal.show()
        },
        async cancelPlan() {
            this.showLoader()
            try {
                await this.$http.postAuth(`${this.$http.apiUrl()}profile/cancel-membership_plan`);
                this.cancelPlanModal.hide()
                this.user_plan.continuation_plan = 0
                errorMessage('Success!')
            } catch (e) {
                if (e?.response?.data?.error?.message) {
                    errorMessage(e.response.data.error.message)
                } else {
                    errorMessage('ERROR')
                }
            }
            this.hideLoader()
        },
        async addPlan() {
            this.showLoader()
            try {
                const plan = await this.$http.postAuth(`${this.$http.apiUrl()}profile/add_membership_plan`, {
                    plan: this.selected_new_plan,
                    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                });
                this.user_plan = plan?.data?.data || {}
                this.addPlanModal.hide();
            } catch (e) {
                if (e?.response?.data?.error?.message) {
                    errorMessage(e.response.data.error.message)
                } else {
                    errorMessage('ERROR')
                }
            }
            this.hideLoader()
        },


    }

}
</script>

<style lang="scss" scoped>
.plans_box {
    display: flex;
    flex-wrap: wrap;
    gap: 13px;
}

.item_plan {
    flex: 1 1 15em;
    min-width: 229px;
    background: #FFFFFF;
    border: 0.3px solid #D8C3AF;
    box-sizing: border-box;
    box-shadow: 3px 3px 8px rgba(216, 195, 175, 0.4);
}

.plan_title {
    font-style: normal;
    font-weight: 600;
    font-size: 23px;
    line-height: 44px;
    color: #494949;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 30px 0 17px 0;
}

.list_p {
    font-style: normal;
    font-weight: 400;
    font-size: 16px;
    line-height: 24px;
    color: #000000;
    margin-bottom: 0;
}

.list_p li {
    padding: 1px 11px 10px 11px;
}

.price_plan {
    font-style: normal;
    font-weight: 600;
    font-size: 24px;
    line-height: 44px;
    color: #494949;
    display: flex;
    justify-content: center;
}

.font_weight {
    font-weight: 700;
}

.pb_box {
    padding-bottom: 50px;
}

.btn_box {
    padding-top: 17px;
    display: flex;
    justify-content: center;
}

.btn_p1 {
    background: #D8C3AF;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.25);
    border-radius: 100px;
    width: 125px;
    height: 40px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    line-height: 22px;
    color: #FFFFFF;
}

.btn_p1_0 {
    background: #FFFFFF;
    color: #AD967F;
}

.cancel_memb {
    font-style: normal;
    font-weight: 400;
    font-size: 16px;
    line-height: 20px;
    color: #AD967F;
    text-align: center;
    padding-top: 9px;
    cursor: pointer;
    padding-bottom: 20px;
}

.modal-title {
    width: 100%;
    text-align: center;
}

.modal-footer {
    border-top: none;
    display: flex;
    justify-content: center;
    padding-bottom: 30px;
    gap: 35px;
}

.modal-header {
    border-bottom: 0px solid #dee2e6;
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

@media only screen and (max-width: 992px) {
    .modal-footer {
        border-top: none;
        display: flex;
        justify-content: center;
        padding-bottom: 30px;
        gap: 15px;
    }
}
</style>
