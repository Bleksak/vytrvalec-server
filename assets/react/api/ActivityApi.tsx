import axios from "axios";

export const getAllActivities = async () => {
    return axios.get('/api/activity/list').then(
        res => {
            console.log('res', res);
            return res.data;
        },
        err => console.log('err', err)
    );
}