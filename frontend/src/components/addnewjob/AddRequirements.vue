<template>
    <div class="item_body">
        <div class="row">
            <div class="col-lg-6 col-12">
                <div class="row input_lines align-items-center">
                    <div class="col-6 main_text label_title">
                        Editing Level
                        <span class="red_color star">*</span>
                    </div>

                    <div class="col-6 required_select">
                        <select
                            class="form-select"
                            @click="dropError"
                            id="validationCustom04"
                            v-model="requirements.editing_level"
                            :class="{ 'is-invalid': errors.editing_level }"
                            required>
                            <option selected disabled hidden value="">simple, intermediate...</option>
                            <option v-for="(level, index) in editing_levels" :key="'editing-level-' + index">
                                {{ level }}
                            </option>
                        </select>
                        <div id="editingLevelFeedback" class="invalid-feedback">
                            Please choose editing level.
                        </div>
                    </div>
                </div>

                <div class="row input_lines align-items-center">
                    <div class="col-6 main_text label_title">
                        Style Guide
                        <!--                        <span class="red_color star">*</span>-->
                    </div>

                    <div class="col-6 required_select">
                        <select
                            class="form-select"
                            @click="dropError"
                            v-model="requirements.style_guide"
                            id="styleGuide"
                            @change="selectedStyleGuide">
                            <option selected disabled value="">jeans, lifestyle, social...</option>
                            <option v-for="(style, index) in style_guides" :value="style" :key="index">
                                {{ style.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="row input_lines align-items-center">
                    <div class="col-6 main_text label_title">
                        Color Profile
                        <span class="red_color star">*</span>
                    </div>

                    <div class="col-6 required_select">
                        <select
                            class="form-select"
                            @click="dropError"
                            v-model="requirements.color_profile"
                            id="colorProfile"
                            :class="{ 'is-invalid': errors.color_profile }"
                            required>
                            <option selected disabled hidden value="">RBG, CMYK, ICC...</option>
                            <option v-for="(color_prof, index) in color_profiles" :key="'color-profile-' + index">
                                {{ color_prof }}
                            </option>
                        </select>
                        <div id="colorProfileFeedback" class="invalid-feedback">
                            Please choose a color profile.
                        </div>
                    </div>
                </div>

                <div class="row input_lines align-items-center">
                    <div class="col-6 main_text label_title">
                        File Format
                        <span class="red_color star">*</span>
                    </div>

                    <div class="col-6 required_select">
                        <select
                            class="form-select"
                            @click="dropError"
                            v-model="requirements.file_format"
                            id="fileFormat"
                            :class="{ 'is-invalid': errors.file_format }"
                            required>
                            <option selected disabled hidden value="">PNG, JPEG, TIFF...</option>
                            <option v-for="(format, index) in file_formats" :key="'color-profile-' + index">
                                {{ format }}
                            </option>
                        </select>
                        <div id="fileFormatFeedback" class="invalid-feedback">
                            Please choose a file format.
                        </div>
                    </div>
                </div>

                <div class="row input_lines align-items-center">
                    <div class="col-6 main_text label_title">
                        Other
                    </div>

                    <div class="col-6">
                        <input type="text"
                               class="input_text_stile"
                               placeholder="Add pink background..."
                               v-model="requirements.other">
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="row input_lines align-items-center">
                    <div class="col-6 main_text label_title">
                        Add Logo
                    </div>
                    <div class="col-6 required_select">
                        <select
                            class="form-select"
                            v-model="requirements.add_logo"
                        >
                            <option selected disabled value="">Logo</option>
                            <option v-for="(file, index) in user_files" :value="file.id" :key="index">
                                {{ file.file_name }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="row input_lines align-items-center">
                    <div class="col-6 main_text label_title">
                        Add Watermark/s
                    </div>
                    <div class="col-6 required_select">
                        <select
                            class="form-select"
                            v-model="requirements.add_watermark"
                             >
                            <option selected disabled value="">Watermark</option>
                            <option v-for="(file, index) in user_files" :value="file.id" :key="index">
                                {{ file.file_name }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="row input_lines align-items-center">
                    <div class="col-6 main_text label_title">
                        Video instruction
                    </div>
                    <div class="col-6 required_select">
                        <select
                            class="form-select"
                            v-model="requirements.file_id_video_instruction"
                        >
                            <option selected disabled value="">Video instruction</option>
                            <option v-for="(file, index) in user_files" :value="file.id" :key="index">
                                {{ file.file_name }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="row input_lines align-items-center">
                    <div class="col-6 main_text label_title">
                        Color palette
                    </div>
                    <div class="col-6 required_select">
                        <select
                            class="form-select"
                            v-model="requirements.file_id_color_palette"
                        >
                            <option selected disabled value=""> Color palette</option>
                            <option v-for="(file, index) in user_files" :value="file.id" :key="index">
                                {{ file.file_name }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="row input_lines align-items-center">
                    <div class="col-6 main_text label_title">
                        Typography
                    </div>
                    <div class="col-6 required_select">
                        <select
                            class="form-select"
                            v-model="requirements.file_id_typography"
                        >
                            <option selected disabled value="">Typography</option>
                            <option v-for="(file, index) in user_files" :value="file.id" :key="index">
                                {{ file.file_name }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="row input_lines align-items-center">
                    <div class="col-6 main_text label_title">
                        White Background
                    </div>

                    <div class="col-6 section_checkbox">
                        <div class="input_checkbox cp" @click="checkedItem('white_background')">
                            <div class="checked_icon" v-if="requirements.checked_option.white_background">&#10004;</div>
                        </div>
                    </div>
                </div>

                <div class="row input_lines align-items-center">
                    <div class="col-6 main_text label_title">
                        Red Eye
                    </div>

                    <div class="col-6 section_checkbox">
                        <div class="input_checkbox cp" @click="checkedItem('red_eye')">
                            <div class="checked_icon" v-if="requirements.checked_option.red_eye">&#10004;</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-12">
                <div class="row input_lines">
                    <div class="col-12 red_color label_title">
                        Required
                        <span class="star">*</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center gap-5 m_t_60 m_b_126" v-if="window_desktop">
            <div class="text-center">
                <button class="text-center custom_button white_background main_text"
                        @click="goHome">
                    Cancel
                </button>
            </div>
            <div class=" text-center">
                <button
                    class="text-center custom_button braun_background white_text"
                    :class="validated ? '' : 'disabled_button'"
                    :disabled="!validated"
                    @click="nextTab">
                    Next
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import {errorMessage} from "../../services/messages";
import {mapMutations} from "vuex";

export default {
    name: 'AddRequirements',
    components: {},
    props: [
        'requirements',
        'validated_array',
        'errors',
    ],
    data() {
        return {
            sizeW: null,
            window_desktop: true,
            validated: true,
            editing_levels: [
                'Simple',
                'Intermediate',
                'Advanced',
            ],
            style_guides: [],
            user_files: [],
            color_profiles: [
                'RGB',
                'CMYK',
                'ICC',
            ],
            file_formats: [
                'PNG',
                'JPG',
                'JPEG',
                'TIFF',
                'GIF',
            ],
        }
    },
    async mounted() {
        await this.getUserStyleGuide()
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
        selectedStyleGuide(event) {
            console.log(event.target)
            this.requirements.color_profile = this.requirements?.style_guide?.color_profile || ''
            this.requirements.file_format = this.requirements?.style_guide?.file_format || ''
            this.requirements.other = this.requirements?.style_guide?.other || ''
            this.requirements.add_logo = this.requirements?.style_guide?.file_id_logo ||''
            this.requirements.checked_option.white_background = this.requirements?.style_guide?.remove_background == 'Yes' ? true : false
            this.requirements.add_watermark = this.requirements?.style_guide?.file_id_watermark ||''
            this.requirements.file_id_video_instruction = this.requirements?.style_guide?.file_id_video_instructions ||''
            this.requirements.file_id_color_palette = this.requirements?.style_guide?.file_id_color_palette ||''
            this.requirements.file_id_typography = this.requirements?.style_guide?.file_id_video_typography ||''
        },
        async getUserStyleGuide() {
            try {
                this.showLoader();
                const response = await this.$http.getAuth(`${this.$http.apiUrl()}profile/get-styles-job`);
                this.style_guides = response?.data?.data?.style_guides || [];
                this.user_files = response?.data?.data?.user_files || [];
            } catch (e) {
                const message = e?.response?.data?.error?.message || 'ERROR';
                errorMessage(message)
            }
            this.hideLoader();
        },
        updateWidth() {
            this.sizeW = document.documentElement.clientWidth;
        },
        goHome() {
            this.$router.push({to: '/business-homepage', name: "BusinessHomePage"});
        },
        checkedItem(val) {
            this.requirements.checked_option[val] = !this.requirements.checked_option[val];
        },
        nextTab() {
            for (let i = 0; i < this.validated_array.length; i++) {
                let test = this.requirements[this.validated_array[i]] ?? null;
                if (test === '' || test === null) {
                    this.validated = false;
                    this.errors[this.validated_array[i]] = true;
                }
            }
            if (!this.validated) {
                return;
            }
            this.$emit('next-tab');
        },
        dropError() {
            for (let x = 0; x < this.validated_array.length; x++) {
                this.errors[this.validated_array[x]] = false;
            }
            this.validated = true;
        },
    },
    computed: {},
    watch: {
        sizeW(val) {
            this.window_desktop = val > 992;
        },
    }
}
</script>

<style lang="scss" scoped>
$primary_font_family: 'Montserrat', sans-serif;
.section_checkbox, .checked_icon {
    display: flex;
    justify-content: center;
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

.item_body {
    width: 100%;
    min-height: 340px;
    padding: 11px;
}

.label_title {
    font-weight: normal;
    font-size: 13px;
    line-height: 18px;
    padding-top: 5px;
}

.input_text_stile {
    width: 100%;
    height: 38px;
    border-radius: 19px;
    padding-left: 14px;
    padding-right: 14px;
    background: #FFFFFF;
    border: 0.3px solid #494949;
    box-sizing: border-box;
    box-shadow: 0 3px 3px rgba(0, 0, 0, 0.15);
    filter: opacity(0.35);
    overflow: hidden;
    text-overflow: ellipsis;
}

.input_style {
    width: 100%;
    height: 30px;
    border-radius: 15px;
    padding-left: 14px;
    padding-right: 39px;
    appearance: none;
    background: url('../../../public/img/icons/ep_arrow-down-bold.svg') no-repeat right 10px center, #FFFFFF;
    border: 0.3px solid #494949;
    box-sizing: border-box;
    box-shadow: 0 3px 3px rgba(0, 0, 0, 0.15);
    filter: opacity(0.35);
    overflow: hidden;
    text-overflow: ellipsis;
}

.input_checkbox {
    width: 22px;
    height: 22px;
    background: #FFFFFF;
    border: 0.3px solid #494949;
    filter: opacity(0.35);
    padding: 0 0 2px;
}

.input_lines {
    margin: 14px 0;
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

.disabled_button {
    opacity: 0.45;
}

.required_select {
    position: relative;
}

.error_message {
    position: absolute;
    top: -58px;
    left: 0;
    background: #FFFFFF;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    box-shadow: 0 4px 4px rgba(0, 0, 0, 0.25);
    backdrop-filter: blur(4px);
    color: #FA0B0B;
    font-style: normal;
    font-weight: normal;
    font-size: 14px;
    line-height: 17px;
    padding: 19px;
}

.red_color {
    color: #bb2d3b;
}

.white_background {
    background: #FFFFFF;
}

.m_t_44 {
    margin-top: 44px;
}

.m_t_60 {
    margin-top: 60px;
}

.m_b_126 {
    margin-bottom: 126px;
}

.is-invalid {
    border: 1px solid #bb2d3b !important;
}
</style>
