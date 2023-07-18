import axios from "axios";

export const getUserData = async (id) => {
    return axios.get('/api/user/profile/' + id).then(
        res => {
            console.log('res', res);
            return res.data;
        },
        err => console.log('err', err)
    );
    // return (await axios.get('/api/user/profile/' + id)).data;
}

export const getUserSubmissions = async (page) => {
    return axios.get('/api/submission/list/' + page).then(
        res => {
            console.log('res', res);
            return res.data;
        },
        err => console.log('err', err)
    );
    // return (await axios.get('/api/submission/list/' + page)).data;
}