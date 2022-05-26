<template>
    <div class="section_page pb-60">
        <div class="title padding_t">Files</div>
        <div class="text pt-20 padding_t">Upload the edited files. You will get paid as soon as the file is approved by
            the company.
        </div>
        <div class="pt-110 pb_images images">
            <div class="img">
                <div class="img_d d-flex justify-content-center align-items-center">
                    <img class="img_d2" src="@/assets/images/bi_image.png" alt=""/>
                </div>
            </div>
            <div class="img_items_d">
                <div class="img_d im1"></div>
                <div class="img_d im2"></div>
            </div>
        </div>
        <div class="box_f">
            <div class="items_b opacity">
                <div class="pb-22"><img src="@/assets/icons/download.svg" alt=""/></div>
                <div class="text_2">download</div>
            </div>
            <div class="items_b " @click="uploadFile">
                <input id="fileUpload" type="file" accept="image/*" @change="uploadFileChange" hidden>
                <div class="pb-22"><img src="@/assets/icons/upload.svg" alt=""/></div>
                <div class="text_2">upload</div>
            </div>
        </div>
    </div>
</template>

<script>
import {mapMutations} from 'vuex';
import {errorMessage} from "../services/messages";
import WorkedImagesStatusEnum from "../enums/WorkedImagesStatusEnum";
import JobWorkStepsEnum from "../enums/JobWorkStepsEnum";

export default {
    name: "JobEditorStep1",
    props: ['work_image'],

    components: {},
    data() {
        return {
            uploaded_image: null,
            rawData: [],
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
        uploadFile() {
            document.getElementById("fileUpload").click();
        },
        async uploadFileChange(e) {
            const tmpFiles = e.target.files
            if (tmpFiles.length === 0) {
                return false;
            }
            const file = tmpFiles[0]
            this.uploaded_image = file
            const self = this
            const reader = new FileReader()
            reader.onload = function (e) {
                self.rawData.push(e.target.result)
            }
            reader.readAsDataURL(file)
            if (this.uploaded_image) {
                try {
                    this.showLoader();
                    let data = new FormData();
                    data.append('file', this.uploaded_image);
                    data.append('work_image_id', this.work_image.id);
                    await this.$http.postAuth(`${this.$http.apiUrl()}upload-work-image`, data, this.$http.formDataHeader());
                    this.$emit('jobEditorStep3',
                        JobWorkStepsEnum.JOB_WORK_STEP_4)
                } catch (e) {
                    const message = e?.response?.data?.error?.message || 'ERROR';
                    errorMessage(message)
                }
                this.hideLoader();
            }
        },
        async homePage() {
            this.$router.push({to: '/login', name: "Login"}).then();
        },
    }
}
</script>

<style lang="scss" scoped>
.pt-20 {
    padding-top: 20px;
}

.pt-110 {
    padding-top: 110px;
}

.pb-112, .pb_images {
    padding-bottom: 112px;
}

.pb-22 {
    padding-bottom: 22px;
}

.pb-60 {
    padding-bottom: 60px;
}

.section_page {
    padding-top: 102px;
}

.box_f {
    display: flex;
    justify-content: center;
    gap: 40px;
}

.items_b {
    display: flex;
    justify-content: center;
    flex-direction: column;
    text-align: center;
    cursor: pointer;
}

.title {
    font-style: normal;
    font-weight: 600;
    font-size: 35px;
    line-height: 44px;
    color: #494949;
}

.text {
    font-style: normal;
    font-weight: normal;
    font-size: 17px;
    line-height: 24px;
    color: #494949;
}

.text_2 {
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    line-height: 44px;
    color: #000000;
}

.images {
    display: flex;
    justify-content: center;
    padding-left: 34px;
}

.img_d {
    background: #F4F4F3;
    filter: drop-shadow(0px 2px 2px rgba(0, 0, 0, 0.2));
    border-radius: 5px;
    width: 370px;
    height: 354px;
}

.img {
    background-size: cover;
    filter: drop-shadow(0px 2px 2px rgba(0, 0, 0, 0.2));
    border-radius: 5px;
    width: 370px;
    height: 354px;
    z-index: 1;
}

.img_items_d {
    filter: drop-shadow(0px 2px 2px rgba(0, 0, 0, 0.2));
    border-radius: 5px;
    width: 370px;
    height: 354px;
    position: absolute;
}

.im1 {
    position: absolute;
    left: -44px;
    top: -44px;
}

.im2 {
    position: absolute;
    top: -22px;
    left: -22px;
}

.opacity {
    opacity: 0.5;
}

.btn-mc3:hover {
    background: #D8C3AF;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.25);
    border-radius: 100px;
    color: #FFFFFF;
}

.img_d2 {
    width: 67px;
    height: 67px;
}

@media only screen and (max-width: 992px) {
    .title {
        font-size: 18px;
        line-height: 22px;
    }
    .text {
        font-size: 12.5px;
        line-height: 20px;
    }
    .img {
        filter: drop-shadow(0px 2px 2px rgba(0, 0, 0, 0.2));
        border-radius: 5px;
        width: 284px;
        height: 272px;
        z-index: 1;
        background-size: cover;
    }
    .img_items_d {
        filter: drop-shadow(0px 2px 2px rgba(0, 0, 0, 0.2));
        border-radius: 5px;
        width: 284px;
        height: 272px;
        position: absolute;
    }
    .img_d {
        background: #F4F4F3;
        filter: drop-shadow(0px 2px 2px rgba(0, 0, 0, 0.2));
        border-radius: 5px;
        width: 284px;
        height: 272px;
    }
    .im1 {
        position: absolute;
        left: -34px;
        top: -34px;
    }
    .im2 {
        position: absolute;
        top: -17px;
        left: -17px;
    }
    .section_page {
        padding-top: 50px;
    }
    .pb_images {
        padding-bottom: 100px;
    }
    .padding_t {
        padding: 0 19px;
    }
}

</style>
