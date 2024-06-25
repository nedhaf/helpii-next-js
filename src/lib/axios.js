import Axios from 'axios'

const csrf = () => axios.get('/sanctum/csrf-cookie')
const axios = Axios.create({
    baseURL: process.env.NEXT_PUBLIC_BACKEND_URL,
    headers: {
        "Access-Control-Allow-Origin": "*",
        "Content-Type": "application/json",
        'X-Requested-With': 'XMLHttpRequest',
    },
    withCredentials: true,
    // withXSRFToken: true
})

export default axios
