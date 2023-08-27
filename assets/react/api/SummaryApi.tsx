import axios from "axios";

export const getSummaryDistance = async () => {
    return axios.get('/api/summary/distances').then(
        res => {
            console.log('res', res);
            return res.data;
        },
        err => console.log('err', err)
    );
}