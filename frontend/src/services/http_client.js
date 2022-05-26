import axios from "axios";

export default {
    imagesPath() {
        return process.env.VUE_APP_IMAGES_PATH;
    },
    formDataHeader() {
        return {
            'Content-Type': 'multipart/form-data'
        };
    },
    apiUrl() {
        return process.env.VUE_APP_API_URL;
    },
    async get(url, data = {}, headers = {}) {
        return await axios.get(url, {data, headers});
    },
    async getAuth(url, data = {}, headers = {}) {
        try {
            return await axios.get(url, {data, headers: Object.assign(this.getAuthHeader(), headers)});
        } catch (e) {
            if (e.response.status === 401) {
                await this.refreshToken();
                return await axios.get(url, {data, headers: Object.assign(this.getAuthHeader(), headers)});
            } else {
                throw e;
            }
        }
    },
    async post(url, data = {}, headers = {}) {
        return await axios.post(url, data, {headers: headers})
    },
    async postAuth(url, data = {}, headers = {}) {
        try {
            return await axios.post(url, data, {
                headers: Object.assign(this.getAuthHeader(), headers)
            });
        } catch (e) {
            if (e.response && e.response.status === 401) {
                await this.refreshToken();
                return await axios.post(url, data, {
                    headers: Object.assign(this.getAuthHeader(), headers)
                });
            } else {
                throw e;
            }
        }
    },
    async refreshToken() {
        try {
            const response = await axios.post(`${this.apiUrl()}refresh_token`, {}, {headers: this.getAuthHeader()})
            const token = response?.data?.data?.token || null;
            localStorage.setItem('strobeart_jwt', token);
            return token;
        } catch (e) {
            console.log('ERROR:', e);
            throw e;
        }
    },
    getAuthHeader() {
        return {Authorization: `Bearer ${localStorage.getItem('strobeart_jwt')}`};
    }
}
