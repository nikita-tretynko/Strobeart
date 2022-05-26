<template>
    <div class="section_page pb-60">
        <div class="d-flex justify-content-center">
      <div class="title t-border">File/s have been uploaded successfully!</div>
            </div>
        <div class="d-flex justify-content-center flex-column pt-5">
            <div class="text_s">Would you like to continue</div>
            <div class="text_s">working on the same project?</div>
        </div>
        <div class="box_btn">
            <button class="btn-mc3 w_btn" @click="finishWorkImage(false)">No</button>
            <button class="btn-mc2 w_btn" @click="finishWorkImage(true)">Yes</button>
        </div>
    </div>
</template>

<script>
import {mapMutations} from 'vuex';
import {errorMessage} from "../services/messages";
import WorkedImagesStatusEnum from "../enums/WorkedImagesStatusEnum";
import JobWorkStepsEnum from "../enums/JobWorkStepsEnum";

export default {
    name: "JobEditorStep4",
    props: ['work_image'],

    components: {},
    data() {
        return {
            finish_work_image:{}
        };
    },
    mounted() {
    },
    computed: {},
    methods: {
        ...mapMutations([
            'showLoader',
            'hideLoader',
        ]),
       async finishWorkImage(data){
           try {
               this.showLoader();
               const response =  await this.$http.postAuth(`${this.$http.apiUrl()}finish-work-image`, {
                   'work_image_id':this.work_image?.id||null,
               });
               this.finish_work_image = response?.data?.data?.work_image|| {}
               const finish_job = response?.data?.data?.finish_job|| false

               if (data&&!finish_job){
                   this.$emit('jobEditorStep4',
                       JobWorkStepsEnum.JOB_WORK_STEP_1)
                   this.$emit('indexImage',
                       this.finish_work_image.number_file+1)
               }else{
                   this.$router.push({name: "Home"}).then();
               }
           } catch (e) {
               const message = e?.response?.data?.error?.message || 'ERROR';
               errorMessage(message)
           }
           this.hideLoader();
       },


    }
}
</script>

<style lang="scss" scoped>

.pb-60 {
    padding-bottom: 60px;
}

.section_page {
    padding-top: 102px;
}
.t-border{
    padding: 40px 50px;
    border-bottom: 1px solid #494949;
}

.title{
    font-family: Montserrat;
    font-style: normal;
    font-weight: 600;
    font-size: 24px;
    line-height: 50px;
    display: flex;
    align-items: center;
    color: #494949;

}
.text_s{
    font-style: normal;
    font-weight: normal;
    font-size: 20px;
    line-height: 24px;
    display: flex;
    align-items: center;
   justify-content: center;
    color: #494949;
}
.w_btn{
    width: 200px;
    height: 60px;

}
.box_btn{
    display: flex;
    justify-content: center;
    gap: 42px;
    padding-top: 80px;
}
@media only screen and (max-width: 992px) {
    .section_page {
        padding-top: 50px;
    }
.title{
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    line-height: 22px;
}
    .w_btn{
        width: 120px;
        height: 50px;
    }
}

</style>
