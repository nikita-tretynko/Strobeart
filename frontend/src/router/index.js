import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'

Vue.use(VueRouter)

const routes = [
    {
        path: '/',
        name: 'Home',
        component: Home,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: '/profile',
        name: 'Profile',
        meta: {
            requiresAuth: true,
        },
        component: () => import(/* webpackChunkName: "profile" */ '../views/Profile')
    },
    {
        path: '/profile/settings',
        name: 'Settings',
        meta: {
            requiresAuth: true,
        },
        component: () => import(/* webpackChunkName: "settings" */ '../views/ProfileSettings')
    },
    {
        path: '/messages',
        name: 'Messages',
        meta: {
            requiresAuth: true,
        },
        component: () => import(/* webpackChunkName: "messages" */ '../views/Messages')
    },
    {
        path: '/registration',
        name: 'Registration',
        component: () => import(/* webpackChunkName: "registration" */ '../views/Registration')
    },
    {
        path: '/login',
        name: 'Login',
        component: () => import(/* webpackChunkName: "login" */ '../views/Login')
    },
    {
        path: '/set-new-password',
        name: 'SetNewPassword',
        component: () => import(/* webpackChunkName: "setnewpassword" */ '../views/SetNewPassword')
    },
    {
        path: '/add-new-job',
        name: 'AddNewJob',
        meta: {
            requiresAuth: true,
        },
        component: () => import(/* webpackChunkName: "AddNewJob" */ '../views/AddNewJob')
    },
    {
        path: '/business-homepage',
        name: 'BusinessHomePage',
        meta: {
            requiresAuth: true,
        },
        component: () => import(/* webpackChunkName: "BusinessHomePage" */ '../views/BusinessHomePage')
    },
    {
        path: '/file-approval/:id',
        name: 'FileApproval',
        meta: {
            requiresAuth: true,
        },
        component: () => import(/* webpackChunkName: "FileApproval" */ '../views/FileApproval')
    },
    {
        path: '/working-job/:id',
        name: 'WorkingJob',
        meta: {
            requiresAuth: true,
        },
        component: () => import(/* webpackChunkName: "WorkingJob" */ '../views/WorkingJob')
    },
    {
        path: '/job/:id/upload-files',
        name: 'UploadFiles',
        meta: {
            requiresAuth: true,
        },
        component: () => import(/* webpackChunkName: "UploadFiles" */ '../views/UploadFiles')
    },
    {
        path: '/job/:id/chat',
        name: 'Chat',
        meta: {
            requiresAuth: true,
        },
        component: () => import(/* webpackChunkName: "Chat" */ '../views/Chat')
    },
    {
        path: '/chats',
        name: 'ChatsList',
        meta: {
            requiresAuth: true,
        },
        component: () => import(/* webpackChunkName: "Chat" */ '../views/ChatsList')
    },
    {
        path: '/past-jobs',
        name: 'PastJobs',
        meta: {
            requiresAuth: true,
        },
        component: () => import(/* webpackChunkName: "PastJob" */ '../views/PastJob')
    },
]

const router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes
})
router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (localStorage.getItem('strobeart_jwt') == null) {
            next({
                path: '/login',
                params: {nextUrl: to.fullPath}
            })
        } else {
            next();
        }
    } else {
        next()
    }
})
export default router
